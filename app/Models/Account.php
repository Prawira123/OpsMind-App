<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Account extends Model
{
    use LogsActivity;

    protected $fillable = [
        'tenant_id', 'name', 'type', 'balance', 'bank_name', 'account_number', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['tenant_id', 'name', 'type', 'balance', 'bank_name', 'account_number', 'is_active'])
        ->logOnlyDirty()
        ->submitEmptyLogs()
        ->setDescriptionForEvent(fn(string $eventName) => match($eventName){
            'created' => 'Account telah berhasil dibuat',
            'update' => 'Account telah berhasil diupdate',
            'delete' => 'Account dihapus',
            default => "Account {$eventName}",
        });
    }

    public function tenant(){
        return $this->belongsTo(Tenant::class);
    }
}
