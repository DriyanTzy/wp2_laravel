<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DatasetController extends Controller
{
    // GET /api/datasets — list semua dataset
    public function index(): JsonResponse
    {
        $datasets = Dataset::with('user:id,name,username,photo')
            ->withCount('accessedBy')
            ->latest()
            ->get();

        return response()->json(['datasets' => $datasets]);
    }

    // GET /api/datasets/{id} — detail 1 dataset
    public function show(Dataset $dataset): JsonResponse
    {
        $dataset->load('user:id,name,username,photo');
        $dataset->loadCount('accessedBy');

        return response()->json(['dataset' => $dataset]);
    }

    // POST /api/datasets — upload dataset baru (butuh login)
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'class'           => ['required', 'string', 'max:100'],
            'description'     => ['nullable', 'string'],
            'thumbnail'       => ['nullable', 'image', 'max:2048'],
            'file'            => ['required', 'file', 'max:10240'],
            'points_required' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $validated['file_path'] = $request->file('file')->store('datasets', 'public');

        $dataset = $request->user()->datasets()->create($validated);

        return response()->json([
            'message' => 'Dataset berhasil diupload.',
            'dataset' => $dataset,
        ], 201);
    }

    // PUT /api/datasets/{id} — edit dataset milik sendiri
    public function update(Request $request, Dataset $dataset): JsonResponse
    {
        if ($dataset->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'title'           => ['sometimes', 'string', 'max:255'],
            'class'           => ['sometimes', 'string', 'max:100'],
            'description'     => ['sometimes', 'nullable', 'string'],
            'thumbnail'       => ['sometimes', 'nullable', 'image', 'max:2048'],
            'file'            => ['sometimes', 'file', 'max:10240'],
            'points_required' => ['sometimes', 'integer', 'min:1'],
        ]);

        // Ganti thumbnail kalau ada yang baru
        if ($request->hasFile('thumbnail')) {
            if ($dataset->thumbnail) Storage::delete($dataset->thumbnail);
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        // Ganti file dataset kalau ada yang baru
        if ($request->hasFile('file')) {
            if ($dataset->file_path) Storage::delete($dataset->file_path);
            $validated['file_path'] = $request->file('file')->store('datasets', 'public');
        }

        $dataset->update($validated);

        return response()->json([
            'message' => 'Dataset berhasil diupdate.',
            'dataset' => $dataset->fresh(),
        ]);
    }

    // POST /api/datasets/{id}/access — ambil dataset pakai poin
    public function access(Request $request, Dataset $dataset): JsonResponse
    {
        $user = $request->user();

        if ($user->accessedDatasets()->where('dataset_id', $dataset->id)->exists()) {
            return response()->json([
                'message'  => 'Kamu sudah pernah mengakses dataset ini.',
                'file_url' => Storage::url($dataset->file_path),
            ]);
        }

        if ($user->points < $dataset->points_required) {
            return response()->json([
                'message' => "Poin tidak cukup. Kamu butuh {$dataset->points_required} poin, kamu punya {$user->points} poin.",
            ], 403);
        }

        $user->decrement('points', $dataset->points_required);
        $user->accessedDatasets()->attach($dataset->id);
        $dataset->increment('present_count');

        return response()->json([
            'message'          => 'Dataset berhasil diakses.',
            'file_url'         => Storage::url($dataset->file_path),
            'points_remaining' => $user->fresh()->points,
        ]);
    }

    // DELETE /api/datasets/{id} — hapus dataset milik sendiri
    public function destroy(Request $request, Dataset $dataset): JsonResponse
    {
        if ($dataset->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        Storage::delete([$dataset->file_path, $dataset->thumbnail]);
        $dataset->delete();

        return response()->json(['message' => 'Dataset berhasil dihapus.']);
    }
}