<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

        $isAdmin = $request->user()->isAdmin();
        $viewData = [
            'summary' => $summary,
            'totalCount' => (clone $complaints)->count(),
        ];

        if ($isAdmin) {
            $userCounts = User::query()
                ->selectRaw('role, COUNT(*) as total')
                ->groupBy('role')
                ->pluck('total', 'role');

            $viewData['userPieChart'] = $this->makePieChart([
                ['label' => 'Mahasiswa', 'value' => (int) $userCounts->get('student', 0), 'color' => '#16856d'],
                ['label' => 'Admin', 'value' => (int) $userCounts->get('admin', 0), 'color' => '#e6a23c'],
            ]);
            $viewData['complaintPieChart'] = $this->makePieChart([
                ['label' => 'Menunggu', 'value' => (int) $summary->get('Menunggu', 0), 'color' => '#e6a23c'],
                ['label' => 'Diproses', 'value' => (int) $summary->get('Diproses', 0), 'color' => '#5796bd'],
                ['label' => 'Selesai', 'value' => (int) $summary->get('Selesai', 0), 'color' => '#16856d'],
                ['label' => 'Ditolak', 'value' => (int) $summary->get('Ditolak', 0), 'color' => '#cf6258'],
            ]);
        } else {
            $viewData['recentComplaints'] = (clone $complaints)
                ->with(['user', 'category'])
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->limit(5)
                ->get()
                ->sortBy('id')
                ->values();
        }

        return view($isAdmin ? 'dashboard.admin' : 'dashboard.user', $viewData);
    }

    /**
     * @param  list<array{label: string, value: int, color: string}>  $slices
     * @return array{total: int, gradient: string, slices: list<array{label: string, value: int, color: string}>}
     */
    private function makePieChart(array $slices): array
    {
        $total = array_sum(array_column($slices, 'value'));
        $offset = 0;
        $gradientSlices = [];

        foreach ($slices as $slice) {
            $end = $total === 0
                ? 0
                : $offset + ($slice['value'] / $total * 100);

            $gradientSlices[] = sprintf(
                '%s %s%% %s%%',
                $slice['color'],
                number_format($offset, 4, '.', ''),
                number_format($end, 4, '.', ''),
            );
            $offset = $end;
        }

        $gradient = $total === 0
            ? 'conic-gradient(#e8eeea 0% 100%)'
            : 'conic-gradient('.implode(', ', $gradientSlices).')';

        return [
            'total' => $total,
            'gradient' => $gradient,
            'slices' => $slices,
        ];
    }
}
