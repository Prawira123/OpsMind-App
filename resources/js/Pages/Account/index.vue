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
    accounts: Array,
})

// TIPE REKENING — label & style per tipe
const accountTypes = {
    cash:    { label: 'Kas Tunai',  color: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' },
    bank:    { label: 'Bank',       color: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' },
    ewallet: { label: 'E-Wallet',   color: 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400' },
    other:   { label: 'Lainnya',    color: 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400' },
}

const tableHead = [
        { key: 'name',           label: 'Nama Rekening' },
        { key: 'type',           label: 'Tipe' },
        { key: 'bank_name',      label: 'Bank' },
        { key: 'account_number', label: 'No. Rekening' },
        { key: 'balance',        label: 'Saldo' },
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
        key : 'cash',
        value : 'Kas Tunai',
    },
    {
        key : 'bank',
        value : 'Bank',
    },
    {
        key : 'ewallet',
        value : 'E-Wallet',
    },
    {
        key : 'other',
        value : 'Lainnya',
    },
    
]

const columnTypes = {
    name:           'nameWithIcon',
    type:           'badge',
    account_number: 'mono',
    balance:        'currency',
}

// SORT
const sortKey = ref('name')
const sortDir = ref('asc')

// FILTERED & SORTED DATA
const filteredAccounts = computed(() => {
    let data = [...(props.accounts ?? [])]

    // Filter search
    if (search.value) {
        const q = search.value.toLowerCase()
        data = data.filter(a =>
            (a.name?.toLowerCase() ?? '').includes(q) ||
            (a.bank_name?.toLowerCase() ?? '').includes(q) ||
            (a.account_number?.toLowerCase() ?? '').includes(q)
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

        // Khusus balance — sort numerik
        if (sortKey.value === 'balance') {
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

// SUMMARY CARDS — total per tipe
const summary = computed(() => {
    const accounts = props.accounts ?? []
    return {
        total:   accounts.length,
        cash:    accounts.filter(a => a.type === 'cash').length,
        bank:    accounts.filter(a => a.type === 'bank').length,
        ewallet: accounts.filter(a => a.type === 'ewallet').length,
        totalBalance: accounts.reduce((sum, a) => sum + (parseFloat(a.balance) || 0), 0),
    }
})

// BULK DELETE
const selected    = ref([])
const selectAll   = ref(false)


const toggleSelectAll = (value) => {

    selectAll.value = value
    console.log(selectAll.value)

    if (selectAll.value) {
        selected.value = filteredAccounts.value.map(a => a.id)
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
    selectAll.value = selected.value.length === filteredAccounts.value.length
}

const bulkDeleteForm = useForm({ ids: [] })

const confirmBulkDelete = () => {
    if (!selected.value.length) return
    if (!confirm(`Hapus ${selected.value.length} rekening yang dipilih?`)) return

    bulkDeleteForm.ids = selected.value
    bulkDeleteForm.delete(route('accounts.bulk-destroy'), {
        onSuccess: () => {
            selected.value = []
            selectAll.value = false
        }
    })

    console.log(selected.value)
}

// DELETE SINGLE
const deleteAccount = (id) => {

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
            router.delete(route('accounts.destroy', id))
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
</script>

<template>
    <AppLayout title="Rekening Page">

        <!-- PAGE HEADER -->
        
        <PageHeader :href="route('accounts.create')" :title="'Rekening'" :desc="'Kelola rekening kas, bank, dan e-wallet bisnis kamu'" :btnDesc="'Rekening'"/>

        <!-- SUMMARY CARDS -->
        <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">

            <PrimaryCard :titleCard="'Total Balance'" :mainDesc="formatCurrency(summary.totalBalance)" :subDesc="summary.total" :subTitle="'Rekening'"/>

            <!-- Kas -->   
            <SecondaryCard :main-desc="summary.cash" :title-card="'Kas Tunai'" :sub-title="'Rekening'" :logoPath="'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'" :classLogo="'h-4 w-4 text-emerald-600'"/>

            <!-- Bank -->
            <SecondaryCard :main-desc="summary.bank" :title-card="'Bank'" :sub-title="'Rekening'" :logoPath="'M3 6l9-4 9 4M3 10h18M5 10v8m4-8v8m4-8v8m4-8v8 M19 10v8M3 18h18'" :classLogo="'h-4 w-4 text-blue-600'"/>

            <!-- E-Wallet -->
            <SecondaryCard :main-desc="summary.ewallet" :title-card="'E-Wallet'" :sub-title="'Rekening'" :logoPath="'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'" :classLogo="'h-4 w-4 text-violet-600'"/>

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
            :selectAll="selectAll" :tableHeadData="tableHead" :column-types="columnTypes" edit-route="accounts.edit" delete-route="accounts.destroy" :filteredDatas="filteredAccounts" :sortKey="sortKey" :search="search" :selected="selected" :toggleSelect="toggleSelect" :type="accountTypes" :deleteFunction="deleteAccount" :datas="props.accounts" :formatCurrency="formatCurrency" :sortDir="sortDir" :toggleSort="toggleSort"/>


            <!-- Footer tabel -->
            
        </div>

    </AppLayout>
</template>