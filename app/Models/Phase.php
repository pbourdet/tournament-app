<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PhaseType;
use Database\Factories\PhaseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @template TDetail of Model
 *
 * @mixin IdeHelperPhase
 */
class Phase extends Model
{
    use HasUuids;
    /** @use HasFactory<PhaseFactory> */
    use HasFactory;

    protected $table = 'phases';

    protected $fillable = ['type'];

    /** @return BelongsTo<Tournament, $this> */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /** @return HasMany<Round, $this> */
    public function rounds(): HasMany
    {
        return $this->hasMany(Round::class, 'phase_id');
    }

    /** @return HasOne<TDetail, $this> */
    public function details(): HasOne
    {
        return $this->hasOne($this->getDetailClassName(), 'phase_id');
    }

    /** @return TDetail */
    public function getDetails()
    {
        return $this->details()->firstOrFail(); // @phpstan-ignore-line
    }

    /** @return class-string<TDetail> */
    public function getDetailClassName(): string
    {
        throw new \LogicException('Method getDetailClassName must be implemented in child class');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'type' => PhaseType::class,
        ];
    }
}
