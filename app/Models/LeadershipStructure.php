<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LeadershipStructure extends Model
{
    /** @use HasFactory<\Database\Factories\LeadershipStructureFactory> */
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'generation_id',
        'period_label',
        'period_year',
        'is_active',
        'general_leader_name',
        'general_leader_photo_path',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $structure) {
            if (empty($structure->{$structure->getKeyName()})) {
                $structure->{$structure->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(LeadershipStructureRole::class)->orderBy('display_order');
    }

    public function generation(): BelongsTo
    {
        return $this->belongsTo(Generation::class, 'generation_id');
    }
}
