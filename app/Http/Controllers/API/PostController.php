<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // POST /api/posts — buat post baru
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'content'     => ['nullable', 'string'],
            'survey_link' => ['nullable', 'url'],
        ]);

        $post = $request->user()->posts()->create($validated);

        return response()->json([
            'message' => 'Post berhasil dibuat.',
            'post'    => $post,
        ], 201);
    }

    // PUT /api/posts/{id} — edit post
    public function update(Request $request, Post $post): JsonResponse
    {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'title'       => ['sometimes', 'string', 'max:255'],
            'content'     => ['sometimes', 'nullable', 'string'],
            'survey_link' => ['sometimes', 'nullable', 'url'],
        ]);

        $post->update($validated);

        return response()->json([
            'message' => 'Post berhasil diupdate.',
            'post'    => $post->fresh(),
        ]);
    }

    // DELETE /api/posts/{id}
    public function destroy(Request $request, Post $post): JsonResponse
    {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post berhasil dihapus.']);
    }
}