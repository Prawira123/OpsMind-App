<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// =========================================================
// PROPS
// =========================================================
const props = defineProps({
    totalBalance:       { type: Number,  default: 0 },
    monthlyStats:       { type: Object,  default: () => ({ income: 0, expense: 0 }) },
    invoicePending:     { type: Number,  default: 0 },
    recentTransactions: { type: Object,  default: () => ({ data: [] }) },
    topClients:         { type: Array,   default: () => [] },
    incomePerMonth:     { type: Object,  default: () => ({ labels: [], totals: [] }) },
})

const page = usePage()
const user = computed(() => page.props.auth?.user)

// =========================================================
// FORMAT
// =========================================================
const formatCurrency = (val) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0,
    }).format(val || 0)

const formatShort = (val) => {
    const n = parseFloat(val || 0)
    if (n >= 1_000_000_000) return `Rp ${(n / 1_000_000_000).toFixed(1)}M`
    if (n >= 1_000_000)     return `Rp ${(n / 1_000_000).toFixed(1)}Jt`
    if (n >= 1_000)         return `Rp ${(n / 1_000).toFixed(0)}Rb`
    return formatCurrency(n)
}

const formatDate = (val) => {
    if (!val) return '—'
    return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short' }).format(new Date(val))
}

// =========================================================
// COMPUTED
// =========================================================
const netMonthly = computed(() =>
    (props.monthlyStats?.income || 0) - (props.monthlyStats?.expense || 0)
)

const incomeRatio = computed(() => {
    const inc = props.monthlyStats?.income || 0
    const exp = props.monthlyStats?.expense || 0
    const total = inc + exp
    return total > 0 ? Math.round((inc / total) * 100) : 0
})

// =========================================================
// CHART — Simple SVG sparkline for income per month
// =========================================================
const chartPoints = computed(() => {
    const totals = props.incomePerMonth?.totals ?? []
    if (totals.length < 2) return ''

    const w = 600
    const h = 120
    const max = Math.max(...totals, 1)
    const min = Math.min(...totals, 0)
    const range = max - min || 1

    return totals.map((val, i) => {
        const x = (i / (totals.length - 1)) * w
        const y = h - ((val - min) / range) * h
        return `${x},${y}`
    }).join(' ')
})

const chartArea = computed(() => {
    const totals = props.incomePerMonth?.totals ?? []
    if (totals.length < 2) return ''

    const w = 600
    const h = 120
    const max = Math.max(...totals, 1)
    const min = Math.min(...totals, 0)
    const range = max - min || 1

    const pts = totals.map((val, i) => {
        const x = (i / (totals.length - 1)) * w
        const y = h - ((val - min) / range) * h
        return `${x},${y}`
    })

    return `0,${h} ${pts.join(' ')} ${w},${h}`
})

// Tooltip for chart
const hoverIndex = ref(null)

const getChartX = (index) => {
    const totals = props.incomePerMonth?.totals ?? []
    if (totals.length < 2) return 0
    return (index / (totals.length - 1)) * 600
}

const getChartY = (index) => {
    const totals = props.incomePerMonth?.totals ?? []
    if (totals.length < 2) return 120
    const max = Math.max(...totals, 1)
    const min = Math.min(...totals, 0)
    const range = max - min || 1
    return 120 - ((totals[index] - min) / range) * 120
}

// Greeting
const greeting = computed(() => {
    const hour = new Date().getHours()
    if (hour < 12) return 'Selamat Pagi'
    if (hour < 17) return 'Selamat Siang'
    return 'Selamat Malam'
})
</script>

