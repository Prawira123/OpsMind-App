<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Category extends Model
{
    public static function booted(): void{
        static::addGlobalScope(new TenantScope());

        static::creating(function($model){
            if(Auth::check() && Auth::user()->tenant_id){
                $model->tenant_id = Auth::user()->tenant_id;
            }
        });
    }
    
    protected $fillable = [
        'tenant_id', 'name', 'type', 'color', 'icon', 'is_default'
    ];

    public function tenant(){
        return $this->belongsTo(Tenant::class);
    }
}
