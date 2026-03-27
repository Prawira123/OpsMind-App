<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;

class Account extends Model
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

    public function transactions(){
        return $this->hasMany(Transaction::class, 'rekening_id');
    }
}
