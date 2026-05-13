    <script setup>
    import { ref, computed, watch } from 'vue'
    import { Link, router, useForm, Deferred } from '@inertiajs/vue3'
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
    import AccountSummaryCardsSkeleton from '@/Components/Account/AccountSummaryCardsSkeleton.vue'
    import AccountTableSkeleton from '@/Components/Account/AccountTableSkeleton.vue'
    // PROPS — dikirim dari AccountController::index()
    const props = defineProps({
        accounts: Array,
        status: String,
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
            { key: 'is_active',      label: 'Status' },]

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
        is_active:      'badge',
    }

    const statusTypes = {
        true:  { label: 'Aktif',    color: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' },
        false: { label: 'Nonaktif', color: 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' },
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

            if (sortKey.value === 'is_active') {
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

        const accountWithBalance = props.accounts.filter(a => 
            selected.value.includes(a.id) && a.balance > 0
        )

        if(accountWithBalance.length > 0 ){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Tidak dapat menghapus rekening yang masih memiliki saldo',
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
                bulkDeleteForm.delete(route('accounts.bulk-destroy'), {
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
    const deleteAccount = (id) => {

        const account = props.accounts.find(a => a.id == id)

            if(account.balance > 0){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: `Rekening "${account.name}" masih memiliki saldo. Kosongkan saldo atau transafer ke rekening lain terlebih dahulu.`,
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

    function getCellType(key) {
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
        <AppLayout title="Rekening Page">

            <!-- PAGE HEADER -->
            
            <PageHeader :href="route('accounts.create')" :title="'Rekening'" :desc="'Kelola rekening kas, bank, dan e-wallet bisnis kamu'" :btnDesc="'Rekening'"/>

            <!-- SUMMARY CARDS -->
            <Deferred data="accounts">

                <template #fallback>
                    <AccountSummaryCardsSkeleton/>
                    <div class="rounded-xl bg-white dark:bg-gray-900 border
                            border-gray-200 dark:border-gray-800 shadow-sm">
                        <AccountTableSkeleton/>
                    </div>
                </template>

                <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">

                    <PrimaryCard :titleCard="'Total Balance'" :mainDesc="formatCurrency(summary.totalBalance)" :subDesc="summary.total" :subTitle="'Rekening'"/>

                    <!-- Kas -->   
                    <SecondaryCard :main-desc="summary.cash" :title-card="'Kas Tunai'" :sub-title="'Rekening'" :logoPath="'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'" :classLogo="'h-4 w-4 text-emerald-600'"/>

                    <!-- Bank -->
                    <SecondaryCard :main-desc="summary.bank" :title-card="'Bank'" :sub-title="'Rekening'" :logoPath="'M3 6l9-4 9 4M3 10h18M5 10v8m4-8v8m4-8v8m4-8v8 M19 10v8M3 18h18'" :classLogo="'h-4 w-4 text-blue-600'"/>

                    <!-- E-Wallet -->
                    <SecondaryCard :main-desc="summary.ewallet" :title-card="'E-Wallet'" :sub-title="'Rekening'" :logoPath="'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'" :classLogo="'h-4 w-4 text-violet-600'"/>

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
                                <tr v-if="filteredAccounts.length === 0">
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
                                    v-for="row in filteredAccounts"
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
                                            v-else-if="getCellType(col.key) === 'badge' && col.key === 'type'"
                                            :class="[
                                                'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                                accountTypes?.[row[col.key]]?.color ?? 'bg-gray-100 text-gray-700'
                                            ]"
                                        >
                                            {{ accountTypes?.[row[col.key]]?.label ?? row[col.key] }}
                                        </span>
                                        <span
                                            v-else-if="getCellType(col.key) === 'badge' && col.key === 'is_active'"
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
                                                :href="route('accounts.edit', row.id)"
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
                                                @click="deleteAccount(row.id)"
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
            </Deferred>
        </AppLayout>
    </template>