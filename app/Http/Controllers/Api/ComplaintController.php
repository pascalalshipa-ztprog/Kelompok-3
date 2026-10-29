<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ComplaintResource;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ComplaintController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $complaints = Complaint::query()->with(['category', 'user']);

        if (! $request->user()->isAdmin()) {
            $complaints->whereBelongsTo($request->user());
        }

        $search = trim($request->string('search')->toString());
        if ($search !== '') {
            $complaints->where(function ($query) use ($search): void {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', '%'.$search.'%'));
            });
        }

        return ComplaintResource::collection(
            $complaints->orderByDesc('created_at')->orderByDesc('id')->paginate(10)->withQueryString(),
        );
    }
}
