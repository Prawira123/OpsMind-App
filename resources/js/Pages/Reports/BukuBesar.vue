<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    accounts:     { type: Array, default: () => [] },
    selectedCoa:  { type: Number, default: null },
    ledger:       { type: Array, default: () => [] },
    openingBal:   { type: Number, default: 0 },
    dateFrom:     { type: String, default: '' },
    dateTo:       { type: String, default: '' },
})

const dateFrom   = ref(props.dateFrom || new Date(new Date().getFullYear(), 0, 1).toISOString().split('T')[0])
const dateTo     = ref(props.dateTo   || new Date().toISOString().split('T')[0])
const search     = ref('')
const selectedId = ref(props.selectedCoa || '')

const applyFilter = () => {
    router.get(route('reports.ledger'), {
        date_from: dateFrom.value,
        date_to:   dateTo.value,
        coa_id:    selectedId.value,
    }, { preserveState: true })
}

const quickRanges = [
    { label: 'Bulan Ini',   from: () => new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0], to: () => new Date().toISOString().split('T')[0] },
    { label: 'Kuartal Ini', from: () => new Date(new Date().getFullYear(), Math.floor(new Date().getMonth()/3)*3, 1).toISOString().split('T')[0], to: () => new Date().toISOString().split('T')[0] },
    { label: 'Tahun Ini',   from: () => new Date(new Date().getFullYear(), 0, 1).toISOString().split('T')[0], to: () => new Date().toISOString().split('T')[0] },
]

const setQuick = (r) => { dateFrom.value = r.from(); dateTo.value = r.to(); applyFilter() }

const formatCurrency = (val) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val || 0)

const formatDate = (val) => {
    if (!val) return '—'
    return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }).format(new Date(val))
}

const filteredLedger = computed(() => {
    if (!search.value) return props.ledger
    const q = search.value.toLowerCase()
    return props.ledger.filter(e =>
        e.description?.toLowerCase().includes(q) ||
        e.reference?.toLowerCase().includes(q)
    )
})

// Running balance
const ledgerWithBalance = computed(() => {
    let runningBal = parseFloat(props.openingBal || 0)
    return filteredLedger.value.map(entry => {
        const debit  = parseFloat(entry.debit  || 0)
        const credit = parseFloat(entry.credit || 0)
        runningBal += debit - credit
        return { ...entry, running_balance: runningBal }
    })
})

const totalDebit  = computed(() => filteredLedger.value.reduce((s, e) => s + parseFloat(e.debit  || 0), 0))
const totalCredit = computed(() => filteredLedger.value.reduce((s, e) => s + parseFloat(e.credit || 0), 0))
const endingBal   = computed(() => parseFloat(props.openingBal || 0) + totalDebit.value - totalCredit.value)

const selectedAccount = computed(() =>
    props.accounts.find(a => a.id == selectedId.value)
)

const exportPdf = () => {
    const params = new URLSearchParams({
        date_from: dateFrom.value,
        date_to:   dateTo.value,
        coa_id:    selectedId.value,
    })
    window.open(`${route('reports.ledger.pdf')}?${params}`, '_blank')
}
</script>

