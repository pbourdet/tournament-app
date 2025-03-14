<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperTournamentInvitation
 */
class TournamentInvitation extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'code',
        'expires_at',
    ];

    /**
     * @param Builder<TournamentInvitation> $query
     *
     * @return Builder<TournamentInvitation>
     */
    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '>', now());
    }

    /** @return BelongsTo<Tournament, $this> */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function isNotExpired(): bool
    {
        return $this->expires_at->isFuture();
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }
}
