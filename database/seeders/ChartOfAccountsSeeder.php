<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // database/seeders/ChartOfAccountsSeeder.php

    use WithoutModelEvents;

    public function run(Tenant $tenant): void
    {
        $accounts = [
            // ========== ASET (1xxx) ==========
            // Aset Lancar
            ['code' => '1100', 'name' => 'Aset Lancar',           'type' => 'asset', 'parent' => null],
            ['code' => '1110', 'name' => 'Kas',                   'type' => 'asset', 'parent' => '1100'],
            ['code' => '1120', 'name' => 'Bank',                  'type' => 'asset', 'parent' => '1100'],
            ['code' => '1130', 'name' => 'E-Wallet',              'type' => 'asset', 'parent' => '1100'],
            ['code' => '1140', 'name' => 'Piutang Usaha',         'type' => 'asset', 'parent' => '1100'],
            ['code' => '1150', 'name' => 'Persediaan',            'type' => 'asset', 'parent' => '1100'],
            ['code' => '1160', 'name' => 'Beban Dibayar Dimuka',  'type' => 'asset', 'parent' => '1100'],

            // Aset Tetap
            ['code' => '1200', 'name' => 'Aset Tetap',            'type' => 'asset', 'parent' => null],
            ['code' => '1210', 'name' => 'Peralatan',             'type' => 'asset', 'parent' => '1200'],
            ['code' => '1220', 'name' => 'Kendaraan',             'type' => 'asset', 'parent' => '1200'],
            ['code' => '1230', 'name' => 'Inventaris Kantor',     'type' => 'asset', 'parent' => '1200'],

            // ========== LIABILITAS (2xxx) ==========
            ['code' => '2100', 'name' => 'Liabilitas Jangka Pendek', 'type' => 'liability', 'parent' => null],
            ['code' => '2110', 'name' => 'Hutang Usaha',          'type' => 'liability', 'parent' => '2100'],
            ['code' => '2120', 'name' => 'Hutang Pajak',          'type' => 'liability', 'parent' => '2100'],
            ['code' => '2130', 'name' => 'Pendapatan Diterima Dimuka', 'type' => 'liability', 'parent' => '2100'],
            ['code' => '2200', 'name' => 'Liabilitas Jangka Panjang', 'type' => 'liability', 'parent' => null],
            ['code' => '2210', 'name' => 'Hutang Bank',           'type' => 'liability', 'parent' => '2200'],

            // ========== MODAL (3xxx) ==========
            ['code' => '3100', 'name' => 'Modal',                 'type' => 'equity', 'parent' => null],
            ['code' => '3110', 'name' => 'Modal Pemilik',         'type' => 'equity', 'parent' => '3100'],
            ['code' => '3120', 'name' => 'Laba Ditahan',          'type' => 'equity', 'parent' => '3100'],
            ['code' => '3130', 'name' => 'Laba/Rugi Tahun Berjalan', 'type' => 'equity', 'parent' => '3100'],

            // ========== PENDAPATAN (4xxx) ==========
            ['code' => '4100', 'name' => 'Pendapatan Usaha',      'type' => 'revenue', 'parent' => null],
            ['code' => '4110', 'name' => 'Pendapatan Penjualan',  'type' => 'revenue', 'parent' => '4100'],
            ['code' => '4120', 'name' => 'Pendapatan Jasa',       'type' => 'revenue', 'parent' => '4100'],
            ['code' => '4200', 'name' => 'Pendapatan Lain-lain',  'type' => 'revenue', 'parent' => null],
            ['code' => '4210', 'name' => 'Pendapatan Bunga',      'type' => 'revenue', 'parent' => '4200'],

            // ========== BEBAN (5xxx) ==========
            ['code' => '5100', 'name' => 'Beban Operasional',     'type' => 'expense', 'parent' => null],
            ['code' => '5110', 'name' => 'Beban Gaji & Upah',     'type' => 'expense', 'parent' => '5100'],
            ['code' => '5120', 'name' => 'Beban Sewa',            'type' => 'expense', 'parent' => '5100'],
            ['code' => '5130', 'name' => 'Beban Listrik & Air',   'type' => 'expense', 'parent' => '5100'],
            ['code' => '5140', 'name' => 'Beban Perlengkapan',    'type' => 'expense', 'parent' => '5100'],
            ['code' => '5150', 'name' => 'Beban Pemasaran',       'type' => 'expense', 'parent' => '5100'],
            ['code' => '5160', 'name' => 'Beban Transportasi',    'type' => 'expense', 'parent' => '5100'],
            ['code' => '5170', 'name' => 'Beban Komunikasi',      'type' => 'expense', 'parent' => '5100'],
            ['code' => '5200', 'name' => 'Harga Pokok Penjualan', 'type' => 'expense', 'parent' => null],
            ['code' => '5210', 'name' => 'HPP Bahan Baku',        'type' => 'expense', 'parent' => '5200'],
            ['code' => '5300', 'name' => 'Beban Lain-lain',       'type' => 'expense', 'parent' => null],
            ['code' => '5310', 'name' => 'Beban Bunga',           'type' => 'expense', 'parent' => '5300'],
            ['code' => '5320', 'name' => 'Beban Pajak',           'type' => 'expense', 'parent' => '5300'],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::create([
                'tenant_id'  => $tenant->id,
                'code'       => $account['code'],
                'name'       => $account['name'],
                'is_default' => true,
                'is_locked'  => true,
            ]);
        }
    }
}
