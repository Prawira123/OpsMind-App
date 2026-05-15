<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\Scopes\TenantScope;
use Illuminate\Support\Facades\Auth;

class TenantInvitation extends Model
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
        'tenant_id', 'invited_by', 'email', 'role', 'token', 'status', 'expires_at', 'accepted_at', 'user_id'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['tenant_id', 'invited_by', 'email', 'role', 'token', 'status', 'expires_at', 'accepted_at', 'user_id'])
        ->logOnlyDirty()
        ->submitEmptyLogs()
        ->setDescriptionForEvent(fn(string $eventName) => "invitation {$eventName}");
    }

    public function tenant(){
        return $this->belongsTo(Tenant::class);
    }

    public function invitedBy(){
        return $this->belongsTo(User::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }   
}
