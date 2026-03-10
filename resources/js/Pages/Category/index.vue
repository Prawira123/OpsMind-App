<script setup>
import { ref, computed, watch } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import AppLayout from '@/Layouts/AppLayout.vue'
import PageHeader from '@/Components/Tenant/PageHeader.vue'
import PrimaryCard from '@/Components/Tenant/PrimaryCard.vue'
import SecondaryCard from '@/Components/Tenant/SecondaryCard.vue'
import LogoCard from '@/Components/Partials/LogoCard.vue'
import SearchBar from '@/Components/Partials/SearchBar.vue'
import OptionSelect from '@/Components/Partials/OptionSelect.vue'
import ButtonDelete from '@/Components/Partials/ButtonDelete.vue'
import BadgeSuccess from '@/Components/Partials/BadgeSuccess.vue'

// PROPS — dikirim dari AccountController::index()
const props = defineProps({
    categories: Array,
    status: String,
})

// TIPE REKENING — label & style per tipe
const categoryTypes = {
    income:    { label: 'Pemasukan',  color: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' },
    expense:    { label: 'Pengeluaran',       color: 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' },
}

const tableHead = [
    { key: 'name',           label: 'Nama Kategori' },
    { key: 'type',           label: 'Tipe' },
    { key: 'is_default',     label: 'Status' },
]

const icons = [
    { name: 'tag',           label: 'Tag',          path: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z' },
    { name: 'shopping-bag',  label: 'Belanja',      path: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' },
    { name: 'home',          label: 'Rumah',        path: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'car',           label: 'Transportasi', path: 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0' },
    { name: 'food',          label: 'Makanan',      path: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z' },
    { name: 'heart',         label: 'Kesehatan',    path: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' },
    { name: 'academic',      label: 'Pendidikan',   path: 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z' },
    { name: 'briefcase',     label: 'Bisnis',       path: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
    { name: 'mobile',        label: 'Elektronik',   path: 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z' },
    { name: 'gift',          label: 'Hadiah',       path: 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7' },
    { name: 'trending-up',   label: 'Investasi',    path: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' },
    { name: 'currency',      label: 'Keuangan',     path: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'lightning',     label: 'Utilitas',     path: 'M13 10V3L4 14h7v7l9-11h-7z' },
    { name: 'globe',         label: 'Perjalanan',   path: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'music',         label: 'Hiburan',      path: 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3' },
    { name: 'office',        label: 'Kantor',       path: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
    { name: 'dots',          label: 'Lainnya',      path: 'M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z' },
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
        key : 'income',
        value : 'Pemasukan',
    },
    {
        key : 'expense',
        value : 'Pengeluaran',
    },
    {
        key : 'other',
        value : 'Lainnya',
    },
    
]

const columnTypes = {
    name:           'nameWithIcon',
    type:           'badge',
    is_default:     'badge',
}

const statusTypes = {
    1:  { label: 'Default',    color: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' },
    0: { label: 'Tidak Default', color: 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' },
}

// SORT
const sortKey = ref('name')
const sortDir = ref('asc')

// FILTERED & SORTED DATA
const filteredCategories = computed(() => {
    let data = [...(props.categories ?? [])]

    // Filter search
    if (search.value) {
        const q = search.value.toLowerCase()
        data = data.filter(a =>
            (a.name?.toLowerCase() ?? '').includes(q) ||
            (a.type?.toLowerCase() ?? '').includes(q)
        )
    }
    console.log(data);


    // Filter tipe
    if (filterType.value !== 'all') {
        data = data.filter(a => a.type === filterType.value)
    }

    // Sort
    data.sort((a, b) => {
        let valA = a[sortKey.value] ?? ''
        let valB = b[sortKey.value] ?? ''

        if(sortKey.value === 'is_default') {
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
const summary = computed(() => {
    const category = props.categories ?? []
    return {
        total:   props.categories.length,
        income:    category.filter(a => a.type === 'income').length,
        expense:    category.filter(a => a.type === 'expense').length,
        other:    category.filter(a => a.type !== 'expense' && a.type !== 'income').length,
    }
})

// BULK DELETE
const selected    = ref([])
const selectAll   = ref(false)

const toggleSelectAll = (value) => {

    selectAll.value = value
    console.log(selectAll.value)

    if (selectAll.value) {
        selected.value = filteredCategories.value.map(a => a.id)
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
    selectAll.value = selected.value.length === filteredCategories.value.length
}

const bulkDeleteForm = useForm({ ids: [] })

const confirmBulkDelete = () => {
    if (!selected.value.length) return

    const categoryDefault = props.categories.filter(c => 
        selected.value.includes(c.id) && c.is_default == true
    )

    if(categoryDefault.length > 0 ){
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Tidak dapat menghapus kategori default',
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
            bulkDeleteForm.delete(route('categories.bulk-destroy'), {
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
const deleteCategory = (id) => {

    const category = props.categories.find(c => c.id == id)

    if(category.is_default == true){
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Tidak dapat menghapus kategori default',
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
            router.delete(route('categories.destroy', id))
        }
    })

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

function getPathIcon(iconName){
    const found = icons.find(i => i.name === iconName)
    console.log(found)
    return found ? found.path : icons[0].path
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
    <AppLayout title="Kategori Page">

        <!-- PAGE HEADER -->
        
        <PageHeader :href="route('categories.create')" :title="'Kategori'" :desc="'Kelola Kategori kamu'" :btnDesc="'Kategori'"/>

        <!-- SUMMARY CARDS -->
        <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">

            <PrimaryCard :titleCard="'Total Kategori'" :mainDesc="summary.total" :subDesc="summary.total" :subTitle="'Kategori'"/>

            <!-- Income -->   
            <SecondaryCard :main-desc="summary.income" :title-card="'Income'" :sub-title="'Kategori'" :logoPath="'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'" :classLogo="'h-4 w-4 text-emerald-600'"/>

            <!-- Expense -->
            <SecondaryCard :main-desc="summary.expense" :title-card="'Expense'" :sub-title="'Kategori'" :logoPath="'M3 6l9-4 9 4M3 10h18M5 10v8m4-8v8m4-8v8m4-8v8 M19 10v8M3 18h18'" :classLogo="'h-4 w-4 text-blue-600'"/>

            <SecondaryCard :main-desc="summary.other" :title-card="'Other'" :sub-title="'Kategori'" :logoPath="'M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z'" :classLogo="'h-4 w-4 text-red-600'"/>

        </div>

        <div class="" v-if="showStatus">
            <BadgeSuccess :status="props.status"/>
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
                        <LogoCard :class="'absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400'" 
                        :logo-path="'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0'"/>
                        <SearchBar v-model="search" :class="'w-full sm:w-64 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 pl-9 pr-4 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition'" :type="'text'" :placeholder="'Cari Kategori...'"/>
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
                        <tr v-if="filteredCategories.length === 0">
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
                            v-for="row in filteredCategories"
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
                                                :d="getPathIcon(row?.icon)"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ row[col.key] }}
                                    </p>
                                </div>

                                <!-- Tipe: badge — menggunakan object `type` untuk label & warna -->
                                <span
                                    v-else-if="getCellType(col.key) === 'badge' && col.key === 'type'"
                                    :class="[
                                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                        categoryTypes?.[row[col.key]]?.color ?? 'bg-gray-100 text-gray-700'
                                    ]"
                                >
                                    {{ categoryTypes?.[row[col.key]]?.label ?? row[col.key] }}
                                </span>

                                <span
                                    v-else-if="getCellType(col.key) === 'badge' && col.key === 'is_default'"
                                    :class="[
                                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                        statusTypes?.[row[col.key]]?.color ?? 'bg-gray-100 text-gray-700'
                                    ]"
                                >
                                    {{ statusTypes?.[row[col.key]]?.label ?? row[col.key] }}
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
                                    class="text-sm font-mono text-gray-600 dark:text-gray-400"
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
                                        :href="route('categories.edit', row.id)"
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
                                        @click="deleteCategory(row.id)"
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