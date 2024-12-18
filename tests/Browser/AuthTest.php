<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuthTest extends DuskTestCase
{
    use DatabaseTruncation;

    public function testLogin(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->visit(route('login'))
                ->type('email', $user->email)
                ->type('password', 'wrong password')
                ->press('button[type="submit"]')
                ->waitForText(__('auth.failed'))
            ;

            $browser
                ->type('password', 'password')
                ->press('button[type="submit"]')
                ->waitForRoute('dashboard')
                ->assertAuthenticated()
            ;
        });
    }
}
