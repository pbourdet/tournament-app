<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * @mixin IdeHelperTeam
 */
class Team extends Contestant
{
    /** @use HasFactory<TeamFactory> */
    use HasFactory;
    use HasUuids;

    protected $fillable = ['name'];

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

    /** @param Collection<int, string|User>|array<int, string|User> $users */
    public function addMembers(array|Collection $users): void
    {
        if ($this->isFull() || $this->members->count() + count($users) > $this->tournament->team_size) {
            throw new \DomainException('Team is full');
        }

        $this->members()->attach($users);
    }

    public function addMember(string|User $user): void
    {
        $this->addMembers([$user]);
    }

    public function isFull(): bool
    {
        return $this->members->count() >= $this->tournament->team_size;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
