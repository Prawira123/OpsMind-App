<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import ButtonDelete from '@/Components/Partials/ButtonDelete.vue'

const props = defineProps({
    transactions: Array,
    selectAll: Boolean,
    selected: Array,
    search: String,
    filterType: String,
})

const emit = defineEmits(['update:selectAll', 'update:selected'])

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
// SORT
// =========================================================
const sortKey = ref('date')
const sortDir = ref('desc')

// =========================================================
// FILTERED & SORTED
// =========================================================
const filteredTransactions = computed(() => {
    let data = [...(props.transactions ?? [])]

    if (props.search) {
        const q = props.search.toLowerCase()
        data = data.filter(t =>
            (t.description?.toLowerCase()         ?? '').includes(q) ||
            (t.reference_no?.toLowerCase()        ?? '').includes(q) ||
            (t.category?.name?.toLowerCase()      ?? '').includes(q)
        )
    }

    if (props.filterType && props.filterType !== 'all') {
        data = data.filter(t => t.type === props.filterType)
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
// TYPE CONFIG
// =========================================================
const typeConfig = {
    expense: { label: 'Pengeluaran', color: 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' },
    income:  { label: 'Pemasukan',   color: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' },
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
const toggleSelectAll = (value) => {
    emit('update:selectAll', value)
    emit('update:selected', value ? filteredTransactions.value.map(t => t.id) : [])
}

const toggleSelect = (id) => {
    const idx = props.selected.indexOf(id)
    const newSelected = [...props.selected]

    if (idx === -1) 
        newSelected.push(id)
    else            
        newSelected.splice(idx, 1)

    emit('update:selected', newSelected)
    emit('update:selectAll', newSelected.length === filteredTransactions.value.length)
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
</script>

<template>
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
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                        />
                    </th>

                    <th
                        v-for="col in tableHead"
                        :key="col.key"
                        @click="toggleSort(col.key)"
                        class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer select-none hover:text-gray-900 dark:hover:text-white transition-colors"
                    >
                        <div class="flex items-center gap-1">
                            {{ col.label }}
                            <span class="flex flex-col">
                                <svg class="h-3 w-3 transition-colors"
                                     :class="sortKey === col.key && sortDir === 'asc' ? 'text-indigo-600' : 'text-gray-300 dark:text-gray-600'"
                                     viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 4l8 8H4z"/>    
                                </svg>
                                <svg class="h-3 w-3 transition-colors"
                                     :class="sortKey === col.key && sortDir === 'desc' ? 'text-indigo-600' : 'text-gray-300 dark:text-gray-600'"
                                     viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 20l-8-8h16z"/>
                                </svg>
                            </span>
                        </div>
                    </th>

                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 dark:divide-gray-800/50">

                <!-- Empty state -->
                <tr v-if="filteredTransactions.length === 0">
                    <td :colspan="tableHead.length + 2" class="px-4 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="h-14 w-14 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <svg class="h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4 4l4-4m-4 4v12m0 0l4 4m-4-4v5a2 2 0 01-2 2h12a2 2 0 002 2v-5m0 0l-4 4m4-4-4 4"/>
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
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                        />
                    </td>

                    <!-- Dynamic columns -->
                    <td
                        v-for="col in tableHead"
                        :key="col.key"
                        class="px-4 py-3.5"
                    >
                        <!-- Date -->
                        <p v-if="getCellType(col.key) === 'date'" class="text-sm text-gray-600 dark:text-gray-400 font-mono">
                            {{ formatDate(row[col.key]) }}
                        </p>

                        <!-- Currency -->
                        <p v-else-if="getCellType(col.key) === 'currency'" :class="[
                               'text-sm font-semibold',
                               row.type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400',
                           ]">
                            {{ row.type === 'income' ? '+' : '-' }}{{ formatCurrency(row[col.key]) }}
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

                        <!-- Relation (category.name) -->
                        <div v-else-if="getCellType(col.key) === 'relation'" class="flex items-center gap-2">
                            <p class="text-sm text-gray-900 dark:text-white truncate">
                                {{ row.category?.name ?? '—' }}
                            </p>
                        </div>

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
                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition"
                                    title="Detail"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057 -5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </Link>

                            <!-- Edit -->
                            <Link
                                :href="route('transactions.edit', row.id)"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition"
                                    title="Edit"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </Link>

                            <!-- Hapus -->
                            <button
                                @click="deleteTransaction(row.id)"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition"
                                    title="Hapus"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
