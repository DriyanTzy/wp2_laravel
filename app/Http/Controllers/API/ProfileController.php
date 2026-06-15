<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    // GET /api/profile/{username} — publik, bisa dilihat siapapun
    public function show($username)
    {
        $user = User::where('username', $username)->orWhere('name', $username)->firstOrFail();

        // Statistik
        $datasets = Dataset::where('user_id', $user->id)->withCount('accessedBy')->latest()->get();
        $totalDownloads = $datasets->sum('present_count');
        $avgRating = $datasets->avg('rating') ?? 4.4; // asumsi ada kolom rating atau dummy

        // Postingan user
        $posts = Post::where('user_id', $user->id)->with('dataset')->latest()->get();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'photo' => $user->photo,
                'bio' => $user->bio,
                'institution' => $user->institution,
                'location' => $user->location,
                'joined' => $user->created_at->format('F Y'),
            ],
            'stats' => [
                'total_datasets' => $datasets->count(),
                'total_downloads' => $totalDownloads,
                'avg_rating' => number_format($avgRating, 1),
            ],
            'datasets' => $datasets->map(function($ds) {
                return [
                    'id' => $ds->id,
                    'title' => $ds->title,
                    'class' => $ds->class,
                    'present_count' => $ds->present_count ?? 0,
                    'created_at' => $ds->created_at->diffForHumans(),
                ];
            }),
            'posts' => $posts->map(function($post) {
                return [
                    'id' => $post->id,
                    'content' => $post->content,
                    'dataset' => $post->dataset ? [
                        'id' => $post->dataset->id,
                        'title' => $post->dataset->title,
                        'class' => $post->dataset->class,
                    ] : null,
                    'likes_count' => $post->likes_count,
                    'comments_count' => $post->comments_count,
                    'shares_count' => $post->shares_count,
                    'created_at' => $post->created_at->diffForHumans(),
                ];
            }),
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
            'password'         => ['required', Password::min(8)->letters()->numbers()],
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
    public function showPublic($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        return view('profile.public', compact('user'));
    }

    public function edit()
    {
        return view('profile.edit'); // form edit profil Anda yang lama
    }
}
