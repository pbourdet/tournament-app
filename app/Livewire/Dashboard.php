<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\View\View;

class Dashboard extends Component
{
    public function render(): View
    {
        $user = $this->getUser();

        $managedTournaments = $user->managedTournaments()->get();
        $joinedTournaments = $user->tournaments()
            ->whereNotIn('id', $managedTournaments->pluck('id'))
            ->get();

        return view('livewire.dashboard', compact('managedTournaments', 'joinedTournaments'));
    }
}
