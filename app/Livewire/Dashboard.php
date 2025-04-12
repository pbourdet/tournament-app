<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\View\View;

class Dashboard extends Component
{
    public function render(): View
    {
        $managedTournaments = $this->user->managedTournaments;
        $joinedTournaments = $this->user->tournaments->whereNotIn('id', $managedTournaments->pluck('id'));

        return view('livewire.dashboard', compact('managedTournaments', 'joinedTournaments'));
    }
}
