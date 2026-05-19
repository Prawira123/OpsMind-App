<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\SocialAccount;
use App\Models\Tenant;
use App\Observers\DashboardCacheObserver;
use App\Observers\HandleInertiaRequestCacheObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles, LogsActivity;

    protected static function booted(): void
    {
        static::observe(HandleInertiaRequestCacheObserver::class);
        static::observe(DashboardCacheObserver::class);
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'is_active',           // apakah akun aktif
        'two_factor_enabled',  // apakah 2FA diaktifkan
        'two_factor_secret',   // kunci 2FA (dienkripsi)
        'github_id',
        'tenant_id',
        'is_online',
        'last_seen',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
            'is_active' => 'boolean',
            'is_online' => 'boolean',
            'last_seen' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'is_active', 'two_factor_enabled'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "user {$eventName}");
    }

    public function is_verified(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function isOwner()
    {
        return $this->hasRole('owner');
    }
    
    public function isManager()
    {
        return $this->hasRole('manager');
    }

    public function isAccountant(){
        return $this->hasRole('accountant');
    }

    public function isStaff(){
        return $this->hasRole('staff');
    }

    public function Otp_codes()
    {
        return $this->hasMany(OTPCode::class);
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'created_by');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function tenant_invitation(){
        return $this->hasOne(TenantInvitation::class);
    }

}
