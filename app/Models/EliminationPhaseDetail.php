<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperEliminationPhaseDetail
 */
class EliminationPhaseDetail extends Model
{
    protected $primaryKey = 'phase_id';
    protected $keyType = 'string';

    protected $fillable = ['number_of_contestants'];

    /** @return BelongsTo<EliminationPhase, $this> */
    public function phase(): BelongsTo
    {
        return $this->belongsTo(EliminationPhase::class, 'phase_id');
    }
}
