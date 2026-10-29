<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Complaint;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ComplaintController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Complaint::class);

        $complaints = Complaint::query()->with(['user', 'category']);
        if (! $request->user()->isAdmin()) {
            $complaints->whereBelongsTo($request->user());
        }

        $search = trim($request->string('search')->toString());
        if ($search !== '') {
            $complaints->where(function ($query) use ($search): void {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%'.$search.'%'))
                    ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', '%'.$search.'%'));
            });
        }

        $complaints = $complaints
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('complaints.index', compact('complaints', 'search'));
    }

    public function create(): View
    {
        Gate::authorize('create', Complaint::class);

        return view('complaints.form', [
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Complaint::class);
        $validated = $request->validate([
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->where('is_active', true)],
            'title' => ['required', 'string', 'min:5', 'max:140'],
            'description' => ['required', 'string', 'min:10', 'max:3000'],
            'location' => ['nullable', 'string', 'max:180'],
        ]);

        $complaint = $request->user()->complaints()->create($validated);

        return redirect()->route('complaints.show', $complaint)->with('status', 'Pengaduan berhasil dikirim.');
    }

    public function show(Complaint $complaint): View
    {
        Gate::authorize('view', $complaint);

        return view('complaints.show', [
            'complaint' => $complaint->load(['user', 'category']),
        ]);
    }

    public function edit(Complaint $complaint): View
    {
        Gate::authorize('update', $complaint);

        return view('complaints.form', [
            'complaint' => $complaint,
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Complaint $complaint): RedirectResponse
    {
        Gate::authorize('update', $complaint);
        $isAdmin = $request->user()->isAdmin();
        $validated = $request->validate([
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->where('is_active', true)],
            'title' => ['required', 'string', 'min:5', 'max:140'],
            'description' => ['required', 'string', 'min:10', 'max:3000'],
            'location' => ['nullable', 'string', 'max:180'],
            'status' => [Rule::requiredIf($isAdmin), 'nullable', Rule::in(['Menunggu', 'Diproses', 'Selesai', 'Ditolak'])],
        ]);

        if (! $isAdmin) {
            unset($validated['status']);
        }

        $complaint->update($validated);

        return redirect()->route('complaints.show', $complaint)->with('status', 'Pengaduan berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Complaint $complaint): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['Menunggu', 'Diproses', 'Selesai', 'Ditolak'])],
        ]);

        $complaint->update($validated);

        return back()->with('status', 'Status pengaduan berhasil diperbarui.');
    }

    public function destroy(Complaint $complaint): RedirectResponse
    {
        Gate::authorize('delete', $complaint);
        $complaint->delete();

        return redirect()->route('complaints.index')->with('status', 'Pengaduan berhasil dihapus.');
    }
}
