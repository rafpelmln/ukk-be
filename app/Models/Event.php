<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\EventOrder;

class Event extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'title',
        'subtitle',
        'description',
        'event_date',
        'location',
        'price',
        'photo',
    ];

    protected $casts = [
        'event_date' => 'date',
        'price' => 'decimal:2',
    ];

    protected $appends = ['photo_url'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (empty($this->photo)) {
            return null;
        }

        if (str_starts_with($this->photo, 'foto/')) {
            return asset($this->photo);
        }

        return asset('storage/' . ltrim($this->photo, '/'));
    }

    public function orders(): HasMany
    {
        return $this->hasMany(EventOrder::class);
    }
}
