<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        if ($request->routeIs('admin.dashboard') && ! $request->user()->isAdmin()) {
            abort(403);
        }

        if ($request->routeIs('dashboard') && $request->user()->isAdmin()) {
            abort(403);
        }

        $dashboardRoute = $request->user()->isAdmin()
            ? 'admin.dashboard'
            : 'dashboard';

        if (! $request->routeIs($dashboardRoute)) {
            return redirect()->route($dashboardRoute);
        }

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

        $view = $request->user()->isAdmin()
            ? 'dashboard.admin'
            : 'dashboard.user';

        return view($view, [
            'summary' => $summary,
            'totalCount' => (clone $complaints)->count(),
            'recentComplaints' => $recentComplaints,
        ]);
    }
}
