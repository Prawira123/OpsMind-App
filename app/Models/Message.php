<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Message extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'conversation_id',
        'user_id',
        'client_id',
        'body',
        'type',
        'attachment_path',
        'attachment_name',
        'attachment_size',
        'attachment_mime',
        'reply_to_id',
        'forwarded_from_id',
        'edited_at',
    ];

    protected $casts = [
        'edited_at' => 'datetime',
        'attachment_size' => 'integer',
    ];

    public static function booted(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            if (Auth::check() && Auth::user()->tenant_id) {
                $model->tenant_id = $model->tenant_id ?? Auth::user()->tenant_id;
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replyTo()
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    public function forwardedFrom()
    {
        return $this->belongsTo(Message::class, 'forwarded_from_id');
    }

    public function deliveries()
    {
        return $this->hasMany(MessageDelivery::class);
    }

    public function reads()
    {
        return $this->hasMany(MessageRead::class);
    }
}
