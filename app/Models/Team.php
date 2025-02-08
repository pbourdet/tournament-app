<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperTeam
 */
class Team extends Contestant
{
    /** @use HasFactory<TeamFactory> */
    use HasFactory;
    use HasUuids;

    protected $fillable = ['name', 'tournament_id'];

    /** @return BelongsToMany<User, $this> */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /** @return BelongsTo<Tournament, $this> */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function isFull(): bool
    {
        return $this->members->count() === $this->tournament->team_size;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
