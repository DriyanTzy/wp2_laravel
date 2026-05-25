<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // GET /api/dashboard — statistik milik user yang login
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Total survey yang user punya
        $totalSurveys = $user->surveys()->count();

        // Total responden dari semua survey milik user
        $totalResponses = $user->surveys()
            ->withCount('responses')
            ->get()
            ->sum('responses_count');

        // Total reach = berapa user yang pernah lihat/isi survey lo
        // (dihitung dari unique user di tabel responses)
        $totalReach = \App\Models\Response::whereIn(
            'survey_id', $user->surveys()->pluck('id')
        )->distinct('user_id')->count('user_id');

        // Survey aktif
        $activeSurveys = $user->surveys()
            ->where('is_active', true)
            ->withCount('responses')
            ->latest()
            ->get();

        // Dataset yang user punya
        $totalDatasets = $user->datasets()->count();

        return response()->json([
            'stats' => [
                'total_surveys'   => $totalSurveys,
                'total_responses' => $totalResponses,
                'total_reach'     => $totalReach,
                'total_datasets'  => $totalDatasets,
                'points'          => $user->points,
            ],
            'active_surveys' => $activeSurveys,
            'user' => [
                'name'     => $user->name,
                'username' => $user->username,
                'photo'    => $user->photo,
                'email'    => $user->email,
            ],
        ]);
    }
}