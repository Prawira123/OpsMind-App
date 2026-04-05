<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// =========================================================
// PROPS
// =========================================================
const props = defineProps({
    report:     { type: Object, default: () => ({}) },
    dateFrom:   { type: String, default: '' },
    dateTo:     { type: String, default: '' },
    tenant:     { type: Object, default: () => ({}) },
})

// =========================================================
// FILTER
// =========================================================
const dateFrom = ref(props.dateFrom || new Date(new Date().getFullYear(), 0, 1).toISOString().split('T')[0])
const dateTo   = ref(props.dateTo   || new Date().toISOString().split('T')[0])
const search   = ref('')

const applyFilter = () => {
    router.get(route('reports.income'), {
        date_from: dateFrom.value,
        date_to:   dateTo.value,
    }, { preserveState: true })
}

const quickRanges = [
    { label: 'Bulan Ini',   from: () => new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],   to: () => new Date().toISOString().split('T')[0] },
    { label: 'Kuartal Ini', from: () => new Date(new Date().getFullYear(), Math.floor(new Date().getMonth()/3)*3, 1).toISOString().split('T')[0], to: () => new Date().toISOString().split('T')[0] },
    { label: 'Tahun Ini',   from: () => new Date(new Date().getFullYear(), 0, 1).toISOString().split('T')[0],                       to: () => new Date().toISOString().split('T')[0] },
]

const setQuick = (range) => {
    dateFrom.value = range.from()
    dateTo.value   = range.to()
    applyFilter()
}

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
        day: '2-digit', month: 'long', year: 'numeric',
    }).format(new Date(val))
}

// =========================================================
// REPORT DATA
// =========================================================
const revenues = computed(() => {
    const items = props.report?.revenues ?? []
    if (!search.value) return items
    const q = search.value.toLowerCase()
    return items.filter(i => i.name?.toLowerCase().includes(q) || i.code?.toLowerCase().includes(q))
})

const expenses = computed(() => {
    const items = props.report?.expenses ?? []
    if (!search.value) return items
    const q = search.value.toLowerCase()
    return items.filter(i => i.name?.toLowerCase().includes(q) || i.code?.toLowerCase().includes(q))
})

const totalRevenue = computed(() => revenues.value.reduce((s, i) => s + parseFloat(i.balance || 0), 0))
const totalExpense = computed(() => expenses.value.reduce((s, i) => s + parseFloat(i.balance || 0), 0))
const netIncome    = computed(() => totalRevenue.value - totalExpense.value)

// =========================================================
// EXPORT PDF
// =========================================================
const exportPdf = () => {
    const params = new URLSearchParams({ date_from: dateFrom.value, date_to: dateTo.value })
    window.open(`${route('reports.income.pdf')}?${params}`, '_blank')
}
</script>

