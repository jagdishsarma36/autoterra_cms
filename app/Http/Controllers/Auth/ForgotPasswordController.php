<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.forgot-password');
    }

    public function sendPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'We could not find an account with that email address.',
            ])->onlyInput('email');
        }

        $newPassword = Str::password(12);

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        try {
            Mail::to($user->email)->send(new ForgotPasswordMail($user, $newPassword));
        } catch (\Throwable $e) {
            Log::error('Failed to send forgot password email: ' . $e->getMessage());
        }

        try {
            $adminEmail = Setting::get('site_email', 'support@autoterra.net');
            Mail::to($adminEmail)->send(new ForgotPasswordMail($user, $newPassword));
        } catch (\Throwable $e) {
            Log::error('Failed to send admin forgot password notification: ' . $e->getMessage());
        }

        return redirect()->route('login')->with(
            'success',
            'A new password has been sent to your email address.'
        );
    }
}
