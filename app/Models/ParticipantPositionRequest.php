<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ParticipantPositionRequest extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'participants_position_request';

    protected $fillable = [
        'id',
        'participants_id',
        'position_id',
        'status',
        'notes',
        'admin_notes',
    ];

    protected static function booted(): void
    {
        static::creating(function (ParticipantPositionRequest $request) {
            if (empty($request->{$request->getKeyName()})) {
                $request->{$request->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participants_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
