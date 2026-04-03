<script setup>
import { ref, computed, watch } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import AppLayout from '@/Layouts/AppLayout.vue'
import PageHeader from '@/Components/Tenant/PageHeader.vue'
import LogoCard from '@/Components/Partials/LogoCard.vue'
import SearchBar from '@/Components/Partials/SearchBar.vue'
import ButtonDelete from '@/Components/Partials/ButtonDelete.vue'
import BadgeSuccess from '@/Components/Partials/BadgeSuccess.vue'

// =========================================================
// PROPS
// =========================================================
const props = defineProps({
    transactions: Array,
    status:       String,
    summary:      Object,
})

// =========================================================
// TABLE HEAD
// =========================================================
const tableHead = [
    { key: 'date',        label: 'Tanggal' },
    { key: 'description', label: 'Deskripsi' },
    { key: 'category',    label: 'Kategori' },
    { key: 'type',        label: 'Tipe' },
    { key: 'amountTotal', label: 'Total' },
]

const columnTypes = {
    date:        'date',
    amountTotal: 'currency',
    type:        'badge',
    category:    'relation',
}

// =========================================================
// TYPE CONFIG
// =========================================================
const typeConfig = {
    expense: { label: 'Pengeluaran', color: 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' },
    income:  { label: 'Pemasukan',   color: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' },
}

// =========================================================
// SEARCH & FILTER
// =========================================================
const search     = ref('')
const filterType = ref('all')

// =========================================================
// SORT
// =========================================================
const sortKey = ref('date')
const sortDir = ref('desc')

// =========================================================
// FILTERED & SORTED
// =========================================================
const filteredTransactions = computed(() => {
    let data = [...(props.transactions ?? [])]

    if (search.value) {
        const q = search.value.toLowerCase()
        data = data.filter(t =>
            (t.description?.toLowerCase()         ?? '').includes(q) ||
            (t.reference_no?.toLowerCase()        ?? '').includes(q) ||
            (t.category?.name?.toLowerCase()      ?? '').includes(q)
        )
    }

    if (filterType.value !== 'all') {
        data = data.filter(t => t.type === filterType.value)
    }

    data.sort((a, b) => {
        let valA = a[sortKey.value] ?? ''
        let valB = b[sortKey.value] ?? ''

        if (sortKey.value === 'amountTotal') {
            valA = parseFloat(valA) || 0
            valB = parseFloat(valB) || 0
            return sortDir.value === 'asc' ? valA - valB : valB - valA
        }

        valA = String(valA).toLowerCase()
        valB = String(valB).toLowerCase()
        if (valA < valB) return sortDir.value === 'asc' ? -1 : 1
        if (valA > valB) return sortDir.value === 'asc' ? 1 : -1
        return 0
    })

    return data
})

// =========================================================
// FORMAT
// =========================================================
const formatCurrency = (val) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0,
    }).format(val || 0)

const formatDate = (val) => {
    if (!val) return '—'
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit', month: 'short', year: 'numeric',
    }).format(new Date(val))
}

// =========================================================
// SORT
// =========================================================
const toggleSort = (key) => {
    if (sortKey.value === key) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortKey.value = key
        sortDir.value = 'asc'
    }
}

function getCellType(key) {
    return columnTypes[key] ?? 'text'
}

// =========================================================
// SELECT
// =========================================================
const selected  = ref([])
const selectAll = ref(false)

const toggleSelectAll = (value) => {
    selectAll.value = value
    selected.value  = value ? filteredTransactions.value.map(t => t.id) : []
}

const toggleSelect = (id) => {
    const idx = selected.value.indexOf(id)
    if (idx === -1) selected.value.push(id)
    else            selected.value.splice(idx, 1)
    selectAll.value = selected.value.length === filteredTransactions.value.length
}

// =========================================================
// BULK DELETE
// =========================================================
const bulkDeleteForm = useForm({ ids: [] })

const confirmBulkDelete = () => {
    if (!selected.value.length) return

    bulkDeleteForm.ids = selected.value

    Swal.fire({
        title:              `Hapus ${selected.value.length} Transaksi?`,
        text:               'Data yang dihapus tidak bisa dikembalikan!',
        icon:               'warning',
        showCancelButton:   true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor:  '#d33',
        confirmButtonText:  'Ya, Hapus!',
        cancelButtonText:   'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            bulkDeleteForm.delete(route('transactions.bulk-destroy'), {
                onSuccess: () => {
                    selected.value  = []
                    selectAll.value = false
                },
            })
        }
    })
}

// =========================================================
// DELETE SINGLE
// =========================================================
const deleteTransaction = (id) => {
    Swal.fire({
        title:              'Hapus Transaksi?',
        text:               'Data tidak bisa dikembalikan!',
        icon:               'warning',
        showCancelButton:   true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor:  '#d33',
        confirmButtonText:  'Ya, Hapus!',
        cancelButtonText:   'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('transactions.destroy', id))
        }
    })
}

