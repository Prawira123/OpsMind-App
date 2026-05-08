<script setup>
import { computed } from 'vue'

const props = defineProps({
    monthlyStats: { type: Object, default: () => ({ income: 0, expense: 0 }) },
})

const netMonthly = computed(() => (props.monthlyStats?.income || 0) - (props.monthlyStats?.expense || 0))

const incomeRatio = computed(() => {
    const inc = props.monthlyStats?.income || 0
    const exp = props.monthlyStats?.expense || 0
    const total = inc + exp
    return total > 0 ? Math.round((inc / total) * 100) : 0
})

const formatShort = (val) => {
    const n = parseFloat(val || 0)
    if (n >= 1_000_000_000) return `Rp ${(n / 1_000_000_000).toFixed(1)}M`
    if (n >= 1_000_000) return `Rp ${(n / 1_000_000).toFixed(1)}Jt`
    if (n >= 1_000) return `Rp ${(n / 1_000).toFixed(0)}Rb`
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n)
}
</script>

<template>
    <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 shadow-sm flex flex-col justify-between">
        <div>
            <h2 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Ringkasan Bulan Ini</h2>
            <p class="text-xs text-gray-400">{{ new Date().toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }) }}</p>
        </div>

        <div class="my-4 space-y-4">
            <!-- Income bar -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 shrink-0"/>Pemasukan
                    </span>
                    <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">{{ formatShort(monthlyStats?.income) }}</span>
                </div>
                <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                    <div class="h-full rounded-full bg-emerald-500 transition-all duration-700" :style="{ width: incomeRatio + '%' }"/>
                </div>
            </div>

            <!-- Expense bar -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full bg-rose-500 shrink-0"/>Pengeluaran
                    </span>
                    <span class="text-xs font-semibold text-rose-600 dark:text-rose-400">{{ formatShort(monthlyStats?.expense) }}</span>
                </div>
                <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                    <div class="h-full rounded-full bg-rose-500 transition-all duration-700" :style="{ width: (100 - incomeRatio) + '%' }"/>
                </div>
            </div>
        </div>

        <!-- Net -->
        <div :class="['rounded-xl px-4 py-3 flex items-center justify-between', netMonthly >= 0 ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-rose-50 dark:bg-rose-900/20']">
            <div>
                <p class="text-xs text-gray-400">Net Bulan Ini</p>
                <p :class="['text-lg font-extrabold mt-0.5', netMonthly >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400']">
                    {{ netMonthly >= 0 ? '+' : '' }}{{ formatShort(netMonthly) }}
                </p>
            </div>
            <div :class="['h-10 w-10 rounded-xl flex items-center justify-center text-xl', netMonthly >= 0 ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-rose-100 dark:bg-rose-900/40']">
                {{ netMonthly >= 0 ? '📈' : '📉' }}
            </div>
        </div>
    </div>
</template>
