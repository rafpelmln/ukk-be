<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EventOrder extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'participant_id',
        'event_id',
        'order_number',
        'quantity',
        'price',
        'service_fee',
        'total_amount',
        'payment_method',
        'bank_account_id',
        'status',
        'notes',
        'payment_proof',
        'expires_at',
        'paid_at',
        'checked_in_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'checked_in_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->order_number)) {
                $model->order_number = static::generateOrderNumber();
            }
            if (empty($model->expires_at)) {
                $model->expires_at = now()->addHours(24); // Expired setelah 24 jam
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD' . date('Ymd') . strtoupper(Str::random(6));
        } while (static::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    // Relations
    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    // Accessors
    public function getPaymentProofUrlAttribute(): ?string
    {
        if (!$this->payment_proof) {
            return null;
        }

        return asset($this->payment_proof);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at < now() && $this->status === 'pending';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Sudah Bayar',
            'completed' => 'Selesai',
            'expired' => 'Expired',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status)
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'text-amber-600 bg-amber-100',
            'paid', 'completed' => 'text-emerald-600 bg-emerald-100',
            'expired' => 'text-slate-600 bg-slate-100',
            'cancelled' => 'text-rose-600 bg-rose-100',
            default => 'text-slate-600 bg-slate-100'
        };
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
