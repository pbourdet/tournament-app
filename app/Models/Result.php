<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ResultOutcome;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperResult
 */
class Result extends Model
{
    protected $fillable = ['outcome', 'contestant_id', 'contestant_type', 'score'];

    /** @return BelongsTo<Matchup, $this> */
    public function match(): BelongsTo
    {
        return $this->belongsTo(Matchup::class);
    }

    /** @return MorphTo<Model, $this> */
    public function contestant(): MorphTo
    {
        return $this->morphTo();
    }

    public function isWin(): bool
    {
        return ResultOutcome::WIN === $this->outcome;
    }

    public function isLoss(): bool
    {
        return ResultOutcome::LOSS === $this->outcome;
    }

    public function isTie(): bool
    {
        return ResultOutcome::TIE === $this->outcome;
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'outcome' => ResultOutcome::class,
        ];
    }
}
