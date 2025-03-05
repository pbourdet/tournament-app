<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property Tournament $tournament
 * @property string $tournament_id
 */
abstract class Phase extends Model
{
    /** @return BelongsTo<Tournament, $this> */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /** @return MorphMany<Round, $this> */
    public function rounds(): MorphMany
    {
        return $this->morphMany(Round::class, 'phase');
    }

    /** @phpstan-assert-if-true =EliminationPhase $this */
    public function isElimination(): bool
    {
        return $this instanceof EliminationPhase;
    }

    /** @phpstan-assert-if-true GroupPhase $this */
    public function isGroup(): bool
    {
        return $this instanceof GroupPhase;
    }

    public function isFinished(): bool
    {
        return $this->rounds->every(fn (Round $round) => $round->matches->every(fn (Matchup $match) => $match->isPlayed()));
    }

    abstract public function getNextMatchOf(Matchup $match): ?Matchup;

    public function isReadyToStart(): bool
    {
        return true;
    }
}