<template>
    <AppLayout title="Dashboard">

        <!-- WELCOME HEADER -->
        <div class="mb-7 flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ greeting }}, 👋
                </p>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">
                    {{ user?.name?.split(' ')[0] ?? 'Pengguna' }}
                </h1>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                    Berikut ringkasan keuangan bisnis kamu hari ini
                </p>
            </div>
            <Link :href="route('transactions.create')"
                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5
                       text-sm font-semibold text-white hover:bg-indigo-500
                       shadow-lg shadow-indigo-500/30 transition-all hover:shadow-indigo-500/50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Transaksi
            </Link>
        </div>

        <!-- =========================================
             ROW 1: KPI CARDS
        ========================================= -->
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4 mb-6">

            <!-- Total Saldo -->
            <div class="col-span-2 lg:col-span-1 relative overflow-hidden rounded-2xl
                        bg-gradient-to-br from-indigo-600 to-violet-700
                        p-5 shadow-xl shadow-indigo-500/20">
                <!-- Decorative circle -->
                <div class="absolute -right-6 -top-6 h-28 w-28 rounded-full
                            bg-white/10 blur-sm"/>
                <div class="absolute -right-2 -bottom-8 h-20 w-20 rounded-full
                            bg-white/5"/>
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="h-7 w-7 rounded-lg bg-white/20 flex items-center justify-center">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-white/70 uppercase tracking-wider">
                            Total Saldo
                        </p>
                    </div>
                    <p class="text-2xl font-extrabold text-white leading-none">
                        {{ formatShort(totalBalance) }}
                    </p>
                    <p class="text-xs text-white/60 mt-1.5">Semua rekening aktif</p>
                </div>
            </div>

            <!-- Pemasukan Bulan Ini -->
            <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900
                        border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <div class="h-7 w-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/40
                                flex items-center justify-center">
                        <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-400" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Pemasukan
                    </p>
                </div>
                <p class="text-xl font-extrabold text-gray-900 dark:text-white leading-none">
                    {{ formatShort(monthlyStats?.income) }}
                </p>
                <p class="text-xs text-gray-400 mt-1.5">Bulan ini</p>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-500 rounded-b-2xl opacity-60"/>
            </div>

            <!-- Pengeluaran Bulan Ini -->
            <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900
                        border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <div class="h-7 w-7 rounded-lg bg-rose-100 dark:bg-rose-900/40
                                flex items-center justify-center">
                        <svg class="h-4 w-4 text-rose-600 dark:text-rose-400" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Pengeluaran
                    </p>
                </div>
                <p class="text-xl font-extrabold text-gray-900 dark:text-white leading-none">
                    {{ formatShort(monthlyStats?.expense) }}
                </p>
                <p class="text-xs text-gray-400 mt-1.5">Bulan ini</p>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-rose-500 rounded-b-2xl opacity-60"/>
            </div>

            <!-- Invoice Pending -->
            <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900
                        border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <div class="h-7 w-7 rounded-lg bg-amber-100 dark:bg-amber-900/40
                                flex items-center justify-center">
                        <svg class="h-4 w-4 text-amber-600 dark:text-amber-400" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                                     a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Invoice
                    </p>
                </div>
                <p class="text-xl font-extrabold text-gray-900 dark:text-white leading-none">
                    {{ invoicePending }}
                </p>
                <p class="text-xs text-gray-400 mt-1.5">Menunggu pembayaran</p>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-amber-500 rounded-b-2xl opacity-60"/>
            </div>
        </div>

        <!-- =========================================
             ROW 2: CHART + NET INCOME
        ========================================= -->
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3 mb-5">

            <!-- Income Chart -->
            <div class="lg:col-span-2 rounded-2xl bg-white dark:bg-gray-900
                        border border-gray-200 dark:border-gray-800 p-5 shadow-sm">

                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white">
                            Tren Pemasukan
                        </h2>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Per bulan
                        </p>
                    </div>
                    <Link :href="route('reports.income')"
                        class="text-xs font-medium text-indigo-600 dark:text-indigo-400
                               hover:underline">
                        Lihat Laporan →
                    </Link>
                </div>

                <!-- SVG Chart -->
                <div v-if="(incomePerMonth?.totals?.length ?? 0) > 1"
                     class="relative">
                    <svg viewBox="0 0 600 130" class="w-full h-36 overflow-visible"
                         @mouseleave="hoverIndex = null">

                        <!-- Grid lines -->
                        <line x1="0" y1="0"   x2="600" y2="0"   stroke="currentColor" stroke-opacity="0.05" class="text-gray-500"/>
                        <line x1="0" y1="40"  x2="600" y2="40"  stroke="currentColor" stroke-opacity="0.05" class="text-gray-500"/>
                        <line x1="0" y1="80"  x2="600" y2="80"  stroke="currentColor" stroke-opacity="0.05" class="text-gray-500"/>
                        <line x1="0" y1="120" x2="600" y2="120" stroke="currentColor" stroke-opacity="0.05" class="text-gray-500"/>

                        <!-- Area fill -->
                        <defs>
                            <linearGradient id="incomeGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%"   stop-color="#6366f1" stop-opacity="0.3"/>
                                <stop offset="100%" stop-color="#6366f1" stop-opacity="0.01"/>
                            </linearGradient>
                        </defs>
                        <polygon :points="chartArea" fill="url(#incomeGrad)"/>

                        <!-- Line -->
                        <polyline
                            :points="chartPoints"
                            fill="none"
                            stroke="#6366f1"
                            stroke-width="2.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />

                        <!-- Hover points -->
                        <g v-for="(val, i) in (incomePerMonth?.totals ?? [])" :key="i">
                            <circle
                                :cx="getChartX(i)"
                                :cy="getChartY(i)"
                                r="12"
                                fill="transparent"
                                class="cursor-pointer"
                                @mouseenter="hoverIndex = i"
                            />
                            <circle
                                v-if="hoverIndex === i"
                                :cx="getChartX(i)"
                                :cy="getChartY(i)"
                                r="4"
                                fill="#6366f1"
                                stroke="white"
                                stroke-width="2"
                            />
                        </g>

                        <!-- Tooltip -->
                        <g v-if="hoverIndex !== null">
                            <rect
                                :x="Math.min(getChartX(hoverIndex) - 50, 500)"
                                :y="getChartY(hoverIndex) - 38"
                                width="100" height="30" rx="6"
                                fill="#1e1b4b" fill-opacity="0.95"
                            />
                            <text
                                :x="Math.min(getChartX(hoverIndex), 550)"
                                :y="getChartY(hoverIndex) - 20"
                                text-anchor="middle"
                                font-size="9"
                                fill="white"
                                font-family="monospace"
                            >
                                {{ incomePerMonth?.labels?.[hoverIndex] }}
                            </text>
                            <text
                                :x="Math.min(getChartX(hoverIndex), 550)"
                                :y="getChartY(hoverIndex) - 10"
                                text-anchor="middle"
                                font-size="10"
                                fill="#a5b4fc"
                                font-weight="bold"
                                font-family="monospace"
                            >
                                {{ formatShort(incomePerMonth?.totals?.[hoverIndex]) }}
                            </text>
                        </g>
                    </svg>

                    <!-- X labels -->
                    <div class="flex justify-between mt-1 px-0.5">
                        <span v-for="(label, i) in (incomePerMonth?.labels ?? [])"
                              :key="i"
                              class="text-xs text-gray-400 truncate"
                              style="max-width:60px; font-size:10px">
                            {{ label?.split(' ')[0] }}
                        </span>
                    </div>
                </div>

                <!-- Empty chart -->
                <div v-else
                     class="flex flex-col items-center justify-center h-36 gap-2">
                    <div class="h-12 w-12 rounded-xl bg-gray-100 dark:bg-gray-800
                                flex items-center justify-center text-2xl">📈</div>
                    <p class="text-sm text-gray-400">Belum ada data pemasukan</p>
                </div>
            </div>

            <!-- Net Income + Ratio -->
            <div class="rounded-2xl bg-white dark:bg-gray-900
                        border border-gray-200 dark:border-gray-800 p-5 shadow-sm
                        flex flex-col justify-between">

                <div>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white mb-1">
                        Ringkasan Bulan Ini
                    </h2>
                    <p class="text-xs text-gray-400">
                        {{ new Date().toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }) }}
                    </p>
                </div>

                <div class="my-4 space-y-4">

                    <!-- Income bar -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                <span class="h-2 w-2 rounded-full bg-emerald-500 shrink-0"/>
                                Pemasukan
                            </span>
                            <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ formatShort(monthlyStats?.income) }}
                            </span>
                        </div>
                        <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-500 transition-all duration-700"
                                 :style="{ width: incomeRatio + '%' }"/>
                        </div>
                    </div>

                    <!-- Expense bar -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                <span class="h-2 w-2 rounded-full bg-rose-500 shrink-0"/>
                                Pengeluaran
                            </span>
                            <span class="text-xs font-semibold text-rose-600 dark:text-rose-400">
                                {{ formatShort(monthlyStats?.expense) }}
                            </span>
                        </div>
                        <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                            <div class="h-full rounded-full bg-rose-500 transition-all duration-700"
                                 :style="{ width: (100 - incomeRatio) + '%' }"/>
                        </div>
                    </div>
                </div>

                <!-- Net -->
                <div :class="[
                    'rounded-xl px-4 py-3 flex items-center justify-between',
                    netMonthly >= 0
                        ? 'bg-emerald-50 dark:bg-emerald-900/20'
                        : 'bg-rose-50 dark:bg-rose-900/20',
                ]">
                    <div>
                        <p class="text-xs text-gray-400">Net Bulan Ini</p>
                        <p :class="[
                            'text-lg font-extrabold mt-0.5',
                            netMonthly >= 0
                                ? 'text-emerald-600 dark:text-emerald-400'
                                : 'text-rose-600 dark:text-rose-400',
                        ]">
                            {{ netMonthly >= 0 ? '+' : '' }}{{ formatShort(netMonthly) }}
                        </p>
                    </div>
                    <div :class="[
                        'h-10 w-10 rounded-xl flex items-center justify-center text-xl',
                        netMonthly >= 0 ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-rose-100 dark:bg-rose-900/40',
                    ]">
                        {{ netMonthly >= 0 ? '📈' : '📉' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================================
             ROW 3: TRANSACTIONS + TOP CLIENTS
        ========================================= -->
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

            <!-- Recent Transactions -->
            <div class="lg:col-span-2 rounded-2xl bg-white dark:bg-gray-900
                        border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

                <div class="flex items-center justify-between px-5 py-4
                            border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white">
                        Transaksi Terbaru
                    </h2>
                    <Link :href="route('transactions.index')"
                        class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                        Lihat Semua →
                    </Link>
                </div>

                <div class="divide-y divide-gray-50 dark:divide-gray-800/50">

                    <!-- Empty -->
                    <div v-if="!recentTransactions?.data?.length"
                         class="flex flex-col items-center gap-2 py-12 text-center">
                        <div class="h-12 w-12 rounded-xl bg-gray-100 dark:bg-gray-800
                                    flex items-center justify-center text-2xl">🧾</div>
                        <p class="text-sm text-gray-400">Belum ada transaksi</p>
                    </div>

                    <div v-for="trx in (recentTransactions?.data ?? [])"
                         :key="trx.id"
                         class="flex items-center gap-4 px-5 py-3.5
                                hover:bg-gray-50 dark:hover:bg-gray-800/40 transition group">

                        <!-- Icon tipe -->
                        <div :class="[
                            'h-9 w-9 rounded-xl flex items-center justify-center shrink-0',
                            trx.type === 'income'
                                ? 'bg-emerald-100 dark:bg-emerald-900/40'
                                : 'bg-rose-100 dark:bg-rose-900/40',
                        ]">
                            <svg class="h-4 w-4" :class="trx.type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path v-if="trx.type === 'income'" stroke-linecap="round" stroke-linejoin="round"
                                      d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                <path v-else stroke-linecap="round" stroke-linejoin="round"
                                      d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                            </svg>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ trx.description }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ formatDate(trx.date) }}
                                <span v-if="trx.client?.name" class="ml-1.5">
                                    · {{ trx.client.name }}
                                </span>
                            </p>
                        </div>

                        <!-- Amount -->
                        <p :class="[
                            'text-sm font-bold shrink-0',
                            trx.type === 'income'
                                ? 'text-emerald-600 dark:text-emerald-400'
                                : 'text-rose-600 dark:text-rose-400',
                        ]">
                            {{ trx.type === 'income' ? '+' : '-' }}{{ formatShort(trx.amountTotal) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Top Clients -->
            <div class="rounded-2xl bg-white dark:bg-gray-900
                        border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

                <div class="flex items-center justify-between px-5 py-4
                            border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white">
                        Top Klien
                    </h2>
                    <Link :href="route('clients.index')"
                        class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                        Lihat Semua →
                    </Link>
                </div>

                <div class="divide-y divide-gray-50 dark:divide-gray-800/50">

                    <div v-if="!topClients?.length"
                         class="flex flex-col items-center gap-2 py-12 text-center">
                        <div class="h-12 w-12 rounded-xl bg-gray-100 dark:bg-gray-800
                                    flex items-center justify-center text-2xl">👥</div>
                        <p class="text-sm text-gray-400">Belum ada data klien</p>
                    </div>

                    <div v-for="(client, index) in topClients" :key="client.client_id"
                         class="flex items-center gap-3 px-5 py-3.5
                                hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">

                        <!-- Rank badge -->
                        <div :class="[
                            'h-7 w-7 rounded-lg flex items-center justify-center text-xs font-extrabold shrink-0',
                            index === 0 ? 'bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400' :
                            index === 1 ? 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400' :
                            index === 2 ? 'bg-orange-100 dark:bg-orange-900/40 text-orange-600 dark:text-orange-400' :
                            'bg-gray-50 dark:bg-gray-800/50 text-gray-400',
                        ]">
                            {{ index + 1 }}
                        </div>

                        <!-- Client info -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ client.client?.name ?? 'Klien #' + client.client_id }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ formatShort(client.total_spent) }} total
                            </p>
                        </div>

                        <!-- Medal for top 3 -->
                        <span v-if="index === 0" class="text-base">🥇</span>
                        <span v-else-if="index === 1" class="text-base">🥈</span>
                        <span v-else-if="index === 2" class="text-base">🥉</span>
                    </div>
                </div>

                <!-- Quick links -->
                <div class="border-t border-gray-100 dark:border-gray-800 p-4 space-y-2">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">
                        Akses Cepat
                    </p>
                    <div class="grid grid-cols-2 gap-2">
                        <Link :href="route('invoices.create')"
                            class="flex items-center gap-1.5 rounded-lg bg-gray-50 dark:bg-gray-800
                                   px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400
                                   hover:bg-indigo-50 dark:hover:bg-indigo-900/20
                                   hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                                         a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Invoice
                        </Link>
                        <Link :href="route('reports.income')"
                            class="flex items-center gap-1.5 rounded-lg bg-gray-50 dark:bg-gray-800
                                   px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400
                                   hover:bg-indigo-50 dark:hover:bg-indigo-900/20
                                   hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0
                                         002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2
                                         2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2
                                         2 0 01-2-2z"/>
                            </svg>
                            Laporan
                        </Link>
                        <Link :href="route('accounts.index')"
                            class="flex items-center gap-1.5 rounded-lg bg-gray-50 dark:bg-gray-800
                                   px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400
                                   hover:bg-indigo-50 dark:hover:bg-indigo-900/20
                                   hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3
                                         3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Rekening
                        </Link>
                        <Link :href="route('clients.index')"
                            class="flex items-center gap-1.5 rounded-lg bg-gray-50 dark:bg-gray-800
                                   px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400
                                   hover:bg-indigo-50 dark:hover:bg-indigo-900/20
                                   hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126
                                         -1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656
                                         .126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6
                                         0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0
                                         2 2 0 014 0z"/>
                            </svg>
                            Klien
                        </Link>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>