<script setup>
import { computed } from 'vue'
import { defineAsyncComponent } from 'vue'
import { usePage, Link, Deferred } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardSkeleton from '@/Components/Dashboard/DashboardSkeleton.vue'

// =========================================================
// LAZY LOADED COMPONENTS
// =========================================================
const KpiCards = defineAsyncComponent(() =>
    import('@/Components/Dashboard/KpiCards.vue'))
const IncomeChart = defineAsyncComponent(() =>
    import('@/Components/Dashboard/IncomeChart.vue'))
const MonthlySummary = defineAsyncComponent(() =>
    import('@/Components/Dashboard/MonthlySummary.vue'))
const RecentTransactions = defineAsyncComponent(() =>
    import('@/Components/Dashboard/RecentTransactions.vue'))
const TopClients = defineAsyncComponent(() =>
    import('@/Components/Dashboard/TopClients.vue'))

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
// GREETING
// =========================================================
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
             DEFERRED DASHBOARD CONTENT
             Data loaded asynchronously from DashboardController via Inertia::defer()
        ========================================= -->
        <Deferred :data="['totalBalance', 'monthlyStats', 'invoicePending', 'recentTransactions', 'topClients', 'incomePerMonth']">

            <template #fallback>
                <DashboardSkeleton />
            </template>

            <!-- =========================================
                 ROW 1: KPI CARDS
            ========================================= -->
        <KpiCards
                :totalBalance="totalBalance"
                :monthlyStats="monthlyStats"
                :invoicePending="invoicePending"
            />

            <!-- =========================================
                 ROW 2: CHART + NET INCOME (Lazy loaded)
            ========================================= -->
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3 mt-5 mb-5">
                <IncomeChart
                    :incomePerMonth="incomePerMonth"
                    class="lg:col-span-2"
                />
                <MonthlySummary :monthlyStats="monthlyStats" />
            </div>

            <!-- =========================================
                 ROW 3: TRANSACTIONS + TOP CLIENTS (Lazy loaded)
            ========================================= -->
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                <RecentTransactions
                    :recentTransactions="recentTransactions"
                    class="lg:col-span-2"
                />
                <TopClients :topClients="topClients" />
            </div>

        </Deferred>

    </AppLayout>
</template>
