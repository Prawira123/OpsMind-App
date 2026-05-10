<script setup>
import { computed } from 'vue'

const props = defineProps({
    transaction: { type: Object, required: true },
})

const formatCurrency = (val) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0,
    }).format(val || 0)

const formatDate = (val) => {
    if (!val) return '—'
    return new Intl.DateTimeFormat('id-ID', {
        weekday: 'long', day: '2-digit', month: 'long', year: 'numeric',
    }).format(new Date(val))
}

const formatDateTime = (val) => {
    if (!val) return '—'
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
    }).format(new Date(val))
}

const isIncome = computed(() => props.transaction.type === 'income')

const typeConfig = {
    income:  { label: 'Pemasukan',   color: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400', dot: 'bg-emerald-500' },
    expense: { label: 'Pengeluaran', color: 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400',             dot: 'bg-rose-500'    },
}

const config = computed(() =>
    typeConfig[props.transaction.type] ?? typeConfig.expense
)
</script>

<template>
    <div class="rounded-2xl bg-white dark:bg-gray-900 border
                border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

        <!-- Top accent -->
        <div :class="[
            'h-1.5',
            isIncome ? 'bg-emerald-500' : 'bg-rose-500',
        ]"/>

        <div class="p-6">
            <div class="flex items-start justify-between gap-4">

                <!-- Kiri: Tipe + Jumlah -->
                <div>
                    <span :class="[
                        'inline-flex items-center gap-1.5 rounded-full px-3 py-1',
                        'text-xs font-semibold mb-3',
                        config.color,
                    ]">
                        <span :class="['h-1.5 w-1.5 rounded-full', config.dot]"/>
                        {{ config.label }}
                    </span>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                        Total Transaksi
                    </p>
                    <p :class="[
                        'text-4xl font-extrabold',
                        isIncome
                            ? 'text-emerald-600 dark:text-emerald-400'
                            : 'text-rose-600 dark:text-rose-400',
                    ]">
                        {{ isIncome ? '+' : '-' }}{{ formatCurrency(transaction.amountTotal) }}
                    </p>
                </div>

                <!-- Kanan: Tanggal -->
                <div class="text-right shrink-0">
                    <p class="text-xs text-gray-400 mb-1">Tanggal Transaksi</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ formatDate(transaction.date) }}
                    </p>
                    <p class="text-xs text-gray-400 mt-3">Dibuat</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ formatDateTime(transaction.created_at) }}
                    </p>
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="mt-4 rounded-xl bg-gray-50 dark:bg-gray-800/50
                        border border-gray-100 dark:border-gray-700 px-4 py-3">
                <p class="text-xs text-gray-400 mb-1">Deskripsi</p>
                <p class="text-sm text-gray-900 dark:text-white">
                    {{ transaction.description }}
                </p>
            </div>
        </div>
    </div>
</template>
