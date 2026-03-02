<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OTPCode extends Model
{
    protected $table = 'otp_codes';
    protected $fillable = [
        'user_id',
        'code',
        'type',
        'expires_at',
        'is_used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query
            ->where('is_used', false)
            ->where('expires_at', '>', now());
    }

    public function user(){
        $this->belongsTo(User::class);
    }
}
