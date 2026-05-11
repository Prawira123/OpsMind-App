<script setup>
import { computed, defineAsyncComponent } from 'vue'
import { Link, router, Deferred } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import InvoiceDetailsSkeleton from '@/Components/Invoice/Skeletons/InvoiceDetailsSkeleton.vue'
import InvoiceItemsSkeleton from '@/Components/Invoice/Skeletons/InvoiceItemsSkeleton.vue'

const props = defineProps({
    invoice: { type: Object, required: false },
})

// =========================================================
// COMPONENTS
// =========================================================
const InvoiceDetails = defineAsyncComponent(() => import('@/Components/Invoice/InvoiceDetails.vue'))
const InvoiceItems   = defineAsyncComponent(() => import('@/Components/Invoice/InvoiceItems.vue'))

const statusConfig = {
    draft:     { label: 'Draft',      color: 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',           border: 'border-gray-200 dark:border-gray-700' },
    sent:      { label: 'Terkirim',   color: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',        border: 'border-blue-200 dark:border-blue-800'  },
    paid:      { label: 'Lunas',      color: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400', border: 'border-emerald-200 dark:border-emerald-800' },
    overdue:   { label: 'Terlambat',  color: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',            border: 'border-red-200 dark:border-red-800'    },
    cancelled: { label: 'Dibatalkan', color: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',border: 'border-orange-200 dark:border-orange-800' },
}

const config = computed(() =>
    props.invoice ? (statusConfig[props.invoice.status] ?? statusConfig.draft) : statusConfig.draft
)

const isPaid    = computed(() => props.invoice?.status === 'paid')
const isDraft   = computed(() => props.invoice?.status === 'draft')
const isOverdue = computed(() => props.invoice?.status === 'overdue')
const isSent    = computed(() => props.invoice?.status === 'sent')

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
                <Link :href="route('invoices.index')"
                   class="flex h-9 w-9 items-center justify-center rounded-lg border
                          border-gray-200 dark:border-gray-700 text-gray-500
                          hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </Link>
                <div v-if="invoice">
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
                <div v-else class="animate-pulse">
                    <div class="h-8 w-48 bg-gray-200 dark:bg-gray-800 rounded mb-1"></div>
                    <div class="h-4 w-32 bg-gray-100 dark:bg-gray-800/50 rounded"></div>
                </div>
            </div>

            <!-- Action buttons -->
            <div v-if="invoice" class="flex items-center gap-2">
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

        <Deferred data="invoice">
            <template #fallback>
                <div class="space-y-5">
                    <InvoiceDetailsSkeleton />
                    <InvoiceItemsSkeleton />
                </div>
            </template>

            <div v-if="invoice" class="space-y-5">
                <InvoiceDetails :invoice="invoice" />
                <InvoiceItems :invoice="invoice" />
            </div>
        </Deferred>
    </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>