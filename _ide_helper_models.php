<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $tournament_id
 * @property int $number_of_contestants
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Round> $rounds
 * @property-read int|null $rounds_count
 * @property-read \App\Models\Tournament $tournament
 * @method static \Database\Factories\EliminationPhaseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EliminationPhase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EliminationPhase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EliminationPhase query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EliminationPhase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EliminationPhase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EliminationPhase whereNumberOfContestants($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EliminationPhase whereTournamentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EliminationPhase whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEliminationPhase {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $group_phase_id
 * @property int $size
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupContestant> $contestants
 * @property-read int|null $contestants_count
 * @property-read \App\Models\GroupPhase $phase
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereGroupPhaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperGroup {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $group_id
 * @property string $contestant_type
 * @property string $contestant_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Contestant $contestant
 * @property-read \App\Models\Group $group
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupContestant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupContestant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupContestant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupContestant whereContestantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupContestant whereContestantType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupContestant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupContestant whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupContestant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupContestant whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperGroupContestant {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $tournament_id
 * @property int $number_of_groups
 * @property int $qualifying_per_group
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Round> $rounds
 * @property-read int|null $rounds_count
 * @property-read \App\Models\Tournament $tournament
 * @method static \Database\Factories\GroupPhaseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupPhase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupPhase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupPhase query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupPhase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupPhase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupPhase whereNumberOfGroups($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupPhase whereQualifyingPerGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupPhase whereTournamentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupPhase whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperGroupPhase {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $match_id
 * @property string $contestant_type
 * @property string $contestant_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Contestant $contestant
 * @property-read \App\Models\Matchup $match
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchContestant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchContestant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchContestant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchContestant whereContestantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchContestant whereContestantType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchContestant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchContestant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchContestant whereMatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchContestant whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMatchContestant {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $round_id
 * @property int $index
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MatchContestant> $contestants
 * @property-read int|null $contestants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Result> $results
 * @property-read int|null $results_count
 * @property-read \App\Models\Round $round
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matchup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matchup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matchup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matchup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matchup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matchup whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matchup whereRoundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Matchup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMatchup {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $match_id
 * @property string $contestant_type
 * @property string $contestant_id
 * @property \App\Enums\ResultOutcome $outcome
 * @property int|null $score
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Contestant $contestant
 * @property-read \App\Models\Matchup $match
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result whereContestantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result whereContestantType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result whereMatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result whereOutcome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperResult {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $phase_type
 * @property string $phase_id
 * @property string $stage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Matchup> $matches
 * @property-read int|null $matches_count
 * @property-read \App\Models\Phase $phase
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Round newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Round newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Round query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Round whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Round whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Round wherePhaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Round wherePhaseType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Round whereStage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Round whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRound {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string $tournament_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Matchup> $matches
 * @property-read int|null $matches_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $members
 * @property-read int|null $members_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Result> $results
 * @property-read int|null $results_count
 * @property-read \App\Models\Tournament $tournament
 * @method static \Database\Factories\TeamFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereTournamentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTeam {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $organizer_id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $number_of_players
 * @property bool $team_based
 * @property int|null $team_size
 * @property \App\Enums\TournamentStatus $status
 * @property-read \App\Models\EliminationPhase|null $eliminationPhase
 * @property-read \App\Models\GroupPhase|null $groupPhase
 * @property-read \App\Models\TournamentInvitation|null $invitation
 * @property-read \App\Models\User $organizer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $players
 * @property-read int|null $players_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $playersWithoutTeams
 * @property-read int|null $players_without_teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $teams
 * @property-read int|null $teams_count
 * @method static \Database\Factories\TournamentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereNumberOfPlayers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereOrganizerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereTeamBased($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereTeamSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTournament {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $tournament_id
 * @property string $code
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tournament $tournament
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentInvitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentInvitation notExpired()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentInvitation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentInvitation whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentInvitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentInvitation whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentInvitation whereTournamentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TournamentInvitation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTournamentInvitation {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $username
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $profile_picture
 * @property string $language
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tournament> $managedTournaments
 * @property-read int|null $managed_tournaments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Matchup> $matches
 * @property-read int|null $matches_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Result> $results
 * @property-read int|null $results_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $teams
 * @property-read int|null $teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tournament> $tournaments
 * @property-read int|null $tournaments_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

