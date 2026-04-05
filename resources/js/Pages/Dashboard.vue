<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted, computed } from 'vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const props = defineProps({
    totalBalance: { type: Number, default: 0 },
    monthlyStats: { type: Object, default: () => ({ income: 0, expense: 0 }) },
    invoicePending: { type: Number, default: 0 },
    recentTransactions: { type: Object, default: () => ({ data: [] }) },
    topClients: { type: Array, default: () => [] },
    incomePerMonth: {
        type: Object,
        default: () => ({ labels: [], totals: [] })
    }
})

const chartRef = ref(null)

const formatCurrency = (val) => 
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(Math.abs(val))

onMounted(() => {
    // console.log('Dashboard Props:', props) // Debug data flow
    
    const ctx = chartRef.value.getContext('2d')
    
    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 400)
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)') // Indigo
    gradient.addColorStop(0.5, 'rgba(139, 92, 246, 0.1)') // Violet
    gradient.addColorStop(1, 'rgba(139, 92, 246, 0)')

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: props.incomePerMonth?.labels?.length ? props.incomePerMonth.labels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            datasets: [{
                label: 'Performance',
                data: props.incomePerMonth?.totals?.length ? props.incomePerMonth.totals : [0, 0, 0, 0, 0, 0, 0],
                fill: true,
                backgroundColor: gradient,
                borderColor: '#818cf8', // Indigo-400
                borderWidth: 3,
                pointBackgroundColor: '#8b5cf6', // Violet-500
                pointBorderColor: '#fff',
                pointHoverRadius: 6,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    grid: { color: 'rgba(255, 255, 255, 0.03)' },
                    ticks: { color: 'rgba(255, 255, 255, 0.3)', font: { size: 10 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: 'rgba(255, 255, 255, 0.3)', font: { size: 10 } }
                }
            }
        }
    })
})
</script>

