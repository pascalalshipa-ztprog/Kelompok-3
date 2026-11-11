<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        return view('profile.edit', compact('user'));
    }

    public function photo(Request $request): StreamedResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        $photoPath = $user->profile_photo_path;
        abort_if($photoPath === null || ! Storage::disk('public')->exists($photoPath), 404);

        $photoStream = Storage::disk('public')->readStream($photoPath);

        if ($photoStream === false) {
            throw new RuntimeException('Unable to read the stored profile photo.');
        }

        return response()->stream(function () use ($photoStream): void {
            fpassthru($photoStream);
            fclose($photoStream);
        }, 200, [
            'Content-Type' => Storage::disk('public')->mimeType($photoPath) ?: 'application/octet-stream',
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        $request->validate([
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:500'],
        ]);

        $previousPhotoPath = $user->profile_photo_path;
        $newPhotoPath = null;

        if ($request->hasFile('profile_photo')) {
            $newPhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');

            if ($newPhotoPath === false) {
                throw new RuntimeException('Unable to store the uploaded profile photo.');
            }

            try {
                $user->update(['profile_photo_path' => $newPhotoPath]);
            } catch (Throwable $exception) {
                Storage::disk('public')->delete($newPhotoPath);

                throw $exception;
            }
        }

        if ($newPhotoPath !== null && $previousPhotoPath !== null
            && ! Storage::disk('public')->delete($previousPhotoPath)) {
            Log::warning('Unable to remove the previous profile photo.', [
                'user_id' => $user->id,
                'profile_photo_path' => $previousPhotoPath,
            ]);
        }

        $routeName = $user->isAdmin() ? 'admin.profile.edit' : 'profile.edit';

        return redirect()->route($routeName)->with('status', 'Profil berhasil diperbarui.');
    }
}
