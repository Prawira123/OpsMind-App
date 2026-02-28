<?php

namespace App\Models;

use App\Models\JournalEntryLine;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class JournalEntry extends Model
{
 use LogsActivity;

    protected $fillable = [
        'tenant_id',
        'transaction_id',
        'created_by',
        'entry_number',
        'description',
        'date',
        'status',  // draft, posted
        'source',  // manual, transaction, invoice, subscription
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['description', 'date', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relasi ke tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Relasi ke transaksi yang memicu jurnal ini
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // Relasi ke user yang membuat jurnal
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke detail baris jurnal (debit & kredit)
    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    // Helper: hitung total debit (harus selalu = total kredit)
    public function getTotalDebitAttribute(): float
    {
        return $this->lines->sum('debit');
    }

    // Helper: hitung total kredit
    public function getTotalCreditAttribute(): float
    {
        return $this->lines->sum('credit');
    }

    // Helper: cek apakah jurnal balance (debit = kredit)
    public function isBalanced(): bool
    {
        return $this->total_debit === $this->total_credit;
    }
}
