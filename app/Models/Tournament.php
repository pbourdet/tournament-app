<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TournamentFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin IdeHelperTournament
 */
class Tournament extends Model
{
    /** @use HasFactory<TournamentFactory> */
    use HasFactory;
    use HasUuids;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'description',
        'organizer_id',
        'number_of_players',
        'team_based',
        'team_size',
    ];

    /** @return BelongsTo<User, $this> */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /** @return BelongsToMany<User, $this> */
    public function players(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class, 'tournament_player')
            ->withTimestamps();
    }

    /** @return HasOne<TournamentInvitation, $this> */
    public function invitation(): HasOne
    {
        return $this->hasOne(TournamentInvitation::class);
    }

    /** @return HasMany<Team, $this> */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function isFull(): bool
    {
        return $this->players()->count() === $this->number_of_players;
    }

    public function getNextTeamName(): string
    {
        return sprintf('%s %s', __('Team'), $this->teams()->count() + 1);
    }

    public function hasAllTeams(): bool
    {
        return $this->team_based && $this->teams()->count() === $this->number_of_players / $this->team_size;
    }
}
