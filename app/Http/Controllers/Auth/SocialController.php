<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    /**
     * Redirect to Google/Microsoft OAuth.
     */
    public function redirect(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the OAuth callback.
     */
    public function callback(string $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            $user = User::findOrCreateFromSocial($socialUser, $provider);

            Auth::login($user, true);

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Welcome, ' . $user->name . '!');

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['social' => 'Unable to authenticate with ' . ucfirst($provider) . '. Please try again.']);
        }
    }
}
