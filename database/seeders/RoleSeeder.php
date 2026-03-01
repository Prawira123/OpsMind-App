<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PhpParser\Node\Stmt\Foreach_;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();

            // BUAT SEMUA PERMISSION
            $permissions = [
                // Transaksi
                'view transactions',
                'create transactions',
                'edit transactions',
                'delete transactions',

                // Invoice
                'view invoices',
                'create invoices',
                'edit invoices',
                'delete invoices',
                'send invoices',

                // Laporan
                'view reports',
                'export reports',

                // Klien
                'view clients',
                'create clients',
                'edit clients',
                'delete clients',

                // Rekening
                'view accounts',
                'create accounts',
                'edit accounts',

                // Chart of Accounts
                'view coa',
                'manage coa',

                // Tim & Pengaturan
                'manage team',
                'manage settings',
                'manage subscription',

                // AI
                'use ai assistant',

                // Audit
                'view audit log',
            ];

            foreach($permissions as $permission){
                Permission::firstOrCreate([
                    'name' => $permission
                ]);
            }

            // BUAT ROLE DAN ASSIGN PERMISSION
            $owner = Role::firstOrCreate(['name' => 'owner']);
            $owner->givePermissionTo(Permission::all());

            $manager = Role::firstOrCreate(['name' => 'manager']);
            $manager->givePermissionTo([
                'view transactions', 'create transactions', 'edit transactions',
                'view invoices', 'create invoices', 'edit invoices', 'send invoices',
                'view reports',
                'view clients', 'create clients', 'edit clients',
                'view accounts',
                'view coa',
                'use ai assistant',
                'view audit log',
            ]);

            $accountant = Role::firstOrCreate(['name' => 'accountant']);
            $accountant->givePermissionTo([
                'view transactions',
                'view invoices',
                'view reports', 'export reports',
                'view clients',
                'view accounts',
                'view coa', 'manage coa',
                'use ai assistant', 
            ]);

            $staff = Role::firstOrCreate(['name' => 'staff']);
            $staff->givePermissionTo([
                'view transactions', 'create transactions',
                'view invoices',
                'view clients',
            ]);
    }
}
