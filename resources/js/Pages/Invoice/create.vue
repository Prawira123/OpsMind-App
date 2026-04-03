<script setup>
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    clients: { type: Array, default: () => [] },
    transactions: { type: Array, default: () => [] },
})

// =========================================================
// FORM INVOICE HEADER
// =========================================================
const form = useForm({
    transaction_id: '',
    number:     '',
    status:     'draft',
    issue_date: new Date().toISOString().split('T')[0],
    notes:      '',
})

// =========================================================
// ITEM FORM (stateless)
// =========================================================
// const emptyItem = () => ({
//     name:        '',
//     description: '',
//     quantity:    1,
//     price:       '',
//     total:       0,
// })

// const itemForm  = ref(emptyItem())
// const itemError = ref('')

// // Auto hitung total item
// watch(
//     () => [itemForm.value.quantity, itemForm.value.price],
//     ([qty, price]) => {
//         const q = parseFloat(qty)  || 0
//         const p = parseFloat(String(price).replace(/\D/g, '')) || 0
//         itemForm.value.total = q * p
//     }
// )

// =========================================================
// COMPUTED
// =========================================================
// const subtotal = computed(() =>
//     form.items.reduce((s, i) => s + parseFloat(i.total || 0), 0)
// )

// const taxAmount = computed(() => {
//     return Math.round(subtotal.value * ((form.tax || 0) / 100))
// })

// const grandTotal = computed(() =>
//     subtotal.value + taxAmount.value
// )

// Sync ke form saat berubah
// watch([subtotal, taxAmount, grandTotal], () => {
//     form.subtotal = subtotal.value
//     form.total    = grandTotal.value
// })

// =========================================================
// FORMAT
// =========================================================
const formatCurrency = (val) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0,
    }).format(val || 0)

const displayInput = (val) =>
    val ? new Intl.NumberFormat('id-ID').format(val) : ''

// =========================================================
// ITEM ACTIONS
// =========================================================
// const addItem = () => {
//     itemError.value = ''

//     if (!itemForm.value.name.trim()) {
//         itemError.value = 'Nama item wajib diisi'
//         return
//     }
//     if (!itemForm.value.quantity || parseFloat(itemForm.value.quantity) <= 0) {
//         itemError.value = 'Qty wajib lebih dari 0'
//         return
//     }
//     if (!itemForm.value.price || parseFloat(itemForm.value.price) <= 0) {
//         itemError.value = 'Harga wajib diisi'
//         return
//     }

//     form.items.push({
//         name:        itemForm.value.name,
//         description: itemForm.value.description,
//         quantity:    parseFloat(itemForm.value.quantity),
//         price:       parseFloat(String(itemForm.value.price).replace(/\D/g, '')),
//         total:       itemForm.value.total,
//     })

//     itemForm.value = emptyItem()
// }

// const removeItem = (index) => form.items.splice(index, 1)

const selectedTransaction = computed(() => {
    return props.transactions.find(t => t.id === form.transaction_id)
})

const submit = () => {
    if (!form.transaction_id) {
        return alert('Silakan pilih transaksi terlebih dahulu')
    }
    form.post(route('invoices.store'))
}

const ic = (err = false) => [
    'w-full rounded-lg border px-3.5 py-2.5 text-sm',
    'text-gray-900 dark:text-white placeholder-gray-400',
    'bg-white dark:bg-gray-800 transition',
    'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
    err ? 'border-red-400' : 'border-gray-200 dark:border-gray-700',
]

const dc = [
    'w-full rounded-lg border px-3.5 py-2.5 text-sm font-semibold',
    'border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50',
    'text-gray-700 dark:text-gray-300 cursor-not-allowed',
]
</script>

