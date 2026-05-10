<script setup>
import { computed, defineAsyncComponent } from 'vue'
import { Link, router, Deferred } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

import TransactionSummarySkeleton from '@/Components/Transaction/Skeletons/TransactionSummarySkeleton.vue'
import TransactionInfoSkeleton    from '@/Components/Transaction/Skeletons/TransactionInfoSkeleton.vue'
import TransactionItemsSkeleton   from '@/Components/Transaction/Skeletons/TransactionItemsSkeleton.vue'

// =========================================================
// COMPONENTS
// =========================================================
const TransactionSummary = defineAsyncComponent(() => import('@/Components/Transaction/TransactionSummary.vue'))
const TransactionDetails = defineAsyncComponent(() => import('@/Components/Transaction/TransactionDetails.vue'))
const TransactionItems   = defineAsyncComponent(() => import('@/Components/Transaction/TransactionItems.vue'))

// =========================================================
// PROPS
// =========================================================
const props = defineProps({
    transaction: { type: Object, required: true },
})

// =========================================================
// DELETE
// =========================================================
const deleteTransaction = () => {
    Swal.fire({
        title:              'Hapus Transaksi?',
        text:               'Journal entry terkait juga akan dihapus. Tindakan ini tidak bisa dibatalkan.',
        icon:               'warning',
        showCancelButton:   true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor:  '#6b7280',
        confirmButtonText:  'Ya, Hapus!',
        cancelButtonText:   'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('transactions.destroy', props.transaction.id))
        }
    })
}
</script>

<template>
    <div class="max-w-full mx-auto">

        <!-- HEADER -->
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a :href="route('transactions.index')"
                   class="flex h-9 w-9 items-center justify-center rounded-lg border
                          border-gray-200 dark:border-gray-700 text-gray-500
                          hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Detail Transaksi
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5" v-if="transaction">
                        {{ transaction.reference_no ? `Ref: ${transaction.reference_no}` : `ID: #${transaction.id}` }}
                    </p>
                    <div v-else class="h-4 w-32 bg-gray-200 dark:bg-gray-700 rounded animate-pulse mt-2"></div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center gap-2" v-if="transaction">
                <Link
                    :href="route('transactions.edit', transaction.id)"
                    class="inline-flex items-center gap-2 rounded-lg border
                           border-gray-200 dark:border-gray-700 px-4 py-2
                           text-sm font-medium text-gray-700 dark:text-gray-300
                           hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0
                                 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828
                                 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </Link>
                <button
                    @click="deleteTransaction"
                    class="inline-flex items-center gap-2 rounded-lg border
                           border-red-200 dark:border-red-800 px-4 py-2
                           text-sm font-medium text-red-600 dark:text-red-400
                           hover:bg-red-50 dark:hover:bg-red-900/20 transition"
                >
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

        <Deferred data="transaction">
            <template #fallback>
                <div class="space-y-5">
                    <TransactionSummarySkeleton />
                    <TransactionInfoSkeleton />
                    <TransactionItemsSkeleton />
                </div>
            </template>

            <div class="space-y-5" v-if="transaction">
                <TransactionSummary :transaction="transaction" />
                <TransactionDetails :transaction="transaction" />
                <TransactionItems   :transaction="transaction" />
            </div>
        </Deferred>
    </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>