<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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

    /** @return MorphTo<Model, $this> */
    public function contestant(): MorphTo
    {
        return $this->morphTo();
    }
}
