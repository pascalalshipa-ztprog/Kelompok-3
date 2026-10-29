<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $complaints = Complaint::query();

        if (! $request->user()->isAdmin()) {
            $complaints->whereBelongsTo($request->user());
        }

        $summary = (clone $complaints)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $recentComplaints = (clone $complaints)
            ->with(['user', 'category'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('dashboard.index', [
            'summary' => $summary,
            'totalCount' => (clone $complaints)->count(),
            'recentComplaints' => $recentComplaints,
        ]);
    }
}
