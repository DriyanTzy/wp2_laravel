<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // GET /api/posts/feed
    public function feed()
    {
        $posts = Post::with('user')->latest()->get();
        $user = auth()->user();
        foreach ($posts as $post) {
            // Cek apakah user sudah like (opsional, jika ada tabel likes)
            $post->is_liked = false;
        }
        return response()->json(['posts' => $posts]);
    }

    // POST /api/posts
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'nullable|string',
            'survey_link' => 'nullable|url|max:500',
        ]);

        $post = $request->user()->posts()->create($validated);

        return response()->json([
            'message' => 'Postingan berhasil dibuat',
            'post'    => $post->load('user')
        ], 201);
    }

    // POST /api/posts/{id}/like
    public function toggleLike(Post $post)
    {
        // Implementasi like/unlike jika punya tabel likes
        // Sementara return dummy
        return response()->json([
            'liked' => true,
            'likes_count' => $post->likes_count + 1
        ]);
    }
}