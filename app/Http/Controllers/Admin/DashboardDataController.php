<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardDataController extends Controller
{
    /**
     * Get dashboard stats for AJAX updates
     */
    public function getStats()
    {
        // Clear any query cache
        \DB::connection()->disableQueryLog();
        
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'approved_students' => User::approved()->count(),
            'pending_students' => User::pending()->count(),
            'rejected_students' => User::rejected()->count(),
        ];

        $pendingStudents = User::pending()
            ->latest()
            ->take(5)
            ->get(['id', 'name', 'email', 'created_at'])
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'created_at' => $student->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'pending_students' => $pendingStudents,
            'timestamp' => now()->toDateTimeString(),
        ])
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
    }
}