<template>
        <div class="max-w-full mx-auto">

            <!-- HEADER -->
            <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Buku Besar</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        <span v-if="selectedAccount">
                            {{ selectedAccount.code }} — {{ selectedAccount.name }}
                        </span>
                        <span v-else>Pilih akun untuk melihat buku besar</span>
                    </p>
                </div>
                <button @click="exportPdf"
                    class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2.5
                           text-sm font-semibold text-white hover:bg-rose-500 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0
                                 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export PDF
                </button>
            </div>

            <!-- FILTER -->
            <div class="mb-5 rounded-xl bg-white dark:bg-gray-900 border
                        border-gray-200 dark:border-gray-800 p-4 shadow-sm space-y-3">

                <!-- Quick ranges -->
                <div class="flex gap-2 flex-wrap">
                    <button v-for="r in quickRanges" :key="r.label" @click="setQuick(r)"
                        class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-1.5
                               text-xs font-medium text-gray-600 dark:text-gray-400
                               hover:border-indigo-400 hover:text-indigo-600 transition">
                        {{ r.label }}
                    </button>
                </div>

                <div class="flex items-end gap-3 flex-wrap">

                    <!-- Pilih Akun COA -->
                    <div class="flex-1 min-w-48">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Pilih Akun (COA)</label>
                        <select v-model="selectedId"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-700
                                   bg-gray-50 dark:bg-gray-800 px-3 py-2 text-sm
                                   text-gray-900 dark:text-white focus:outline-none
                                   focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="">— Semua Akun —</option>
                            <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                                {{ acc.code }} — {{ acc.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Dari</label>
                        <input v-model="dateFrom" type="date"
                            class="rounded-lg border border-gray-200 dark:border-gray-700
                                   bg-gray-50 dark:bg-gray-800 px-3 py-2 text-sm
                                   text-gray-900 dark:text-white focus:outline-none
                                   focus:ring-2 focus:ring-indigo-500 transition"/>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Sampai</label>
                        <input v-model="dateTo" type="date"
                            class="rounded-lg border border-gray-200 dark:border-gray-700
                                   bg-gray-50 dark:bg-gray-800 px-3 py-2 text-sm
                                   text-gray-900 dark:text-white focus:outline-none
                                   focus:ring-2 focus:ring-indigo-500 transition"/>
                    </div>

                    <!-- Search -->
                    <div class="relative flex-1 min-w-40">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                        <input v-model="search" type="text" placeholder="Cari keterangan..."
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-700
                                   bg-gray-50 dark:bg-gray-800 pl-9 pr-4 py-2 text-sm
                                   text-gray-900 dark:text-white placeholder-gray-400
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"/>
                    </div>

                    <button @click="applyFilter"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2
                               text-sm font-semibold text-white hover:bg-indigo-500 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0
                                     01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        Terapkan
                    </button>
                </div>
            </div>

            <!-- SUMMARY CARDS -->
            <div class="mb-5 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded-xl bg-white dark:bg-gray-900 border
                            border-gray-200 dark:border-gray-800 p-4">
                    <p class="text-xs text-gray-400 mb-1">Saldo Awal</p>
                    <p class="text-base font-bold text-gray-900 dark:text-white">
                        {{ formatCurrency(openingBal) }}
                    </p>
                </div>
                <div class="rounded-xl bg-indigo-50 dark:bg-indigo-900/20 border
                            border-indigo-100 dark:border-indigo-800 p-4">
                    <p class="text-xs text-indigo-500 dark:text-indigo-400 mb-1">Total Debit</p>
                    <p class="text-base font-bold text-indigo-700 dark:text-indigo-400">
                        {{ formatCurrency(totalDebit) }}
                    </p>
                </div>
                <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border
                            border-amber-100 dark:border-amber-800 p-4">
                    <p class="text-xs text-amber-500 dark:text-amber-400 mb-1">Total Kredit</p>
                    <p class="text-base font-bold text-amber-700 dark:text-amber-400">
                        {{ formatCurrency(totalCredit) }}
                    </p>
                </div>
                <div :class="[
                    'rounded-xl border p-4',
                    endingBal >= 0
                        ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-100 dark:border-emerald-800'
                        : 'bg-red-50 dark:bg-red-900/20 border-red-100 dark:border-red-800',
                ]">
                    <p :class="['text-xs mb-1', endingBal >= 0 ? 'text-emerald-500 dark:text-emerald-400' : 'text-red-500 dark:text-red-400']">
                        Saldo Akhir
                    </p>
                    <p :class="['text-base font-bold', endingBal >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-700 dark:text-red-400']">
                        {{ formatCurrency(endingBal) }}
                    </p>
                </div>
            </div>

            <!-- LEDGER TABLE -->
            <div class="rounded-xl bg-white dark:bg-gray-900 border
                        border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-800">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">No. Jurnal</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Keterangan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Referensi</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-indigo-500 uppercase tracking-wider">Debit</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-amber-500 uppercase tracking-wider">Kredit</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">

                            <!-- Saldo awal -->
                            <tr class="bg-gray-50/50 dark:bg-gray-800/20">
                                <td colspan="4" class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400">
                                    Saldo Awal
                                </td>
                                <td class="px-4 py-3"/>
                                <td class="px-4 py-3"/>
                                <td class="px-4 py-3 text-right text-sm font-bold text-gray-900 dark:text-white">
                                    {{ formatCurrency(openingBal) }}
                                </td>
                            </tr>

                            <!-- Empty -->
                            <tr v-if="ledgerWithBalance.length === 0">
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="h-12 w-12 rounded-xl bg-gray-100 dark:bg-gray-800
                                                    flex items-center justify-center text-2xl">📒</div>
                                        <p class="text-sm text-gray-400">Belum ada entri</p>
                                        <p class="text-xs text-gray-300 dark:text-gray-600">
                                            Pilih akun dan periode untuk melihat buku besar
                                        </p>
                                    </div>
                                </td>
                            </tr>

                            <!-- Data rows -->
                            <tr v-for="entry in ledgerWithBalance" :key="entry.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                                <td class="px-4 py-3 text-xs font-mono text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ formatDate(entry.date) }}
                                </td>
                                <td class="px-4 py-3 text-xs font-mono text-gray-400 whitespace-nowrap">
                                    {{ entry.entry_number }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white max-w-xs truncate">
                                    {{ entry.description }}
                                </td>
                                <td class="px-4 py-3 text-xs font-mono text-gray-400">
                                    {{ entry.reference || '—' }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                                    {{ parseFloat(entry.debit) > 0 ? formatCurrency(entry.debit) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-amber-600 dark:text-amber-400">
                                    {{ parseFloat(entry.credit) > 0 ? formatCurrency(entry.credit) : '—' }}
                                </td>
                                <td :class="[
                                    'px-4 py-3 text-right text-sm font-bold',
                                    entry.running_balance >= 0
                                        ? 'text-gray-900 dark:text-white'
                                        : 'text-red-600 dark:text-red-400',
                                ]">
                                    {{ formatCurrency(entry.running_balance) }}
                                </td>
                            </tr>
                        </tbody>

                        <!-- Footer total -->
                        <tfoot v-if="ledgerWithBalance.length > 0">
                            <tr class="bg-gray-900 dark:bg-gray-800 border-t border-gray-700">
                                <td colspan="4" class="px-4 py-3 text-sm font-bold text-white">
                                    Total
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-extrabold text-indigo-400">
                                    {{ formatCurrency(totalDebit) }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-extrabold text-amber-400">
                                    {{ formatCurrency(totalCredit) }}
                                </td>
                                <td :class="[
                                    'px-4 py-3 text-right text-sm font-extrabold',
                                    endingBal >= 0 ? 'text-emerald-400' : 'text-red-400',
                                ]">
                                    {{ formatCurrency(endingBal) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>