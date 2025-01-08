<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\EliminationPhaseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperEliminationPhase
 */
class EliminationPhase extends Phase
{
    use HasUuids;
    /** @use HasFactory<EliminationPhaseFactory> */
    use HasFactory;

    protected $fillable = [
        'number_of_contestants',
    ];
}
