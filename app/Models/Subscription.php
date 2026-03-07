<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\Scopes\TenantScope;
use Illuminate\Support\Facades\Auth;

class Subscription extends Model
{
    use LogsActivity;

    public static function booted(): void{
        static::addGlobalScope(new TenantScope());

        static::creating(function($model){
            if(Auth::check() && Auth::user()->tenant_id){
                $model->tenant_id = Auth::user()->tenant_id;
            }
        });
    }

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
        'subs_plan_id',
        'user_id'
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
