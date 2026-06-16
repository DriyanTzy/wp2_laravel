<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SurveyController;
// use App\Http\Controllers\PostController;
/*
|--------------------------------------------------------------------------
| Guest routes (Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');

});

/*
|--------------------------------------------------------------------------
| Public profile data endpoint (no login required)
|--------------------------------------------------------------------------
*/
Route::get('/profile-data/{username}', function ($username) {
    $user = \App\Models\User::where('username', $username)->orWhere('name', $username)->firstOrFail();
    $datasets = \App\Models\Dataset::where('user_id', $user->id)->withCount('accessedBy')->latest()->get();
    $totalDownloads = $datasets->sum('present_count');
    $avgRating = $datasets->avg('rating') ?? 4.4;
    $posts = \App\Models\Post::where('user_id', $user->id)->with('dataset')->latest()->get();

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
        'datasets' => $datasets->map(fn($ds) => [
            'id' => $ds->id,
            'title' => $ds->title,
            'class' => $ds->class,
            'present_count' => $ds->present_count ?? 0,
            'created_at' => $ds->created_at->diffForHumans(),
        ]),
        'posts' => $posts->map(fn($post) => [
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
        ]),
    ]);
});

/*
|--------------------------------------------------------------------------
| Authenticated Blade routes (shell views, data from Web endpoints or API)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));

    // Blade pages (shell only, data diambil via fetch)
    Route::get('/dashboard', fn() => view('dashboard.index'))->name('dashboard');
    Route::get('/home', fn() => view('home.index'))->name('home');
    Route::get('/datasets', fn() => view('datasets.index'))->name('datasets.index');
    Route::get('/profile', fn() => view('profile.show'))->name('profile.show');
    Route::get('/datasets/{id}', fn() => view('datasets.show'))->name('datasets.show');
    Route::get('/logout', fn() => view('dashboard.logout'))->name('logout.confirm');

    // Web logout (hapus session & cookie)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Public profile page (via web route, not API)
    Route::get('/u/{username}', fn($username) => view('profile.public', compact('username')))->name('profile.public');
    Route::get('/settings', fn() => view('profile.edit'))->name('profile.settings');

    // Dashboard data endpoint (session-based, not API)
    Route::get('/dashboard-data', function () {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $total_surveys = \App\Models\Survey::where('user_id', $user->id)->count();

        // ─── Perbaiki: cek apakah tabel survey_responses ada ───
        try {
            $total_responses = \App\Models\SurveyResponse::whereIn(
                'survey_id',
                \App\Models\Survey::where('user_id', $user->id)->pluck('id')
            )->count();
        } catch (\Exception $e) {
            $total_responses = 0; // fallback kalau tabel belum ada
        }

        $total_datasets = \App\Models\Dataset::where('user_id', $user->id)->count();
        $total_reach = \App\Models\Dataset::where('user_id', $user->id)->sum('present_count');
        $active_surveys = \App\Models\Survey::where('user_id', $user->id)
            ->where('is_active', true)
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'stats' => [
                'total_surveys' => $total_surveys,
                'total_responses' => $total_responses,
                'total_datasets' => $total_datasets,
                'total_reach' => $total_reach,
            ],
            'active_surveys' => $active_surveys,
            'user' => $user->only(['name', 'email', 'photo']),
        ]);
    });

    Route::get('/datasets-data', function () {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $datasets = \App\Models\Dataset::where('user_id', $user->id)
            ->with('user')
            ->latest()
            ->get();

        return response()->json([
            'datasets' => $datasets->map(fn($ds) => [
                'id' => $ds->id,
                'title' => $ds->title,
                'class' => $ds->class,
                'description' => $ds->description,
                'present_count' => $ds->present_count ?? 0,
                'created_at' => $ds->created_at,
                'user' => [
                    'name' => $ds->user->name ?? 'Unknown',
                    'username' => $ds->user->username ?? 'unknown',
                ],
            ]),
        ]);
    });

    // User data for edit profile (session-based)
    Route::get('/me-data', function () {
        $user = auth()->user();
        return response()->json(['user' => $user->only(['name', 'username', 'bio', 'institution', 'location', 'photo', 'email'])]);
    });

    // Update profile (session-based, POST for file upload)
    Route::post('/me-update', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:50|unique:users,username,' . $user->id,
            'bio' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('photo')) {
            if ($user->photo) {
                \Illuminate\Support\Facades\Storage::delete($user->photo);
            }
            $validated['photo'] = $request->file('photo')->store('photos', 'public');
        }
        $user->update($validated);
        return response()->json([
            'message' => 'Profil berhasil diupdate.',
            'user' => $user->fresh()->only(['name', 'username', 'bio', 'institution', 'location', 'photo'])
        ]);
    });

    // Datasets data endpoint (session-based)
    Route::get('/datasets-data', function () {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $datasets = \App\Models\Dataset::where('user_id', $user->id)
            ->with('user')
            ->latest()
            ->get();

        return response()->json([
            'datasets' => $datasets->map(fn($ds) => [
                'id' => $ds->id,
                'title' => $ds->title,
                'class' => $ds->class,
                'description' => $ds->description,
                'present_count' => $ds->present_count ?? 0,
                'created_at' => $ds->created_at,
                'user' => [
                    'name' => $ds->user->name ?? 'Unknown',
                    'username' => $ds->user->username ?? 'unknown',
                    'photo' => $ds->user->photo ?? null,
                ],
            ]),
        ]);
    });

    // Surveys (web views)
    Route::get('/surveys', [SurveyController::class, 'indexWeb'])->name('surveys.index');
    Route::get('/surveys/create', [SurveyController::class, 'createWeb'])->name('surveys.create');
    Route::post('/surveys', [SurveyController::class, 'store'])->name('surveys.store');
    Route::get('/surveys/{survey}', [SurveyController::class, 'showWeb'])->name('surveys.show');


    // Post
    // Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    // Route::get('/posts/feed', [PostController::class, 'feed'])->name('posts.feed');
    // Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
});
