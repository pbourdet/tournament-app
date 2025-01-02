<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperMatchContestant
 */
class MatchContestant extends MorphPivot
{
    /**  @return BelongsTo<Matchup, $this> */
    public function match(): BelongsTo
    {
        return $this->belongsTo(Matchup::class, 'match_id');
    }

    /** @return MorphTo<Team|User, $this> */
    public function contestant(): MorphTo
    {
        /* @phpstan-ignore-next-line */
        return $this->morphTo();
    }
}
