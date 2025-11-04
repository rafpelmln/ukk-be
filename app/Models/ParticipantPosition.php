<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ParticipantPosition extends Pivot
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'participant_position';

    protected $fillable = [
        'id',
        'participant_id',
        'position_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (ParticipantPosition $participantPosition) {
            if (empty($participantPosition->{$participantPosition->getKeyName()})) {
                $participantPosition->{$participantPosition->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
