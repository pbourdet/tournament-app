<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $managedTournaments = $user->managedTournaments()->get();
        $tournaments = $user->tournaments()
            ->whereNotIn('id', $managedTournaments->pluck('id'))
            ->get();

        return view('dashboard.index', compact('managedTournaments', 'tournaments'));
    }
}
