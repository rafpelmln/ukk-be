<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ActivityReport extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'activity_id',
        'participant_id',
        'status',
        'checked_in_at',
        'notes',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
    ];

    protected $appends = ['status_label'];

    protected static function booted(): void
    {
        static::creating(function (ActivityReport $report) {
            if (empty($report->{$report->getKeyName()})) {
                $report->{$report->getKeyName()} = (string) Str::uuid();
            }

            if (empty($report->checked_in_at)) {
                $report->checked_in_at = now();
            }
        });
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'present' => 'Hadir',
            'excused' => 'Izin',
            'absent' => 'Absen',
        ][$this->status] ?? ucfirst($this->status ?? '-');
    }
}
