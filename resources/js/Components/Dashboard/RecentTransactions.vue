<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    recentTransactions: { type: Object, default: () => ({ data: [] }) },
})

const formatDate = (val) => {
    if (!val) return '—'
    return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short' }).format(new Date(val))
}

const formatCurrency = (val) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val || 0)
</script>

<template>
    <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Transaksi Terbaru</h2>
            <Link :href="route('transactions.index')" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                Lihat Semua →
            </Link>
        </div>

        <div class="divide-y divide-gray-50 dark:divide-gray-800/50">
            <div v-if="!recentTransactions?.data?.length" class="flex flex-col items-center gap-2 py-12 text-center">
                <div class="h-12 w-12 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-2xl">🧾</div>
                <p class="text-sm text-gray-400">Belum ada transaksi</p>
            </div>

            <div v-for="trx in (recentTransactions?.data ?? [])" :key="trx.id"
                 class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition group">
                <div :class="[
                    'h-9 w-9 rounded-xl flex items-center justify-center shrink-0',
                    trx.type === 'income' ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-rose-100 dark:bg-rose-900/40',
                ]">
                    <svg class="h-4 w-4" :class="trx.type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path v-if="trx.type === 'income'" stroke-linecap="round" stroke-linejoin="round" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ trx.description }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ formatDate(trx.date) }}
                        <span v-if="trx.client?.name" class="ml-1.5">· {{ trx.client.name }}</span>
                    </p>
                </div>

                <p :class="['text-sm font-bold shrink-0', trx.type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400']">
                    {{ trx.type === 'income' ? '+' : '-' }}{{ formatCurrency(trx.amountTotal) }}
                </p>
            </div>
        </div>
    </div>
</template>
