<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TenantInvitation extends Model
{
    use LogsActivity;

    protected $fillable = [
        'tenant_id', 'invited_by', 'email', 'role', 'token', 'status', 'expires_at', 'accepted_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['tenant_id', 'invited_by', 'email', 'role', 'token', 'status', 'expires_at', 'accepted_at'])
        ->logOnlyDirty()
        ->submitEmptyLogs()
        ->setDescriptionForEvent(fn(string $eventName) => match($eventName){
            'created' => 'Invitation baru telah didaftarkan',
            'updated' => 'Invitation telah diperbarui',
            'deleted' => 'Invitation telah dihapus',
            default => "invitation {$eventName}",
        });
    }

    public function tenant(){
        return $this->belongsTo(Tenant::class);
    }

    public function invitedBy(){
        return $this->belongsTo(User::class);
    }
}
