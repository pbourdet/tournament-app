<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\GroupPhaseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperGroupPhase
 */
class GroupPhase extends Phase
{
    use HasUuids;
    /** @use HasFactory<GroupPhaseFactory> */
    use HasFactory;

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
