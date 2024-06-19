<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperTeam
 */
class Team extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = ['name', 'tournament_id'];

    /** @return BelongsToMany<User> */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /** @return BelongsTo<Tournament, Team> */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }
}
