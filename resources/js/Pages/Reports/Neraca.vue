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
    router.get(route('reports.balance'), { date_from: dateFrom.value, date_to: dateTo.value }, { preserveState: true })
}

const quickRanges = [
    { label: 'Bulan Ini',   from: () => new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],   to: () => new Date().toISOString().split('T')[0] },
    { label: 'Tahun Ini',   from: () => new Date(new Date().getFullYear(), 0, 1).toISOString().split('T')[0],                       to: () => new Date().toISOString().split('T')[0] },
]

const setQuick = (range) => { dateFrom.value = range.from(); dateTo.value = range.to(); applyFilter() }

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
    return items.filter(i => i.name?.toLowerCase().includes(q) || i.code?.toLowerCase().includes(q))
}

const assets      = computed(() => filterItems(props.report?.assets))
const liabilities = computed(() => filterItems(props.report?.liabilities))
const equity      = computed(() => filterItems(props.report?.equity))

const totalAssets      = computed(() => assets.value.reduce((s, i) => s + parseFloat(i.balance || 0), 0))
const totalLiabilities = computed(() => liabilities.value.reduce((s, i) => s + parseFloat(i.balance || 0), 0))
const totalEquity      = computed(() => equity.value.reduce((s, i) => s + parseFloat(i.balance || 0), 0))
const totalLiabEquity  = computed(() => totalLiabilities.value + totalEquity.value)
const isBalance        = computed(() => Math.abs(totalAssets.value - totalLiabEquity.value) < 1)

const exportPdf = () => {
    const params = new URLSearchParams({ date_from: dateFrom.value, date_to: dateTo.value })
    window.open(`${route('reports.balance.pdf')}?${params}`, '_blank')
}

// Reusable section table component data
const sections = computed(() => [
    { title: 'Aset',        items: assets.value,      total: totalAssets.value,      color: 'blue'    },
    { title: 'Liabilitas',  items: liabilities.value, total: totalLiabilities.value, color: 'orange'  },
    { title: 'Modal/Ekuitas', items: equity.value,    total: totalEquity.value,      color: 'violet'  },
])

const colorMap = {
    blue:   { header: 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400', footer: 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400', text: 'text-blue-600 dark:text-blue-400' },
    orange: { header: 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400', footer: 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400', text: 'text-orange-600 dark:text-orange-400' },
    violet: { header: 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-400', footer: 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-400', text: 'text-violet-600 dark:text-violet-400' },
}
</script>

<template>
        <div class="max-w-full mx-auto">

            <!-- HEADER -->
            <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laporan Neraca</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        Per {{ formatDate(dateTo) }}
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

            <!-- FILTER BAR -->
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
                                       text-gray-900 dark:text-white focus:outline-none focus:ring-2
                                       focus:ring-indigo-500 transition"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Sampai</label>
                            <input v-model="dateTo" type="date"
                                class="rounded-lg border border-gray-200 dark:border-gray-700
                                       bg-gray-50 dark:bg-gray-800 px-3 py-2 text-sm
                                       text-gray-900 dark:text-white focus:outline-none focus:ring-2
                                       focus:ring-indigo-500 transition"/>
                        </div>
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
                        <button @click="applyFilter"
                            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2
                                   text-sm font-semibold text-white hover:bg-indigo-500 transition">
                            Terapkan
                        </button>
                    </div>
                </div>
            </div>

            <!-- BALANCE CHECK -->
            <div :class="[
                'mb-5 rounded-xl px-4 py-3 flex items-center gap-3 text-sm font-medium',
                isBalance
                    ? 'bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400'
                    : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400',
            ]">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path v-if="isBalance" stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    <path v-else stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                {{ isBalance ? 'Neraca seimbang — Aset = Liabilitas + Modal' : 'Neraca tidak seimbang! Periksa pencatatan jurnal' }}
            </div>

            <!-- SECTIONS -->
            <div class="space-y-4">
                <div v-for="section in sections" :key="section.title"
                     class="rounded-xl bg-white dark:bg-gray-900 border
                            border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

                    <div :class="['px-6 py-3', colorMap[section.color].header]">
                        <h3 class="text-sm font-bold uppercase tracking-wider">{{ section.title }}</h3>
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
                            <tr v-if="section.items.length === 0">
                                <td colspan="3" class="px-6 py-6 text-center text-sm text-gray-400">
                                    Tidak ada data
                                </td>
                            </tr>
                            <tr v-for="item in section.items" :key="item.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                                <td class="px-6 py-3 text-xs font-mono text-gray-400">{{ item.code }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ item.name }}</td>
                                <td :class="['px-4 py-3 text-right text-sm font-semibold', colorMap[section.color].text]">
                                    {{ formatCurrency(item.balance) }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr :class="['border-t', colorMap[section.color].footer]">
                                <td colspan="2" class="px-6 py-3 text-sm font-bold">
                                    Total {{ section.title }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-extrabold">
                                    {{ formatCurrency(section.total) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Total Liab + Equity -->
                <div class="rounded-xl bg-gray-900 dark:bg-gray-800 px-6 py-4
                            flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-white">Total Liabilitas + Modal</p>
                        <p class="text-xs text-gray-400 mt-0.5">Harus sama dengan Total Aset</p>
                    </div>
                    <p class="text-xl font-extrabold text-white">
                        {{ formatCurrency(totalLiabEquity) }}
                    </p>
                </div>
            </div>
        </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>