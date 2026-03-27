<script setup>
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    accounts:   { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    clients:    { type: Array, default: () => [] },
    transaction_items: { type: Array, default: () => [] },
    transaction : { type: Object, default: () => {} },
    rekenings:  { type: Array, default: () => [] },
})

// =========================================================
// FORM TRANSAKSI HEADER
// sesuai storeTransactionHeader()
// =========================================================
const form = useForm({
    type:              props.transaction.type ?? 'expense',
    category_id:       props.transaction.category?.id ?? '',
    debit_account_id:  props.transaction.debit_account_id ?? '',
    credit_account_id: props.transaction.credit_account_id ?? '',
    client_id:         props.transaction.client_id ?? '',
    description:       props.transaction.description ?? '',
    date:              toInputDate(props.transaction?.date) ?? new Date().toISOString().split('T')[0],
    reference_no:      props.transaction.reference_no ?? '',
    discount:          props.transaction.discount ?? 0,
    tax_percent:       props.transaction.tax_percent ?? 0,
    other_fee:         props.transaction.other_fee ?? 0,
    rekening_id:       props.transaction.rekening_id ?? '',
    items:             props.transaction_items ?? [],  // ← dikirim sekaligus saat submit
})

console.log(form)

function toInputDate(dateStr) {
  if (!dateStr) return new Date().toISOString().split('T')[0]
  
  // Jika sudah format YYYY-MM-DD
  if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) return dateStr
  
  // Jika ISO string (dari database): "2026-03-24T00:00:00.000Z"
  return new Date(dateStr).toISOString().split('T')[0]
}


// =========================================================
// FORM ITEM SEMENTARA — STATELESS di Vue
// Tidak dikirim ke backend sampai tombol submit ditekan
// Sesuai storeTransactionItem(): name, description,
// unit_price, qty, amount
// =========================================================
const emptyItem = () => ({
    name:           '',   // ← sesuai service
    description:    '',   // ← sesuai service
    qty:            1,
    unit_price:     '',
    amount:         0,   // ← sesuai service
})

const itemForm  = ref(emptyItem())
const itemError = ref('')

// Auto hitung amount saat qty / unit_price berubah
watch(
    () => [itemForm.value.qty, itemForm.value.unit_price],
    ([qty, price]) => {
        const q = parseInt(qty)   || 0
        const p = parseFloat(String(price).replace(/\D/g, '')) || 0
        itemForm.value.amount = q * p
    }
)

// =========================================================
// COMPUTED
// =========================================================
const filteredCategories = computed(() =>
    props.categories.filter(c => c.type === form.type)
)

// Subtotal dari semua item
const subtotal = computed(() =>
    form.items.reduce((sum, i) => sum + (parseFloat(i.amount) || 0), 0)
)

const totalQty = computed(() =>
    form.items.reduce((sum, i) => sum + (parseInt(i.qty) || 0), 0)
)

// Pajak dihitung dari subtotal - diskon
const taxAmount = computed(() => {
    const base = subtotal.value - (parseFloat(form.discount) || 0)
    return Math.round(base * ((form.tax_percent || 0) / 100))
})

// Grand total akhir
const finalTotal = computed(() =>
    subtotal.value
    - (parseFloat(form.discount)  || 0)
    + taxAmount.value
    + (parseFloat(form.other_fee) || 0)
)

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
// ITEM ACTIONS — hanya manipulasi state Vue, belum ke server
// =========================================================
const addItem = () => {
    itemError.value = ''

    if (!itemForm.value.name.trim()) {
        itemError.value = 'Nama barang wajib diisi'
        return
    }
    if (!itemForm.value.qty || parseInt(itemForm.value.qty) <= 0) {
        itemError.value = 'Qty wajib lebih dari 0'
        return
    }
    if (!itemForm.value.unit_price || parseFloat(itemForm.value.unit_price) <= 0) {
        itemError.value = 'Harga satuan wajib diisi'
        return
    }

    // Push ke form.items (stateless di Vue)
    form.items.push({
        name:           itemForm.value.name,
        description:    itemForm.value.description,
        qty:            parseInt(itemForm.value.qty),
        unit_price:     parseFloat(String(itemForm.value.unit_price).replace(/\D/g, '')),
        amount:         itemForm.value.amount,
    })

    // Reset form item → kosong lagi untuk item berikutnya
    itemForm.value = emptyItem()
}

