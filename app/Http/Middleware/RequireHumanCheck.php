<?php

namespace App\Http\Middleware;

use App\Services\VisitorCountry;
use Closure;
use Illuminate\Http\Request;

class RequireHumanCheck
{
    public function handle(Request $request, Closure $next)
    {
        // Prevent shared caches from serving another visitor's unlocked page.
        $finish = function ($response) {
            $response->headers->set('Cache-Control', 'private, no-store, max-age=0');

            return $response;
        };
        if (! config('human-check.enabled') || $request->is('human-check')) {
            return $next($request);
        }
        $geo = app(VisitorCountry::class);
        $ip = $geo->ip($request);
        if ($ip === $request->server('REMOTE_ADDR') && in_array($ip, ['127.0.0.1', '::1'], true)) {
            return $next($request);
        }
        $verified = $request->session()->get('human_check.verified', []);
        if (($verified['expires'] ?? 0) > time() && ($verified['ip'] ?? '') === $ip) {
            return $finish($next($request));
        }
        $country = $geo->country($ip);
        if ($country !== null && ! in_array($country, config('human-check.countries'), true)) {
            return $finish($next($request));
        }
        if ($request->isMethod('GET')) {
            $target = $request->getRequestUri();
            if (str_starts_with($target, '/') && ! str_starts_with($target, '//') && ! str_contains($target, '\\')) {
                $request->session()->put('human_check.return_to', $target);
            }
        }

        return $finish(redirect('/human-check', 303));
    }
}
