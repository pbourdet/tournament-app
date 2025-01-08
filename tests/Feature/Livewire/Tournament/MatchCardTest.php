<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Livewire\Tournament\MatchCard;
use Livewire\Livewire;
use Tests\TestCase;

class MatchCardTest extends TestCase
{
    public function rendersSuccessfully(): void
    {
        Livewire::test(MatchCard::class)
            ->assertStatus(200);
    }
}
