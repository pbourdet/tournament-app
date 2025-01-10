<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\PhaseConfigurationCast;
use App\Enums\PhaseType;
use Database\Factories\PhaseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @template TConfig of PhaseConfiguration = PhaseConfiguration
 *
 * @property TConfig $configuration
 *
 * @mixin IdeHelperPhase
 */
class Phase extends Model
{
    use HasUuids;
    /** @use HasFactory<PhaseFactory> */
    use HasFactory;

    protected $fillable = ['type', 'configuration'];

    /** @return BelongsTo<Tournament, $this> */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /** @return HasMany<Round, $this> */
    public function rounds(): HasMany
    {
        return $this->hasMany(Round::class);
    }

    public function isElimination(): bool
    {
        return PhaseType::ELIMINATION === $this->type;
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'type' => PhaseType::class,
            'configuration' => PhaseConfigurationCast::class,
        ];
    }
}
