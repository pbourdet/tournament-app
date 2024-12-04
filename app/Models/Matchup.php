<?php

declare(strict_types=1);

namespace App\Models;

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
    ];

    /** @return BelongsTo<Tournament, $this> */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /** @phpstan-return MorphToMany<User|Team, $this> */
    public function contestants(): MorphToMany
    {
        /* @phpstan-ignore-next-line */
        return $this->morphedByMany($this->getContestantType(), 'contestant', 'match_contestant', 'match_id');
    }

    /** @return class-string<User|Team> */
    public function getContestantType(): string
    {
        return $this->tournament->team_based ? Team::class : User::class;
    }
}
