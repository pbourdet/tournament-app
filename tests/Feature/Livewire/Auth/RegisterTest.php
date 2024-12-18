<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Auth;

use App\Livewire\Auth\Register;
use Livewire\Livewire;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    /** @test */
    public function rendersSuccessfully(): void
    {
        Livewire::test(Register::class)
            ->assertStatus(200);
    }
}
