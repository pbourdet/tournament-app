<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property string $id Replace with property hooks when php-cs-fixer supports it.
 */
abstract class Contestant extends Model
{
    abstract public function getName(): string;

    /** @return MorphToMany<Matchup, $this> */
    public function matches(): MorphToMany
    {
        return $this->morphToMany(Matchup::class, 'contestant', 'match_contestant', 'contestant_id', 'match_id');
    }

    /** @return MorphMany<Result, $this> */
    public function results(): MorphMany
    {
        return $this->morphMany(Result::class, 'contestant');
    }
}
