<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            // ─────────────────────────────────────────────
            // STARTER — Gratis / trial untuk UMKM kecil
            // ─────────────────────────────────────────────
            [
                'name'          => 'Starter',
                'price'         => 0.00,
                'billing_cycle' => 'monthly',
                'features'      => json_encode([
                    'dashboard',
                    'accounts',
                    'categories',
                    'transactions',
                    'basic_reports',
                ]),
                'limits'        => json_encode([
                    'transactions_per_month' => 50,
                    'team_members'           => 1,
                    'clients'                => 5,
                    'invoices_per_month'     => 0,
                    'storage_mb'             => 100,
                ]),
                'is_active'     => true,
            ],

            // ─────────────────────────────────────────────
            // PRO MONTHLY — Untuk bisnis berkembang
            // ─────────────────────────────────────────────
            [
                'name'          => 'Pro',
                'price'         => 149000.00,
                'billing_cycle' => 'monthly',
                'features'      => json_encode([
                    'dashboard',
                    'accounts',
                    'categories',
                    'transactions',
                    'basic_reports',
                    'invoices',
                    'clients',
                    'chart_of_accounts',
                    'advanced_reports',
                    'export_reports',
                    'ai_assistant',
                ]),
                'limits'        => json_encode([
                    'transactions_per_month' => -1,     // unlimited
                    'team_members'           => 5,
                    'clients'                => 50,
                    'invoices_per_month'     => -1,     // unlimited
                    'storage_mb'             => 2048,   // 2 GB
                ]),
                'is_active'     => true,
            ],

            // ─────────────────────────────────────────────
            // PRO YEARLY — Diskon 2 bulan
            // ─────────────────────────────────────────────
            [
                'name'          => 'Pro',
                'price'         => 1490000.00,   // 149.000 × 10 bulan (hemat 2 bulan)
                'billing_cycle' => 'yearly',
                'features'      => json_encode([
                    'dashboard',
                    'accounts',
                    'categories',
                    'transactions',
                    'basic_reports',
                    'invoices',
                    'clients',
                    'chart_of_accounts',
                    'advanced_reports',
                    'export_reports',
                    'ai_assistant',
                ]),
                'limits'        => json_encode([
                    'transactions_per_month' => -1,
                    'team_members'           => 5,
                    'clients'                => 50,
                    'invoices_per_month'     => -1,
                    'storage_mb'             => 2048,
                ]),
                'is_active'     => true,
            ],

            // ─────────────────────────────────────────────
            // ENTERPRISE MONTHLY — Untuk bisnis besar
            // ─────────────────────────────────────────────
            [
                'name'          => 'Enterprise',
                'price'         => 399000.00,
                'billing_cycle' => 'monthly',
                'features'      => json_encode([
                    'dashboard',
                    'accounts',
                    'categories',
                    'transactions',
                    'basic_reports',
                    'invoices',
                    'clients',
                    'chart_of_accounts',
                    'advanced_reports',
                    'export_reports',
                    'ai_assistant',
                    'audit_log',
                    'manage_team',
                    'custom_reports',
                    'api_access',
                    'priority_support',
                ]),
                'limits'        => json_encode([
                    'transactions_per_month' => -1,     // unlimited
                    'team_members'           => -1,     // unlimited
                    'clients'                => -1,     // unlimited
                    'invoices_per_month'     => -1,     // unlimited
                    'storage_mb'             => -1,     // unlimited
                ]),
                'is_active'     => true,
            ],

            // ─────────────────────────────────────────────
            // ENTERPRISE YEARLY — Diskon 2 bulan
            // ─────────────────────────────────────────────
            [
                'name'          => 'Enterprise',
                'price'         => 3990000.00,   // 399.000 × 10 bulan (hemat 2 bulan)
                'billing_cycle' => 'yearly',
                'features'      => json_encode([
                    'dashboard',
                    'accounts',
                    'categories',
                    'transactions',
                    'basic_reports',
                    'invoices',
                    'clients',
                    'chart_of_accounts',
                    'advanced_reports',
                    'export_reports',
                    'ai_assistant',
                    'audit_log',
                    'manage_team',
                    'custom_reports',
                    'api_access',
                    'priority_support',
                ]),
                'limits'        => json_encode([
                    'transactions_per_month' => -1,
                    'team_members'           => -1,
                    'clients'                => -1,
                    'invoices_per_month'     => -1,
                    'storage_mb'             => -1,
                ]),
                'is_active'     => true,
            ],
        ];

        foreach ($plans as $plan) {
            // firstOrCreate agar aman dijalankan berulang kali
            DB::table('subscription_plans')->updateOrInsert(
                [
                    'name'          => $plan['name'],
                    'billing_cycle' => $plan['billing_cycle'],
                ],
                [
                    ...$plan,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
