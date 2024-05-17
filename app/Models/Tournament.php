<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin IdeHelperTournament
 */
class Tournament extends Model
{
    use HasFactory;
    use HasUuids;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'description',
        'organizer_id',
    ];

    /** @return BelongsTo<User, Tournament> */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /** @return BelongsToMany<User> */
    public function players(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /** @return HasOne<TournamentInvitation> */
    public function invitation(): HasOne
    {
        return $this->hasOne(TournamentInvitation::class);
    }

    public function isFull(): bool
    {
        return $this->players()->count() === $this->number_of_players;
    }
}
