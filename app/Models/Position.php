<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Position extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'slug',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Position $position) {
            if (empty($position->{$position->getKeyName()})) {
                $position->{$position->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Participant::class, 'participant_position')
            ->withTimestamps();
    }

    public function positionRequests(): HasMany
    {
        return $this->hasMany(ParticipantPositionRequest::class);
    }
}
