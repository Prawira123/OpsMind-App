<script setup>
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    invoice: { type: Object, required: true },
    clients: { type: Array, default: () => [] },
    transactions: { type: Array, default: () => [] },
})

// =========================================================
// FORM INVOICE HEADER
// =========================================================
const form = useForm({
    client_id:  props.invoice.client_id,
    transaction_id: props.invoice.transaction_id || '',
    number:     props.invoice.number,
    status:     props.invoice.status,
    issue_date: props.invoice.issue_date,
    due_date:   props.invoice.due_date,
    notes:      props.invoice.notes || '',
    subtotal:   props.invoice.subtotal,
    tax:        props.invoice.tax,
    total:      props.invoice.total,
    items:      props.invoice.items.map(i => ({
        name:        i.name,
        description: i.description || '',
        quantity:    parseFloat(i.quantity),
        price:       parseFloat(i.price),
        total:       parseFloat(i.total),
    })),
})

// =========================================================
// ITEM FORM (stateless)
// =========================================================
const emptyItem = () => ({
    name:        '',
    description: '',
    quantity:    1,
    price:       '',
    total:       0,
})

const itemForm  = ref(emptyItem())
const itemError = ref('')

// Auto hitung total item
watch(
    () => [itemForm.value.quantity, itemForm.value.price],
    ([qty, price]) => {
        const q = parseFloat(qty)  || 0
        const p = parseFloat(String(price).replace(/\D/g, '')) || 0
        itemForm.value.total = q * p
    }
)

// =========================================================
// COMPUTED
// =========================================================
const subtotal = computed(() =>
    form.items.reduce((s, i) => s + parseFloat(i.total || 0), 0)
)

const taxAmount = computed(() => {
    return Math.round(subtotal.value * ((form.tax || 0) / 100))
})

const grandTotal = computed(() =>
    subtotal.value + taxAmount.value
)

// Sync ke form saat berubah
watch([subtotal, taxAmount, grandTotal], () => {
    form.subtotal = subtotal.value
    form.total    = grandTotal.value
})

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
const addItem = () => {
    itemError.value = ''

    if (!itemForm.value.name.trim()) {
        itemError.value = 'Nama item wajib diisi'
        return
    }
    if (!itemForm.value.quantity || parseFloat(itemForm.value.quantity) <= 0) {
        itemError.value = 'Qty wajib lebih dari 0'
        return
    }
    if (!itemForm.value.price || parseFloat(itemForm.value.price) <= 0) {
        itemError.value = 'Harga wajib diisi'
        return
    }

    form.items.push({
        name:        itemForm.value.name,
        description: itemForm.value.description,
        quantity:    parseFloat(itemForm.value.quantity),
        price:       parseFloat(String(itemForm.value.price).replace(/\D/g, '')),
        total:       itemForm.value.total,
    })

    itemForm.value = emptyItem()
}

const selectedTransaction = computed(() => {
    return props.transactions.find(t => t.id === form.transaction_id)
})

const removeItem = (index) => form.items.splice(index, 1)

