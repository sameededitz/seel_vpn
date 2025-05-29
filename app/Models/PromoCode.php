<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = [
        'code',
        'plan_id',
        'user_id',
        'used_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeValid($query)
    {
        return $query->where('is_active', true)
            ->where('expires_at', '>', now());
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }
    public function scopeUsed($query)
    {
        return $query->whereNotNull('used_at');
    }
    public function scopeUnused($query)
    {
        return $query->whereNull('used_at');
    }
}
