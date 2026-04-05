<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted, computed } from 'vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const props = defineProps({
    incomePerMonth: {
        type: Object,
        default: () => ({ labels: [], totals: [] })
    }
})

const chartRef = ref(null)

// Dummy Data for Preview
const stats = ref([
    { label: 'Total Saldo', value: 'Rp 128.450.000', change: '+12.5%', isPositive: true, icon: '💰' },
    { label: 'Pendapatan (Bln Ini)', value: 'Rp 45.200.000', change: '+8.2%', isPositive: true, icon: '📈' },
    { label: 'Pengeluaran (Bln Ini)', value: 'Rp 12.800.000', change: '-2.4%', isPositive: false, icon: '📉' },
    { label: 'Invoice Pending', value: '14 Items', change: '5 Due Soon', isPositive: false, icon: '📄' },
])

const recentTransactions = ref([
    { id: 1, date: '2024-03-20', desc: 'Pembayaran Invoice #INV-001', cat: 'Sales', amount: 5000000, type: 'income' },
    { id: 2, date: '2024-03-19', desc: 'Sewa Kantor Bulanan', cat: 'Rent', amount: -12000000, type: 'expense' },
    { id: 3, date: '2024-03-18', desc: 'Pendapatan Jasa Konsultasi', cat: 'Service', amount: 8500000, type: 'income' },
    { id: 4, date: '2024-03-17', desc: 'Listrik & Internet', cat: 'Utility', amount: -1500000, type: 'expense' },
    { id: 5, date: '2024-03-16', desc: 'Pembelian Inventaris', cat: 'Asset', amount: -3500000, type: 'expense' },
])

const topClients = ref([
    { name: 'PT. Maju Bersama', industry: 'Teknologi', total: 'Rp 85M' },
    { name: 'CV. Sumber Rejeki', industry: 'Retail', total: 'Rp 42M' },
    { name: 'Bpk. Ahmad Bakri', industry: 'Personal', total: 'Rp 12M' },
])

const formatCurrency = (val) => 
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(Math.abs(val))

