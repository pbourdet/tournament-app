<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /** @var array<string> */
    private const array SUPPORTED_LOCALE = ['fr', 'en'];

    /** @param \Closure(Request): (Response) $next */
    public function handle(Request $request, \Closure $next): Response
    {
        $locale = Auth::user()->language ?? $request->getPreferredLanguage();

        in_array($locale, self::SUPPORTED_LOCALE, true) ? App::setLocale($locale) : App::setLocale('en');

        return $next($request);
    }
}