const removeItem = (index) => form.items.splice(index, 1)

// =========================================================
// SUBMIT — semua item dikirim sekaligus ke backend
// =========================================================
const submit = () => {
    if (form.items.length === 0) {
        itemError.value = 'Tambahkan minimal 1 item transaksi'
        return
    }
    form.put(route('transactions.update', props.transaction.id))
}

// =========================================================
// INPUT CLASS HELPER
// =========================================================
const ic = (err = false) => [
    'w-full rounded-lg border px-3.5 py-2.5 text-sm',
    'text-gray-900 dark:text-white placeholder-gray-400',
    'bg-white dark:bg-gray-800 transition',
    'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
    err ? 'border-red-400' : 'border-gray-200 dark:border-gray-700',
]

const dc = [
    'w-full rounded-lg border px-3.5 py-2.5 text-sm font-semibold',
    'border-gray-100 dark:border-gray-700',
    'bg-gray-50 dark:bg-gray-800/50',
    'text-gray-700 dark:text-gray-300 cursor-not-allowed',
]
</script>

<template>
    <div class="max-w-full mx-auto">

        <!-- HEADER -->
        <div class="mb-6 flex items-center gap-4">
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
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Update Transaksi</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Update detail transaksi dan update item
                </p>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-5">

            <!-- =========================================
                 CARD 1: INFO TRANSAKSI
            ========================================= -->
            <div class="rounded-xl bg-white dark:bg-gray-900 border
                        border-gray-200 dark:border-gray-800 p-6 shadow-sm space-y-4">

                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                    Informasi Transaksi
                </h2>

                <!-- Tipe -->
                <div class="grid grid-cols-2 gap-3">
                    <button type="button"
                        @click="form.type = 'expense'; form.category_id = ''"
                        :class="[
                            'rounded-xl border-2 px-4 py-3 text-sm font-semibold transition-all',
                            form.type === 'expense'
                                ? 'border-rose-500 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400'
                                : 'border-gray-200 dark:border-gray-700 text-gray-500 hover:border-gray-300',
                        ]">
                        <div class="flex items-center justify-center gap-2">
                         Pengeluaran
                        </div>
                    </button>
                    <button type="button"
                        @click="form.type = 'income'; form.category_id = ''"
                        :class="[
                            'rounded-xl border-2 px-4 py-3 text-sm font-semibold transition-all',
                            form.type === 'income'
                                ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400'
                                : 'border-gray-200 dark:border-gray-700 text-gray-500 hover:border-gray-300',
                        ]">
                        <div class="flex items-center justify-center gap-2">
                            Pemasukan
                        </div>
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                    <!-- Tanggal -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input v-model="form.date" type="date" :class="ic(!!form.errors.date)"/>
                        <p v-if="form.errors.date" class="mt-1 text-xs text-red-500">{{ form.errors.date }}</p>
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select v-model="form.category_id" :class="ic(!!form.errors.category_id)">
                            <option value="">— Pilih Kategori —</option>
                            <option v-for="cat in filteredCategories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.category_id" class="mt-1 text-xs text-red-500">{{ form.errors.category_id }}</p>
                    </div>

                    <!-- Rekening Debit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Akun Debit <span class="text-red-500">*</span>
                        </label>
                        <select v-model="form.debit_account_id" :class="ic(!!form.errors.debit_account_id)">
                            <option value="">— Pilih COA —</option>
                            <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                                {{ acc.code }} — {{ acc.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.debit_account_id" class="mt-1 text-xs text-red-500">{{ form.errors.debit_account_id }}</p>
                    </div>

                    <!-- Akun Kredit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Akun Kredit <span class="text-red-500">*</span>
                        </label>
                        <select v-model="form.credit_account_id" :class="ic(!!form.errors.credit_account_id)">
                            <option value="">— Pilih COA —</option>
                            <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                                {{ acc.code }} — {{ acc.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.credit_account_id" class="mt-1 text-xs text-red-500">{{ form.errors.credit_account_id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Rekening <span class="text-red-500">*</span>
                        </label>
                        <select v-model="form.rekening_id" :class="ic(!!form.errors.rekening_id)">
                            <option value="">— Pilih Rekening —</option>
                            <option v-for="acc in rekenings" :key="acc.id" :value="acc.id">
                                {{ acc.code }} — {{ acc.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.rekening_id" class="mt-1 text-xs text-red-500">{{ form.errors.rekening_id }}</p>
                    </div>

                    <!-- Klien -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Klien <span class="text-xs text-gray-400 ml-1">(opsional)</span>
                        </label>
                        <select v-model="form.client_id" :class="ic()">
                            <option value="">— Pilih Klien —</option>
                            <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>

                    <!-- Referensi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            No. Referensi <span class="text-xs text-gray-400 ml-1">(opsional)</span>
                        </label>
                        <input v-model="form.reference_no" type="text"
                               placeholder="INV-001, PO-002..." :class="ic()"/>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Deskripsi <span class="text-red-500">*</span>
                    </label>
                    <textarea v-model="form.description" rows="2"
                        placeholder="Keterangan transaksi..."
                        :class="[...ic(!!form.errors.description), 'resize-none']"/>
                    <p v-if="form.errors.description" class="mt-1 text-xs text-red-500">{{ form.errors.description }}</p>
                </div>
            </div>

            <!-- =========================================
                 CARD 2: ITEM TRANSAKSI
            ========================================= -->
            <div class="rounded-xl bg-white dark:bg-gray-900 border
                        border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800
                            flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Item Transaksi
                    </h2>
                    <span v-if="form.items.length > 0"
                          class="inline-flex items-center rounded-full bg-indigo-100
                                 dark:bg-indigo-900/30 px-2.5 py-0.5 text-xs font-semibold
                                 text-indigo-700 dark:text-indigo-400">
                        {{ form.items.length }} item
                    </span>
                </div>

                <div class="p-6 space-y-4">

                    <!-- Form tambah item (stateless) -->
                    <div class="rounded-xl border-2 border-dashed border-indigo-200
                                dark:border-indigo-800 bg-indigo-50/40
                                dark:bg-indigo-900/10 p-4 space-y-3">

                        <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400
                                  flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Item Baru
                            <span class="font-normal text-indigo-400">
                                — belum tersimpan sampai transaksi dikirim
                            </span>
                        </p>

                        <!-- Baris 1: Nama + Deskripsi -->
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-600
                                              dark:text-gray-400 mb-1">
                                    Nama Barang / Jasa <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="itemForm.name"
                                    type="text"
                                    placeholder="Nama barang atau jasa..."
                                    :class="ic()"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600
                                              dark:text-gray-400 mb-1">
                                    Keterangan Item
                                    <span class="text-gray-400 font-normal">(opsional)</span>
                                </label>
                                <input
                                    v-model="itemForm.description"
                                    type="text"
                                    placeholder="Detail tambahan..."
                                    :class="ic()"
                                />
                            </div>
                        </div>

                        <!-- Baris 2: Qty, Harga Satuan, Total (disabled), Tombol -->
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">

                            <!-- Qty -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600
                                              dark:text-gray-400 mb-1">
                                    Qty <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="itemForm.qty"
                                    type="number" min="1"
                                    placeholder="1"
                                    :class="ic()"
                                />
                            </div>

                            <!-- Harga Satuan -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600
                                              dark:text-gray-400 mb-1">
                                    Harga Satuan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2
                                                 text-xs text-gray-400 pointer-events-none">Rp</span>
                                    <input
                                        :value="displayInput(itemForm.unit_price)"
                                        @input="(e) => { itemForm.unit_price = e.target.value.replace(/\D/g, '') }"
                                        type="text" inputmode="numeric" placeholder="0"
                                        class="w-full rounded-lg border border-gray-200 dark:border-gray-700
                                               pl-9 pr-3.5 py-2.5 text-sm text-gray-900 dark:text-white
                                               placeholder-gray-400 bg-white dark:bg-gray-800
                                               focus:outline-none focus:ring-2 focus:ring-indigo-500
                                               focus:border-transparent transition"
                                    />
                                </div>
                            </div>

                            <!-- Amount per item (DISABLED — otomatis) -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600
                                              dark:text-gray-400 mb-1">
                                    Total
                                    <span class="text-indigo-400 font-normal ml-1">otomatis</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2
                                                 text-xs text-gray-400 pointer-events-none">Rp</span>
                                    <input
                                        :value="displayInput(itemForm.amount)"
                                        type="text" disabled
                                        :class="[...dc, 'pl-9']"
                                    />
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

                    <!-- Daftar item sementara -->
                    <div v-if="form.items.length > 0">

                        <!-- Header kolom -->
                        <div class="grid grid-cols-12 gap-2 px-3 py-2 mb-1.5
                                    rounded-lg bg-gray-50 dark:bg-gray-800/50">
                            <p class="col-span-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                Nama
                            </p>
                            <p class="col-span-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                Keterangan
                            </p>
                            <p class="col-span-1 text-xs font-semibold text-gray-400 uppercase tracking-wider text-center">
                                Qty
                            </p>
                            <p class="col-span-2 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">
                                Harga Satuan
                            </p>
                            <p class="col-span-1 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">
                                Total
                            </p>
                            <p class="col-span-1"/>
                        </div>

                        <TransitionGroup
                            enter-from-class="opacity-0 -translate-y-1"
                            enter-active-class="transition duration-200"
                            leave-to-class="opacity-0 scale-95"
                            leave-active-class="transition duration-150"
                            tag="div" class="space-y-1.5"
                        >
                            <div
                                v-for="(item, index) in form.items"
                                :key="index"
                                class="grid grid-cols-12 gap-2 items-center rounded-xl border
                                       border-gray-100 dark:border-gray-800 px-3 py-3
                                       hover:border-indigo-200 dark:hover:border-indigo-800/50
                                       transition group"
                            >
                                <!-- Nama -->
                                <div class="col-span-4 flex items-center gap-2 min-w-0">
                                    <span class="h-6 w-6 rounded-full bg-indigo-100
                                                 dark:bg-indigo-900/40 text-indigo-600
                                                 dark:text-indigo-400 text-xs font-bold
                                                 flex items-center justify-center shrink-0">
                                        {{ index + 1 }}
                                    </span>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ item.name }}
                                    </p>
                                </div>

                                <!-- Keterangan -->
                                <p class="col-span-3 text-xs text-gray-400 truncate">
                                    {{ item.description || '—' }}
                                </p>

                                <!-- Qty -->
                                <p class="col-span-1 text-sm text-gray-600 dark:text-gray-400
                                          text-center font-medium">
                                    {{ item.qty }}
                                </p>

                                <!-- Harga satuan -->
                                <p class="col-span-2 text-xs text-gray-500 dark:text-gray-400 text-right">
                                    {{ formatCurrency(item.unit_price) }}
                                </p>

                                <!-- Total per item -->
                                <p class="col-span-1 text-sm font-semibold text-gray-900
                                          dark:text-white text-right">
                                    {{ formatCurrency(item.amount) }}
                                </p>

                                <!-- Hapus -->
                                <div class="col-span-1 flex justify-end">
                                    <button type="button" @click="removeItem(index)"
                                        class="h-7 w-7 rounded-lg flex items-center justify-center
                                               text-gray-300 dark:text-gray-600
                                               hover:text-red-600 hover:bg-red-50
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

                    <!-- Empty state -->
                    <div v-else class="flex flex-col items-center gap-2 py-10 text-center">
                        <div class="h-12 w-12 rounded-xl bg-gray-100 dark:bg-gray-800
                                    flex items-center justify-center text-2xl">🧾</div>
                        <p class="text-sm text-gray-400">Belum ada item</p>
                        <p class="text-xs text-gray-300 dark:text-gray-600">
                            Isi form di atas lalu klik "Tambah"
                        </p>
                    </div>
                </div>

                <!-- =========================================
                     NOTA SUMMARY
                ========================================= -->
                <div v-if="form.items.length > 0"
                     class="border-t border-gray-100 dark:border-gray-800 px-6 py-5
                            bg-gray-50/50 dark:bg-gray-800/30">

                    <div class="ml-auto w-full sm:w-96 space-y-2.5">

                        <!-- Subtotal -->
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">
                                Subtotal ({{ form.items.length }} item, {{ totalQty }} qty)
                            </span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ formatCurrency(subtotal) }}
                            </span>
                        </div>

                        <!-- Diskon -->
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Diskon</span>
                            <div class="relative w-36">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2
                                             text-xs text-gray-400 pointer-events-none">Rp</span>
                                <input
                                    :value="displayInput(form.discount)"
                                    @input="(e) => { form.discount = e.target.value.replace(/\D/g, '') }"
                                    type="text" inputmode="numeric" placeholder="0"
                                    class="w-full rounded-lg border border-gray-200 dark:border-gray-700
                                           pl-8 pr-3 py-1.5 text-sm text-right text-gray-900
                                           dark:text-white placeholder-gray-400
                                           bg-white dark:bg-gray-800 focus:outline-none
                                           focus:ring-2 focus:ring-indigo-500 transition"
                                />
                            </div>
                        </div>

                        <!-- PPN -->
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 dark:text-gray-400">PPN</span>
                                <select v-model="form.tax_percent"
                                    class="rounded-lg border border-gray-200 dark:border-gray-700
                                           bg-white dark:bg-gray-800 px-2 py-1.5 text-xs
                                           text-gray-700 dark:text-gray-300 focus:outline-none
                                           focus:ring-2 focus:ring-indigo-500 transition">
                                    <option :value="0">0%</option>
                                    <option :value="11">11%</option>
                                    <option :value="12">12%</option>
                                </select>
                            </div>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ formatCurrency(taxAmount) }}
                            </span>
                        </div>

                        <!-- Biaya lain -->
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Biaya Lain-lain</span>
                            <div class="relative w-36">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2
                                             text-xs text-gray-400 pointer-events-none">Rp</span>
                                <input
                                    :value="displayInput(form.other_fee)"
                                    @input="(e) => { form.other_fee = e.target.value.replace(/\D/g, '') }"
                                    type="text" inputmode="numeric" placeholder="0"
                                    class="w-full rounded-lg border border-gray-200 dark:border-gray-700
                                           pl-8 pr-3 py-1.5 text-sm text-right text-gray-900
                                           dark:text-white placeholder-gray-400
                                           bg-white dark:bg-gray-800 focus:outline-none
                                           focus:ring-2 focus:ring-indigo-500 transition"
                                />
                            </div>
                        </div>

                        <!-- Garis -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-2.5"/>

                        <!-- Grand Total -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">
                                Grand Total
                            </span>
                            <span class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400">
                                {{ formatCurrency(finalTotal) }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ACTION -->
            <div class="flex items-center justify-between pt-2 pb-8">
                <a :href="route('transactions.index')"
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
                            ? 'Menyimpan...'
                            : `Update Transaksi${form.items.length > 0 ? ` (${form.items.length} item)` : ''}`
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