<?php

namespace Database\Seeders;

use App\Models\AccountType;
use App\Models\Category;
use App\Models\ChartOfAccount;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{

    public function __construct(private Tenant $tenant){}
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedCategories();
        $this->seedChartOfAccounts();
    }

    private function seedCategories(){
        $categories = [
            [
                'name'  => 'Penjualan Produk',
                'type'  => 'income',
                'icon'  => 'shopping-bag',
                'color' => '#10b981',
            ],
            [
                'name'  => 'Pendapatan Jasa',
                'type'  => 'income',
                'icon'  => 'briefcase',
                'color' => '#3b82f6',
            ],
            [
                'name'  => 'Pendapatan Komisi',
                'type'  => 'income',
                'icon'  => 'percent',
                'color' => '#8b5cf6',
            ],
            [
                'name'  => 'Pendapatan Bunga',
                'type'  => 'income',
                'icon'  => 'trending-up',
                'color' => '#06b6d4',
            ],
            [
                'name'  => 'Pendapatan Sewa',
                'type'  => 'income',
                'icon'  => 'home',
                'color' => '#f59e0b',
            ],
            [
                'name'  => 'Pendapatan Lain-lain',
                'type'  => 'income',
                'icon'  => 'plus-circle',
                'color' => '#64748b',
            ],

            // ==========================================
            // PENGELUARAN (expense)
            // ==========================================

            // SDM
            [
                'name'  => 'Gaji & Upah',
                'type'  => 'expense',
                'icon'  => 'users',
                'color' => '#ef4444',
            ],
            [
                'name'  => 'Tunjangan Karyawan',
                'type'  => 'expense',
                'icon'  => 'gift',
                'color' => '#f43f5e',
            ],

            // Operasional
            [
                'name'  => 'Sewa Tempat',
                'type'  => 'expense',
                'icon'  => 'building',
                'color' => '#f59e0b',
            ],
            [
                'name'  => 'Listrik & Air',
                'type'  => 'expense',
                'icon'  => 'zap',
                'color' => '#eab308',
            ],
            [
                'name'  => 'Internet & Telepon',
                'type'  => 'expense',
                'icon'  => 'wifi',
                'color' => '#06b6d4',
            ],
            [
                'name'  => 'Perlengkapan Kantor',
                'type'  => 'expense',
                'icon'  => 'clipboard',
                'color' => '#64748b',
            ],
            [
                'name'  => 'Perawatan & Perbaikan',
                'type'  => 'expense',
                'icon'  => 'tool',
                'color' => '#78716c',
            ],

            // Produksi
            [
                'name'  => 'Pembelian Bahan Baku',
                'type'  => 'expense',
                'icon'  => 'package',
                'color' => '#0ea5e9',
            ],
            [
                'name'  => 'Pembelian Barang Dagangan',
                'type'  => 'expense',
                'icon'  => 'shopping-cart',
                'color' => '#6366f1',
            ],
            [
                'name'  => 'Biaya Produksi',
                'type'  => 'expense',
                'icon'  => 'settings',
                'color' => '#8b5cf6',
            ],

            // Pemasaran
            [
                'name'  => 'Pemasaran & Iklan',
                'type'  => 'expense',
                'icon'  => 'megaphone',
                'color' => '#ec4899',
            ],
            [
                'name'  => 'Biaya Pengiriman',
                'type'  => 'expense',
                'icon'  => 'truck',
                'color' => '#14b8a6',
            ],

            // Keuangan
            [
                'name'  => 'Pajak',
                'type'  => 'expense',
                'icon'  => 'file-text',
                'color' => '#dc2626',
            ],
            [
                'name'  => 'Biaya Administrasi Bank',
                'type'  => 'expense',
                'icon'  => 'credit-card',
                'color' => '#2563eb',
            ],
            [
                'name'  => 'Cicilan & Bunga Pinjaman',
                'type'  => 'expense',
                'icon'  => 'trending-down',
                'color' => '#9f1239',
            ],

            // Lain-lain
            [
                'name'  => 'Transportasi & BBM',
                'type'  => 'expense',
                'icon'  => 'navigation',
                'color' => '#7c3aed',
            ],
            [
                'name'  => 'Makan & Minum',
                'type'  => 'expense',
                'icon'  => 'coffee',
                'color' => '#92400e',
            ],
            [
                'name'  => 'Beban Lain-lain',
                'type'  => 'expense',
                'icon'  => 'minus-circle',
                'color' => '#94a3b8',
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'tenant_id'  => $this->tenant->id,
                'name'       => $category['name'],
                'type'       => $category['type'],
                'icon'       => $category['icon'],
                'color'      => $category['color'],
                'is_default' => true,
            ]);
        }
    }

    private function seedChartOfAccounts(){
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

        $codeToId = [];

        foreach ($accounts as $account) {
            // Cari account_type berdasarkan category
            // Kolom 'category' di tabel account_types: asset, liability, equity, revenue, expense
            $accountType = AccountType::where('category', $account['category'])->first();

            $coa = ChartOfAccount::create([
                'tenant_id'       => $this->tenant->id,  // ✅ $this->tenant
                'account_type_id' => $accountType->id,   // ✅ wajib diisi
                'parent_id'       => $account['parent']
                                        ? ($codeToId[$account['parent']] ?? null)
                                        : null,           // ✅ resolve parent_id dari kode
                'code'            => $account['code'],
                'name'            => $account['name'],
                'is_default'      => true,
                'is_locked'       => true,
                'is_active'       => true,
            ]);

            // Simpan id untuk dipakai sebagai parent akun berikutnya
            $codeToId[$account['code']] = $coa->id;
        }
    }
}
