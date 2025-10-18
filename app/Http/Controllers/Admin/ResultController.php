<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\StudentAssessment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultController extends Controller
{
    /**
     * Display a listing of results for an assessment
     */
    public function index(Assessment $assessment): View
    {
        $studentAssessments = $assessment->studentAssessments()
            ->with('student')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('admin.results.index', compact('assessment', 'studentAssessments'));
    }

    /**
     * Display the specified result
     */
    public function show(StudentAssessment $studentAssessment): View
    {
        $studentAssessment->load(['student', 'assessment', 'studentAnswers.question']);
        return view('admin.results.show', compact('studentAssessment'));
    }

    /**
     * Export assessment results to CSV
     */
    public function export(Assessment $assessment)
    {
        $studentAssessments = $assessment->studentAssessments()
            ->with('student')
            ->where('status', 'completed')
            ->orderBy('percentage', 'desc')
            ->get();
        
        $fileName = 'assessment_' . $assessment->id . '_results_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];
        
        $callback = function() use ($studentAssessments, $assessment) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add assessment metadata
            fputcsv($file, ['Assessment Report']);
            fputcsv($file, ['Assessment Title', $assessment->name]);
            fputcsv($file, ['Category', $assessment->category]);
            fputcsv($file, ['Total Marks', $assessment->total_marks]);
            fputcsv($file, ['Pass Percentage', $assessment->pass_percentage . '%']);
            fputcsv($file, ['Total Attempts', $studentAssessments->count()]);
            fputcsv($file, ['Passed', $studentAssessments->where('pass_status', 'pass')->count()]);
            fputcsv($file, ['Failed', $studentAssessments->where('pass_status', 'fail')->count()]);
            fputcsv($file, ['Average Score', $studentAssessments->avg('percentage') ? number_format($studentAssessments->avg('percentage'), 2) . '%' : '0%']);
            fputcsv($file, ['Generated On', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, []);  // Empty row
            
            // Headers
            fputcsv($file, ['Student Name', 'Email', 'Score', 'Percentage', 'Status', 'Time Taken', 'Submitted At']);
            
            // Data rows
            foreach ($studentAssessments as $result) {
                fputcsv($file, [
                    $result->student->name,
                    $result->student->email,
                    $result->obtained_marks . '/' . $result->total_marks,
                    number_format($result->percentage, 2) . '%',
                    ucfirst($result->pass_status),
                    gmdate('H:i:s', $result->time_taken),
                    $result->submit_time ? $result->submit_time->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export detailed results with question-wise analysis
     */
    public function exportDetailed(Assessment $assessment)
    {
        $studentAssessments = $assessment->studentAssessments()
            ->with(['student', 'studentAnswers.question'])
            ->where('status', 'completed')
            ->orderBy('percentage', 'desc')
            ->get();
        
        $fileName = 'assessment_' . $assessment->id . '_detailed_results_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];
        
        $callback = function() use ($studentAssessments, $assessment) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xF));
            
            // Add assessment metadata
            fputcsv($file, ['Detailed Assessment Report with Question-wise Analysis']);
            fputcsv($file, ['Assessment', $assessment->name]);
            fputcsv($file, []);
            
            foreach ($studentAssessments as $result) {
                // Student header
                fputcsv($file, ['Student', $result->student->name]);
                fputcsv($file, ['Score', $result->obtained_marks . '/' . $result->total_marks]);
                fputcsv($file, ['Percentage', number_format($result->percentage, 2) . '%']);
                fputcsv($file, ['Status', ucfirst($result->pass_status)]);
                fputcsv($file, []);
                
                // Question headers
                fputcsv($file, ['Q#', 'Question', 'Student Answer', 'Correct Answer', 'Result', 'Marks', 'Time Spent']);
                
                // Questions
                foreach ($result->studentAnswers as $index => $answer) {
                    fputcsv($file, [
                        $index + 1,
                        strip_tags($answer->question->question ?? $answer->question->question_text),
                        $answer->student_answer ?? 'No answer',
                        chr(65 + $answer->question->correct_option),
                        $answer->is_correct ? 'Correct' : 'Incorrect',
                        $answer->marks_obtained . '/' . $answer->question->marks,
                        $answer->time_spent ? $answer->time_spent . 's' : 'N/A'
                    ]);
                }
                
                fputcsv($file, []);
                fputcsv($file, []);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}