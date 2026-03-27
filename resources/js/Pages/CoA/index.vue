<script setup>
import { ref, computed, watch } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import AppLayout from '@/Layouts/AppLayout.vue'
import PageHeader from '@/Components/Tenant/PageHeader.vue'
import LogoCard from '@/Components/Partials/LogoCard.vue'
import SearchBar from '@/Components/Partials/SearchBar.vue'
import OptionSelect from '@/Components/Partials/OptionSelect.vue'
import ButtonDelete from '@/Components/Partials/ButtonDelete.vue'
import BadgeSuccess from '@/Components/Partials/BadgeSuccess.vue'

// PROPS — dikirim dari AccountController::index()
const props = defineProps({
    chartOfAccounts: Array,
    status : String,
})

// TIPE REKENING — label & style per tipe
const chartOfAccountTypes = {
    asset:    { label: 'Asset',  color: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' },
    liability:    { label: 'Liability',       color: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' },
    equity: { label: 'Equity',   color: 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400' },
    revenue:   { label: 'Revenue',    color: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-800/30 dark:text-yellow-400' },
    expense:   { label: 'Expense',    color: 'bg-rose-100 text-rose-700 dark:bg-rose-800/30 dark:text-rose-400' },
}

const tableHead = [
        { key: 'account_type',      label: 'Tipe Akun' },
        { key: 'code',      label: 'Kode Akun' },
        { key: 'name',      label: 'Nama Akun' },
        { key: 'balance',   label: 'Saldo' },
        { key: 'is_active',   label: 'Status Aktif' },
        { key: 'is_locked',   label: 'Status Terkunci' },
        { key: 'normal_post',   label: 'Normal Post' },
]

// SEARCH
const search = ref('')

// FILTER TIPE
const filterType = ref('all')

const optionFilter = [
    {
        key : 'all',
        value : 'Semua Tipe',
    },
    {
        key : 'asset',
        value : 'asset',
    },
    {
        key : 'liability',
        value : 'liability',
    },
    {
        key : 'equity',
        value : 'equity',
    },
    {
        key : 'revenue',
        value : 'revenue',
    },
    {
        key : 'expense',
        value : 'expense',
    },
    
]

const columnTypes = {
    name:'nameWithIcon',
    balance:'currency',
    is_active:'badge',
    is_locked:'badge',
    account_type:'badge',
    normal_post:'badge',
}

const statusAktifTypes = {
    true:  { label: 'Aktif',    color: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' },
    false: { label: 'Tidak Aktif', color: 'bg-red-100 text-red-600 dark:bg-red-800 dark:text-red-400' },
}

const statusLockedTypes = {
    true:  { label: 'Terkunci',    color: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' },
    false: { label: 'Tidak Terkunci', color: 'bg-red-100 text-red-600 dark:bg-red-800 dark:text-red-400' },
}

const normalPostTypes = {
    debit:  { label: 'Debit',    color: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' },
    credit: { label: 'Credit', color: 'bg-red-100 text-red-600 dark:bg-red-800 dark:text-red-400' },
}

// SORT
const sortKey = ref('name')
const sortDir = ref('asc')

// FILTERED & SORTED DATA
const filteredCoAs = computed(() => {
    let data = [...(props.chartOfAccounts ?? [])]

    // Filter search
    if (search.value) {
        const q = search.value.toLowerCase()
        data = data.filter(a =>
            (a.type?.toLowerCase() ?? '').includes(q) ||
            (a.name?.toLowerCase() ?? '').includes(q)
        )
    }
    console.log(data);


    // Filter tipe
    if (filterType.value !== 'all') {
        data = data.filter(a => a.account_type.category === filterType.value)
    }

    // Sort
    data.sort((a, b) => {
        let valA = a[sortKey.value] ?? ''
        let valB = b[sortKey.value] ?? ''

        // Khusus balance — sort numerik
        if (sortKey.value === 'balance') {
            valA = parseFloat(valA) || 0
            valB = parseFloat(valB) || 0
            return sortDir.value === 'asc' ? valA - valB : valB - valA
        }

        if(sortKey.value === 'is_active') {
            valA = valA === true ? 1 : 0
            valB = valB === true ? 1 : 0
            return sortDir.value === 'asc' ? valA - valB : valB - valA
        }

        if(sortKey.value === 'is_locked') {
            valA = valA === true ? 1 : 0
            valB = valB === true ? 1 : 0
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

// SUMMARY CARDS — total per tipe
// const summary = computed(() => {
//     const CoAs = props.chartOfAccounts ?? []
//     return {
//         total:   CoAs.length,
//         cash:    CoAs.filter(a => a.type === 'cash').length,
//         bank:    CoAs.filter(a => a.type === 'bank').length,
//         ewallet: CoAs.filter(a => a.type === 'ewallet').length,
//         totalBalance: CoAs.reduce((sum, a) => sum + (parseFloat(a.balance) || 0), 0),
//     }
// })

// BULK DELETE
const selected    = ref([])
const selectAll   = ref(false)


const toggleSelectAll = (value) => {

    selectAll.value = value
    console.log(selectAll.value)

    if (selectAll.value) {
        selected.value = filteredCoAs.value.map(a => a.id)
    } else {
        selected.value = []
    }
}


const toggleSelect = (id) => {
    const idx = selected.value.indexOf(id)
    if (idx === -1) {
        selected.value.push(id)
    } else {
        selected.value.splice(idx, 1)
    }
    selectAll.value = selected.value.length === filteredCoAs.value.length
}

const bulkDeleteForm = useForm({ ids: [] })

const confirmBulkDelete = () => {
    if (!selected.value.length) return

    const accountWithBalance = props.chartOfAccounts.filter(a => 
        selected.value.includes(a.id) && a.balance > 0 || a.is_locked == true
    )

    if(accountWithBalance.length > 0 ){
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Tidak dapat menghapus rekening yang masih memiliki saldo dan akun yang terkunci',
        })
        return
    }

    bulkDeleteForm.ids = selected.value
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            bulkDeleteForm.delete(route('CoA.bulk-destroy'), {
            onSuccess: () => {
                selected.value = []
                selectAll.value = false
            }
        })
        }
    })
    

    console.log(selected.value)
}

// DELETE SINGLE
const deleteCoA = (id) => {

    const account = props.chartOfAccounts.find(a => a.id == id)

        if(account.is_locked == true || account.balance > 0){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: `Akun "${account.name}" masih memiliki saldo dan terkunci. Kosongkan saldo atau transafer ke rekening lain dan buka kunci terlebih dahulu.`,
            })
            return
        }

        if(account.is_default == true){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: `Akun "${account.name}" merupakan akun default yang tidak dapat dihapus.`,
            })
            return
        }
    
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('chart-of-accounts.destroy', id))
        }
    })
}

