<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to Google's OAuth page.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     * Shows an animated transition page that then redirects to the dashboard.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Login dengan Google gagal. Silakan coba lagi.');
        }

        // Find or create the local user
        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name'              => $googleUser->getName(),
                'username'          => $this->generateUsername($googleUser->getName()),
                'google_id'         => $googleUser->getId(),
                'avatar'            => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'password'          => bcrypt(Str::random(24)), // unusable password
            ]
        );

        // Update google_id / avatar if the user already existed but signed in via Google
        if (!$user->google_id) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
            ]);
        }

        Auth::login($user, true); // remember = true

        // Show the animated "Login berhasil" callback page
        // That page will auto-redirect to dashboard after 2 seconds
        return view('auth.google-callback');
    }

    // ────────────────────────────────────────────────
    //  Helpers
    // ────────────────────────────────────────────────

    private function generateUsername(string $name): string
    {
        $base    = strtolower(str_replace(' ', '', $name));
        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $i++;
        }
        return $username;
    }
}