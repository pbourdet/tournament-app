<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperTournamentInvitation
 */
class TournamentInvitation extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'tournament_id',
        'code',
        'expires_at',
    ];

    /** @return BelongsTo<Tournament, TournamentInvitation> */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }
}
