<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchGame extends Model
{
    protected $fillable = [
        'tournament_id',
        'home_team_id',
        'away_team_id',
        'home_score',
        'away_score',
        'scheduled_at',
        'live_started_at',
        'elapsed_seconds',
        'status',
        'round',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'live_started_at' => 'datetime',
            'home_score' => 'integer',
            'away_score' => 'integer',
            'elapsed_seconds' => 'integer',
        ];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    /** Fut-e az óra (élő és nincs szüneteltetve). */
    public function isRunning(): bool
    {
        return $this->isLive() && $this->live_started_at !== null;
    }

    /** A meccsidő másodpercben, a szerver aktuális idejéhez mérve. */
    public function elapsedSeconds(): int
    {
        $seconds = (int) $this->elapsed_seconds;

        if ($this->live_started_at) {
            $seconds += max(0, now()->getTimestamp() - $this->live_started_at->getTimestamp());
        }

        return $seconds;
    }

    /** Pl. 07:42 */
    public function clockLabel(): string
    {
        $s = $this->elapsedSeconds();

        return sprintf('%02d:%02d', intdiv($s, 60), $s % 60);
    }
}
