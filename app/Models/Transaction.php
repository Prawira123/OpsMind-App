<?php

namespace App\Models;

use App\Models\Account;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\Scopes\TenantScope;
use Illuminate\Support\Facades\Auth;

class Transaction extends Model
{
    use LogsActivity, SoftDeletes;

    public static function booted(): void{
        static::addGlobalScope(new TenantScope());

        static::creating(function($model){
            if(Auth::check() && Auth::user()->tenant_id){
                $model->tenant_id = Auth::user()->tenant_id;
            }
        });
    }

    protected $fillable = [
        'tenant_id', 'category_id', 'debit_account_id', 'credit_account_id', 'created_by','client_id', 'type', 'description', 'amountTotal', 'date', 'reference_no', 'tax_percent', 'other_fee', 'discount', 'rekening_id', 'status'
    ];

    protected $casts = [
        'amountTotal' => 'float',
        'tax_percent' => 'float',
        'discount'    => 'float',
        'other_fee'   => 'float',
        'date'        => 'date'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['tenant_id', 'category_id', 'debit_account_id', 'credit_account_id', 'created_by','client_id', 'type', 'description', 'amountTotal', 'date', 'reference_no', 'tax_percent', 'other_fee', 'discount', 'status', 'rekening_id'])
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

    public function debit_account(){
        return $this->belongsTo(ChartOfAccount::class);
    }
    public function credit_account(){
        return $this->belongsTo(ChartOfAccount::class);
    }
    public function rekening(){
        return $this->belongsTo(Account::class);
    }
    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }
    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function invoice(){
        return $this->hasOne(Invoice::class);
    }

    public function transaction_items(){
        return $this->hasMany(TransactionItem::class);
    }
}
