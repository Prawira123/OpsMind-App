<script setup>
import { computed } from 'vue'

const props = defineProps({
    summary: Object,
})

const formatCurrency = (val) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0,
    }).format(val || 0)

const progressWidth = computed(() => {
    const income = props.summary?.total_income || 0
    const expense = props.summary?.total_expense || 0
    const total = income + expense
    return total > 0 ? Math.round((income / total) * 100) : 50
})
</script>

<template>
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <!-- Saldo Total -->
        <div class="rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                Saldo Total
            </p>
            <p class="text-2xl font-extrabold text-gray-900 dark:text-white">
                {{ formatCurrency(summary.total_balance) }}
            </p>
            <div class="mt-2 h-1 w-full rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                <div class="h-full rounded-full bg-indigo-500 transition-all duration-700" :style="{ width: '100%' }"></div>
            </div>
        </div>

        <!-- Total Pemasukan -->
        <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 p-5 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-emerald-500/10 transition-transform group-hover:scale-150"></div>
            <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-1">
                Total Pemasukan
            </p>
            <p class="text-2xl font-extrabold text-emerald-700 dark:text-emerald-400">
                {{ formatCurrency(summary.total_income) }}
            </p>
            <div class="mt-2 h-1 w-full rounded-full bg-emerald-100 dark:bg-emerald-800/50 overflow-hidden">
                <div class="h-full rounded-full bg-emerald-500 transition-all duration-700" :style="{ width: progressWidth + '%' }"></div>
            </div>
        </div>

        <!-- Total Pengeluaran -->
        <div class="rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 p-5 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-rose-500/10 transition-transform group-hover:scale-150"></div>
            <p class="text-xs font-semibold text-rose-600 dark:text-rose-400 uppercase tracking-wider mb-1">
                Total Pengeluaran
            </p>
            <p class="text-2xl font-extrabold text-rose-700 dark:text-rose-400">
                {{ formatCurrency(summary.total_expense) }}
            </p>
            <div class="mt-2 h-1 w-full rounded-full bg-rose-100 dark:bg-rose-800/50 overflow-hidden">
                <div class="h-full rounded-full bg-rose-500 transition-all duration-700" :style="{ width: (100 - progressWidth) + '%' }"></div>
            </div>
        </div>
    </div>
</template>
