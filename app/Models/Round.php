<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperRound
 */
class Round extends Model
{
    use HasUuids;

    /** @return MorphTo<EliminationPhase, $this> */
    public function phase(): MorphTo
    {
        return $this->morphTo(EliminationPhase::class);
    }

    /** @return HasMany<Matchup, $this> */
    public function matches(): HasMany
    {
        return $this->hasMany(Matchup::class);
    }

    /** @return HasOneThrough<Tournament, EliminationPhase, $this> */
    public function tournament(): HasOneThrough
    {
        return $this->hasOneThrough(
            Tournament::class,
            EliminationPhase::class,
            'id',
            'id',
            'phase_id',
            'tournament_id'
        );
    }
}
