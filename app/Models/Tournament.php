<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TournamentStatus;
use Database\Factories\TournamentFactory;
use Illuminate\Database\Eloquent\Builder;
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

    /** @return BelongsToMany<User, $this> */
    public function playersWithoutTeams(): BelongsToMany
    {
        return $this->players()->whereDoesntHave('teams', fn (Builder $teamQuery) => $teamQuery->where('tournament_id', $this->id));
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

    /** @return HasOne<EliminationPhase, $this> */
    public function eliminationPhase(): HasOne
    {
        return $this->hasOne(EliminationPhase::class);
    }

    /** @return HasOne<GroupPhase, $this> */
    public function groupPhase(): HasOne
    {
        return $this->hasOne(GroupPhase::class);
    }

    /** @param Collection<int, string|User>|array<int, string|User> $users */
    public function addPlayers(array|Collection $users): void
    {
        if ($this->isFull() || $this->players->count() + count($users) > $this->number_of_players) {
            throw new \DomainException('Tournament is full');
        }

        $this->players()->attach($users);
    }

    public function addPlayer(string|User $player): void
    {
        $this->addPlayers([$player]);
    }

    /** @return Collection<int, covariant Phase> */
    public function getPhases(): Collection
    {
        return collect([
            $this->eliminationPhase,
            $this->groupPhase,
        ])->filter();
    }

    public function createInvitation(): void
    {
        $this->invitation()->create([
            'code' => mb_strtoupper(Str::random(6)),
            'expires_at' => now()->addDays(7),
        ]);
    }

    public function createTeams(): void
    {
        if (!$this->team_based) {
            return;
        }

        $teams = [];
        for ($i = 1; $i <= $this->maxTeamsCount(); ++$i) {
            $teams[] = ['name' => __('Team :number', ['number' => $i])];
        }

        $this->teams()->createMany($teams);
    }

    public function isFull(): bool
    {
        return $this->players()->count() >= $this->number_of_players;
    }

    public function isNotFull(): bool
    {
        return !$this->isFull();
    }

    /** @return Collection<int, covariant Contestant> */
    public function contestants(): Collection
    {
        return $this->team_based ? $this->teams : $this->players;
    }

    public function contestantsCount(): int
    {
        return $this->team_based ? $this->maxTeamsCount() : $this->number_of_players;
    }

    /** @return Collection<int, covariant Contestant> */
    public function contestantsWithoutGroup(): Collection
    {
        if (null === $this->groupPhase) return Collection::empty();

        $contestantsWithGroup = $this->groupPhase->groups->flatMap(fn (Group $group) => $group->getContestants()->map->id);

        return $this->contestants()->whereNotIn('id', $contestantsWithGroup);
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
        return $this->isFull() && $this->team_based && !$this->hasAllTeamsFull();
    }

    public function hasAllTeamsFull(): bool
    {
        return $this->hasAllTeams() && $this->teams->every(fn (Team $team) => $team->isFull());
    }

    public function maxTeamsCount(): int
    {
        if (!$this->team_based) {
            return 0;
        }

        return intdiv($this->number_of_players, (int) $this->team_size);
    }

    public function isNotStarted(): bool
    {
        return in_array($this->status, TournamentStatus::EDITABLE_STATUSES, true);
    }

    public function isReadyToStart(): bool
    {
        return TournamentStatus::READY_TO_START === $this->status && $this->hasAllContestants();
    }

    public function start(): void
    {
        $this->update(['status' => TournamentStatus::IN_PROGRESS]);
    }

    public function isStarted(): bool
    {
        return TournamentStatus::IN_PROGRESS === $this->status;
    }

    public function getLockKey(): string
    {
        return sprintf('tournament:%s:lock', $this->id);
    }

    public function getContestantsTranslation(bool $plural = false): string
    {
        return __(($this->team_based ? 'team' : 'player').($plural ? 's' : ''));
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => TournamentStatus::class,
        ];
    }
}
