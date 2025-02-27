<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ResultOutcome;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

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

    /** @return Collection<int, Matchup> */
    public function getMatchesForGroup(Group $group, ?ResultOutcome $outcome = null): Collection
    {
        $matches = $group->getMatches()->filter(fn (Matchup $match) => $match->getContestants()->contains($this));

        if (null === $outcome) return $matches;

        return match ($outcome) {
            ResultOutcome::WIN => $matches->filter(fn (Matchup $match) => $this->won($match)),
            ResultOutcome::LOSS => $matches->filter(fn (Matchup $match) => $this->lost($match)),
            ResultOutcome::TIE => $matches->filter(fn (Matchup $match) => $this->tied($match)),
        };
    }

    public function won(Matchup $match): bool
    {
        return $match->results->contains(fn (Result $result) => $result->contestant->is($this) && $result->isWin());
    }

    public function lost(Matchup $match): bool
    {
        return $match->results->contains(fn (Result $result) => $result->contestant->is($this) && $result->isLoss());
    }

    public function tied(Matchup $match): bool
    {
        return $match->results->contains(fn (Result $result) => $result->contestant->is($this) && $result->isTie());
    }
}
