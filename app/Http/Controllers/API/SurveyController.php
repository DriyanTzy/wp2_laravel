<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    // GET /api/surveys — semua survey (untuk browse)
    public function index(): JsonResponse
    {
        $surveys = Survey::with('user:id,name,username,photo')
            ->where('is_active', true)
            ->withCount('responses')
            ->latest()
            ->get();

        return response()->json(['surveys' => $surveys]);
    }

    // POST /api/surveys — buat survey baru
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'link'             => ['required', 'url'],
            'target_responses' => ['nullable', 'integer', 'min:1'],
        ]);

        $survey = $request->user()->surveys()->create($validated);

        return response()->json([
            'message' => 'Survey berhasil dibuat.',
            'survey'  => $survey,
        ], 201);
    }

    // PUT /api/surveys/{id} — edit survey milik sendiri
    public function update(Request $request, Survey $survey): JsonResponse
    {
        if ($survey->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'title'            => ['sometimes', 'string', 'max:255'],
            'description'      => ['sometimes', 'nullable', 'string'],
            'link'             => ['sometimes', 'url'],
            'is_active'        => ['sometimes', 'boolean'],
            'target_responses' => ['sometimes', 'integer', 'min:1'],
        ]);

        $survey->update($validated);

        return response()->json([
            'message' => 'Survey berhasil diupdate.',
            'survey'  => $survey->fresh(),
        ]);
    }

    // DELETE /api/surveys/{id}
    public function destroy(Request $request, Survey $survey): JsonResponse
    {
        if ($survey->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $survey->delete();

        return response()->json(['message' => 'Survey berhasil dihapus.']);
    }

    // POST /api/surveys/{id}/respond — catat bahwa user ini udah isi survey + kasih poin
    public function respond(Request $request, Survey $survey): JsonResponse
    {
        $user = $request->user();

        // Gak bisa isi survey sendiri
        if ($survey->user_id === $user->id) {
            return response()->json([
                'message' => 'Kamu tidak bisa mengisi survey milik sendiri.',
            ], 403);
        }

        // Cek udah pernah isi belum
        if ($user->filledSurveys()->where('survey_id', $survey->id)->exists()) {
            return response()->json([
                'message' => 'Kamu sudah pernah mengisi survey ini.',
            ], 409);
        }

        // Catat respons + tambah 1 poin ke user yang isi
        $user->filledSurveys()->attach($survey->id);
        $user->increment('points', 1);

        return response()->json([
            'message'        => 'Respons berhasil dicatat. Kamu mendapat 1 poin!',
            'points'         => $user->fresh()->points,
            'survey_link'    => $survey->link,
        ]);
    }
}