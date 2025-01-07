<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RoundStage;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperRound
 */
class Round extends Model
{
    use HasUuids;

    protected $fillable = [
        'stage',
    ];

    /** @return BelongsTo<Phase, $this> */
    public function phase(): BelongsTo // @phpstan-ignore-line
    {
        return $this->belongsTo(Phase::class);
    }

    /** @return HasMany<Matchup, $this> */
    public function matches(): HasMany
    {
        return $this->hasMany(Matchup::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'stage' => RoundStage::class,
        ];
    }
}
