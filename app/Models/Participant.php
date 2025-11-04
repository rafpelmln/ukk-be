<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Participant extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'generations_id',
        'name',
        'username',
        'email',
        'no_hp',
        'birthday',
        'from_school',
        'photo',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'birthday' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Participant $participant) {
            if (empty($participant->{$participant->getKeyName()})) {
                $participant->{$participant->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function generation(): BelongsTo
    {
        return $this->belongsTo(Generation::class, 'generations_id');
    }

    public function generationLed(): HasOne
    {
        return $this->hasOne(Generation::class, 'participants_id');
    }

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'participant_position')
            ->using(ParticipantPosition::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    public function positionRequests(): HasMany
    {
        return $this->hasMany(ParticipantPositionRequest::class, 'participants_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
