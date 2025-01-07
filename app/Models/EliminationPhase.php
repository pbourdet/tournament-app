<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PhaseType;
use Database\Factories\EliminationPhaseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @extends Phase<EliminationPhaseDetail>
 *
 * @mixin IdeHelperEliminationPhase
 */
class EliminationPhase extends Phase
{
    /** @use HasFactory<EliminationPhaseFactory> */
    use HasFactory;

    public static function booted(): void
    {
        static::addGlobalScope('type', function (Builder $query): void {
            $query->where('type', PhaseType::ELIMINATION);
        });
        static::creating(function (EliminationPhase $phase): void {
            $phase->type = PhaseType::ELIMINATION;
        });
    }

    public function getDetailClassName(): string
    {
        return EliminationPhaseDetail::class;
    }
}
