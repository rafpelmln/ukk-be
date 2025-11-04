<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PositionRequest extends Model
{
    protected $table = 'participants_position_request';

    protected $fillable = [
        'participant_id',
        'position_id',
        'status',
        'notes',
        'admin_notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the participant that owns this position request.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * Get the position for this request.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
