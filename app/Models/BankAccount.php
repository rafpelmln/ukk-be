<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'nama_bank',
        'nama',
        'no_rek',
        'photo'
    ];

    /**
     * Get the photo URL attribute
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        return asset($this->photo);
    }
}
