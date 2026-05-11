<script setup>
const props = defineProps({
    invoice: { type: Object, required: true },
})

const formatCurrency = (val) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0,
    }).format(val || 0)
</script>

<template>
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
</template>
