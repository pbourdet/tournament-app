<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

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

    /** @phpstan-return HasMany<MatchContestant, $this> */
    public function contestants(): HasMany
    {
        return $this->hasMany(MatchContestant::class, 'match_id');
    }

    /** @param Collection<int, Team>|Collection<int, User> $contestants */
    public function addContestants(Collection $contestants): void
    {
        foreach ($contestants as $contestant) {
            $this->contestants()->create([
                'contestant_id' => $contestant->id,
                'contestant_type' => $contestant->getMorphClass(),
            ]);
        }
    }

    /** @return class-string<User|Team> */
    public function getContestantType(): string
    {
        return $this->tournament->team_based ? Team::class : User::class;
    }
}
