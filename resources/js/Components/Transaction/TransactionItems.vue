<script setup>
import { computed } from 'vue'

const props = defineProps({
    transaction: { type: Object, required: true },
})

const formatCurrency = (val) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0,
    }).format(val || 0)

const subtotal = computed(() =>
    (props.transaction.items ?? []).reduce((s, i) => s + parseFloat(i.amount_per_item || 0), 0)
)

const totalQty = computed(() =>
    (props.transaction.items ?? []).reduce((s, i) => s + parseInt(i.qty || 0), 0)
)

const isIncome = computed(() => props.transaction.type === 'income')
</script>

<template>
    <div class="space-y-5">
        <!-- =========================================
             CARD 3: DAFTAR ITEM
        ========================================= -->
        <div class="rounded-xl bg-white dark:bg-gray-900 border
                    border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800
                        flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                    Item Transaksi
                </h2>
                <span class="inline-flex items-center rounded-full bg-indigo-100
                             dark:bg-indigo-900/30 px-2.5 py-0.5 text-xs font-semibold
                             text-indigo-700 dark:text-indigo-400">
                    {{ transaction.items?.length ?? 0 }} item
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold
                                       text-gray-400 uppercase tracking-wider w-8">
                                #
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold
                                       text-gray-400 uppercase tracking-wider">
                                Nama
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold
                                       text-gray-400 uppercase tracking-wider">
                                Keterangan
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold
                                       text-gray-400 uppercase tracking-wider">
                                Qty
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold
                                       text-gray-400 uppercase tracking-wider">
                                Harga Satuan
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold
                                       text-gray-400 uppercase tracking-wider">
                                Total
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <tr
                            v-for="(item, index) in transaction.items"
                            :key="item.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition"
                        >
                            <td class="px-6 py-3.5">
                                <span class="h-6 w-6 rounded-full bg-indigo-100
                                             dark:bg-indigo-900/30 text-indigo-600
                                             dark:text-indigo-400 text-xs font-bold
                                             flex items-center justify-center">
                                    {{ index + 1 }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ item.name }}
                                </p>
                            </td>
                            <td class="px-4 py-3.5">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ item.description || '—' }}
                                </p>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center justify-center rounded-full
                                             bg-gray-100 dark:bg-gray-800 px-2.5 py-0.5
                                             text-xs font-semibold text-gray-700 dark:text-gray-300">
                                    {{ item.qty }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ formatCurrency(item.unit_price) }}
                                </p>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ formatCurrency(item.amount_per_item) }}
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Summary footer -->
            <div class="border-t border-gray-100 dark:border-gray-800 px-6 py-4
                        bg-gray-50/50 dark:bg-gray-800/30">
                <div class="ml-auto w-full sm:w-80 space-y-2">

                    <!-- Subtotal -->
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">
                            Subtotal
                            <span class="text-xs text-gray-400 ml-1">
                                ({{ transaction.items?.length }} item, {{ totalQty }} qty)
                            </span>
                        </span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ formatCurrency(subtotal) }}
                        </span>
                    </div>

                    <!-- Diskon jika ada -->
                    <div v-if="transaction.discount && parseFloat(transaction.discount) > 0"
                         class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Diskon</span>
                        <span class="font-medium text-rose-600 dark:text-rose-400">
                            - {{ formatCurrency(transaction.discount) }}
                        </span>
                    </div>

                    <!-- Pajak jika ada -->
                    <div v-if="transaction.tax_percent && parseInt(transaction.tax_percent) > 0"
                         class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">
                            PPN {{ transaction.tax_percent }}%
                        </span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ formatCurrency(
                                (subtotal - (parseFloat(transaction.discount) || 0))
                                * (transaction.tax_percent / 100)
                            ) }}
                        </span>
                    </div>

                    <!-- Biaya lain jika ada -->
                    <div v-if="transaction.other_fee && parseFloat(transaction.other_fee) > 0"
                         class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Biaya Lain-lain</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ formatCurrency(transaction.other_fee) }}
                        </span>
                    </div>

                    <!-- Garis pemisah -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-2"/>

                    <!-- Grand Total -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-gray-900 dark:text-white">
                            Grand Total
                        </span>
                        <span :class="[
                            'text-xl font-extrabold',
                            isIncome
                                ? 'text-emerald-600 dark:text-emerald-400'
                                : 'text-rose-600 dark:text-rose-400',
                        ]">
                            {{ formatCurrency(transaction.amountTotal) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================================
             CARD 4: JOURNAL ENTRY
        ========================================= -->
        <div v-if="transaction.journalEntry"
             class="rounded-xl bg-white dark:bg-gray-900 border
                     border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800
                        flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Journal Entry
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ transaction.journalEntry.entry_number }}
                    </p>
                </div>
                <span class="inline-flex items-center rounded-full bg-emerald-100
                             dark:bg-emerald-900/30 px-2.5 py-0.5 text-xs font-semibold
                             text-emerald-700 dark:text-emerald-400">
                    {{ transaction.journalEntry.status }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold
                                       text-gray-400 uppercase tracking-wider">
                                Akun (COA)
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold
                                       text-gray-400 uppercase tracking-wider">
                                Debit
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold
                                       text-gray-400 uppercase tracking-wider">
                                Kredit
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <tr
                            v-for="line in transaction.journalEntry.lines"
                            :key="line.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition"
                        >
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-2">
                                    <span :class="[
                                        'h-2 w-2 rounded-full shrink-0',
                                        line.type === 'debit'
                                            ? 'bg-indigo-500'
                                            : 'bg-amber-500',
                                    ]"/>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        <span class="font-mono text-gray-400 mr-1.5 text-xs">
                                            {{ line.chartOfAccount?.code }}
                                        </span>
                                        {{ line.chartOfAccount?.name }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <p v-if="line.type === 'debit'"
                                   class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                                    {{ formatCurrency(line.amount) }}
                                </p>
                                <p v-else class="text-sm text-gray-300 dark:text-gray-600">—</p>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <p v-if="line.type === 'credit'"
                                   class="text-sm font-semibold text-amber-600 dark:text-amber-400">
                                    {{ formatCurrency(line.amount) }}
                                </p>
                                <p v-else class="text-sm text-gray-300 dark:text-gray-600">—</p>
                            </td>
                        </tr>
                    </tbody>

                    <!-- Total baris -->
                    <tfoot>
                        <tr class="bg-gray-50 dark:bg-gray-800/50 border-t
                                   border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-3 text-xs font-semibold text-gray-500
                                       dark:text-gray-400 uppercase">
                                Total
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-bold
                                       text-indigo-600 dark:text-indigo-400">
                                {{ formatCurrency(transaction.amountTotal) }}
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-bold
                                       text-amber-600 dark:text-amber-400">
                                {{ formatCurrency(transaction.amountTotal) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</template>
