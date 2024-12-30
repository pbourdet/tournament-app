<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TournamentStatus;
use Database\Factories\TournamentFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperTournament
 */
class Tournament extends Model
{
    /** @use HasFactory<TournamentFactory> */
    use HasFactory;
    use HasUuids;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'description',
        'organizer_id',
        'number_of_players',
        'team_based',
        'team_size',
        'status',
    ];

    /** @return BelongsTo<User, $this> */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /** @return BelongsToMany<User, $this> */
    public function players(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class, 'tournament_player')
            ->withTimestamps();
    }

    /** @return HasOne<TournamentInvitation, $this> */
    public function invitation(): HasOne
    {
        return $this->hasOne(TournamentInvitation::class);
    }

    /** @return HasMany<Team, $this> */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /** @return HasMany<Matchup, $this> */
    public function matches(): HasMany
    {
        return $this->hasMany(Matchup::class);
    }

    /** @return HasOne<EliminationPhase, $this> */
    public function eliminationPhase(): HasOne
    {
        return $this->hasOne(EliminationPhase::class);
    }

    /** @return Collection<int, EliminationPhase> */
    public function phases(): Collection
    {
        return collect([
            $this->eliminationPhase,
        ])->filter();
    }

    public function createInvitation(): void
    {
        $this->invitation()->create([
            'code' => mb_strtoupper(Str::random(6)),
            'expires_at' => now()->addDays(7),
        ]);
    }

    public function isFull(): bool
    {
        return $this->players()->count() === $this->number_of_players;
    }

    public function isNotFull(): bool
    {
        return !$this->isFull();
    }

    /** @return Collection<int, User>|Collection<int, Team> */
    public function contestants(): Collection
    {
        return $this->team_based ? $this->teams : $this->players;
    }

    public function contestantsCount(): int
    {
        return $this->team_based ? $this->maxTeamsCount() : $this->number_of_players;
    }

    public function hasAllContestants(): bool
    {
        return $this->team_based ? $this->hasAllTeams() : $this->isFull();
    }

    public function hasAllTeams(): bool
    {
        return $this->team_based && $this->teams()->count() === $this->maxTeamsCount();
    }

    public function canGenerateTeams(): bool
    {
        return $this->isFull() && $this->team_based && !$this->hasAllTeams();
    }

    public function missingTeamsCount(): int
    {
        if (!$this->team_based) {
            return 0;
        }

        return $this->maxTeamsCount() - $this->teams()->count();
    }

    public function maxTeamsCount(): int
    {
        if (!$this->team_based) {
            return 0;
        }

        return $this->number_of_players / $this->team_size;
    }

    public function isNotStarted(): bool
    {
        return in_array($this->status, TournamentStatus::EDITABLE_STATUSES, true);
    }

    public function isReadyToStart(): bool
    {
        return TournamentStatus::READY_TO_START === $this->status && $this->hasAllContestants();
    }

    public function getLockKey(): string
    {
        return sprintf('tournament:%s:lock', $this->id);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => TournamentStatus::class,
        ];
    }
}
