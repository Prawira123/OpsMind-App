<?php

namespace App\Models;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'billing_cycle', // monthly, yearly
        'features',      // JSON
        'max_users',
        'is_active',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'features'  => 'array',  // JSON otomatis jadi array PHP
        'is_active' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }
}
