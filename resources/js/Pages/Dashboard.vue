<script setup>
import { computed } from 'vue'
import { defineAsyncComponent } from 'vue'
import { usePage, Link, Deferred, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardSkeleton from '@/Components/Dashboard/DashboardSkeleton.vue'

// =========================================================
// LAZY LOADED COMPONENTS
// =========================================================
import KpiCards from '@/Components/Dashboard/KpiCards.vue'
import IncomeChart from '@/Components/Dashboard/IncomeChart.vue'
import MonthlySummary from '@/Components/Dashboard/MonthlySummary.vue'
import RecentTransactions from '@/Components/Dashboard/RecentTransactions.vue'
import TopClients from '@/Components/Dashboard/TopClients.vue'

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

const isRefreshing = ref(false)

const refreshDashboard = () => {
    isRefreshing.value = true
    router.reload({
        only: ['totalBalance', 'monthlyStats', 'invoicePending', 'recentTransactions', 'topClients', 'incomePerMonth'],
        onFinish: () => {
            isRefreshing.value = false
        }
    })
}
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
            <div class="flex items-center gap-3">
                <button @click="refreshDashboard"
                    :disabled="isRefreshing"
                    class="inline-flex items-center gap-2 rounded-xl bg-white dark:bg-gray-800 px-4 py-2.5
                           text-sm font-semibold text-gray-700 dark:text-gray-200 border border-gray-200
                           dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700
                           transition-all shadow-sm disabled:opacity-50">
                    <svg class="h-4 w-4" :class="{ 'animate-spin': isRefreshing }"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Refresh
                </button>
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
        </div>

        <!-- =========================================
             DASHBOARD CONTENT
             Data loaded instantly from DashboardController (Redis cached)
        ========================================= -->
        <KpiCards
            :totalBalance="totalBalance"
            :monthlyStats="monthlyStats"
            :invoicePending="invoicePending"
        />

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3 mt-5 mb-5">
            <IncomeChart
                :incomePerMonth="incomePerMonth"
                class="lg:col-span-2"
            />
            <MonthlySummary :monthlyStats="monthlyStats" />
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            <RecentTransactions
                :recentTransactions="recentTransactions"
                class="lg:col-span-2"
            />
            <TopClients :topClients="topClients" />
        </div>

    </AppLayout>
</template>
