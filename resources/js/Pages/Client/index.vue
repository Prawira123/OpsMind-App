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

    // PROPS — dikirim dari AccountController::index()
    const props = defineProps({
        clients: Array,
        status : String,
    })

    // TIPE REKENING — label & style per tipe
    const tableHead = [
            { key: 'name',           label: 'Nama Client' },
            { key: 'email',           label: 'Email' },
            { key: 'phone',      label: 'No Telp' },
            { key: 'address', label: 'Alamat' },
            { key: 'company',        label: 'Perusahaan' },
    ]
    // SEARCH
    const search = ref('')

    // FILTER TIP

    const columnTypes = {
        phone : 'mono'
    }

    // SORT
    const sortKey = ref('name')
    const sortDir = ref('asc')

    // FILTERED & SORTED DATA
    const filteredClients = computed(() => {
        let data = [...(props.clients ?? [])]

        // Filter search
        if (search.value) {
            const q = search.value.toLowerCase()
            data = data.filter(a =>
                (a.name?.toLowerCase() ?? '').includes(q) ||
                (a.company?.toLowerCase() ?? '').includes(q) ||
                (a.email?.toLowerCase() ?? '').includes(q)
            )
        }
        console.log(data);

    
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
    // const summary = computed(() => {
    //     const accounts = props.accounts ?? []
    //     return {
    //         total:   accounts.length,
    //         cash:    accounts.filter(a => a.type === 'cash').length,
    //         bank:    accounts.filter(a => a.type === 'bank').length,
    //         ewallet: accounts.filter(a => a.type === 'ewallet').length,
    //         totalBalance: accounts.reduce((sum, a) => sum + (parseFloat(a.balance) || 0), 0),
    //     }
    // })

    // BULK DELETE
    const selected    = ref([])
    const selectAll   = ref(false)


    const toggleSelectAll = (value) => {

        selectAll.value = value
        console.log(selectAll.value)

        if (selectAll.value) {
            selected.value = filteredClients.value.map(a => a.id)
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
        selectAll.value = selected.value.length === filteredClients.value.length
    }

    const bulkDeleteForm = useForm({ ids: [] })

    const confirmBulkDelete = () => {
        if (!selected.value.length) return

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
                bulkDeleteForm.delete(route('clients.bulk-destroy'), {
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
    const deleteClient = (id) => {
        
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
                router.delete(route('clients.destroy', id))
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

    function getCellType(key) {
        return columnTypes[key] ?? 'text'
    }

    const showStatus = ref(false)

    watch(() => props.status, (val) => {
        if (val) {
            showStatus.value = true
            setTimeout(() => {
                showStatus.value = false
            }, 3000)
        }
    }, {immediate: true})

    </script>

    <template>
        <AppLayout title="Client Page">

            <!-- PAGE HEADER -->
            
            <PageHeader :href="route('clients.create')" :title="'Client'" :desc="'Kelola Daftar Client penting kamu'" :btnDesc="'Client'"/>
            
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
                            <SearchBar v-model="search" :class="'w-full sm:w-64 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 pl-9 pr-4 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition'" :type="'text'" :placeholder="'Cari Client...'"/>
                        </div>
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
                            <tr v-if="filteredClients.length === 0">
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
                                v-for="row in filteredClients"
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
                                            :href="route('clients.edit', row.id)"
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
                                            @click="deleteClient(row.id)"
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