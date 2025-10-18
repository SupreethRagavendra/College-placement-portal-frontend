<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\SupabaseService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;


class StudentController extends Controller
{
    private SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function dashboard()
    {
        $userId = Auth::id();
        
        // Cache assessments for 10 minutes - AGGRESSIVE CACHING for speed
        $assessments = Cache::remember('student_assessments_list', 600, function() {
            return \App\Models\Assessment::active()
                ->withCount('questions')
                ->select('id', 'title', 'name', 'description', 'category', 'total_time', 'difficulty_level', 'total_marks', 'pass_percentage', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
        });

        // Cache user results for 5 minutes - AGGRESSIVE CACHING for speed
        $userResults = Cache::remember("student_results_{$userId}", 300, function() use ($userId) {
            return \App\Models\StudentResult::where('student_id', $userId)
                ->with('assessment:id,name,category')
                ->select('id', 'student_id', 'assessment_id', 'score', 'total_questions', 'time_taken', 'submitted_at')
                ->orderBy('submitted_at', 'desc')
                ->get();
        });

        // Map attempts to assessments for backward compatibility
        $attemptsByAssessment = [];
        foreach ($userResults as $result) {
            $attemptsByAssessment[$result->assessment_id] = [
                'score' => $result->score,
                'total_questions' => $result->total_questions,
                'time_taken' => $result->time_taken,
                'submitted_at' => $result->submitted_at,
            ];
        }

        // Compute statistics directly
        $totalTests = $userResults->count();
        $avgScore = 0;
        $recentTests = [];
        
        if ($totalTests > 0) {
            $totalScore = 0;
            $totalQuestions = 0;
            
            foreach ($userResults as $result) {
                $totalScore += $result->score;
                $totalQuestions += $result->total_questions;
            }
            
            $avgScore = $totalQuestions > 0 ? round(($totalScore / $totalQuestions) * 100) : 0;
            
            // Get recent tests (last 5)
            $recentAttempts = $userResults->take(5);
            foreach ($recentAttempts as $result) {
                $recentTests[] = [
                    'date' => $result->submitted_at ? $result->submitted_at->format('Y-m-d H:i:s') : null,
                    'category' => $result->assessment ? $result->assessment->category : 'Unknown',
                    'score' => $result->total_questions > 0 ? 
                        round(($result->score / $result->total_questions) * 100) : 0,
                    'assessment_id' => $result->assessment_id,
                    'assessment_name' => $result->assessment ? $result->assessment->title : 'Unknown Assessment',
                ];
            }
        }

        // Calculate rank (if possible)
        $rankText = '—';
        try {
            $totalStudents = \App\Models\User::where('role', 'student')->count();
            $betterCount = \App\Models\StudentResult::selectRaw('(score / total_questions) * 100 as percentage')
                ->where('student_id', $userId)
                ->get()
                ->filter(function($result) {
                    return $result->percentage > 0;
                })
                ->count();
            
            if ($betterCount > 0) {
                $rankText = '#' . ($betterCount + 1);
            }
        } catch (\Throwable $e) {
            // If calculation fails, keep default
        }

        // Compute trend data
        $trendLabels = [];
        $trendScores = [];
        if (!empty($recentTests)) {
            $trendLabels = array_map(function($test) {
                return \Carbon\Carbon::parse($test['date'])->format('M d');
            }, array_reverse($recentTests));
            
            $trendScores = array_map(function($test) {
                return $test['score'];
            }, array_reverse($recentTests));
        }

        return view('student.dashboard', compact(
            'totalTests', 'avgScore', 'rankText', 'recentTests'
        ))
        ->with('trendLabels', $trendLabels)
        ->with('trendScores', $trendScores)
        ->with('assessments', $assessments)
        ->with('attemptsByAssessment', $attemptsByAssessment);
    }

    public function categories()
    {
        // Get active assessments grouped by category
        $assessments = \App\Models\Assessment::active()
            ->withCount('questions') // Use withCount instead of with to avoid Closure issues
            ->orderBy('category')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Group by category
        $categories = $assessments->groupBy('category')->map(function ($assessments, $category) {
            return (object) [
                'id' => $category === 'Aptitude' ? 1 : 2,
                'name' => $category,
                'assessments' => $assessments,
                'total_assessments' => $assessments->count(),
                'total_questions' => $assessments->sum('questions_count')
            ];
        })->values();
        
        // If no categories found, provide default structure
        if ($categories->isEmpty()) {
            $categories = collect([
                (object) [
                    'id' => 1,
                    'name' => 'Aptitude',
                    'assessments' => collect(),
                    'total_assessments' => 0,
                    'total_questions' => 0
                ],
                (object) [
                    'id' => 2,
                    'name' => 'Technical',
                    'assessments' => collect(),
                    'total_assessments' => 0,
                    'total_questions' => 0
                ]
            ]);
        }
        
        return view('student.categories', ['categories' => $categories]);
    }

    public function test($id)
    {
        $categoryId = (int) $id;
        $categoryName = $categoryId === 1 ? 'Aptitude' : 'Technical';

        $userId = Auth::id();

        // Get the first active assessment for this category
        $assessment = \App\Models\Assessment::active()
            ->where('category', $categoryName)
            ->first();

        if (!$assessment) {
            return redirect()->route('student.categories')
                ->withErrors(['error' => 'No active assessment found for this category.']);
        }

        // Get questions for this assessment
        $questions = $assessment->questions()->where('is_active', true)->get();

        if ($questions->isEmpty()) {
            return redirect()->route('student.categories')
                ->withErrors(['error' => 'No questions available for this assessment.']);
        }

        // Create a student result record
        $studentResult = \App\Models\StudentResult::create([
            'student_id' => $userId,
            'assessment_id' => $assessment->id,
            'score' => 0,
            'total_questions' => $questions->count(),
            'time_taken' => 0,
            'submitted_at' => now(),
        ]);

        // Get duration from assessment (handle both duration and total_time fields)
        $duration = $assessment->total_time ?? $assessment->duration ?? 30;
        
        return view('student.test', [
            'category' => (object) ['id' => $categoryId, 'name' => $categoryName],
            'assessment' => $assessment,
            'questions' => $questions,
            'test_id' => $studentResult->id,
            'minutes' => (int)$duration,
        ]);
    }

    public function submitTest(Request $request, $id)
    {
        $request->validate([
            'test_id' => 'required|integer',
            'answers' => 'required|array',
        ]);

        $userId = Auth::id();
        $testId = (int) $request->input('test_id');
        $answers = $request->input('answers');

        // Get the student result
        $studentResult = \App\Models\StudentResult::where('id', $testId)
            ->where('student_id', $userId)
            ->first();

        if (!$studentResult) {
            return redirect()->route('student.categories')
                ->withErrors(['error' => 'Test not found.']);
        }

        // Get the assessment and questions
        $assessment = $studentResult->assessment;
        $questions = $assessment->questions()->whereIn('id', array_keys($answers))->get();

        $score = 0;
        $total = count($answers);

        // Calculate score
        foreach ($answers as $questionId => $selectedAnswer) {
            $question = $questions->find($questionId);
            if ($question) {
                // Check if the answer is correct
                // The answer comes as a letter (A, B, C, D)
                if ($question->isCorrectAnswer($selectedAnswer)) {
                    $score++;
                }
            }
        }

        // Update student result
        $studentResult->update([
            'score' => $score,
            'time_taken' => (int) $request->input('time_taken', 0),
            'submitted_at' => now(),
        ]);

        return redirect()->route('student.results', ['id' => $testId]);
    }

    public function results($id)
    {
        $userId = Auth::id();
        
        // Get the student result
        $studentResult = \App\Models\StudentResult::where('id', $id)
            ->where('student_id', $userId)
            ->with('assessment')
            ->first();

        if (!$studentResult) {
            return redirect()->route('student.categories')
                ->withErrors(['error' => 'Test result not found.']);
        }

        $result = (object) [
            'test_id' => $studentResult->id,
            'user_id' => $userId,
            'score' => $studentResult->score,
            'total' => $studentResult->total_questions,
            'time_taken' => $studentResult->time_taken,
            'created_at' => $studentResult->submitted_at,
            'assessment_name' => $studentResult->assessment->title ?? $studentResult->assessment->name ?? 'Unknown Assessment',
            'category' => $studentResult->assessment->category ?? 'Unknown',
        ];

        // Calculate peer average
        $peerAvg = \App\Models\StudentResult::where('assessment_id', $studentResult->assessment_id)
            ->where('id', '!=', $studentResult->id)
            ->avg('score') ?? 0;

        $analysis = [
            'percentage' => $studentResult->total_questions > 0 ? round(($studentResult->score / $studentResult->total_questions) * 100, 2) : 0,
            'peer_average' => round($peerAvg, 2),
        ];

        return view('student.results', compact('result', 'analysis', 'peerAvg'));
    }

    public function history()
    {
        $userId = Auth::id();
        $rows = $this->supabase->selectFrom('tests', ['student_id' => 'eq.' . $userId], [
            'order' => ['column' => 'attempted_at', 'asc' => false],
            'limit' => 100,
        ]);
        $history = array_map(function ($r) {
            return [
                'id' => $r['id'] ?? null,
                'attempted_at' => $r['attempted_at'] ?? null,
                'category' => $r['category'] ?? '',
                'score' => (int)($r['score'] ?? 0),
                'time_taken' => (int)($r['time_taken'] ?? 0),
            ];
        }, $rows);
        return view('student.history', compact('history'));
    }

    public function analytics()
    {
        $userId = Auth::id();
        $rows = DB::table('results')
            ->where('user_id', $userId)
            ->orderBy('created_at')
            ->limit(200)
            ->get(['test_id', 'score', 'total', 'created_at']);
        $trendLabels = collect($rows)->map(fn($r) => (string) Str::of((string) $r->created_at)->substr(0, 10))->all();
        $scoreTrends = collect($rows)->map(fn($r) => ($r->total ?? 0) > 0 ? round(($r->score / $r->total) * 100) : 0)->all();

        $testIds = collect($rows)->pluck('test_id')->filter()->unique()->values()->all();
        $categoryStats = ['Aptitude' => 0, 'Technical' => 0];
        if (!empty($testIds)) {
            $tests = DB::table('tests')->whereIn('id', $testIds)->get(['id', 'category_id']);
            $categoryIds = $tests->pluck('category_id')->unique()->values();
            $categories = DB::table('categories')->whereIn('id', $categoryIds)->pluck('name', 'id');
            $byTestId = collect($rows)->groupBy('test_id');
            foreach ($byTestId as $tid => $attempts) {
                $test = $tests->firstWhere('id', (int) $tid);
                if (!$test) continue;
                $name = $categories[$test->category_id] ?? null;
                if (!in_array($name, ['Aptitude','Technical'])) continue;
                $avg = $attempts->map(fn($r) => ($r->total ?? 0) > 0 ? ($r->score / $r->total) * 100 : 0)->avg();
                $categoryStats[$name] = round($categoryStats[$name] + ($avg ?? 0));
            }
        }

        return view('student.analytics', compact('trendLabels', 'scoreTrends', 'categoryStats'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('student.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|min:2',
            'email' => 'required|email',
            'password' => 'nullable|min:6',
        ]);
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
        $user->save();

        // Mirror to Supabase students table if present
        try {
            $exists = DB::table('students')->where('id', $user->id)->exists();
            if ($exists) {
                DB::table('students')->where('id', $user->id)->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'updated_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {}

        return back()->with('status', 'Profile updated!');
    }

    
}
