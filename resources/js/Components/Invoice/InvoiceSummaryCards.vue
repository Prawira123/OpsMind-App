<script setup>
import { computed } from 'vue'

const props = defineProps({
    invoices: { type: Array, required: true },
})

const formatCurrency = (val) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0,
    }).format(val || 0)

const summary = computed(() => {
    const all = props.invoices ?? []
    return {
        total:   all.length,
        draft:   all.filter(i => i.status === 'draft').length,
        send:    all.filter(i => i.status === 'send' || i.status === 'sent').length,
        paid:    all.filter(i => i.status === 'paid').length,
        overdue: all.filter(i => i.status === 'overdue').length,
        totalValue: all.reduce((s, i) => s + parseFloat(i.total || 0), 0),
        paidValue:  all.filter(i => i.status === 'paid').reduce((s, i) => s + parseFloat(i.total || 0), 0),
    }
})
</script>

<template>
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-5">

        <div class="rounded-xl bg-white dark:bg-gray-900 border
                    border-gray-200 dark:border-gray-800 p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">
                Total Invoice
            </p>
            <div class="flex items-baseline gap-2">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ summary.total }}
                </p>
            </div>
            <p class="text-xs text-gray-400 mt-1">{{ formatCurrency(summary.totalValue) }}</p>
        </div>

        <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border
                    border-emerald-100 dark:border-emerald-800 p-5 shadow-sm">
            <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-1">
                Lunas
            </p>
            <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-400">
                {{ summary.paid }}
            </p>
            <p class="text-xs text-emerald-500 mt-1">{{ formatCurrency(summary.paidValue) }}</p>
        </div>

        <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 border
                    border-blue-100 dark:border-blue-800 p-5 shadow-sm">
            <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">
                Terkirim
            </p>
            <p class="text-2xl font-bold text-blue-700 dark:text-blue-400">
                {{ summary.send }}
            </p>
            <p class="text-xs text-blue-400 mt-1">Menunggu pembayaran</p>
        </div>

        <div class="rounded-xl bg-yellow-50 dark:bg-yellow-900/20 border
                    border-yellow-100 dark:border-yellow-800 p-5 shadow-sm">
            <p class="text-xs font-semibold text-yellow-600 dark:text-yellow-400 uppercase tracking-wider mb-1">
                Draft
            </p>
            <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-400">
                {{ summary.draft }}
            </p>
            <p class="text-xs text-yellow-400 mt-1">Belum dikirim</p>
        </div>

        <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border
                    border-red-100 dark:border-red-800 p-5 shadow-sm">
            <p class="text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider mb-1">
                Terlambat
            </p>
            <p class="text-2xl font-bold text-red-700 dark:text-red-400">
                {{ summary.overdue }}
            </p>
            <p class="text-xs text-red-400 mt-1">Perlu tindakan segera</p>
        </div>
    </div>
</template>
