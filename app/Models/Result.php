<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ResultOutcome;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperResult
 */
class Result extends Model
{
    use HasUuids;

    protected $fillable = ['outcome', 'contestant_id', 'contestant_type', 'score'];

    /** @return BelongsTo<Matchup, $this> */
    public function match(): BelongsTo
    {
        return $this->belongsTo(Matchup::class);
    }

    /** @return MorphTo<Contestant, $this> */
    public function contestant(): MorphTo
    {
        /* @phpstan-ignore-next-line */
        return $this->morphTo();
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'outcome' => ResultOutcome::class,
        ];
    }
}