const submit = () => {
    if (form.items.length === 0) {
        itemError.value = 'Tambahkan minimal 1 item invoice'
        return
    }
    form.put(route('invoices.update', props.invoice.id))
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
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Invoice</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 font-mono">
                    {{ invoice.number }}
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Klien <span class="text-red-500">*</span>
                        </label>
                        <select v-model="form.client_id" :class="ic(!!form.errors.client_id)">
                            <option value="">— Pilih Klien —</option>
                            <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                        <p v-if="form.errors.client_id" class="mt-1 text-xs text-red-500">{{ form.errors.client_id }}</p>
                    </div>

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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Jatuh Tempo <span class="text-red-500">*</span>
                        </label>
                        <input v-model="form.due_date" type="date" :class="ic(!!form.errors.due_date)"/>
                        <p v-if="form.errors.due_date" class="mt-1 text-xs text-red-500">{{ form.errors.due_date }}</p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Status
                        </label>
                        <select v-model="form.status" :class="ic()">
                            <option value="draft">Draft</option>
                            <option value="send">Kirim ke Klien</option>
                            <option value="paid">Lunas</option>
                            <option value="cancelled">Dibatalkan</option>
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

            <!-- CARD: TRANSACTION PREVIEW -->
            <div v-if="selectedTransaction" class="rounded-xl bg-white dark:bg-gray-900 border
                        border-gray-200 dark:border-gray-800 p-6 shadow-sm space-y-6">
                
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                            Referensi Transaksi Terkait
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">
                            Data item dari transaksi asal ({{ selectedTransaction.reference_no }})
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto -mx-6">
                    <table class="w-full text-left text-[13px] min-w-[500px]">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 uppercase text-[9px] tracking-widest">
                                <th class="px-6 py-2.5 font-semibold">Item</th>
                                <th class="px-6 py-2.5 font-semibold text-right">Qty</th>
                                <th class="px-6 py-2.5 font-semibold text-right">Harga</th>
                                <th class="px-6 py-2.5 font-semibold text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 font-mono">
                            <tr v-for="item in selectedTransaction.transaction_items" :key="item.id">
                                <td class="px-6 py-3">
                                    <span class="text-gray-900 dark:text-white">{{ item.name }}</span>
                                </td>
                                <td class="px-6 py-3 text-right text-gray-600 tracking-tighter">
                                    {{ item.qty }}
                                </td>
                                <td class="px-6 py-3 text-right text-gray-600 tracking-tighter">
                                    {{ formatCurrency(item.unit_price) }}
                                </td>
                                <td class="px-6 py-3 text-right font-bold text-gray-900 dark:text-white tracking-tighter">
                                    {{ formatCurrency(item.amount) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-2 border-t border-gray-50 dark:border-gray-800">
                    <div class="flex items-center gap-4 text-sm font-bold">
                        <span class="text-gray-400 uppercase text-[10px]">Total Transaksi</span>
                        <span class="text-indigo-600 dark:text-indigo-400">
                            {{ formatCurrency(selectedTransaction.amountTotal) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- CARD 2: ITEM INVOICE -->
            <div class="rounded-xl bg-white dark:bg-gray-900 border
                        border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800
                            flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Item Invoice
                    </h2>
                    <span v-if="form.items.length > 0"
                          class="inline-flex items-center rounded-full bg-indigo-100
                                 dark:bg-indigo-900/30 px-2.5 py-0.5 text-xs font-semibold
                                 text-indigo-700 dark:text-indigo-400">
                        {{ form.items.length }} item
                    </span>
                </div>

                <div class="p-6 space-y-4">

                    <!-- Form tambah item -->
                    <div class="rounded-xl border-2 border-dashed border-indigo-200
                                dark:border-indigo-800 bg-indigo-50/40 dark:bg-indigo-900/10 p-4 space-y-3">

                        <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400
                                  flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Item Baru
                        </p>

                        <!-- Nama + Keterangan -->
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                    Nama Item <span class="text-red-500">*</span>
                                </label>
                                <input v-model="itemForm.name" type="text"
                                       placeholder="Nama barang atau jasa..."
                                       @keydown.enter.prevent="addItem"
                                       :class="ic()"/>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                    Keterangan <span class="text-gray-400 font-normal">(opsional)</span>
                                </label>
                                <input v-model="itemForm.description" type="text"
                                       placeholder="Detail tambahan..." :class="ic()"/>
                            </div>
                        </div>

                        <!-- Qty + Harga + Total + Tombol -->
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">

                            <!-- Qty -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                    Qty <span class="text-red-500">*</span>
                                </label>
                                <input v-model="itemForm.quantity" type="number" min="1"
                                       placeholder="1" :class="ic()"/>
                            </div>

                            <!-- Harga -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                    Harga Satuan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2
                                                 text-xs text-gray-400 pointer-events-none">Rp</span>
                                    <input
                                        :value="displayInput(itemForm.price)"
                                        @input="(e) => { itemForm.price = e.target.value.replace(/\D/g, '') }"
                                        type="text" inputmode="numeric" placeholder="0"
                                        class="w-full rounded-lg border border-gray-200 dark:border-gray-700
                                               pl-9 pr-3.5 py-2.5 text-sm text-gray-900 dark:text-white
                                               placeholder-gray-400 bg-white dark:bg-gray-800
                                               focus:outline-none focus:ring-2 focus:ring-indigo-500
                                               focus:border-transparent transition"
                                    />
                                </div>
                            </div>

                            <!-- Total (disabled) -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                    Total <span class="text-indigo-400 font-normal ml-1">otomatis</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2
                                                 text-xs text-gray-400 pointer-events-none">Rp</span>
                                    <input :value="displayInput(itemForm.total)"
                                           type="text" disabled :class="[...dc, 'pl-9']"/>
                                </div>
                            </div>

                            <!-- Tombol -->
                            <div class="flex items-end">
                                <button type="button" @click="addItem"
                                    class="w-full inline-flex items-center justify-center gap-2
                                           rounded-lg bg-indigo-600 px-4 py-2.5 text-sm
                                           font-semibold text-white hover:bg-indigo-500
                                           active:scale-95 transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Tambah
                                </button>
                            </div>
                        </div>

                        <p v-if="itemError"
                           class="text-xs text-red-500 font-medium flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                            </svg>
                            {{ itemError }}
                        </p>
                    </div>

                    <!-- Daftar item -->
                    <div v-if="form.items.length > 0">

                        <div class="grid grid-cols-12 gap-2 px-3 py-2 mb-1.5
                                    rounded-lg bg-gray-50 dark:bg-gray-800/50">
                            <p class="col-span-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama</p>
                            <p class="col-span-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Keterangan</p>
                            <p class="col-span-1 text-xs font-semibold text-gray-400 uppercase tracking-wider text-center">Qty</p>
                            <p class="col-span-2 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Harga</p>
                            <p class="col-span-1 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Total</p>
                            <p class="col-span-1"/>
                        </div>

                        <TransitionGroup
                            enter-from-class="opacity-0 -translate-y-1"
                            enter-active-class="transition duration-200"
                            leave-to-class="opacity-0 scale-95"
                            leave-active-class="transition duration-150"
                            tag="div" class="space-y-1.5"
                        >
                            <div v-for="(item, index) in form.items" :key="index"
                                class="grid grid-cols-12 gap-2 items-center rounded-xl border
                                       border-gray-100 dark:border-gray-800 px-3 py-3
                                       hover:border-indigo-200 dark:hover:border-indigo-800/50
                                       transition group">

                                <div class="col-span-4 flex items-center gap-2 min-w-0">
                                    <span class="h-6 w-6 rounded-full bg-indigo-100 dark:bg-indigo-900/40
                                                 text-indigo-600 dark:text-indigo-400 text-xs font-bold
                                                 flex items-center justify-center shrink-0">
                                        {{ index + 1 }}
                                    </span>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ item.name }}
                                    </p>
                                </div>

                                <p class="col-span-3 text-xs text-gray-400 truncate">
                                    {{ item.description || '—' }}
                                </p>
+
                                <p class="col-span-1 text-sm text-gray-600 dark:text-gray-400 text-center font-medium">
                                    {{ item.quantity }}
                                </p>

                                <p class="col-span-2 text-xs text-gray-500 dark:text-gray-400 text-right">
                                    {{ formatCurrency(item.price) }}
                                </p>

                                <p class="col-span-1 text-sm font-semibold text-gray-900 dark:text-white text-right">
                                    {{ formatCurrency(item.total) }}
                                </p>

                                <div class="col-span-1 flex justify-end">
                                    <button type="button" @click="removeItem(index)"
                                        class="h-7 w-7 rounded-lg flex items-center justify-center
                                               text-gray-300 hover:text-red-600 hover:bg-red-50
                                               dark:hover:bg-red-900/20 transition
                                               opacity-0 group-hover:opacity-100">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </TransitionGroup>
                    </div>

                    <!-- Empty -->
                    <div v-else class="flex flex-col items-center gap-2 py-10 text-center">
                        <div class="h-12 w-12 rounded-xl bg-gray-100 dark:bg-gray-800
                                    flex items-center justify-center text-2xl">📋</div>
                        <p class="text-sm text-gray-400">Belum ada item</p>
                        <p class="text-xs text-gray-300 dark:text-gray-600">
                            Isi form di atas lalu klik "Tambah"
                        </p>
                    </div>
                </div>

                <!-- NOTA SUMMARY -->
                <div v-if="form.items.length > 0"
                     class="border-t border-gray-100 dark:border-gray-800 px-6 py-5
                            bg-gray-50/50 dark:bg-gray-800/30">

                    <div class="ml-auto w-full sm:w-80 space-y-2.5">

                        <!-- Subtotal -->
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ formatCurrency(subtotal) }}
                            </span>
                        </div>

                        <!-- Pajak -->
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 dark:text-gray-400">Pajak/PPN</span>
                                <div class="relative">
                                    <input
                                        v-model.number="form.tax"
                                        type="number" min="0" max="100"
                                        placeholder="0"
                                        class="w-16 rounded-lg border border-gray-200 dark:border-gray-700
                                               bg-white dark:bg-gray-800 px-2 py-1 text-xs text-right
                                               text-gray-700 dark:text-gray-300 focus:outline-none
                                               focus:ring-2 focus:ring-indigo-500 transition"
                                    />
                                    <span class="absolute right-2 top-1/2 -translate-y-1/2
                                                 text-xs text-gray-400 pointer-events-none">%</span>
                                </div>
                            </div>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ formatCurrency(taxAmount) }}
                            </span>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-2.5"/>

                        <!-- Grand Total -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">
                                Total
                            </span>
                            <span class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400">
                                {{ formatCurrency(grandTotal) }}
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
                    :disabled="form.processing || form.items.length === 0"
                    :class="[
                        'inline-flex items-center gap-2 rounded-lg px-6 py-2.5',
                        'text-sm font-semibold text-white transition',
                        form.items.length === 0 || form.processing
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
                            ? 'Memperbarui...'
                            : `Update Invoice${form.items.length > 0 ? ` (${form.items.length} item)` : ''}`
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
