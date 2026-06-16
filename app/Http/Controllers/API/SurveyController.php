<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SurveyController extends Controller
{
    // GET /api/surveys
    public function index(): JsonResponse
    {
        $surveys = Survey::with('user:id,name,username,photo')
            ->where('is_active', true)
            ->withCount('responses')
            ->latest()
            ->get();

        return response()->json(['surveys' => $surveys]);
    }

    // POST /api/surveys
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'link'             => ['required', 'url'],
            'target_responses' => ['nullable', 'integer', 'min:1'],
            'image'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('surveys', 'public');
        }
        unset($validated['image']);

        $survey = $request->user()->surveys()->create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Survey berhasil dibuat.',
                'survey'  => $survey,
            ], 201);
        }

        return redirect()->route('surveys.index')->with('success', 'Survey berhasil dibuat!');
    }

    // PUT /api/surveys/{id}
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
            'image'            => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            if ($survey->image_path) {
                Storage::disk('public')->delete($survey->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('surveys', 'public');
        }
        unset($validated['image']);

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

        if ($survey->image_path) {
            Storage::disk('public')->delete($survey->image_path);
        }

        $survey->delete();

        return response()->json(['message' => 'Survey berhasil dihapus.']);
    }

    // POST /api/surveys/{id}/respond
    public function respond(Request $request, Survey $survey): JsonResponse
    {
        $user = $request->user();

        if ($survey->user_id === $user->id) {
            return response()->json([
                'message' => 'Kamu tidak bisa mengisi survey milik sendiri.',
            ], 403);
        }

        if ($user->filledSurveys()->where('survey_id', $survey->id)->exists()) {
            return response()->json([
                'message' => 'Kamu sudah pernah mengisi survey ini.',
            ], 409);
        }

        $user->filledSurveys()->attach($survey->id);
        $user->increment('points', 1);

        return response()->json([
            'message'     => 'Respons berhasil dicatat. Kamu mendapat 1 poin!',
            'points'      => $user->fresh()->points,
            'survey_link' => $survey->link,
        ]);
    }

    // =================== WEB VIEWS ===================

    // GET /surveys
    public function indexWeb(): \Illuminate\View\View
    {
        $surveys = Survey::with('user:id,name,username,photo')
            ->where('is_active', true)
            ->withCount('responses')
            ->latest()
            ->get();

        return view('surveys.index', compact('surveys'));
    }

    // GET /surveys/create
    public function createWeb(): \Illuminate\View\View
    {
        return view('surveys.create');
    }

    // GET /surveys/{survey}
    public function showWeb(Survey $survey): \Illuminate\View\View
    {
        return view('surveys.show', compact('survey'));
    }

}