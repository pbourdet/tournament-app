<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TournamentInvitationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperTournamentInvitation
 */
class TournamentInvitation extends Model
{
    /** @use HasFactory<TournamentInvitationFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'tournament_id',
        'code',
        'expires_at',
    ];

    public static function fromTournament(Tournament $tournament): self
    {
        return self::create([
            'tournament_id' => $tournament->id,
            'code' => mb_strtoupper(Str::random(6)),
            'expires_at' => now()->addDays(7),
        ]);
    }

    /** @return BelongsTo<Tournament, $this> */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }
}
