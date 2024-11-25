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

    /** @return MorphToMany<User, $this>|MorphToMany<Team, $this> */
    public function contestants(): MorphToMany
    {
        if ($this->tournament?->team_based) {
            return $this->morphedByMany(Team::class, 'contestant', 'match_contestant', 'match_id');
        }

        return $this->morphedByMany(User::class, 'contestant', 'match_contestant', 'match_id');
    }
}
