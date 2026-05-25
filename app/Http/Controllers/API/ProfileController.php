<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    // GET /api/profile/{username} — publik, bisa dilihat siapapun
    public function show(string $username): JsonResponse
    {
        $user = User::where('username', $username)
            ->with(['posts' => fn($q) => $q->latest()])
            ->firstOrFail();

        return response()->json([
            'user' => [
                'id'       => $user->id,
                'name'     => $user->name,
                'username' => $user->username,
                'bio'      => $user->bio,
                'photo'    => $user->photo ? Storage::url($user->photo) : null,
            ],
            'posts' => $user->posts,
        ]);
    }

    // GET /api/profile — profil sendiri (butuh login)
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['posts' => fn($q) => $q->latest()]);

        return response()->json([
            'user'  => $user,
            'posts' => $user->posts,
        ]);
    }

    // PUT /api/profile — edit profil sendiri
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'     => ['sometimes', 'string', 'max:255'],
            'username' => ['sometimes', 'string', 'max:50', 'unique:users,username,' . $user->id, 'alpha_dash'],
            'bio'      => ['sometimes', 'nullable', 'string', 'max:255'],
            'photo'    => ['sometimes', 'nullable', 'image', 'max:2048'], // max 2MB
        ]);

        // Upload foto kalau ada
        if ($request->hasFile('photo')) {
            // Hapus foto lama kalau ada
            if ($user->photo) {
                Storage::delete($user->photo);
            }
            $validated['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profil berhasil diupdate.',
            'user'    => $user->fresh(),
        ]);
    }

    // PUT /api/profile/password — ganti password
    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Password saat ini salah.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Password berhasil diubah.',
        ]);
    }
}