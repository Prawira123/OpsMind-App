<script setup>
const props = defineProps({
    invoice: { type: Object, required: true },
})

const formatCurrency = (val) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0,
    }).format(val || 0)

const formatDate = (val) => {
    if (!val) return '—'
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit', month: 'long', year: 'numeric',
    }).format(new Date(val))
}

const isOverdue = props.invoice.status === 'overdue'
const isPaid    = props.invoice.status === 'paid'
</script>

<template>
    <div class="rounded-2xl bg-white dark:bg-gray-900 border
                border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

        <div :class="[
            'h-1.5',
            isPaid ? 'bg-emerald-500' : isOverdue ? 'bg-red-500' : 'bg-indigo-500',
        ]"/>

        <div class="p-6">
            <div class="flex items-start justify-between gap-6 flex-wrap">

                <!-- Klien -->
                <div>
                    <p class="text-xs text-gray-400 mb-2">Ditagih kepada</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ invoice.client?.name ?? '—' }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ invoice.client?.email }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ invoice.client?.phone }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-xs mt-1">
                        {{ invoice.client?.address }}
                    </p>
                </div>

                <!-- Info tanggal -->
                <div class="text-right space-y-3">
                    <div>
                        <p class="text-xs text-gray-400">Tanggal Invoice</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ formatDate(invoice.issue_date) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Jatuh Tempo</p>
                        <p :class="[
                            'text-sm font-semibold',
                            isOverdue
                                ? 'text-red-600 dark:text-red-400'
                                : 'text-gray-900 dark:text-white',
                        ]">
                            {{ formatDate(invoice.due_date) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Total</p>
                        <p class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400">
                            {{ formatCurrency(invoice.total) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div v-if="invoice.notes"
                 class="mt-4 rounded-xl bg-gray-50 dark:bg-gray-800/50
                        border border-gray-100 dark:border-gray-700 px-4 py-3">
                <p class="text-xs text-gray-400 mb-1">Catatan</p>
                <p class="text-sm text-gray-700 dark:text-gray-300">{{ invoice.notes }}</p>
            </div>
        </div>
    </div>
</template>
