<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const props = defineProps({
    invoice: { type: Object, required: true },
})

const statusConfig = {
    draft:     { label: 'Draft',      color: 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',           border: 'border-gray-200 dark:border-gray-700' },
    sent:      { label: 'Terkirim',   color: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',        border: 'border-blue-200 dark:border-blue-800'  },
    paid:      { label: 'Lunas',      color: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400', border: 'border-emerald-200 dark:border-emerald-800' },
    overdue:   { label: 'Terlambat',  color: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',            border: 'border-red-200 dark:border-red-800'    },
    cancelled: { label: 'Dibatalkan', color: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',border: 'border-orange-200 dark:border-orange-800' },
}

const config = computed(() =>
    statusConfig[props.invoice.status] ?? statusConfig.draft
)

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

const isPaid    = computed(() => props.invoice.status === 'paid')
const isDraft   = computed(() => props.invoice.status === 'draft')
const isOverdue = computed(() => props.invoice.status === 'overdue')
const isSent    = computed(() => props.invoice.status === 'sent')

const markAsPaid = () => {
    Swal.fire({
        title: 'Tandai Lunas?', icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Lunas!', cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) router.patch(route('invoices.mark-paid', props.invoice.id))
    })
}

const deleteInvoice = () => {
    Swal.fire({
        title: 'Hapus Invoice?', icon: 'warning',
        text: 'Data tidak bisa dikembalikan!',
        showCancelButton: true,
        confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) router.delete(route('invoices.destroy', props.invoice.id))
    })
}
</script>

<template>
    <div class="max-w-full mx-auto">

        <!-- HEADER -->
        <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-4">
                <a :href="route('invoices.index')"
                   class="flex h-9 w-9 items-center justify-center rounded-lg border
                          border-gray-200 dark:border-gray-700 text-gray-500
                          hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-mono">
                            {{ invoice.number }}
                        </h1>
                        <span :class="[
                            'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold',
                            config.color,
                        ]">
                            {{ config.label }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        Detail Invoice
                    </p>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center gap-2">
                <a :href="route('invoices.download', invoice.id)"
                   class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2
                        text-sm font-semibold text-white hover:bg-indigo-500 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M7.5 12l4.5 4.5m0 0l4.5-4.5M12 3v13.5"/>
                    </svg>
                    Download PDF
                </a>

                <button v-if="isSent || isOverdue"
                    @click="markAsPaid"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2
                           text-sm font-semibold text-white hover:bg-emerald-500 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Tandai Lunas
                </button>

                <Link v-if="isDraft"
                    :href="route('invoices.edit', invoice.id)"
                    class="inline-flex items-center gap-2 rounded-lg border
                           border-gray-200 dark:border-gray-700 px-4 py-2
                           text-sm font-medium text-gray-700 dark:text-gray-300
                           hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0
                                 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828
                                 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </Link>

                <button @click="deleteInvoice"
                    class="inline-flex items-center gap-2 rounded-lg border
                           border-red-200 dark:border-red-800 px-4 py-2
                           text-sm font-medium text-red-600 dark:text-red-400
                           hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2
                                 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0
                                 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus
                </button>
            </div>
        </div>

        <div class="space-y-5">

            <!-- CARD 1: HEADER INVOICE -->
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

            <!-- CARD 2: ITEM TABLE -->
            <div class="rounded-xl bg-white dark:bg-gray-900 border
                        border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800
                            flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Detail Item
                    </h2>
                    <span class="inline-flex items-center rounded-full bg-indigo-100
                                 dark:bg-indigo-900/30 px-2.5 py-0.5 text-xs font-semibold
                                 text-indigo-700 dark:text-indigo-400">
                        {{ invoice.items?.length ?? 0 }} item
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-8">#</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Keterangan</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Harga</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <tr v-for="(item, index) in invoice.items" :key="item.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                                <td class="px-6 py-3.5">
                                    <span class="h-6 w-6 rounded-full bg-indigo-100 dark:bg-indigo-900/30
                                                 text-indigo-600 dark:text-indigo-400 text-xs font-bold
                                                 flex items-center justify-center">
                                        {{ index + 1 }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ item.name }}</p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ item.description || '—' }}</p>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center justify-center rounded-full
                                                 bg-gray-100 dark:bg-gray-800 px-2.5 py-0.5
                                                 text-xs font-semibold text-gray-700 dark:text-gray-300">
                                        {{ item.quantity }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ formatCurrency(item.price) }}
                                    </p>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ formatCurrency(item.total) }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Summary -->
                <div class="border-t border-gray-100 dark:border-gray-800 px-6 py-5
                            bg-gray-50/50 dark:bg-gray-800/30">
                    <div class="ml-auto w-full sm:w-72 space-y-2.5">

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ formatCurrency(invoice.subtotal) }}
                            </span>
                        </div>

                        <div v-if="invoice.tax && parseFloat(invoice.tax) > 0"
                             class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Pajak {{ invoice.tax }}%</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ formatCurrency(invoice.subtotal * (invoice.tax / 100)) }}
                            </span>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-2.5"/>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">Total</span>
                            <span class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400">
                                {{ formatCurrency(invoice.total) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>