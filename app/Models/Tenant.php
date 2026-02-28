<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Tenant extends Model
{
    use LogsActivity,SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'email', 'phone', 'address', 'logo', 'currency', 'timezone', 'is_active', 'user_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['name', 'slug', 'email', 'phone', 'address', 'logo', 'currency', 'timezone', 'is_active', 'user_id'])
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs()
        ->setDescriptionForEvent(fn(string $eventName) => match($eventName){
            'created' => 'Tenant baru telah didaftarkan',
            'updated' => 'Tenant telah diperbarui',
            'deleted' => 'Tenant telah dihapus',
            default => "tenant {$eventName}",
        });
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function invoices(){
        return $this->hasMany(Invoice::class);
    }

    public function clients(){
        return $this->hasMany(Client::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    public function categories(){
        return $this->hasMany(Category::class);
    }

    public function accounts(){
        return $this->hasMany(Account::class);
    }

    public function socialAccounts(){
        return $this->hasMany(SocialAccount::class);
    }

    public function invitations() {
        return $this->hasMany(TenantInvitation::class);
    }

    public function reports(){
        return $this->hasMany(Report::class);
    }
}