<template>
    <div class="max-w-full mx-auto">

        <!-- HEADER -->
        <div class="mb-6 flex items-center gap-4">
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
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Buat Invoice</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Isi detail invoice dan tambahkan item tagihan
                </p>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-5">

            <!-- CARD 1: INFO INVOICE -->
            <div class="rounded-xl bg-white dark:bg-gray-900 border
                        border-gray-200 dark:border-gray-800 p-6 shadow-sm space-y-4">

                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                    Informasi Invoice
                </h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                    <!-- No Invoice -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            No. Invoice <span class="text-red-500">*</span>
                        </label>
                        <input v-model="form.number" type="text"
                               placeholder="INV-2024-001" :class="ic(!!form.errors.number)"/>
                        <p v-if="form.errors.number" class="mt-1 text-xs text-red-500">{{ form.errors.number }}</p>
                    </div>

                    <!-- Klien -->
                    <!-- <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Klien <span class="text-red-500">*</span>
                        </label>
                        <select v-model="form.client_id" :class="ic(!!form.errors.client_id)">
                            <option value="">— Pilih Klien —</option>
                            <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                        <p v-if="form.errors.client_id" class="mt-1 text-xs text-red-500">{{ form.errors.client_id }}</p>
                    </div> -->
                    
                    <!-- Transaksi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Link ke Transaksi <span class="text-xs text-gray-400 ml-1">(opsional)</span>
                        </label>
                        <select v-model="form.transaction_id" :class="ic(!!form.errors.transaction_id)">
                            <option value="">— Pilih Transaksi —</option>
                            <option v-for="t in transactions" :key="t.id" :value="t.id">
                                {{ t.reference_no }} - {{ t.description }}
                            </option>
                        </select>
                        <p v-if="form.errors.transaction_id" class="mt-1 text-xs text-red-500">{{ form.errors.transaction_id }}</p>
                    </div>

                    <!-- Tanggal Invoice -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Tanggal Invoice <span class="text-red-500">*</span>
                        </label>
                        <input v-model="form.issue_date" type="date" :class="ic(!!form.errors.issue_date)"/>
                        <p v-if="form.errors.issue_date" class="mt-1 text-xs text-red-500">{{ form.errors.issue_date }}</p>
                    </div>

                    <!-- Jatuh Tempo -->
                    <!-- <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Jatuh Tempo <span class="text-red-500">*</span>
                        </label>
                        <input v-model="form.due_date" type="date" :class="ic(!!form.errors.due_date)"/>
                        <p v-if="form.errors.due_date" class="mt-1 text-xs text-red-500">{{ form.errors.due_date }}</p>
                    </div> -->

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Status
                        </label>
                        <select v-model="form.status" :class="ic()">
                            <option value="draft">Draft</option>
                            <option value="send">Kirim ke Klien</option>
                        </select>
                    </div>
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Catatan <span class="text-xs text-gray-400 ml-1">(opsional)</span>
                    </label>
                    <textarea v-model="form.notes" rows="2"
                        placeholder="Catatan untuk klien..."
                        :class="[...ic(), 'resize-none']"/>
                </div>
            </div>

            <!-- CARD 2: TRANSACTION PREVIEW -->
            <div v-if="selectedTransaction" class="rounded-xl bg-white dark:bg-gray-900 border
                        border-gray-200 dark:border-gray-800 p-6 shadow-sm space-y-6">
                
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                            Ringkasan Transaksi
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Detail item yang akan dimasukkan ke dalam invoice
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400">
                            {{ selectedTransaction.reference_no }}
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto -mx-6">
                    <table class="w-full text-left text-sm min-w-[600px]">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 uppercase text-[10px] tracking-wider">
                                <th class="px-6 py-3 font-semibold">Deskripsi Item</th>
                                <th class="px-6 py-3 font-semibold text-right">Qty</th>
                                <th class="px-6 py-3 font-semibold text-right">Harga Satuan</th>
                                <th class="px-6 py-3 font-semibold text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <tr v-for="item in selectedTransaction.transaction_items" :key="item.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ item.name }}</div>
                                    <div v-if="item.description" class="text-xs text-gray-500 mt-0.5 leading-relaxed">
                                        {{ item.description }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">
                                    {{ item.qty }}
                                </td>
                                <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">
                                    {{ formatCurrency(item.unit_price) }}
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white">
                                    {{ formatCurrency(item.amount) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-2">
                    <div class="w-full max-w-[280px] space-y-3">
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                            <span>Pajak ({{ selectedTransaction.tax_percent }}%)</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ formatCurrency(selectedTransaction.amountTotal * (selectedTransaction.tax_percent / 100)) }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                            <span>Diskon</span>
                            <span class="font-medium text-red-600 dark:text-red-400">
                                - {{ formatCurrency(selectedTransaction.discount) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-800">
                            <span class="text-base font-bold text-gray-900 dark:text-white">Total Tagihan</span>
                            <span class="text-xl font-black text-indigo-600 dark:text-indigo-400">
                                {{ formatCurrency(selectedTransaction.amountTotal) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTIONS -->
            <div class="flex items-center justify-between pt-2 pb-8">
                <a :href="route('invoices.index')"
                   class="rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-2.5
                          text-sm font-medium text-gray-700 dark:text-gray-300
                          hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    Batal
                </a>

                <button type="submit"
                    :disabled="form.processing"
                    :class="[
                        'inline-flex items-center gap-2 rounded-lg px-6 py-2.5',
                        'text-sm font-semibold text-white transition',
                        form.processing
                            ? 'bg-gray-300 dark:bg-gray-700 cursor-not-allowed opacity-60'
                            : 'bg-indigo-600 hover:bg-indigo-500 active:scale-95',
                    ]">
                    <svg v-if="form.processing" class="h-4 w-4 animate-spin"
                         fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{
                        form.processing
                            ? 'Menyimpan...'
                            : `Simpan Invoice`
                    }}
                </button>
            </div>

        </form>
    </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>