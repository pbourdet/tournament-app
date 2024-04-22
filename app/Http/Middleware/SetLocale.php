<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED_LOCALE = ['fr', 'en'];

    /** @param \Closure(Request): (Response) $next */
    public function handle(Request $request, \Closure $next): Response
    {
        $locale = $request->getPreferredLanguage();

        in_array($locale, self::SUPPORTED_LOCALE, true) ? App::setLocale($locale) : App::setLocale('en');

        return $next($request);
    }
}
