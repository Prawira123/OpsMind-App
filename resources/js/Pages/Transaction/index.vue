<script setup>
import { ref, watch } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import AppLayout from '@/Layouts/AppLayout.vue'
import PageHeader from '@/Components/Tenant/PageHeader.vue'
import LogoCard from '@/Components/Partials/LogoCard.vue'
import SearchBar from '@/Components/Partials/SearchBar.vue'
import ButtonDelete from '@/Components/Partials/ButtonDelete.vue'
import BadgeSuccess from '@/Components/Partials/BadgeSuccess.vue'
import TransactionTable from '@/Components/Transaction/TransactionTable.vue'
import TransactionSummaryCards from '@/Components/Transaction/TransactionSummaryCards.vue'
import TransactionTableSkeleton from '@/Components/Transaction/TransactionTableSkeleton.vue'
import TransactionSummaryCardsSkeleton from '@/Components/Transaction/TransactionSummaryCardsSkeleton.vue'

// =========================================================
// PROPS
// =========================================================
const props = defineProps({
    transactions: Array,
    status:       String,
    summary:      Object,
})

// =========================================================
// SEARCH & FILTER (moved from TransactionTable for defer)
// =========================================================
const search     = ref('')
const filterType = ref('all')

// =========================================================
// SELECT
// =========================================================
const selected  = ref([])
const selectAll = ref(false)

const toggleSelectAll = (value) => {
    selectAll.value = value
    selected.value  = value ? [...(props.transactions ?? [])].map(t => t.id) : []
}

const toggleSelect = (id) => {
    const idx = selected.value.indexOf(id)
    const newSelected = [...selected.value]
    if (idx === -1) newSelected.push(id)
    else            newSelected.splice(idx, 1)
    selected.value = newSelected
    selectAll.value = newSelected.length === (props.transactions ?? []).length
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
        <TransactionSummaryCardsSkeleton v-if="!summary" />
        <TransactionSummaryCards v-else :summary="summary" />

        <!-- TABEL SECTION -->
        <div class="rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm">

            <!-- Toolbar -->
            <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-800">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

                    <!-- Search -->
                    <div class="relative">
                        <LogoCard
                            :class="'absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400'"
                            :logo-path="'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 01 14 0z'"
                        />
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
                        class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
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

            <!-- Table Component -->
            <TransactionTableSkeleton v-if="!transactions" />
            <TransactionTable
                v-else
                :transactions="transactions"
                :selectAll="selectAll"
                :selected="selected"
                :search="search"
                :filterType="filterType"
                @update:selectAll="toggleSelectAll"
                @update:selected="toggleSelect"
            />
        </div>

    </AppLayout>
</template>
