<?php

namespace App\Models;

use App\Models\AccountType;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ChartOfAccount extends Model
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
        'account_type_id',
        'parent_id',
        'code',
        'name',
        'description',
        'balance',
        'is_default',
        'is_locked',
        'is_active',
    ];

    protected $casts = [
        'balance'    => 'decimal:2',
        'is_default' => 'boolean',
        'is_locked'  => 'boolean',
        'is_active'  => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code', 'balance', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relasi ke tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Relasi ke account type
    public function accountType()
    {
        return $this->belongsTo(AccountType::class);
    }

    // Relasi ke parent account (untuk sub-akun)
    // Contoh: Beban (5000) → Beban Gaji (5110)
    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    // Relasi ke child accounts
    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    // Relasi ke journal entry lines
    public function journalEntryDetails()
    {
        return $this->hasMany(JournalEntryDetail::class, 'account_id');
    }
}
