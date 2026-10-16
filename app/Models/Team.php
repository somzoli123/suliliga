<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Team extends Model
{
protected $fillable = [
    'name',
    'class_name',
    'captain_id',
    'captain_name',
    'logo',
    'status',
];

    public function captain(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captain_id');
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }
}