<template>
    <AppLayout title="Dashboard Overview">
        <Head title="Premium Dashboard" />

        <div class="space-y-6 pb-8">
            <!-- TOP SECTION: TOTAL HOLDING & SUMMARY -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- TOTAL HOLDING (HERO CARD) -->
                <div class="lg:col-span-1 rounded-3xl p-8 bg-gray-800/40 border border-white/5 
                            shadow-2xl flex flex-col justify-between relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-600/10 blur-[80px] group-hover:bg-indigo-600/20 transition-all duration-700"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-6">
                            <span class="text-gray-400 text-sm font-medium uppercase tracking-widest">Total Balance</span>
                            <div class="flex gap-2">
                                <button class="h-8 w-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white text-xs hover:bg-white/10 transition px-2">6M</button>
                                <button class="h-8 w-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white text-xs hover:bg-white/10 transition">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M19 9l-7 7-7-7" stroke-width="3"/></svg>
                                </button>
                            </div>
                        </div>
                        
                        <h2 class="text-4xl sm:text-5xl font-extrabold text-white tracking-tight mb-2">
                            {{ formatCurrency(props.totalBalance) }}
                        </h2>
                        <div class="flex items-center gap-2">
                            <span class="text-indigo-400 text-sm font-bold flex items-center gap-1">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M5 10l7-7m0 0l7 7m-7-7v18" stroke-width="2.5"/></svg>
                                +0%
                            </span>
                            <span class="text-gray-500 text-xs text-balance">Current across accounts</span>
                        </div>
                    </div>

                    <div class="mt-12 flex gap-3 relative z-10">
                        <a :href="route('transactions.create')" class="flex-1 text-center bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-bold py-3 px-4 rounded-2xl shadow-lg shadow-indigo-600/20 transition duration-300">
                            Create Transaction
                        </a>
                        <button class="p-3 bg-white/5 border border-white/10 hover:bg-white/10 rounded-2xl transition duration-300">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 5v14m7-7H5" stroke-width="2.5"/></svg>
                        </button>
                    </div>
                </div>

                <!-- QUICK STATS (GRID) -->
                <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Income Card -->
                    <div class="rounded-3xl p-6 bg-gray-800/40 border border-white/5 hover:border-indigo-500/20 transition-all duration-300 group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="h-12 w-12 rounded-2xl bg-white/5 flex items-center justify-center text-2xl group-hover:scale-110 transition duration-300 group-hover:bg-indigo-500/10">📈</div>
                            <span class="text-indigo-400 bg-indigo-400/10 text-xs font-bold px-2 py-1 rounded-lg">Bulan Ini</span>
                        </div>
                        <p class="text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Total Pendapatan</p>
                        <p class="text-2xl font-bold text-white">{{ formatCurrency(props.monthlyStats.income) }}</p>
                    </div>
                    <!-- Expense Card -->
                    <div class="rounded-3xl p-6 bg-gray-800/40 border border-white/5 hover:border-indigo-500/20 transition-all duration-300 group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="h-12 w-12 rounded-2xl bg-white/5 flex items-center justify-center text-2xl group-hover:scale-110 transition duration-300 group-hover:bg-rose-500/10">📉</div>
                            <span class="text-rose-400 bg-rose-400/10 text-xs font-bold px-2 py-1 rounded-lg">Bulan Ini</span>
                        </div>
                        <p class="text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Total Pengeluaran</p>
                        <p class="text-2xl font-bold text-white text-rose-500">{{ formatCurrency(props.monthlyStats.expense) }}</p>
                    </div>
                    <!-- Invoice Pending -->
                    <div class="rounded-3xl p-6 bg-gray-800/40 border border-white/5 hover:border-indigo-500/20 transition-all duration-300 group sm:col-span-2">
                        <div class="flex justify-between items-start mb-4">
                            <div class="h-12 w-12 rounded-2xl bg-white/5 flex items-center justify-center text-2xl group-hover:scale-110 transition duration-300">📄</div>
                            <a :href="route('invoices.index')" class="text-indigo-400 hover:underline text-xs font-bold">Manage Invoices</a>
                        </div>
                        <p class="text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Invoice Pending / Draft</p>
                        <p class="text-2xl font-bold text-white">{{ props.invoicePending }} Items</p>
                    </div>
                </div>
            </div>

            <!-- MIDDLE SECTION: PERFORMANCE CHART -->
            <div class="rounded-[40px] bg-gray-800/40 border border-white/5 p-8 shadow-2xl relative overflow-hidden group">
                <div class="flex flex-wrap items-center justify-between mb-8">
                    <div>
                        <h3 class="text-xl font-bold text-white tracking-tight">Performance Summary</h3>
                        <p class="text-sm text-gray-500">Income trend across months</p>
                    </div>
                    <div class="flex bg-white/5 p-1 rounded-2xl border border-white/10 mt-4 sm:mt-0">
                        <button v-for="t in ['1D','1W','1M','6M','1Y']" :key="t" 
                                :class="t === '6M' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:text-white'"
                                class="px-4 py-2 text-xs font-bold rounded-xl transition duration-300">
                            {{ t }}
                        </button>
                    </div>
                </div>

                <div class="h-[300px] w-full">
                    <canvas ref="chartRef"></canvas>
                </div>
            </div>

            <!-- BOTTOM SECTION: TRANSACTIONS & CLIENTS -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- TRANSACTIONS -->
                <div class="lg:col-span-2 rounded-[32px] bg-gray-800/40 border border-white/5 p-8">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-bold text-white">Recent Transactions</h3>
                        <a :href="route('transactions.index')" class="text-indigo-500 text-sm font-bold hover:underline">See all</a>
                    </div>

                    <div class="space-y-4">
                        <div v-if="props.recentTransactions.data.length === 0" class="text-center py-10 text-gray-500">
                            Belum ada transaksi.
                        </div>
                        <div v-for="trx in props.recentTransactions.data" :key="trx.id" 
                             class="group flex items-center justify-between p-4 rounded-2xl hover:bg-white/5 transition duration-300 border border-transparent hover:border-white/5">
                            <div class="flex items-center gap-4">
                                <div :class="trx.type === 'income' ? 'bg-indigo-500/10 text-indigo-500' : 'bg-rose-500/10 text-rose-500'" 
                                     class="h-12 w-12 rounded-xl flex items-center justify-center shrink-0">
                                    <svg v-if="trx.type === 'income'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M19 14l-7 7m0 0l-7-7m7 7V3" stroke-width="2"/></svg>
                                    <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M5 10l7-7m0 0l7 7m-7-7v18" stroke-width="2.5"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-white font-bold text-sm truncate group-hover:text-indigo-300 transition">{{ trx.description }}</p>
                                    <p class="text-gray-500 text-xs">{{ trx.client?.name ?? 'Umum' }} &bull; {{ trx.date }}</p>
                                </div>
                            </div>
                            <p :class="trx.type === 'income' ? 'text-indigo-400' : 'text-white text-rose-500'" class="font-black text-sm whitespace-nowrap">
                                {{ trx.type === 'income' ? '+' : '-' }} {{ formatCurrency(trx.amountTotal) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- TOP CLIENTS / WATCHLIST -->
                <div class="rounded-[32px] bg-gray-800/40 border border-white/5 p-8 flex flex-col">
                    <h3 class="text-xl font-bold text-white mb-8">Top Growth Clients</h3>
                    
                    <div class="space-y-6 flex-1">
                        <div v-if="props.topClients.length === 0" class="text-center py-10 text-gray-500">
                            Belum ada data klien.
                        </div>
                        <div v-for="clientStat in props.topClients" :key="clientStat.client_id" class="flex items-center justify-between group">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-900 to-indigo-800 border border-white/10 flex items-center justify-center text-white font-bold text-xs uppercase">
                                    {{ clientStat.client?.name ? clientStat.client.name.charAt(0) : '?' }}
                                </div>
                                <div>
                                    <p class="text-white font-bold text-sm group-hover:text-indigo-400 transition">{{ clientStat.client?.name ?? 'Unknown' }}</p>
                                    <p class="text-gray-500 text-xs">{{ clientStat.client?.email ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-white font-bold text-sm">{{ formatCurrency(clientStat.total_spent) }}</p>
                                <p class="text-indigo-400 text-[10px] font-bold">Total Spent</p>
                            </div>
                        </div>

                        <!-- ADD BUTTON -->
                        <a :href="route('clients.create')" class="mt-8 border-2 border-dashed border-white/20 rounded-2xl p-6 flex flex-col items-center justify-center text-center group cursor-pointer hover:border-indigo-500/50 transition duration-300">
                             <div class="h-10 w-10 rounded-full bg-white/5 flex items-center justify-center mb-2 group-hover:bg-indigo-600 transition shadow-lg shadow-indigo-600/0 group-hover:shadow-indigo-600/20">
                                 <svg class="h-5 w-5 text-gray-400 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 4v16m8-8H4" stroke-width="2"/></svg>
                             </div>
                             <p class="text-gray-500 text-xs font-bold group-hover:text-white">Add New Client</p>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<style>
/* Smooth chart glow effect backdrop */
.chart-container {
    position: relative;
    z-index: 1;
}
</style>
