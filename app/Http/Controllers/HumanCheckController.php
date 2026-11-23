<?php

namespace App\Http\Controllers;

use App\Services\HumanChallenge;
use App\Services\VisitorCountry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class HumanCheckController extends Controller
{
    private function privateResponse($response)
    {
        $response->headers->set('Cache-Control', 'private, no-store, max-age=0');
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');
        $response->headers->set('Referrer-Policy', 'no-referrer');
        $response->headers->set('Content-Security-Policy', "default-src 'none'; img-src 'self'; style-src 'unsafe-inline'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'");

        return $response;
    }

    public function show(Request $request, HumanChallenge $challenge)
    {
        $puzzle = $challenge->create();
        $request->session()->put('human_check.puzzle', $puzzle);

        return $this->privateResponse(response()->view('human-check', [
            'question' => $puzzle['question'], 'challengeId' => $puzzle['id'], 'imagePath' => $puzzle['image'],
        ]));
    }

    public function verify(Request $request, HumanChallenge $challenge, VisitorCountry $geo)
    {
        $ip = $geo->ip($request);
        $key = 'human-check:'.hash('sha256', $ip);
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return $this->privateResponse(response('Çok fazla deneme. Bir dakika sonra yeniden deneyin.', 429));
        }
        RateLimiter::hit($key, 60);
        $puzzle = $request->session()->pull('human_check.puzzle');
        $answer = $request->input('answer');
        $id = $request->input('challenge_id');
        if (! $puzzle || $puzzle['expires'] <= time() || ! is_string($id) || ! hash_equals($puzzle['id'], $id)
            || ! is_string($answer) || mb_strlen($answer) > 100 || ! hash_equals($puzzle['answer'], $challenge->normalize($answer))) {
            return $this->privateResponse(redirect('/human-check')->with('challenge_error', 'Yanıt eşleşmedi veya soru süresi doldu. Yeni soruyu deneyin.'));
        }
        $request->session()->regenerate();
        $request->session()->put('human_check.verified', ['ip' => $ip, 'expires' => time() + config('human-check.verified_seconds')]);
        $target = $request->session()->pull('human_check.return_to', '/');
        if (! is_string($target) || ! str_starts_with($target, '/') || str_starts_with($target, '//') || str_contains($target, '\\')) {
            $target = '/';
        }

        return $this->privateResponse(redirect($target, 303));
    }
}
