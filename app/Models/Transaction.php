<?php

namespace App\Models;

use App\Models\Account;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'category_id', 'accounts_id', 'created_by', 'type', 'description', 'amount', 'date', 'reference_no'
    ];

    protected $casts = [
        'amount' => 'float',
        'date' => 'date'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['tenant_id', 'category_id', 'accounts_id', 'created_by', 'type', 'description', 'amount', 'date', 'reference_no'])
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs()
        ->setDescriptionForEvent(fn(string $eventName) => match($eventName){
            'created' => 'Transaction baru telah didaftarkan',
            'updated' => 'Transaction telah diperbarui',
            'deleted' => 'Transaction telah dihapus',
            default => "transaction {$eventName}",
        });
    }

    public function tenant(){
        return $this->belongsTo(Tenant::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function account(){
        return $this->belongsTo(Account::class);
    }

    public function createdBy(){
        return $this->belongsTo(User::class);
    }
}
