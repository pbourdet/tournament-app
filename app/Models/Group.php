<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ResultOutcome;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @mixin IdeHelperGroup
 */
class Group extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'size'];

    /** @return BelongsTo<GroupPhase, $this> */
    public function phase(): BelongsTo
    {
        return $this->belongsTo(GroupPhase::class, 'group_phase_id');
    }

    /** @return HasMany<GroupContestant, $this> */
    public function contestants(): HasMany
    {
        return $this->hasMany(GroupContestant::class)->with('contestant');
    }

    /** @return Collection<int, Contestant> */
    public function getContestants(): Collection
    {
        return $this->contestants->map(fn (GroupContestant $pivot) => $pivot->contestant);
    }

    /** @return Collection<int, Contestant> */
    public function getSortedContestants(): Collection
    {
        return $this->getContestants()->sortByDesc(fn (Contestant $contestant) => [
            $contestant->getMatchesForGroup($this, ResultOutcome::WIN),
            $contestant->getMatchesForGroup($this, ResultOutcome::TIE),
        ]);
    }

    /** @param Collection<int, covariant Contestant>|array<int, covariant Contestant> $contestants */
    public function addContestants(array|Collection $contestants): void
    {
        if ($this->isFull() || $this->contestants->count() + count($contestants) > $this->size) {
            throw new \DomainException('Group is full');
        }

        $contestantsArray = [];

        foreach ($contestants as $contestant) {
            $contestantsArray[] = [
                'contestant_id' => $contestant->id,
                'contestant_type' => $contestant->getMorphClass(),
            ];
        }

        $this->contestants()->createMany($contestantsArray);
    }

    public function addContestant(Contestant $contestant): void
    {
        $this->addContestants([$contestant]);
    }

    /** @return Collection<int, Matchup> */
    public function getMatches(): Collection
    {
        $round = $this->phase->rounds->first();

        if (null === $round) return Collection::empty();

        return $round->matches->load('contestants')->filter(function (Matchup $match) {
            $matchContestants = $match->getContestants();
            $groupContestants = $this->getContestants();

            return $matchContestants->every(fn (Contestant $contestant) => $groupContestants->contains($contestant));
        })->load('results');
    }

    public function isFull(): bool
    {
        return $this->contestants->count() >= $this->size;
    }
}
