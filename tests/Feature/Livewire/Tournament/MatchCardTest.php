<?php

namespace Tests\Feature\Livewire\Tournament;

use App\Livewire\Tournament\MatchCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class MatchCardTest extends TestCase
{
    /** @test */
    public function renders_successfully()
    {
        Livewire::test(MatchCard::class)
            ->assertStatus(200);
    }
}
