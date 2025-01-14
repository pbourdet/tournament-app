<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament\Phase;

use App\Livewire\Tournament\Phase\Qualification;
use App\Models\Tournament;
use Livewire\Livewire;
use Tests\TestCase;

class QualificationTest extends TestCase
{
    public function testRendersSuccessfully(): void
    {
        Livewire::test(Qualification::class, ['tournament' => Tournament::factory()->create()])
            ->assertStatus(200);
    }
}
