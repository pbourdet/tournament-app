<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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

    public function isElimination(): bool
    {
        return $this instanceof EliminationPhase;
    }
}
