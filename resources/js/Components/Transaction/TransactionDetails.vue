<script setup>
const props = defineProps({
    transaction: { type: Object, required: true },
})
</script>

<template>
    <div class="rounded-xl bg-white dark:bg-gray-900 border
                border-gray-200 dark:border-gray-800 shadow-sm p-6">

        <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
            Informasi Transaksi
        </h2>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

            <!-- Kategori -->
            <div class="rounded-xl border border-gray-100 dark:border-gray-800
                        bg-gray-50/50 dark:bg-gray-800/30 px-4 py-3">
                <p class="text-xs text-gray-400 mb-1.5">Kategori</p>
                <div class="flex items-center gap-2">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ transaction.category?.name ?? '—' }}
                    </p>
                </div>
            </div>

            <!-- Klien -->
            <div class="rounded-xl border border-gray-100 dark:border-gray-800
                        bg-gray-50/50 dark:bg-gray-800/30 px-4 py-3">
                <p class="text-xs text-gray-400 mb-1.5">Klien</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ transaction.client?.name ?? '—' }}
                </p>
            </div>

            <!-- Rekening Debit -->
            <div class="rounded-xl border border-gray-100 dark:border-gray-800
                        bg-gray-50/50 dark:bg-gray-800/30 px-4 py-3">
                <p class="text-xs text-gray-400 mb-1.5">Rekening Debit (COA)</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ transaction.debit_account?.code }}
                    <span class="text-gray-500 dark:text-gray-400 font-normal">
                        — {{ transaction.debit_account?.name ?? '—' }}
                    </span>
                </p>
            </div>

            <!-- Rekening Kredit -->
            <div class="rounded-xl border border-gray-100 dark:border-gray-800
                        bg-gray-50/50 dark:bg-gray-800/30 px-4 py-3">
                <p class="text-xs text-gray-400 mb-1.5">Rekening Kredit (COA)</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ transaction.credit_account?.code }}
                    <span class="text-gray-500 dark:text-gray-400 font-normal">
                        — {{ transaction.credit_account?.name ?? '—' }}
                    </span>
                </p>
            </div>

            <!-- No Referensi -->
            <div v-if="transaction.reference_no"
                 class="rounded-xl border border-gray-100 dark:border-gray-800
                        bg-gray-50/50 dark:bg-gray-800/30 px-4 py-3">
                <p class="text-xs text-gray-400 mb-1.5">No. Referensi</p>
                <p class="text-sm font-mono font-medium text-gray-900 dark:text-white">
                    {{ transaction.reference_no }}
                </p>
            </div>

            <!-- Dibuat oleh -->
            <div class="rounded-xl border border-gray-100 dark:border-gray-800
                        bg-gray-50/50 dark:bg-gray-800/30 px-4 py-3">
                <p class="text-xs text-gray-400 mb-1.5">Dibuat Oleh</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ transaction.created_by?.name ?? '—' }}
                </p>
            </div>
        </div>
    </div>
</template>
