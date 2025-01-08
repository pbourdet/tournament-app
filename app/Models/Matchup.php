<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @mixin IdeHelperMatchup
 */
class Matchup extends Model
{
    use HasUuids;

    protected $table = 'matches';

    protected $fillable = ['index'];

    /** @return BelongsTo<Round, $this> */
    public function round(): BelongsTo
    {
        return $this->belongsTo(Round::class);
    }

    /** @return HasMany<MatchContestant, $this> */
    public function contestants(): HasMany
    {
        return $this->hasMany(MatchContestant::class, 'match_id')->with('contestant');
    }

    /** @param Collection<int, Team>|Collection<int, User> $contestants */
    public function addContestants(Collection $contestants): void
    {
        foreach ($contestants as $contestant) {
            $this->contestants()->create([
                'contestant_id' => $contestant->id,
                'contestant_type' => $contestant->getMorphClass(),
            ]);
        }
    }

    /** @return HasMany<Result, $this> */
    public function results(): HasMany
    {
        return $this->hasMany(Result::class, 'match_id')->with('contestant');
    }

    /** @return Collection<int, Contestant> */
    public function getContestants(): Collection
    {
        return $this->contestants->map(fn (MatchContestant $pivot) => $pivot->contestant);
    }

    public function getResultFor(Contestant $contestant): ?Result
    {
        return $this->results->first(fn (Result $result) => $result->contestant->is($contestant));
    }
}
