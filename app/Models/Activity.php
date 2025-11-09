<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Activity extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'slug',
        'desc',
        'location',
        'datetime',
        'target_scope',
        'status',
        'is_finished',
    ];

    protected $casts = [
        'datetime' => 'datetime',
        'is_finished' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Activity $activity) {
            if (empty($activity->{$activity->getKeyName()})) {
                $activity->{$activity->getKeyName()} = (string) Str::uuid();
            }

            if (empty($activity->slug)) {
                $activity->slug = static::generateUniqueSlug($activity->name ?? 'kegiatan');
            }
        });

        static::updating(function (Activity $activity) {
            if ($activity->isDirty('name')) {
                $activity->slug = static::generateUniqueSlug($activity->name ?? 'kegiatan', $activity->id);
            }
        });
    }

    protected static function generateUniqueSlug(string $name, ?string $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (static::query()
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'meeting_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ActivityReport::class);
    }

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'activity_position');
    }

    public function scopeVisibleForParticipant($query, Participant $participant)
    {
        $positionIds = $participant->positions()->pluck('positions.id')->all();
        return $query->where(function ($subQuery) use ($positionIds) {
            $subQuery->where('target_scope', 'all')
                ->orWhere(function ($inner) use ($positionIds) {
                    if (empty($positionIds)) {
                        return;
                    }
                    $inner->where('target_scope', 'positions')
                        ->whereHas('positions', function ($q) use ($positionIds) {
                            $q->whereIn('positions.id', $positionIds);
                        });
                });
        });
    }

    public function getScheduleLabelAttribute(): string
    {
        return optional($this->datetime)->translatedFormat('d F Y H:i');
    }
}
