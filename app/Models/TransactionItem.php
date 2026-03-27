<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionItem extends Model
{
    use SoftDeletes;
    protected $table = 'transaction_items';

    protected $fillable = [
        'name',
        'transaction_id',
        'description',
        'unit_price',
        'qty',
        'amount',
    ];

    public function transaction(){
        return $this->belongsTo(Transaction::class);
    }
}
