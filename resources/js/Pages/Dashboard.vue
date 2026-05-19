<script setup>
import { computed, onMounted, watch } from 'vue'
import { usePage, Link, Deferred, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

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
    financialData:      { type: Object,  default: () => ({ monthlyStats: { income: 0, expense: 0 }, incomePerMonth: { labels: [], totals: [] }, totalBalance: 0 }) },
    invoicePending:     { type: Number,  default: 0 },
    recentTransactions: { type: Object,  default: () => ({ data: [] }) },
    topClients:         { type: Object,  default: () => ({ data: [] }) },
    userOnline:         { type: Number,  default: 0 },
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
        only: ['financialData', 'invoicePending', 'recentTransactions', 'topClients', 'userOnline'],
        onFinish: () => {
            isRefreshing.value = false
        }
    })
}

//WEB SOCKET
window.Pusher = Pusher
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST === 'localhost' ? '127.0.0.1' : (import.meta.env.VITE_REVERB_HOST || window.location.hostname),
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ['ws', 'wss']
});

const localFinancialData = ref(props.financialData)
const localInvoicePending = ref(props.invoicePending)
const localRecentTransactions = ref(props.recentTransactions)
const localTopClients = ref(props.topClients)
const localUserOnline = ref(props.userOnline)

// Sync local refs if Inertia sends down new props
watch(() => props.financialData, (newVal) => {
    localFinancialData.value = newVal
}, { deep: true })

watch(() => props.invoicePending, (newVal) => {
    localInvoicePending.value = newVal
})

watch(() => props.recentTransactions, (newVal) => {
    localRecentTransactions.value = newVal
}, { deep: true })

watch(() => props.topClients, (newVal) => {
    localTopClients.value = newVal
}, { deep: true })

watch(() => props.userOnline, (newVal) => {
    localUserOnline.value = newVal
})

// Computed properties mapping from local financial data
const totalBalance = computed(() => localFinancialData.value?.totalBalance ?? 0)
const monthlyStats = computed(() => localFinancialData.value?.monthlyStats ?? { income: 0, expense: 0 })
const incomePerMonth = computed(() => localFinancialData.value?.incomePerMonth ?? { labels: [], totals: [] })

onMounted(() => {
    if (user.value?.tenant_id) {
        window.Echo.private(`tenant.${user.value.tenant_id}`)
            .listen('.data-financial', (e) => {
                console.log('data-financial event received:', e)
                if (e.financialData) {
                    localFinancialData.value = e.financialData
                }
            })
            .listen('.data-invoice-pending', (e) => {
                console.log('data-invoice-pending event received:', e)
                if (typeof e.invoicePending !== 'undefined') {
                    localInvoicePending.value = e.invoicePending
                }
            })
            .listen('.data-recent-transactions', (e) => {
                console.log('data-recent-transactions event received:', e)
                if (e.recentTransactions) {
                    localRecentTransactions.value = e.recentTransactions
                }
            })
            .listen('.data-top-client', (e) => {
                console.log('data-top-client event received:', e)
                if (e.topClients) {
                    localTopClients.value = e.topClients
                }
            })
            .listen('.data-user-online', (e) => {
                console.log('data-user-online event received:', e)
                if (typeof e.userOnline !== 'undefined') {
                    localUserOnline.value = e.userOnline
                }
            })
    }
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
                <div class="flex items-center gap-1.5 mt-2 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/50 rounded-full px-2.5 py-0.5 w-fit">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">
                        {{ localUserOnline }} Pengguna Online
                    </span>
                </div>
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
            :invoicePending="localInvoicePending"
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
                :recentTransactions="localRecentTransactions"
                class="lg:col-span-2"
            />
            <TopClients :topClients="localTopClients" />
        </div>

    </AppLayout>
</template>
