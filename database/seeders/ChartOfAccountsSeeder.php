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
            ['code' => '1100', 'name' => 'Aset Lancar',               'type' => 'asset',     'normal_post' => 'debit',  'parent' => null],
            ['code' => '1110', 'name' => 'Kas',                       'type' => 'asset',     'normal_post' => 'debit',  'parent' => '1100'],
            ['code' => '1120', 'name' => 'Bank',                      'type' => 'asset',     'normal_post' => 'debit',  'parent' => '1100'],
            ['code' => '1130', 'name' => 'E-Wallet',                  'type' => 'asset',     'normal_post' => 'debit',  'parent' => '1100'],
            ['code' => '1140', 'name' => 'Piutang Usaha',             'type' => 'asset',     'normal_post' => 'debit',  'parent' => '1100'],
            ['code' => '1150', 'name' => 'Persediaan',                'type' => 'asset',     'normal_post' => 'debit',  'parent' => '1100'],
            ['code' => '1160', 'name' => 'Beban Dibayar Dimuka',      'type' => 'asset',     'normal_post' => 'debit',  'parent' => '1100'],

            ['code' => '1200', 'name' => 'Aset Tetap',                'type' => 'asset',     'normal_post' => 'debit',  'parent' => null],
            ['code' => '1210', 'name' => 'Peralatan',                 'type' => 'asset',     'normal_post' => 'debit',  'parent' => '1200'],
            ['code' => '1220', 'name' => 'Kendaraan',                 'type' => 'asset',     'normal_post' => 'debit',  'parent' => '1200'],
            ['code' => '1230', 'name' => 'Inventaris Kantor',         'type' => 'asset',     'normal_post' => 'debit',  'parent' => '1200'],

            // ========== LIABILITAS (2xxx) ==========
            ['code' => '2100', 'name' => 'Liabilitas Jangka Pendek',  'type' => 'liability', 'normal_post' => 'credit', 'parent' => null],
            ['code' => '2110', 'name' => 'Hutang Usaha',              'type' => 'liability', 'normal_post' => 'credit', 'parent' => '2100'],
            ['code' => '2120', 'name' => 'Hutang Pajak',              'type' => 'liability', 'normal_post' => 'credit', 'parent' => '2100'],
            ['code' => '2130', 'name' => 'Pendapatan Diterima Dimuka','type' => 'liability', 'normal_post' => 'credit', 'parent' => '2100'],
            ['code' => '2200', 'name' => 'Liabilitas Jangka Panjang', 'type' => 'liability', 'normal_post' => 'credit', 'parent' => null],
            ['code' => '2210', 'name' => 'Hutang Bank',               'type' => 'liability', 'normal_post' => 'credit', 'parent' => '2200'],

            // ========== MODAL (3xxx) ==========
            ['code' => '3100', 'name' => 'Modal',                     'type' => 'equity',    'normal_post' => 'credit', 'parent' => null],
            ['code' => '3110', 'name' => 'Modal Pemilik',             'type' => 'equity',    'normal_post' => 'credit', 'parent' => '3100'],
            ['code' => '3120', 'name' => 'Laba Ditahan',              'type' => 'equity',    'normal_post' => 'credit', 'parent' => '3100'],
            ['code' => '3130', 'name' => 'Laba/Rugi Tahun Berjalan',  'type' => 'equity',    'normal_post' => 'credit', 'parent' => '3100'],

            // ========== PENDAPATAN (4xxx) ==========
            ['code' => '4100', 'name' => 'Pendapatan Usaha',          'type' => 'revenue',   'normal_post' => 'credit', 'parent' => null],
            ['code' => '4110', 'name' => 'Pendapatan Penjualan',      'type' => 'revenue',   'normal_post' => 'credit', 'parent' => '4100'],
            ['code' => '4120', 'name' => 'Pendapatan Jasa',           'type' => 'revenue',   'normal_post' => 'credit', 'parent' => '4100'],
            ['code' => '4200', 'name' => 'Pendapatan Lain-lain',      'type' => 'revenue',   'normal_post' => 'credit', 'parent' => null],
            ['code' => '4210', 'name' => 'Pendapatan Bunga',          'type' => 'revenue',   'normal_post' => 'credit', 'parent' => '4200'],

            // ========== BEBAN (5xxx) ==========
            ['code' => '5100', 'name' => 'Beban Operasional',         'type' => 'expense',   'normal_post' => 'debit',  'parent' => null],
            ['code' => '5110', 'name' => 'Beban Gaji & Upah',         'type' => 'expense',   'normal_post' => 'debit',  'parent' => '5100'],
            ['code' => '5120', 'name' => 'Beban Sewa',                'type' => 'expense',   'normal_post' => 'debit',  'parent' => '5100'],
            ['code' => '5130', 'name' => 'Beban Listrik & Air',       'type' => 'expense',   'normal_post' => 'debit',  'parent' => '5100'],
            ['code' => '5140', 'name' => 'Beban Perlengkapan',        'type' => 'expense',   'normal_post' => 'debit',  'parent' => '5100'],
            ['code' => '5150', 'name' => 'Beban Pemasaran',           'type' => 'expense',   'normal_post' => 'debit',  'parent' => '5100'],
            ['code' => '5160', 'name' => 'Beban Transportasi',        'type' => 'expense',   'normal_post' => 'debit',  'parent' => '5100'],
            ['code' => '5170', 'name' => 'Beban Komunikasi',          'type' => 'expense',   'normal_post' => 'debit',  'parent' => '5100'],
            ['code' => '5200', 'name' => 'Harga Pokok Penjualan',     'type' => 'expense',   'normal_post' => 'debit',  'parent' => null],
            ['code' => '5210', 'name' => 'HPP Bahan Baku',            'type' => 'expense',   'normal_post' => 'debit',  'parent' => '5200'],
            ['code' => '5300', 'name' => 'Beban Lain-lain',           'type' => 'expense',   'normal_post' => 'debit',  'parent' => null],
            ['code' => '5310', 'name' => 'Beban Bunga',               'type' => 'expense',   'normal_post' => 'debit',  'parent' => '5300'],
            ['code' => '5320', 'name' => 'Beban Pajak',               'type' => 'expense',   'normal_post' => 'debit',  'parent' => '5300'],
        ];

        // Build map code → id untuk resolve parent_id
        $codeToId = [];

        foreach ($accounts as $account) {
            $created = ChartOfAccount::create([
                'tenant_id'      => $tenant->id,
                'code'           => $account['code'],
                'name'           => $account['name'],
                'normal_post' => $account['normal_post'],
                'parent_id'      => $account['parent']
                                        ? ($codeToId[$account['parent']] ?? null)
                                        : null,
                'is_default'     => true,
                'is_locked'      => true,
            ]);

            // Simpan id untuk dijadikan parent akun berikutnya
            $codeToId[$account['code']] = $created->id;
        }
    }
}
