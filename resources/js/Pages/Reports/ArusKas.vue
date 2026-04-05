<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    report:   { type: Object, default: () => ({}) },
    dateFrom: { type: String, default: '' },
    dateTo:   { type: String, default: '' },
})

const dateFrom = ref(props.dateFrom || new Date(new Date().getFullYear(), 0, 1).toISOString().split('T')[0])
const dateTo   = ref(props.dateTo   || new Date().toISOString().split('T')[0])
const search   = ref('')

const applyFilter = () => {
    router.get(route('reports.cashflow'), { date_from: dateFrom.value, date_to: dateTo.value }, { preserveState: true })
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
    return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }).format(new Date(val))
}

const filterItems = (items) => {
    if (!items) return []
    if (!search.value) return items
    const q = search.value.toLowerCase()
    return items.filter(i => i.description?.toLowerCase().includes(q) || i.account?.toLowerCase().includes(q))
}

const operating  = computed(() => filterItems(props.report?.operating))
const investing  = computed(() => filterItems(props.report?.investing))
const financing  = computed(() => filterItems(props.report?.financing))

const totalOperating = computed(() => operating.value.reduce((s, i) => s + parseFloat(i.amount || 0), 0))
const totalInvesting = computed(() => investing.value.reduce((s, i) => s + parseFloat(i.amount || 0), 0))
const totalFinancing = computed(() => financing.value.reduce((s, i) => s + parseFloat(i.amount || 0), 0))
const netCashFlow    = computed(() => totalOperating.value + totalInvesting.value + totalFinancing.value)

const exportPdf = () => {
    const params = new URLSearchParams({ date_from: dateFrom.value, date_to: dateTo.value })
    window.open(`${route('reports.cashflow.pdf')}?${params}`, '_blank')
}

const sections = computed(() => [
    { key: 'operating', title: 'Arus Kas Operasional', items: operating.value, total: totalOperating.value, color: 'indigo', desc: 'Dari kegiatan operasi bisnis utama' },
    { key: 'investing',  title: 'Arus Kas Investasi',   items: investing.value,  total: totalInvesting.value,  color: 'amber',  desc: 'Dari pembelian/penjualan aset' },
    { key: 'financing',  title: 'Arus Kas Pendanaan',   items: financing.value,  total: totalFinancing.value,  color: 'violet', desc: 'Dari hutang, modal, dan dividen' },
])

const colorMap = {
    indigo: { header: 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400', text: 'text-indigo-600 dark:text-indigo-400' },
    amber:  { header: 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400',     text: 'text-amber-600 dark:text-amber-400'   },
    violet: { header: 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-400', text: 'text-violet-600 dark:text-violet-400' },
}
</script>

<template>
        <div class="max-w-full mx-auto">

            <!-- HEADER -->
            <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laporan Arus Kas</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        Periode {{ formatDate(dateFrom) }} — {{ formatDate(dateTo) }}
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
                        border-gray-200 dark:border-gray-800 p-4 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end flex-wrap">
                    <div class="flex gap-2">
                        <button v-for="r in quickRanges" :key="r.label" @click="setQuick(r)"
                            class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-1.5
                                   text-xs font-medium text-gray-600 dark:text-gray-400
                                   hover:border-indigo-400 hover:text-indigo-600 transition">
                            {{ r.label }}
                        </button>
                    </div>
                    <div class="flex items-end gap-3 flex-wrap flex-1">
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
                        <div class="relative flex-1 min-w-48">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                            </svg>
                            <input v-model="search" type="text" placeholder="Cari transaksi..."
                                class="w-full rounded-lg border border-gray-200 dark:border-gray-700
                                       bg-gray-50 dark:bg-gray-800 pl-9 pr-4 py-2 text-sm
                                       text-gray-900 dark:text-white placeholder-gray-400
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"/>
                        </div>
                        <button @click="applyFilter"
                            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2
                                   text-sm font-semibold text-white hover:bg-indigo-500 transition">
                            Terapkan
                        </button>
                    </div>
                </div>
            </div>

            <!-- SUMMARY -->
            <div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div v-for="s in sections" :key="s.key"
                     class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4">
                    <p class="text-xs text-gray-400 mb-1">{{ s.title }}</p>
                    <p :class="['text-lg font-extrabold', s.total >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400']">
                        {{ s.total >= 0 ? '+' : '' }}{{ formatCurrency(s.total) }}
                    </p>
                </div>
                <div :class="[
                    'rounded-xl border p-4',
                    netCashFlow >= 0
                        ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800'
                        : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800',
                ]">
                    <p class="text-xs text-gray-400 mb-1">Net Arus Kas</p>
                    <p :class="['text-lg font-extrabold', netCashFlow >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400']">
                        {{ netCashFlow >= 0 ? '+' : '' }}{{ formatCurrency(netCashFlow) }}
                    </p>
                </div>
            </div>

            <!-- SECTIONS TABLE -->
            <div class="space-y-4">
                <div v-for="section in sections" :key="section.key"
                     class="rounded-xl bg-white dark:bg-gray-900 border
                            border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

                    <div :class="['px-6 py-3', colorMap[section.color].header]">
                        <h3 class="text-sm font-bold uppercase tracking-wider">{{ section.title }}</h3>
                        <p class="text-xs opacity-70 mt-0.5">{{ section.desc }}</p>
                    </div>

                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50">
                                <th class="px-6 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Keterangan</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Akun</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Masuk</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Keluar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800/50">
                            <tr v-if="section.items.length === 0">
                                <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-400">
                                    Tidak ada data
                                </td>
                            </tr>
                            <tr v-for="item in section.items" :key="item.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                                <td class="px-6 py-3 text-xs font-mono text-gray-400">{{ item.date }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ item.description }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ item.account }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                    {{ parseFloat(item.amount) >= 0 ? formatCurrency(item.amount) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-rose-600 dark:text-rose-400">
                                    {{ parseFloat(item.amount) < 0 ? formatCurrency(Math.abs(item.amount)) : '—' }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr :class="['border-t', colorMap[section.color].header]">
                                <td colspan="3" class="px-6 py-3 text-sm font-bold">
                                    Net {{ section.title }}
                                </td>
                                <td colspan="2" :class="['px-4 py-3 text-right text-sm font-extrabold', section.total >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-700 dark:text-rose-400']">
                                    {{ section.total >= 0 ? '+' : '' }}{{ formatCurrency(section.total) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Net Cash Flow -->
                <div :class="[
                    'rounded-xl px-6 py-4 flex items-center justify-between',
                    netCashFlow >= 0 ? 'bg-emerald-600' : 'bg-red-600',
                ]">
                    <div>
                        <p class="text-sm font-bold text-white">Net Arus Kas Bersih</p>
                        <p class="text-xs text-white/70 mt-0.5">Operasional + Investasi + Pendanaan</p>
                    </div>
                    <p class="text-xl font-extrabold text-white">
                        {{ netCashFlow >= 0 ? '+' : '' }}{{ formatCurrency(netCashFlow) }}
                    </p>
                </div>
            </div>
        </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>