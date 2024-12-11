<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\EliminationPhaseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin IdeHelperEliminationPhase
 */
class EliminationPhase extends Model
{
    use HasUuids;
    /** @use HasFactory<EliminationPhaseFactory> */
    use HasFactory;

    protected $fillable = [
        'number_of_contestants',
    ];

    /** @return BelongsTo<Tournament, $this> */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /** @return MorphMany<Round, $this> */
    public function rounds(): MorphMany
    {
        return $this->morphMany(Round::class, 'phase');
    }
}
