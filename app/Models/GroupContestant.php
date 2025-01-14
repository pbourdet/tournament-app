<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperGroupContestant
 */
class GroupContestant extends MorphPivot
{
    /**  @return BelongsTo<Group, $this> */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /** @return MorphTo<Model, $this> */
    public function contestant(): MorphTo
    {
        return $this->morphTo();
    }
}
