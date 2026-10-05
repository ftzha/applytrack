<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Count applications by status for the authenticated user.
        $statusCounts = Application::query()
            ->where('user_id', $request->user()->id)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $recentApplications = Application::query()
            ->where('user_id', $request->user()->id)
            ->latest() // Newest-created records come first
            ->limit(5)
            ->get([
                'id',
                'company_name',
                'position',
                'status',
                'applied_at',
                'created_at',
            ]);

        return response()->json([
            'data' => [
                'total' => $statusCounts->sum(),
                'interested' => (int) ($statusCounts['interested'] ?? 0),
                'applied' => (int) ($statusCounts['applied'] ?? 0),
                'screening' => (int) ($statusCounts['screening'] ?? 0),
                'interview' => (int) ($statusCounts['interview'] ?? 0),
                'offer' => (int) ($statusCounts['offer'] ?? 0),
                'rejected' => (int) ($statusCounts['rejected'] ?? 0),
                'withdrawn' => (int) ($statusCounts['withdrawn'] ?? 0),

                'recent_applications' => $recentApplications,
            ],
        ]);
    }
}
