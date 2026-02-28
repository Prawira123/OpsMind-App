<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
     protected $fillable = [
        'name',
        'code',
        'category',       // asset, liability, equity, revenue, expense
        'normal_balance', // debit atau credit
        'report_section', // balance_sheet, income_statement, cash_flow
        'cash_flow_type', // operating, investing, financing
        'sort_order',
    ];

    // Relasi: satu account type punya banyak chart of accounts
    public function chartOfAccounts()
    {
        return $this->hasMany(ChartOfAccount::class);
    }
}
