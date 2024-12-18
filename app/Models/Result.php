<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @mixin IdeHelperResult
 */
class Result extends Model
{
    use HasUuids;

    /** @return BelongsTo<Matchup, $this> */
    public function match(): BelongsTo
    {
        return $this->belongsTo(Matchup::class);
    }

    /** @return MorphOne<Team|User, $this> */
    public function winner(): MorphOne
    {
        return $this->morphOne($this->match->getContestantType(), 'winner');
    }
}
