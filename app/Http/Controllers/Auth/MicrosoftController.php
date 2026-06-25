<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MicrosoftController extends Controller
{
    public function redirect()
    {
        $tenant = config('services.microsoft.tenant');
        $clientId = config('services.microsoft.client_id');
        $redirectUri = config('services.microsoft.redirect') ?: route('microsoft.callback');

        $url = "https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/authorize?" . http_build_query([
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'response_mode' => 'query',
            'scope' => 'offline_access User.Read',
        ]);

        return redirect($url);
    }

    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect('/admin/login')->withErrors(['email' => 'Login Microsoft dibatalkan atau gagal.']);
        }

        $code = $request->get('code');

        $tenant = config('services.microsoft.tenant');
        $clientId = config('services.microsoft.client_id');
        $clientSecret = config('services.microsoft.client_secret');
        $redirectUri = config('services.microsoft.redirect') ?: route('microsoft.callback');

        $tokenResponse = Http::asForm()->post("https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/token", [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
            'scope' => 'https://graph.microsoft.com/User.Read',
        ]);

        if (!$tokenResponse->successful()) {
            Log::error('Microsoft token request gagal', [
                'status' => $tokenResponse->status(),
                'body' => $tokenResponse->body(),
            ]);
            return redirect('/admin/login')->withErrors(['email' => 'Gagal mendapatkan token Microsoft.']);
        }

        $accessToken = $tokenResponse->json('access_token');

        $userResponse = Http::withToken($accessToken)->get('https://graph.microsoft.com/v1.0/me');

        if (!$userResponse->successful()) {
            return redirect('/admin/login')->withErrors(['email' => 'Gagal mendapatkan data user dari Microsoft.']);
        }

        $microsoftUser = $userResponse->json();
        $email = $microsoftUser['mail'] ?? $microsoftUser['userPrincipalName'] ?? null;

        if (!$email) {
            return redirect('/admin/login')->withErrors(['email' => 'Email tidak ditemukan di akun Microsoft.']);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $microsoftUser['displayName'] ?? explode('@', $email)[0],
                'email' => $email,
                'password' => Str::random(32),
                'is_active' => true,
            ]);
        }

        if (!$user->microsoft_id) {
            $user->update(['microsoft_id' => $microsoftUser['id']]);
        }

        Auth::login($user);
        $request->session()->save();

        return redirect()->intended('/admin');
    }
}
