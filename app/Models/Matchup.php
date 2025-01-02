<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @mixin IdeHelperMatchup
 */
class Matchup extends Model
{
    use HasUuids;

    protected $table = 'matches';

    protected $fillable = [
        'tournament_id',
        'index',
    ];

    /** @return BelongsTo<Tournament, $this> */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /** @return BelongsTo<Round, $this> */
    public function round(): BelongsTo
    {
        return $this->belongsTo(Round::class);
    }

    /** @return MorphToMany<Team, $this> */
    public function teamContestants(): MorphToMany
    {
        return $this->morphedByMany(Team::class, 'contestant', 'match_contestant', 'match_id');
    }

    /** @return MorphToMany<User, $this> */
    public function userContestants(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'contestant', 'match_contestant', 'match_id');
    }

    /** @return MorphToMany<User, $this>|MorphToMany<Team, $this> */
    public function contestants(): MorphToMany
    {
        return $this->tournament()->firstOrFail()->team_based ? $this->teamContestants() : $this->userContestants();
    }

    /** @return Collection<int, User>|Collection<int, Team> */
    public function getContestants(): Collection
    {
        return $this->tournament->team_based ? $this->teamContestants : $this->userContestants;
    }

    /** @return class-string<User|Team> */
    public function getContestantType(): string
    {
        return $this->tournament->team_based ? Team::class : User::class;
    }
}
