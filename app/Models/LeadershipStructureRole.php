<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LeadershipStructureRole extends Model
{
    /** @use HasFactory<\Database\Factories\LeadershipStructureRoleFactory> */
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'leadership_structure_id',
        'title',
        'person_name',
        'photo_path',
        'display_order',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $role) {
            if (empty($role->{$role->getKeyName()})) {
                $role->{$role->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function structure(): BelongsTo
    {
        return $this->belongsTo(LeadershipStructure::class, 'leadership_structure_id');
    }
}
