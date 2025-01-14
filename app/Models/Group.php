<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperGroup
 */
class Group extends Model
{
    use HasUuids;

    protected $fillable = ['name'];

    /** @return BelongsTo<Phase, $this> */
    public function phase(): BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }

    /** @return HasMany<GroupContestant, $this> */
    public function contestants(): HasMany
    {
        return $this->hasMany(GroupContestant::class)->with('contestant');
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
}
