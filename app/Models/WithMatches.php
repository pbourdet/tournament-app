<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/** @template TDeclaringModel of Model */
trait WithMatches
{
    /** @return MorphToMany<Matchup, TDeclaringModel> */
    public function matches(): MorphToMany
    {
        return $this->morphToMany(Matchup::class, 'contestant', 'match_contestant', 'contestant_id', 'match_id');
    }

    /** @return MorphMany<Result, TDeclaringModel> */
    public function results(): MorphMany
    {
        return $this->morphMany(Result::class, 'contestant');
    }
}
