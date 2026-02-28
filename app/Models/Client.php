<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Client extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'name', 'email', 'phone', 'address', 'company'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['tenant_id', 'name', 'email', 'address', 'company'])
        ->logOnlyDirty()
        ->submitEmptyLogs()
        ->setDescriptionForEvent(fn(string $eventName) => match($eventName){
            'created' => 'Client baru telah didaftarkan',
            'updated' => 'Client telah diperbarui',
            'deleted' => 'Client telah dihapus',
            default => "client {$eventName}",
        });
    }

    public function tenant(){
        return $this->belongsTo(Tenant::class);
    }
}