<template>
        <div class="max-w-full mx-auto">

            <!-- HEADER -->
            <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Laporan Laba Rugi
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        Periode {{ formatDate(dateFrom) }} — {{ formatDate(dateTo) }}
                    </p>
                </div>

                <button @click="exportPdf"
                    class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2.5
                           text-sm font-semibold text-white hover:bg-rose-500 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0
                                 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2
                                 2 0 01-2 2z"/>
                    </svg>
                    Export PDF
                </button>
            </div>

            <!-- FILTER BAR -->
            <div class="mb-5 rounded-xl bg-white dark:bg-gray-900 border
                        border-gray-200 dark:border-gray-800 p-4 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:flex-wrap">

                    <!-- Quick ranges -->
                    <div class="flex gap-2 flex-wrap">
                        <button v-for="r in quickRanges" :key="r.label"
                            @click="setQuick(r)"
                            class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-1.5
                                   text-xs font-medium text-gray-600 dark:text-gray-400
                                   hover:border-indigo-400 hover:text-indigo-600 transition">
                            {{ r.label }}
                        </button>
                    </div>

                    <div class="flex items-end gap-3 flex-wrap flex-1">

                        <!-- Dari -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                Dari
                            </label>
                            <input v-model="dateFrom" type="date"
                                class="rounded-lg border border-gray-200 dark:border-gray-700
                                       bg-gray-50 dark:bg-gray-800 px-3 py-2 text-sm
                                       text-gray-900 dark:text-white focus:outline-none
                                       focus:ring-2 focus:ring-indigo-500 transition"/>
                        </div>

                        <!-- Sampai -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                Sampai
                            </label>
                            <input v-model="dateTo" type="date"
                                class="rounded-lg border border-gray-200 dark:border-gray-700
                                       bg-gray-50 dark:bg-gray-800 px-3 py-2 text-sm
                                       text-gray-900 dark:text-white focus:outline-none
                                       focus:ring-2 focus:ring-indigo-500 transition"/>
                        </div>

                        <!-- Search -->
                        <div class="relative flex-1 min-w-48">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                            </svg>
                            <input v-model="search" type="text" placeholder="Cari akun..."
                                class="w-full rounded-lg border border-gray-200 dark:border-gray-700
                                       bg-gray-50 dark:bg-gray-800 pl-9 pr-4 py-2 text-sm
                                       text-gray-900 dark:text-white placeholder-gray-400
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"/>
                        </div>

                        <!-- Apply -->
                        <button @click="applyFilter"
                            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2
                                   text-sm font-semibold text-white hover:bg-indigo-500 transition">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13
                                         13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293
                                         6.707A1 1 0 013 6V4z"/>
                            </svg>
                            Terapkan
                        </button>
                    </div>
                </div>
            </div>

            <!-- SUMMARY CARDS -->
            <div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border
                            border-emerald-100 dark:border-emerald-800 p-5">
                    <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-1">
                        Total Pendapatan
                    </p>
                    <p class="text-2xl font-extrabold text-emerald-700 dark:text-emerald-400">
                        {{ formatCurrency(totalRevenue) }}
                    </p>
                </div>
                <div class="rounded-xl bg-rose-50 dark:bg-rose-900/20 border
                            border-rose-100 dark:border-rose-800 p-5">
                    <p class="text-xs font-semibold text-rose-600 dark:text-rose-400 uppercase tracking-wider mb-1">
                        Total Beban
                    </p>
                    <p class="text-2xl font-extrabold text-rose-700 dark:text-rose-400">
                        {{ formatCurrency(totalExpense) }}
                    </p>
                </div>
                <div :class="[
                    'rounded-xl border p-5',
                    netIncome >= 0
                        ? 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-100 dark:border-indigo-800'
                        : 'bg-red-50 dark:bg-red-900/20 border-red-100 dark:border-red-800',
                ]">
                    <p :class="[
                        'text-xs font-semibold uppercase tracking-wider mb-1',
                        netIncome >= 0
                            ? 'text-indigo-600 dark:text-indigo-400'
                            : 'text-red-600 dark:text-red-400',
                    ]">
                        {{ netIncome >= 0 ? 'Laba Bersih' : 'Rugi Bersih' }}
                    </p>
                    <p :class="[
                        'text-2xl font-extrabold',
                        netIncome >= 0
                            ? 'text-indigo-700 dark:text-indigo-400'
                            : 'text-red-700 dark:text-red-400',
                    ]">
                        {{ formatCurrency(Math.abs(netIncome)) }}
                    </p>
                </div>
            </div>

            <!-- LAPORAN TABLE -->
            <div class="rounded-xl bg-white dark:bg-gray-900 border
                        border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

                <!-- PENDAPATAN -->
                <div class="border-b border-gray-100 dark:border-gray-800">
                    <div class="px-6 py-3 bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">
                            Pendapatan
                        </h3>
                    </div>

                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50">
                                <th class="px-6 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Kode</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama Akun</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800/50">
                            <tr v-if="revenues.length === 0">
                                <td colspan="3" class="px-6 py-6 text-center text-sm text-gray-400">
                                    Tidak ada data pendapatan
                                </td>
                            </tr>
                            <tr v-for="item in revenues" :key="item.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                                <td class="px-6 py-3 text-xs font-mono text-gray-400">{{ item.code }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ item.name }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                    {{ formatCurrency(item.balance) }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-emerald-50 dark:bg-emerald-900/20 border-t border-emerald-100 dark:border-emerald-800">
                                <td colspan="2" class="px-6 py-3 text-sm font-bold text-emerald-700 dark:text-emerald-400">
                                    Total Pendapatan
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-extrabold text-emerald-700 dark:text-emerald-400">
                                    {{ formatCurrency(totalRevenue) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- BEBAN -->
                <div class="border-b border-gray-100 dark:border-gray-800">
                    <div class="px-6 py-3 bg-rose-50 dark:bg-rose-900/20 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-rose-700 dark:text-rose-400 uppercase tracking-wider">
                            Beban / Pengeluaran
                        </h3>
                    </div>

                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50">
                                <th class="px-6 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Kode</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama Akun</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800/50">
                            <tr v-if="expenses.length === 0">
                                <td colspan="3" class="px-6 py-6 text-center text-sm text-gray-400">
                                    Tidak ada data beban
                                </td>
                            </tr>
                            <tr v-for="item in expenses" :key="item.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                                <td class="px-6 py-3 text-xs font-mono text-gray-400">{{ item.code }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ item.name }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-rose-600 dark:text-rose-400">
                                    {{ formatCurrency(item.balance) }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-rose-50 dark:bg-rose-900/20 border-t border-rose-100 dark:border-rose-800">
                                <td colspan="2" class="px-6 py-3 text-sm font-bold text-rose-700 dark:text-rose-400">
                                    Total Beban
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-extrabold text-rose-700 dark:text-rose-400">
                                    {{ formatCurrency(totalExpense) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- LABA/RUGI BERSIH -->
                <div :class="[
                    'px-6 py-4 flex items-center justify-between',
                    netIncome >= 0
                        ? 'bg-indigo-600 dark:bg-indigo-700'
                        : 'bg-red-600 dark:bg-red-700',
                ]">
                    <p class="text-base font-bold text-white">
                        {{ netIncome >= 0 ? 'Laba Bersih' : 'Rugi Bersih' }}
                    </p>
                    <p class="text-xl font-extrabold text-white">
                        {{ formatCurrency(Math.abs(netIncome)) }}
                    </p>
                </div>
            </div>

        </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>