<?php

declare(strict_types=1);

namespace App\Models;

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

    /** @param iterable<int, Contestant> $contestants */
    public function addContestants(iterable $contestants): void
    {
        foreach ($contestants as $contestant) {
            $this->contestants()->create([
                'contestant_id' => $contestant->id,
                'contestant_type' => $contestant->getMorphClass(),
            ]);
        }
    }

    public function isFull(): bool
    {
        return $this->contestants->count() === $this->size;
    }
}
