<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

// =========================================================
// PROPS
// =========================================================
const props = defineProps({
    transaction: { type: Object, required: true },
})

// =========================================================
// FORMAT
// =========================================================
const formatCurrency = (val) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0,
    }).format(val || 0)

const formatDate = (val) => {
    if (!val) return '—'
    return new Intl.DateTimeFormat('id-ID', {
        weekday: 'long', day: '2-digit', month: 'long', year: 'numeric',
    }).format(new Date(val))
}

const formatDateTime = (val) => {
    if (!val) return '—'
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
    }).format(new Date(val))
}

// =========================================================
// COMPUTED
// =========================================================
const isIncome = computed(() => props.transaction.type === 'income')

const typeConfig = {
    income:  { label: 'Pemasukan',   color: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400', dot: 'bg-emerald-500' },
    expense: { label: 'Pengeluaran', color: 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400',             dot: 'bg-rose-500'    },
}

const config = computed(() =>
    typeConfig[props.transaction.type] ?? typeConfig.expense
)

const subtotal = computed(() =>
    (props.transaction.items ?? []).reduce((s, i) => s + parseFloat(i.amount_per_item || 0), 0)
)

const totalQty = computed(() =>
    (props.transaction.items ?? []).reduce((s, i) => s + parseInt(i.qty || 0), 0)
)

// =========================================================
// DELETE
// =========================================================
const deleteTransaction = () => {
    Swal.fire({
        title:              'Hapus Transaksi?',
        text:               'Journal entry terkait juga akan dihapus. Tindakan ini tidak bisa dibatalkan.',
        icon:               'warning',
        showCancelButton:   true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor:  '#6b7280',
        confirmButtonText:  'Ya, Hapus!',
        cancelButtonText:   'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('transactions.destroy', props.transaction.id))
        }
    })
}
</script>

<template>
    <div class="max-w-full mx-auto">

        <!-- HEADER -->
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a :href="route('transactions.index')"
                   class="flex h-9 w-9 items-center justify-center rounded-lg border
                          border-gray-200 dark:border-gray-700 text-gray-500
                          hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Detail Transaksi
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ transaction.reference_no ? `Ref: ${transaction.reference_no}` : `ID: #${transaction.id}` }}
                    </p>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center gap-2">
                <Link
                    :href="route('transactions.edit', transaction.id)"
                    class="inline-flex items-center gap-2 rounded-lg border
                           border-gray-200 dark:border-gray-700 px-4 py-2
                           text-sm font-medium text-gray-700 dark:text-gray-300
                           hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0
                                 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828
                                 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </Link>
                <button
                    @click="deleteTransaction"
                    class="inline-flex items-center gap-2 rounded-lg border
                           border-red-200 dark:border-red-800 px-4 py-2
                           text-sm font-medium text-red-600 dark:text-red-400
                           hover:bg-red-50 dark:hover:bg-red-900/20 transition"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2
                                 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0
                                 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus
                </button>
            </div>
        </div>

        <div class="space-y-5">

            <!-- =========================================
                 CARD 1: RINGKASAN TRANSAKSI
            ========================================= -->
            <div class="rounded-2xl bg-white dark:bg-gray-900 border
                        border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

                <!-- Top accent -->
                <div :class="[
                    'h-1.5',
                    isIncome ? 'bg-emerald-500' : 'bg-rose-500',
                ]"/>

                <div class="p-6">
                    <div class="flex items-start justify-between gap-4">

                        <!-- Kiri: Tipe + Jumlah -->
                        <div>
                            <span :class="[
                                'inline-flex items-center gap-1.5 rounded-full px-3 py-1',
                                'text-xs font-semibold mb-3',
                                config.color,
                            ]">
                                <span :class="['h-1.5 w-1.5 rounded-full', config.dot]"/>
                                {{ config.label }}
                            </span>

                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                                Total Transaksi
                            </p>
                            <p :class="[
                                'text-4xl font-extrabold',
                                isIncome
                                    ? 'text-emerald-600 dark:text-emerald-400'
                                    : 'text-rose-600 dark:text-rose-400',
                            ]">
                                {{ isIncome ? '+' : '-' }}{{ formatCurrency(transaction.amountTotal) }}
                            </p>
                        </div>

                        <!-- Kanan: Tanggal -->
                        <div class="text-right shrink-0">
                            <p class="text-xs text-gray-400 mb-1">Tanggal Transaksi</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ formatDate(transaction.date) }}
                            </p>
                            <p class="text-xs text-gray-400 mt-3">Dibuat</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ formatDateTime(transaction.created_at) }}
                            </p>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mt-4 rounded-xl bg-gray-50 dark:bg-gray-800/50
                                border border-gray-100 dark:border-gray-700 px-4 py-3">
                        <p class="text-xs text-gray-400 mb-1">Deskripsi</p>
                        <p class="text-sm text-gray-900 dark:text-white">
                            {{ transaction.description }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- =========================================
                 CARD 2: DETAIL INFO
            ========================================= -->
            <div class="rounded-xl bg-white dark:bg-gray-900 border
                        border-gray-200 dark:border-gray-800 shadow-sm p-6">

                <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                    Informasi Transaksi
                </h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                    <!-- Kategori -->
                    <div class="rounded-xl border border-gray-100 dark:border-gray-800
                                bg-gray-50/50 dark:bg-gray-800/30 px-4 py-3">
                        <p class="text-xs text-gray-400 mb-1.5">Kategori</p>
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ transaction.category?.name ?? '—' }}
                            </p>
                        </div>
                    </div>

                    <!-- Klien -->
                    <div class="rounded-xl border border-gray-100 dark:border-gray-800
                                bg-gray-50/50 dark:bg-gray-800/30 px-4 py-3">
                        <p class="text-xs text-gray-400 mb-1.5">Klien</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ transaction.client?.name ?? '—' }}
                        </p>
                    </div>

                    <!-- Rekening Debit -->
                    <div class="rounded-xl border border-gray-100 dark:border-gray-800
                                bg-gray-50/50 dark:bg-gray-800/30 px-4 py-3">
                        <p class="text-xs text-gray-400 mb-1.5">Rekening Debit (COA)</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ transaction.debit_account?.code }}
                            <span class="text-gray-500 dark:text-gray-400 font-normal">
                                — {{ transaction.debit_account?.name ?? '—' }}
                            </span>
                        </p>
                    </div>

                    <!-- Rekening Kredit -->
                    <div class="rounded-xl border border-gray-100 dark:border-gray-800
                                bg-gray-50/50 dark:bg-gray-800/30 px-4 py-3">
                        <p class="text-xs text-gray-400 mb-1.5">Rekening Kredit (COA)</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ transaction.credit_account?.code }}
                            <span class="text-gray-500 dark:text-gray-400 font-normal">
                                — {{ transaction.credit_account?.name ?? '—' }}
                            </span>
                        </p>
                    </div>

                    <!-- No Referensi -->
                    <div v-if="transaction.reference_no"
                         class="rounded-xl border border-gray-100 dark:border-gray-800
                                bg-gray-50/50 dark:bg-gray-800/30 px-4 py-3">
                        <p class="text-xs text-gray-400 mb-1.5">No. Referensi</p>
                        <p class="text-sm font-mono font-medium text-gray-900 dark:text-white">
                            {{ transaction.reference_no }}
                        </p>
                    </div>

                    <!-- Dibuat oleh -->
                    <div class="rounded-xl border border-gray-100 dark:border-gray-800
                                bg-gray-50/50 dark:bg-gray-800/30 px-4 py-3">
                        <p class="text-xs text-gray-400 mb-1.5">Dibuat Oleh</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ transaction.created_by?.name ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- =========================================
                 CARD 3: DAFTAR ITEM
            ========================================= -->
            <div class="rounded-xl bg-white dark:bg-gray-900 border
                        border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800
                            flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Item Transaksi
                    </h2>
                    <span class="inline-flex items-center rounded-full bg-indigo-100
                                 dark:bg-indigo-900/30 px-2.5 py-0.5 text-xs font-semibold
                                 text-indigo-700 dark:text-indigo-400">
                        {{ transaction.items?.length ?? 0 }} item
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50">
                                <th class="px-6 py-3 text-left text-xs font-semibold
                                           text-gray-400 uppercase tracking-wider w-8">
                                    #
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold
                                           text-gray-400 uppercase tracking-wider">
                                    Nama
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold
                                           text-gray-400 uppercase tracking-wider">
                                    Keterangan
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold
                                           text-gray-400 uppercase tracking-wider">
                                    Qty
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-semibold
                                           text-gray-400 uppercase tracking-wider">
                                    Harga Satuan
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-semibold
                                           text-gray-400 uppercase tracking-wider">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <tr
                                v-for="(item, index) in transaction.items"
                                :key="item.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition"
                            >
                                <td class="px-6 py-3.5">
                                    <span class="h-6 w-6 rounded-full bg-indigo-100
                                                 dark:bg-indigo-900/30 text-indigo-600
                                                 dark:text-indigo-400 text-xs font-bold
                                                 flex items-center justify-center">
                                        {{ index + 1 }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ item.name }}
                                    </p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ item.description || '—' }}
                                    </p>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center justify-center rounded-full
                                                 bg-gray-100 dark:bg-gray-800 px-2.5 py-0.5
                                                 text-xs font-semibold text-gray-700 dark:text-gray-300">
                                        {{ item.qty }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ formatCurrency(item.unit_price) }}
                                    </p>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ formatCurrency(item.amount_per_item) }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Summary footer -->
                <div class="border-t border-gray-100 dark:border-gray-800 px-6 py-4
                            bg-gray-50/50 dark:bg-gray-800/30">
                    <div class="ml-auto w-full sm:w-80 space-y-2">

                        <!-- Subtotal -->
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">
                                Subtotal
                                <span class="text-xs text-gray-400 ml-1">
                                    ({{ transaction.items?.length }} item, {{ totalQty }} qty)
                                </span>
                            </span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ formatCurrency(subtotal) }}
                            </span>
                        </div>

                        <!-- Diskon jika ada -->
                        <div v-if="transaction.discount && parseFloat(transaction.discount) > 0"
                             class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Diskon</span>
                            <span class="font-medium text-rose-600 dark:text-rose-400">
                                - {{ formatCurrency(transaction.discount) }}
                            </span>
                        </div>

                        <!-- Pajak jika ada -->
                        <div v-if="transaction.tax_percent && parseInt(transaction.tax_percent) > 0"
                             class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">
                                PPN {{ transaction.tax_percent }}%
                            </span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ formatCurrency(
                                    (subtotal - (parseFloat(transaction.discount) || 0))
                                    * (transaction.tax_percent / 100)
                                ) }}
                            </span>
                        </div>

                        <!-- Biaya lain jika ada -->
                        <div v-if="transaction.other_fee && parseFloat(transaction.other_fee) > 0"
                             class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Biaya Lain-lain</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ formatCurrency(transaction.other_fee) }}
                            </span>
                        </div>

                        <!-- Garis pemisah -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-2"/>

                        <!-- Grand Total -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">
                                Grand Total
                            </span>
                            <span :class="[
                                'text-xl font-extrabold',
                                isIncome
                                    ? 'text-emerald-600 dark:text-emerald-400'
                                    : 'text-rose-600 dark:text-rose-400',
                            ]">
                                {{ formatCurrency(transaction.amountTotal) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- =========================================
                 CARD 4: JOURNAL ENTRY
            ========================================= -->
            <div v-if="transaction.journalEntry"
                 class="rounded-xl bg-white dark:bg-gray-900 border
                         border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800
                            flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                            Journal Entry
                        </h2>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ transaction.journalEntry.entry_number }}
                        </p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-emerald-100
                                 dark:bg-emerald-900/30 px-2.5 py-0.5 text-xs font-semibold
                                 text-emerald-700 dark:text-emerald-400">
                        {{ transaction.journalEntry.status }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50">
                                <th class="px-6 py-3 text-left text-xs font-semibold
                                           text-gray-400 uppercase tracking-wider">
                                    Akun (COA)
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-semibold
                                           text-gray-400 uppercase tracking-wider">
                                    Debit
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-semibold
                                           text-gray-400 uppercase tracking-wider">
                                    Kredit
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <tr
                                v-for="line in transaction.journalEntry.lines"
                                :key="line.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition"
                            >
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <span :class="[
                                            'h-2 w-2 rounded-full shrink-0',
                                            line.type === 'debit'
                                                ? 'bg-indigo-500'
                                                : 'bg-amber-500',
                                        ]"/>
                                        <p class="text-sm text-gray-900 dark:text-white">
                                            <span class="font-mono text-gray-400 mr-1.5 text-xs">
                                                {{ line.chartOfAccount?.code }}
                                            </span>
                                            {{ line.chartOfAccount?.name }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <p v-if="line.type === 'debit'"
                                       class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                                        {{ formatCurrency(line.amount) }}
                                    </p>
                                    <p v-else class="text-sm text-gray-300 dark:text-gray-600">—</p>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <p v-if="line.type === 'credit'"
                                       class="text-sm font-semibold text-amber-600 dark:text-amber-400">
                                        {{ formatCurrency(line.amount) }}
                                    </p>
                                    <p v-else class="text-sm text-gray-300 dark:text-gray-600">—</p>
                                </td>
                            </tr>
                        </tbody>

                        <!-- Total baris -->
                        <tfoot>
                            <tr class="bg-gray-50 dark:bg-gray-800/50 border-t
                                       border-gray-200 dark:border-gray-700">
                                <td class="px-6 py-3 text-xs font-semibold text-gray-500
                                           dark:text-gray-400 uppercase">
                                    Total
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-bold
                                           text-indigo-600 dark:text-indigo-400">
                                    {{ formatCurrency(transaction.amountTotal) }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-bold
                                           text-amber-600 dark:text-amber-400">
                                    {{ formatCurrency(transaction.amountTotal) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>