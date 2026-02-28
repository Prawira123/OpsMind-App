<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use LogsActivity;

    protected $fillable = [
        'tenant_id', 'client_id', 'created_by', 'number', 'issue_date', 'due_date', 'tax', 'status','total', 'subtotal', 'notes', 'public_token'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'tax' => 'float',
        'total' => 'float',
        'subtotal' => 'float',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['tenant_id', 'client_id', 'created_by', 'number', 'issue_date', 'due_date', 'tax', 'status','total', 'subtotal', 'notes', 'public_token'])
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs()
        ->setDescriptionForEvent(fn(string $eventName) => match($eventName){
            'created' => 'Invoice baru telah didaftarkan',
            'updated' => 'Invoice telah diperbarui',
            'deleted' => 'Invoice telah dihapus',
            default => "invoice {$eventName}",
        });
    }

    public function tenant(){
        return $this->belongsTo(Tenant::class);
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function createdBy(){
        return $this->belongsTo(User::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    public function items(){
        return $this->hasMany(InvoiceItem::class);
    }
}
