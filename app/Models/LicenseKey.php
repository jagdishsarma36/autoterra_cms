<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LicenseKey extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'order_id', 'subscription_id', 'license_key',
        'activated_at', 'expires_at', 'is_active',
        'activations_count', 'max_activations',
    ];

    protected function casts(): array
    {
        return [
            'activated_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
            'activations_count' => 'integer',
            'max_activations' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (LicenseKey $model) {
            if (empty($model->license_key)) {
                $model->license_key = Str::uuid()->toString();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canActivate(): bool
    {
        return $this->is_active && !$this->isExpired()
            && $this->activations_count < $this->max_activations;
    }
}
