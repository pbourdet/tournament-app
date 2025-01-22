<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperGroupPhase
 */
class GroupPhase extends Phase
{
    use HasUuids;

    protected $fillable = [
        'number_of_groups',
        'qualifying_per_group',
    ];

    /** @return HasMany<Group, $this> */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function getNextMatchOf(Matchup $match): ?Matchup
    {
        return null;
    }
}
