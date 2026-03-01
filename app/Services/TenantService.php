<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\TenantSeeder;
use Illuminate\Support\Str;

class TenantService extends BaseService
{
    public function __construct()
    {
    }

    public function createForUser(User $user, string $bussinessName): Tenant{
        $tenant = Tenant::create([
            'name' => $bussinessName,
            'email' => $user->email,
            'slug' => $this->generateSlug($bussinessName),
            'user_id' => $user->id,
            'currency'  => 'IDR',           // default currency Indonesia
            'timezone'  => 'Asia/Jakarta',  // default timezone Indonesia
            'is_active' => true
        ]);

        $user->update([
            'tenant_id' => $tenant->id
        ]);
        $user->assignRole('owner');
        $seeder = new TenantSeeder($tenant);
        $seeder->run();

        return $tenant;
    }

    private function generateSlug(string $bussinessName){
        $slug = Str::slug($bussinessName);

        $suffix = Str::random(5);

        $finalSlug = $slug . '-' . $suffix;

        while(Tenant::where('slug', $finalSlug)->exists()){
            $suffix = Str::random(5);
            $finalSlug = $slug . '-' . $suffix;
        };

        return $finalSlug;
    }
}