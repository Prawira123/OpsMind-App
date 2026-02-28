<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Report extends Model
{
    use LogsActivity;

    protected $fillable = [
        'tenant_id', 'requested_by', 'period', 'format', 'status', 'file_path'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['tenant_id', 'requested_by', 'period', 'status', 'file_path', 'format'])
        ->logOnlyDirty()
        ->submitEmptyLogs()
        ->setDescriptionForEvent(fn(string $eventName) => match($eventName){
            'created' => 'Report baru telah didaftarkan',
            'updated' => 'Report telah diperbarui',
            'deleted' => 'Report telah dihapus',
            default => "report {$eventName}",
        });
    }

    public function tenant(){
        return $this->belongsTo(Tenant::class);
    }
}