// FORMAT CURRENCY
const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style:    'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value || 0)
}

const toggleSort = (key) => {
    if (sortKey.value === key) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortKey.value = key
        sortDir.value = 'asc'
    }
}

function getCellType(key){
    return columnTypes[key] ?? 'text'
}

const showStatus = ref(false)

watch(() => props.status, (val) => {
    if(val){
        showStatus.value = true
        setTimeout(() => {
            showStatus.value = false
        }, 3000)
    }
}, {immediate: true})

</script>

<template>
    <AppLayout title="Akun Page">

        <!-- PAGE HEADER -->
        
        <PageHeader :href="route('chart-of-accounts.create')" :title="'Akun'" :desc="'Kelola Akun dalam bisnis kamu'" :btnDesc="'Akun'"/>

        <div class="" v-if="showStatus">
            <BadgeSuccess :status="props.status"/>
        </div>

        <!-- SUMMARY CARDS -->

        <!-- TABEL SECTION -->
        <div class="rounded-xl bg-white dark:bg-gray-900 border
                    border-gray-200 dark:border-gray-800 shadow-sm">

            <!-- Toolbar -->
            <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center
                        sm:justify-between border-b border-gray-200 dark:border-gray-800">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <!-- Search -->
                    <div class="relative">
                        <LogoCard :class="'absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400'" 
                        :logo-path="'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0'"/>
                        <SearchBar v-model="search" :class="'w-full sm:w-64 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 pl-9 pr-4 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition'" :type="'text'" :placeholder="'Cari Akun...'"/>
                    </div>
                    
                    <!-- Filter Tipe -->
                    <select
                        v-model="filterType"
                        class="rounded-lg border border-gray-200 dark:border-gray-700
                               bg-gray-50 dark:bg-gray-800 px-3 py-2 text-sm
                               text-gray-900 dark:text-white focus:outline-none
                               focus:ring-2 focus:ring-indigo-500 transition"
                    >
                        <OptionSelect v-for="item in optionFilter" :key="item.key" :item="item"/>
                    </select>
                </div>

                <!-- Bulk delete button — muncul saat ada yang dipilih -->
                <Transition
                    enter-from-class="opacity-0 scale-95"
                    enter-active-class="transition duration-150"
                    leave-to-class="opacity-0 scale-95"
                    leave-active-class="transition duration-100"
                >
                    <ButtonDelete v-if="selected.length > 0" @click="confirmBulkDelete" :btn-desc="`Hapus ${selected.length} Data`"/>
                </Transition>
            </div>

            <!-- Table -->

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-800">
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
                            <tr v-if="filteredCoAs.length === 0">
                                <td :colspan="tableHead.length + 2" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="h-14 w-14 rounded-full bg-gray-100 dark:bg-gray-800
                                                    flex items-center justify-center">
                                            <svg class="h-7 w-7 text-gray-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ search ? 'Data tidak ditemukan' : 'Belum ada data' }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ search ? 'Coba kata kunci lain' : 'Tambahkan data pertama kamu' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- Data rows -->
                            <tr
                                v-for="row in filteredCoAs"
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
                                    <!-- Tipe: nameWithIcon — nama + icon berdasarkan type -->
                                    <div v-if="getCellType(col.key) === 'nameWithIcon'"
                                        class="flex items-center gap-3">
                                        <div :class="[
                                            'h-9 w-9 rounded-lg flex items-center justify-center shrink-0',
                                            !row.color && row.type === 'cash'    && 'bg-emerald-100 dark:bg-emerald-900/30',
                                            !row.color && row.type === 'bank'    && 'bg-blue-100 dark:bg-blue-900/30',
                                            !row.color && row.type === 'ewallet' && 'bg-violet-100 dark:bg-violet-900/30',
                                            (!row.color && (!row.type || row.type === 'other')) && 'bg-gray-100 dark:bg-gray-800',
                                        ]" :style="row.color ? { backgroundColor: row.color + '20' } : {}">
                                            <svg class="h-4 w-4"
                                                :class="[
                                                    !row.color && row.type === 'cash'    ? 'text-emerald-600' :
                                                    !row.color && row.type === 'bank'    ? 'text-blue-600' :
                                                    !row.color && row.type === 'ewallet' ? 'text-violet-600' :
                                                    !row.color ? 'text-gray-500' : ''
                                                ]"
                                                :style="row.color ? { color: row.color } : {}"
                                                fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ row[col.key] }}
                                        </p>
                                    </div>

                                    <!-- Tipe: badge — menggunakan object `type` untuk label & warna -->
                                    <span
                                        v-else-if="getCellType(col.key) === 'badge' && col.key === 'account_type'"
                                        :class="[
                                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                            chartOfAccountTypes?.[row.account_type?.category]?.color ?? 'bg-gray-100 text-gray-700'
                                        ]"
                                    >
                                        {{ chartOfAccountTypes?.[row.account_type?.category ]?.label ?? row.account_type?.category }}
                                    </span>
                                    <span
                                        v-else-if="getCellType(col.key) === 'badge' && col.key === 'is_active'"
                                        :class="[
                                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                            statusAktifTypes?.[row[col.key]]?.color ?? 'bg-gray-100 text-gray-700'
                                        ]"
                                    >
                                        {{ statusAktifTypes?.[row[col.key]]?.label ?? row[col.key] }}
                                    </span>
                                    <span
                                        v-else-if="getCellType(col.key) === 'badge' && col.key === 'is_locked'"
                                        :class="[
                                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                            statusLockedTypes?.[row[col.key]]?.color ?? 'bg-gray-100 text-gray-700'
                                        ]"
                                    >
                                        {{ statusLockedTypes?.[row[col.key]]?.label ?? row[col.key] }}
                                    </span>
                                    <span
                                        v-else-if="getCellType(col.key) === 'badge' && col.key === 'normal_post'"
                                        :class="[
                                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                            normalPostTypes?.[row[col.key]]?.color ?? 'bg-gray-100 text-gray-700'
                                        ]"
                                    >
                                        {{ normalPostTypes?.[row[col.key]]?.label ?? row[col.key] }}
                                    </span>

                                    <!-- Tipe: currency -->
                                    <p
                                        v-else-if="getCellType(col.key) === 'currency'"
                                        class="text-sm font-semibold text-gray-900 dark:text-white"
                                    >
                                        {{ formatCurrency?.(row[col.key]) ?? row[col.key] }}
                                    </p>

                                    <!-- Tipe: mono — nomor rekening dll -->
                                    <p
                                        v-else-if="getCellType(col.key) === 'mono'"
                                        class="text-sm font-mono text-gray-400 dark:text-gray-400"
                                    >
                                        {{ row[col.key] ?? '—' }}
                                    </p>

                                    <!-- Default: text biasa -->
                                    <p v-else class="text-sm text-gray-900 dark:text-white">
                                        {{ row[col.key] ?? '—' }}
                                    </p>
                                </td>

                                <!-- Aksi -->
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link
                                            :href="route('chart-of-accounts.edit', row.id)"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg
                                                text-gray-400 hover:text-indigo-600
                                                hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition"
                                            title="Edit"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </Link>

                                        <button
                                            @click="deleteCoA(row.id)"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg
                                                text-gray-400 hover:text-red-600
                                                hover:bg-red-50 dark:hover:bg-red-900/20 transition"
                                            title="Hapus"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>


            <!-- Footer tabel -->
            
        </div>

    </AppLayout>
</template>