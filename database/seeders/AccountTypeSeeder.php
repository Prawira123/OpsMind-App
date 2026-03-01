<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'code'           => '1000',
                'name'           => 'Aset',
                'category'       => 'asset',
                'normal_balance' => 'debit',
                'report_section' => 'balance_sheet',
                'cash_flow_type' => null,
                'sort_order'     => 1,
            ],
            [
                'code'           => '2000',
                'name'           => 'Liabilitas',
                'category'       => 'liability',
                'normal_balance' => 'credit',
                'report_section' => 'balance_sheet',
                'cash_flow_type' => null,
                'sort_order'     => 2,
            ],
            [
                'code'           => '3000',
                'name'           => 'Modal',
                'category'       => 'equity',
                'normal_balance' => 'credit',
                'report_section' => 'balance_sheet',
                'cash_flow_type' => null,
                'sort_order'     => 3,
            ],
            [
                'code'           => '4000',
                'name'           => 'Pendapatan',
                'category'       => 'revenue',
                'normal_balance' => 'credit',
                'report_section' => 'income_statement',
                'cash_flow_type' => 'operating',
                'sort_order'     => 4,
            ],
            [
                'code'           => '5000',
                'name'           => 'Beban',
                'category'       => 'expense',
                'normal_balance' => 'debit',
                'report_section' => 'income_statement',
                'cash_flow_type' => 'operating',
                'sort_order'     => 5,
            ],
        ];

        foreach ($types as $type) {
            AccountType::firstOrCreate(
                ['category' => $type['category']],
                $type
            );
        }
    }
}
