<?php

namespace App\Models;

use App\Models\ChartOfAccount;
use Illuminate\Database\Eloquent\Model;

class JournalEntryDetail extends Model
{
    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'description',
        'type',
        'amount',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
    ];

    // Relasi ke journal entry induknya
    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    // Relasi ke akun yang terpengaruh
    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }
}
