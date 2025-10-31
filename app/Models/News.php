<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    // migration uses uuid primary key
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'slug',
        'title',
        'subtitle',
        'deskripsi',
        'photo',
        'author',
    ];

    /**
     * Append computed attributes to model JSON form for API
     */
    protected $appends = ['photo_url'];

    // generate uuid id when creating
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // use slug for route model binding
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Tags relation (many-to-many via news_tags_pivots)
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'news_tags_pivots', 'news_id', 'tag_id');
    }

    /**
     * Return publicly accessible photo URL for API consumers.
     */
    public function getPhotoUrlAttribute()
    {
        if (empty($this->photo)) {
            return null;
        }

        // If photo is stored in public/foto path, return direct asset
        if (str_starts_with($this->photo, 'foto/')) {
            return asset($this->photo);
        }

        // Otherwise assume it's storage path and use storage URL
        return asset('storage/' . $this->photo);
    }
}
