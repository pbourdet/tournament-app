<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\User;
use Illuminate\View\View;

class Dashboard extends Component
{
    public function render(): View
    {
        $user = User::findOrFail(auth()->id());

        $managedTournaments = $user->managedTournaments()->get();
        $joinedTournaments = $user->tournaments()
            ->whereNotIn('id', $managedTournaments->pluck('id'))
            ->get();

        return view('livewire.dashboard', compact('managedTournaments', 'joinedTournaments'));
    }
}
