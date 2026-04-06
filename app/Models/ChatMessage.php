<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\TenantScope;
use Illuminate\Support\Facades\Auth;
 
class ChatMessage extends Model
{
    protected $fillable = [
        'tenant_id', 'user_id', 'role', 'content', 'context_meta'
    ];
 
    protected $casts = [
        'context_meta' => 'array',
    ];
 
    public static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
 
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->tenant_id = $model->tenant_id ?? Auth::user()->tenant_id;
                $model->user_id = $model->user_id ?? Auth::id();
            }
        });
    }
 
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