// =========================================================
// STATUS BADGE
// =========================================================
const showStatus = ref(false)

watch(() => props.status, (val) => {
    if (val) {
        showStatus.value = true
        setTimeout(() => { showStatus.value = false }, 3000)
    }
}, { immediate: true })
</script>

<template>
    <AppLayout title="Transaksi">

        <!-- PAGE HEADER -->
        <PageHeader
            :href="route('transactions.create')"
            :title="'Transaksi'"
            :desc="'Kelola semua transaksi keuangan bisnis kamu'"
            :btnDesc="'Transaksi'"
        />

        <!-- STATUS BADGE -->
        <div v-if="showStatus">
            <BadgeSuccess :status="props.status"/>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">

            <div class="rounded-xl bg-white dark:bg-gray-900 border
                        border-gray-200 dark:border-gray-800 p-5 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                    Saldo Total
                </p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ formatCurrency(summary.total_balance) }}
                </p>
                <div class="mt-2 h-1 w-full rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                    <div class="h-1 bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.5)]" :style="{ width: '100%' }"></div>
                </div>
            </div>

            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border
                        border-emerald-100 dark:border-emerald-800 p-5 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-emerald-500/10 transition-transform group-hover:scale-150"></div>
                <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-1">
                    Total Pemasukan
                </p>
                <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-400">
                    {{ formatCurrency(summary.total_income) }}
                </p>
                <p class="text-xs text-emerald-500/70 mt-1 flex items-center gap-1">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    Uang masuk
                </p>
            </div>

            <div class="rounded-xl bg-rose-50 dark:bg-rose-900/20 border
                        border-rose-100 dark:border-rose-800 p-5 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-rose-500/10 transition-transform group-hover:scale-150"></div>
                <p class="text-xs font-semibold text-rose-600 dark:text-rose-400 uppercase tracking-wider mb-1">
                    Total Pengeluaran
                </p>
                <p class="text-2xl font-bold text-rose-700 dark:text-rose-400">
                    {{ formatCurrency(summary.total_expense) }}
                </p>
                <p class="text-xs text-rose-500/70 mt-1 flex items-center gap-1">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    Uang keluar
                </p>
            </div>

        </div>

        <!-- TABEL SECTION -->
        <div class="rounded-xl bg-white dark:bg-gray-900 border
                    border-gray-200 dark:border-gray-800 shadow-sm">

            <!-- Toolbar -->
            <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center
                        sm:justify-between border-b border-gray-200 dark:border-gray-800">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

                    <!-- Search -->
                    <div class="relative">
                        <LogoCard
                            :class="'absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400'"
                            :logo-path="'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0'"/>
                        <SearchBar
                            v-model="search"
                            :class="'w-full sm:w-64 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 pl-9 pr-4 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition'"
                            :type="'text'"
                            :placeholder="'Cari transaksi...'"
                        />
                    </div>

                    <!-- Filter tipe -->
                    <select
                        v-model="filterType"
                        class="rounded-lg border border-gray-200 dark:border-gray-700
                               bg-gray-50 dark:bg-gray-800 px-3 py-2 text-sm
                               text-gray-900 dark:text-white focus:outline-none
                               focus:ring-2 focus:ring-indigo-500 transition"
                    >
                        <option value="all">Semua Tipe</option>
                        <option value="expense">Pengeluaran</option>
                        <option value="income">Pemasukan</option>
                    </select>
                </div>

                <!-- Bulk delete -->
                <Transition
                    enter-from-class="opacity-0 scale-95"
                    enter-active-class="transition duration-150"
                    leave-to-class="opacity-0 scale-95"
                    leave-active-class="transition duration-100"
                >
                    <ButtonDelete
                        v-if="selected.length > 0"
                        @click="confirmBulkDelete"
                        :btn-desc="`Hapus ${selected.length} Data`"
                    />
                </Transition>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-800">

                            <!-- Checkbox header -->
                            <th class="w-10 px-4 py-3">
                                <input
                                    type="checkbox"
                                    :checked="selectAll"
                                    @change="toggleSelectAll($event.target.checked)"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600
                                           focus:ring-indigo-500 cursor-pointer"
                                />
                            </th>

                            <th
                                v-for="col in tableHead"
                                :key="col.key"
                                @click="toggleSort(col.key)"
                                class="px-4 py-3 text-left text-xs font-semibold
                                       text-gray-500 dark:text-gray-400 uppercase
                                       tracking-wider cursor-pointer select-none
                                       hover:text-gray-900 dark:hover:text-white transition-colors"
                            >
                                <div class="flex items-center gap-1">
                                    {{ col.label }}
                                    <span class="flex flex-col">
                                        <svg class="h-3 w-3 transition-colors"
                                             :class="sortKey === col.key && sortDir === 'asc'
                                                 ? 'text-indigo-600' : 'text-gray-300 dark:text-gray-600'"
                                             viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 4l8 8H4z"/>
                                        </svg>
                                        <svg class="h-3 w-3 transition-colors"
                                             :class="sortKey === col.key && sortDir === 'desc'
                                                 ? 'text-indigo-600' : 'text-gray-300 dark:text-gray-600'"
                                             viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 20l-8-8h16z"/>
                                        </svg>
                                    </span>
                                </div>
                            </th>

                            <th class="px-4 py-3 text-right text-xs font-semibold
                                       text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">

                        <!-- Empty state -->
                        <tr v-if="filteredTransactions.length === 0">
                            <td :colspan="tableHead.length + 2" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="h-14 w-14 rounded-full bg-gray-100 dark:bg-gray-800
                                                flex items-center justify-center">
                                        <svg class="h-7 w-7 text-gray-400" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor"
                                             stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ search ? 'Data tidak ditemukan' : 'Belum ada transaksi' }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ search ? 'Coba kata kunci lain' : 'Tambahkan transaksi pertama kamu' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Data rows -->
                        <tr
                            v-for="row in filteredTransactions"
                            :key="row.id"
                            :class="[
                                'transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50',
                                selected.includes(row.id) && 'bg-indigo-50/50 dark:bg-indigo-900/10',
                            ]"
                        >
                            <!-- Checkbox -->
                            <td class="px-4 py-3.5">
                                <input
                                    type="checkbox"
                                    :checked="selected.includes(row.id)"
                                    @change="toggleSelect(row.id)"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600
                                           focus:ring-indigo-500 cursor-pointer"
                                />
                            </td>

                            <!-- Dynamic columns -->
                            <td
                                v-for="col in tableHead"
                                :key="col.key"
                                class="px-4 py-3.5"
                            >
                                <!-- Date -->
                                <p v-if="getCellType(col.key) === 'date'"
                                   class="text-sm text-gray-600 dark:text-gray-400 font-mono">
                                    {{ formatDate(row[col.key]) }}
                                </p>

                                <!-- Currency -->
                                <p v-else-if="getCellType(col.key) === 'currency'"
                                   :class="[
                                       'text-sm font-semibold',
                                       row.type === 'income'
                                           ? 'text-emerald-600 dark:text-emerald-400'
                                           : 'text-rose-600 dark:text-rose-400',
                                   ]">
                                    {{ row.type === 'income' ? '+' : '-' }}
                                    {{ formatCurrency(row[col.key]) }}
                                </p>

                                <!-- Badge tipe -->
                                <span
                                    v-else-if="getCellType(col.key) === 'badge'"
                                    :class="[
                                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                        typeConfig[row[col.key]]?.color ?? 'bg-gray-100 text-gray-700',
                                    ]"
                                >
                                    {{ typeConfig[row[col.key]]?.label ?? row[col.key] }}
                                </span>

                                <!-- Relasi (category.name) -->
                                <div v-else-if="getCellType(col.key) === 'relation'"
                                     class="flex items-center gap-2">
                                    <!-- <span v-if="row.category?.icon" class="text-base">
                                        {{ row.category.icon }}
                                    </span> -->
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ row.category?.name ?? '—' }}
                                    </p>
                                </div>

                                <!-- Mono -->
                                <p v-else-if="getCellType(col.key) === 'mono'"
                                   class="text-sm font-mono text-gray-600 dark:text-gray-400">
                                    {{ row[col.key] ?? '—' }}
                                </p>

                                <!-- Default text -->
                                <p v-else class="text-sm text-gray-900 dark:text-white">
                                    {{ row[col.key] ?? '—' }}
                                </p>
                            </td>

                            <!-- Aksi -->
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-end gap-1">

                                    <!-- Detail -->
                                    <Link
                                        :href="route('transactions.show', row.id)"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg
                                               text-gray-400 hover:text-indigo-600
                                               hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition"
                                        title="Detail"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943
                                                     9.542 7-1.274 4.057-5.064 7-9.542 7-4.477
                                                     0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </Link>

                                    <!-- Edit -->
                                    <Link
                                        :href="route('transactions.edit', row.id)"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg
                                               text-gray-400 hover:text-indigo-600
                                               hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition"
                                        title="Edit"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0
                                                     002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828
                                                     15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </Link>

                                    <!-- Hapus -->
                                    <button
                                        @click="deleteTransaction(row.id)"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg
                                               text-gray-400 hover:text-red-600
                                               hover:bg-red-50 dark:hover:bg-red-900/20 transition"
                                        title="Hapus"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2
                                                     0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0
                                                     00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </AppLayout>
</template>