onMounted(() => {
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
                data: props.incomePerMonth?.totals?.length ? props.incomePerMonth.totals : [30, 45, 35, 50, 40, 60, 55],
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
    <AppLayout title="Dashboard Preview">
        <Head title="Premium Dashboard" />

        <div class="space-y-6 pb-8">
            <!-- TOP SECTION: TOTAL HOLDING & SUMMARY -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- TOTAL HOLDING (HERO CARD) -->
                <div class="lg:col-span-1 rounded-3xl p-8 bg-[#111114] border border-white/5 
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
                        
                        <h2 class="text-5xl font-extrabold text-white tracking-tight mb-2">
                            Rp 12.304.110
                        </h2>
                        <div class="flex items-center gap-2">
                            <span class="text-indigo-400 text-sm font-bold flex items-center gap-1">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M5 10l7-7m0 0l7 7m-7-7v18" stroke-width="2.5"/></svg>
                                +12.5%
                            </span>
                            <span class="text-gray-500 text-xs text-balance">Return this month</span>
                        </div>
                    </div>

                    <div class="mt-12 flex gap-3 relative z-10">
                        <button class="flex-1 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-bold py-3 px-4 rounded-2xl shadow-lg shadow-indigo-600/20 transition duration-300">
                            Create Transaction
                        </button>
                        <button class="p-3 bg-white/5 border border-white/10 hover:bg-white/10 rounded-2xl transition duration-300">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 5v14m7-7H5" stroke-width="2.5"/></svg>
                        </button>
                    </div>
                </div>

                <!-- QUICK STATS (GRID) -->
                <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div v-for="stat in stats.slice(1)" :key="stat.label" 
                         class="rounded-3xl p-6 bg-[#111114] border border-white/5 hover:border-indigo-500/20 transition-all duration-300 group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="h-12 w-12 rounded-2xl bg-white/5 flex items-center justify-center text-2xl group-hover:scale-110 transition duration-300 group-hover:bg-indigo-500/10">
                                {{ stat.icon }}
                            </div>
                            <span :class="stat.isPositive ? 'text-indigo-400 bg-indigo-400/10' : 'text-rose-400 bg-rose-400/10'" class="text-xs font-bold px-2 py-1 rounded-lg">
                                {{ stat.change }}
                            </span>
                        </div>
                        <p class="text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">{{ stat.label }}</p>
                        <p class="text-2xl font-bold text-white">{{ stat.value }}</p>
                    </div>
                </div>
            </div>

            <!-- MIDDLE SECTION: PERFORMANCE CHART -->
            <div class="rounded-[40px] bg-[#111114] border border-white/5 p-8 shadow-2xl relative overflow-hidden group">
                <div class="flex flex-wrap items-center justify-between mb-8">
                    <div>
                        <h3 class="text-xl font-bold text-white tracking-tight">Performance Summary</h3>
                        <p class="text-sm text-gray-500">Overview of income growth over time</p>
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
                <div class="lg:col-span-2 rounded-[32px] bg-[#111114] border border-white/5 p-8">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-bold text-white">Recent Transactions</h3>
                        <button class="text-indigo-500 text-sm font-bold hover:underline">See all</button>
                    </div>

                    <div class="space-y-4">
                        <div v-for="trx in recentTransactions" :key="trx.id" 
                             class="group flex items-center justify-between p-4 rounded-2xl hover:bg-white/5 transition duration-300 border border-transparent hover:border-white/5">
                            <div class="flex items-center gap-4">
                                <div :class="trx.type === 'income' ? 'bg-indigo-500/10 text-indigo-500' : 'bg-rose-500/10 text-rose-500'" 
                                     class="h-12 w-12 rounded-xl flex items-center justify-center shrink-0">
                                    <svg v-if="trx.type === 'income'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M19 14l-7 7m0 0l-7-7m7 7V3" stroke-width="2"/></svg>
                                    <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M5 10l7-7m0 0l7 7m-7-7v18" stroke-width="2.5"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-white font-bold text-sm truncate group-hover:text-indigo-300 transition">{{ trx.desc }}</p>
                                    <p class="text-gray-500 text-xs">{{ trx.cat }} &bull; {{ trx.date }}</p>
                                </div>
                            </div>
                            <p :class="trx.type === 'income' ? 'text-indigo-400' : 'text-white'" class="font-black text-sm whitespace-nowrap">
                                {{ trx.type === 'income' ? '+' : '-' }} {{ formatCurrency(trx.amount) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- TOP CLIENTS / WATCHLIST -->
                <div class="rounded-[32px] bg-[#111114] border border-white/5 p-8 flex flex-col">
                    <h3 class="text-xl font-bold text-white mb-8">Top Growth Clients</h3>
                    
                    <div class="space-y-6 flex-1">
                        <div v-for="client in topClients" :key="client.name" class="flex items-center justify-between group">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-900 to-indigo-800 border border-white/10 flex items-center justify-center text-white font-bold text-xs uppercase">
                                    {{ client.name.charAt(0) }}
                                </div>
                                <div>
                                    <p class="text-white font-bold text-sm group-hover:text-indigo-400 transition">{{ client.name }}</p>
                                    <p class="text-gray-500 text-xs">{{ client.industry }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-white font-bold text-sm">{{ client.total }}</p>
                                <p class="text-indigo-400 text-[10px] font-bold">+18.2%</p>
                            </div>
                        </div>

                        <!-- EMPTY / ADD BUTTON -->
                        <div class="mt-8 border-2 border-dashed border-white/5 rounded-2xl p-6 flex flex-col items-center justify-center text-center group cursor-pointer hover:border-indigo-500/50 transition duration-300">
                             <div class="h-10 w-10 rounded-full bg-white/5 flex items-center justify-center mb-2 group-hover:bg-indigo-600 transition shadow-lg shadow-indigo-600/0 group-hover:shadow-indigo-600/20">
                                 <svg class="h-5 w-5 text-gray-500 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 4v16m8-8H4" stroke-width="2"/></svg>
                             </div>
                             <p class="text-gray-500 text-xs font-bold group-hover:text-white">Add New Client</p>
                        </div>
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
