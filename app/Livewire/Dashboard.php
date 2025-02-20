<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\View\View;

class Dashboard extends Component
{
    public function render(): View
    {
        $user = $this->user();

        $managedTournaments = $user->managedTournaments->load('organizer');
        $joinedTournaments = $user->tournaments->load('organizer')->whereNotIn('id', $managedTournaments->pluck('id'));

        return view('livewire.dashboard', compact('managedTournaments', 'joinedTournaments'));
    }
}
