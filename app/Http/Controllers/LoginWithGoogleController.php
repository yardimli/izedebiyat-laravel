<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class LoginWithGoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $google = Socialite::driver('google')->user();
            if (! $google->getEmail() || ! filter_var($google->user['email_verified'] ?? $google->user['verified_email'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                return redirect()->route('login')->withErrors(['email' => __('Google could not verify your email address.')]);
            }
            $user = User::where('google_id', $google->getId())->first();
            if (! $user) {
                $user = User::where('email', $google->getEmail())->first();
                if ($user && $user->google_id && (string) $user->google_id !== (string) $google->getId()) {
                    return redirect()->route('login')->withErrors(['email' => __('This email is linked to another Google account.')]);
                }
            }
            if (! $user) {
                $base = Str::limit(Str::slug($google->getNickname() ?: $google->getName() ?: 'author'), 45, '') ?: 'author';
                $username = $base;
                while (User::where('username', $username)->orWhere('slug', $username)->exists()) {
                    $username = $base.'-'.Str::lower(Str::random(10));
                }
                $user = User::create([
                    'name' => $google->getName() ?: $username,
                    'email' => $google->getEmail(),
                    'password' => Hash::make(Str::random(64)),
                    'picture' => $google->getAvatar(),
                    'username' => $username, 'slug' => $username,
                    'about_me' => '', 'page_title' => '',
                    'member_status' => 1, 'member_type' => 2,
                    'last_ip' => request()->ip(), 'background_image' => '',
                    'google_id' => $google->getId(), 'email_verified_at' => now(),
                ]);
            } else {
                $user->update(['google_id' => $google->getId(), 'email_verified_at' => $user->email_verified_at ?? now()]);
            }
            Auth::login($user);
            request()->session()->regenerate();
            return redirect()->intended('/eserlerim');
        } catch (\Exception $e) {
            report($e);
            return redirect()->route('login')->withErrors(['email' => __('Google sign-in could not be completed. Please try again.')]);
        }
    }
}
