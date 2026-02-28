<?php

namespace App\Models;

use App\Models\AccountType;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ChartOfAccount extends Model
{
     use LogsActivity;

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
    public function journalEntryLines()
    {
        return $this->hasMany(JournalEntryLine::class, 'account_id');
    }
}
