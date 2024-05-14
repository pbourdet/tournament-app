<?php

declare(strict_types=1);

namespace Tests\Feature\Middleware;

use Illuminate\Support\Facades\App;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    public function testLocaleIsSetBasedOnAcceptLanguageHeader(): void
    {
        $response = $this
            ->withHeader('Accept-Language', 'en-US,en;q=0.5')
            ->get('/login')
        ;

        $response->assertStatus(200);
        $this->assertSame(App::getLocale(), 'en');

        $response = $this
            ->withHeader('Accept-Language', 'fr,fr-FR;q=0.8,en-US;q=0.5,en;q=0.3')
            ->get('/login')
        ;

        $response->assertStatus(200);
        $this->assertSame(App::getLocale(), 'fr');
    }

    public function testLocaleDefaultsOnEnglishIfLanguageIsNotSupported(): void
    {
        $response = $this
            ->withHeader('Accept-Language', 'es-mx,es,en')
            ->get('/login')
        ;

        $response->assertStatus(200);
        $this->assertSame(App::getLocale(), 'en');

        $response = $this
            ->withHeader('Accept-Language', '')
            ->get('/login')
        ;

        $response->assertStatus(200);
        $this->assertSame(App::getLocale(), 'en');
    }
}
