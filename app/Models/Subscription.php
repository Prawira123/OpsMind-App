<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Subscription extends Model
{
    use LogsActivity;

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'order_id',
        'status',          // pending, active, expired, cancelled, failed
        'starts_at',
        'ends_at',
        'paid_at',
        'payment_method',
        'snap_token',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'paid_at'   => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'ends_at', 'payment_method'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    // Cek apakah subscription masih aktif
    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->ends_at->isFuture();
    }
}
