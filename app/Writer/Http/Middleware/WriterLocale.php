<?php

namespace App\Writer\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WriterLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = strtolower(substr(app()->getLocale(), 0, 2));
        app()->setLocale(in_array($locale, ['tr', 'en']) ? $locale : 'tr');

        return $next($request);
    }
}
