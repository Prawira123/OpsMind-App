<script setup>
import { ref, computed } from 'vue'
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
import TableData from '@/Components/Tenant/TableData.vue'

// PROPS — dikirim dari AccountController::index()
const props = defineProps({
    categories: Array,
})

// TIPE REKENING — label & style per tipe
const categoryTypes = {
    income:    { label: 'Pemasukan',  color: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' },
    expense:    { label: 'Pengeluaran',       color: 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' },
}

const tableHead = [
    { key: 'name',           label: 'Nama Kategori' },
    { key: 'type',           label: 'Tipe' },
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
    
]

const columnTypes = {
    name:           'nameWithIcon',
    type:           'badge',
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
    if (!confirm(`Hapus ${selected.value.length} kategori yang dipilih?`)) return

    bulkDeleteForm.ids = selected.value
    bulkDeleteForm.delete(route('categories.bulk-destroy'), {
        onSuccess: () => {
            selected.value = []
            selectAll.value = false
        }
    })

    console.log(selected.value)
}

// DELETE SINGLE
const deleteCategory = (id) => {

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
</script>

<template>
    <AppLayout title="Kategori Page">

        <!-- PAGE HEADER -->
        
        <PageHeader :href="route('categories.create')" :title="'Kategori'" :desc="'Kelola Kategori kamu'" :btnDesc="'Kategori'"/>

        <!-- SUMMARY CARDS -->
        <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-3">

            <PrimaryCard :titleCard="'Total Kategori'" :mainDesc="summary.total" :subDesc="summary.total" :subTitle="'Kategori'"/>

            <!-- Income -->   
            <SecondaryCard :main-desc="summary.income" :title-card="'Income'" :sub-title="'Kategori'" :logoPath="'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'" :classLogo="'h-4 w-4 text-emerald-600'"/>

            <!-- Expense -->
            <SecondaryCard :main-desc="summary.expense" :title-card="'Expense'" :sub-title="'Kategori'" :logoPath="'M3 6l9-4 9 4M3 10h18M5 10v8m4-8v8m4-8v8m4-8v8 M19 10v8M3 18h18'" :classLogo="'h-4 w-4 text-blue-600'"/>

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
                        <SearchBar v-model="search" :class="'w-full sm:w-64 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 pl-9 pr-4 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition'" :type="'text'" :placeholder="'Cari Rekening...'"/>
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

            <TableData @update:selectAll="toggleSelectAll" 
            :selectAll="selectAll" :tableHeadData="tableHead" :column-types="columnTypes" edit-route="categories.edit" delete-route="categories.destroy" :filteredDatas="filteredCategories" :sortKey="sortKey" :search="search" :selected="selected" :toggleSelect="toggleSelect" :type="categoryTypes" :deleteFunction="deleteCategory" :datas="props.categories" :sortDir="sortDir" :toggleSort="toggleSort"/>

            <!-- Footer tabel -->
            
        </div>

    </AppLayout>
</template>