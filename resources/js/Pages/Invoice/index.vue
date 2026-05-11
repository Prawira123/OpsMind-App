<script setup>
import { ref, watch, defineAsyncComponent } from 'vue'
import { Link, router, useForm, Deferred } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import AppLayout from '@/Layouts/AppLayout.vue'
import PageHeader from '@/Components/Tenant/PageHeader.vue'
import BadgeSuccess from '@/Components/Partials/BadgeSuccess.vue'
import InvoiceSummaryCardsSkeleton from '@/Components/Invoice/Skeletons/InvoiceSummaryCardsSkeleton.vue'
import InvoiceTableSkeleton from '@/Components/Invoice/Skeletons/InvoiceTableSkeleton.vue'

const props = defineProps({
    invoices: Array,
    status:   String,
})

// =========================================================
// COMPONENTS
// =========================================================
const InvoiceSummaryCards = defineAsyncComponent(() => import('@/Components/Invoice/InvoiceSummaryCards.vue'))

// =========================================================
// STATUS CONFIG
// =========================================================
const statusConfig = {
    draft:     { label: 'Draft',     color: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' },
    send:      { label: 'Terkirim',  color: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' },
    paid:      { label: 'Lunas',     color: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' },
    overdue:   { label: 'Terlambat', color: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' },
    cancelled: { label: 'Dibatalkan',color: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' },
}

// =========================================================
// SEARCH & FILTER
// =========================================================
const search       = ref('')
const filterStatus = ref('all')

const filteredInvoices = (dataSrc) => {
    let data = [...(dataSrc ?? [])]

    if (search.value) {
        const q = search.value.toLowerCase()
        data = data.filter(inv =>
            (inv.number?.toLowerCase()       ?? '').includes(q) ||
            (inv.client?.name?.toLowerCase() ?? '').includes(q) ||
            (inv.notes?.toLowerCase()        ?? '').includes(q)
        )
    }

    if (filterStatus.value !== 'all') {
        data = data.filter(inv => inv.status === filterStatus.value)
    }

    return data.sort((a, b) => new Date(b.issue_date) - new Date(a.issue_date))
}

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
// SELECT
// =========================================================
const selected  = ref([])
const selectAll = ref(false)

const handleUpdateSelectAll = (val, dataSrc) => {
    selectAll.value = val
    selected.value  = val ? filteredInvoices(dataSrc).map(i => i.id) : []
}

const toggleSelect = (id, dataSrc) => {
    const idx = selected.value.indexOf(id)
    if (idx === -1) selected.value.push(id)
    else            selected.value.splice(idx, 1)
    selectAll.value = selected.value.length === filteredInvoices(dataSrc).length
}

// =========================================================
// ACTIONS
// =========================================================
const bulkDeleteForm = useForm({ ids: [] })

const confirmBulkDelete = () => {
    if (!selected.value.length) return

    bulkDeleteForm.ids = selected.value

    Swal.fire({
        title:              `Hapus ${selected.value.length} Invoice?`,
        text:               'Data yang dihapus tidak bisa dikembalikan!',
        icon:               'warning',
        showCancelButton:   true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor:  '#d33',
        confirmButtonText:  'Ya, Hapus!',
        cancelButtonText:   'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            bulkDeleteForm.delete(route('invoices.bulk-destroy'), {
                onSuccess: () => {
                    selected.value  = []
                    selectAll.value = false
                },
            })
        }
    })
}

const markAsPaid = (id) => {
    Swal.fire({
        title:              'Tandai Lunas?',
        text:               'Status invoice akan diubah menjadi Lunas.',
        icon:               'question',
        showCancelButton:   true,
        confirmButtonColor: '#10b981',
        cancelButtonColor:  '#6b7280',
        confirmButtonText:  'Ya, Tandai Lunas',
        cancelButtonText:   'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            router.patch(route('invoices.mark-paid', id))
        }
    })
}

const deleteInvoice = (id) => {
    Swal.fire({
        title:              'Hapus Invoice?',
        text:               'Data tidak bisa dikembalikan!',
        icon:               'warning',
        showCancelButton:   true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor:  '#6b7280',
        confirmButtonText:  'Ya, Hapus!',
        cancelButtonText:   'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('invoices.destroy', id))
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
    <AppLayout title="Invoice">

        <PageHeader
            :href="route('invoices.create')"
            title="Invoice"
            desc="Kelola semua invoice dan penagihan klien"
            btnDesc="Invoice"
        />

        <div v-if="showStatus">
            <BadgeSuccess :status="props.status"/>
        </div>

        <Deferred data="invoices">
            <template #fallback>
                <InvoiceSummaryCardsSkeleton />
                <div class="rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                    <InvoiceTableSkeleton />
                </div>
            </template>

            <div v-if="invoices">
                <!-- SUMMARY CARDS -->
                <InvoiceSummaryCards :invoices="invoices" />

                <!-- TABLE SECTION -->
                <div class="rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm">
                    <!-- Toolbar -->
                    <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-800">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <!-- Search -->
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                                </svg>
                                <input v-model="search" type="text" placeholder="Cari invoice atau klien..." class="w-full sm:w-64 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 pl-9 pr-4 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"/>
                            </div>
                            <!-- Filter status -->
                            <select v-model="filterStatus" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                                <option value="all">Semua Status</option>
                                <option v-for="(cfg, key) in statusConfig" :key="key" :value="key">{{ cfg.label }}</option>
                            </select>
                        </div>

                        <!-- Bulk Action -->
                        <div v-if="selected.length > 0">
                            <button @click="confirmBulkDelete" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white animate-fade-in transition hover:bg-red-500">
                                Hapus {{ selected.length }} Data
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">  
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-800">
                                    <th class="w-10 px-4 py-3 text-left">
                                        <input type="checkbox" :checked="selectAll" @change="handleUpdateSelectAll($event.target.checked, invoices)" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"/>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">No. Invoice</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Klien</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tgl. Invoice</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jatuh Tempo</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                <tr v-if="filteredInvoices(invoices).length === 0">
                                    <td colspan="8" class="px-4 py-16 text-center text-gray-500">Data tidak ditemukan</td>
                                </tr>
                                <tr v-for="inv in filteredInvoices(invoices)" :key="inv.id" :class="['transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50', selected.includes(inv.id) && 'bg-indigo-50/50 dark:bg-indigo-900/10']">
                                    <td class="px-4 py-3.5">
                                        <input type="checkbox" :checked="selected.includes(inv.id)" @change="toggleSelect(inv.id, invoices)" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"/>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <Link :href="route('invoices.show', inv.id)" class="text-sm font-mono font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">{{ inv.number }}</Link>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ inv.client?.name ?? '—' }}
                                        </p>
                                        <p class="text-xs text-gray-400 font-normal">
                                            {{ inv.client?.email }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ formatDate(inv.issue_date) }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <p :class="[
                                            'text-sm',
                                            inv.status === 'overdue' ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-600 dark:text-gray-400'
                                        ]">
                                            {{ formatDate(inv.due_date) }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3.5 text-right text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ formatCurrency(inv.total) }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', statusConfig[inv.status]?.color ?? 'bg-gray-100 text-gray-600']">{{ statusConfig[inv.status]?.label ?? inv.status }}</span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center justify-end gap-1">
                                            <button v-if="inv.status === 'sent' || inv.status === 'overdue'" @click="markAsPaid(inv.id)" title="Tandai Lunas" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </button>
                                            <Link :href="route('invoices.show', inv.id)" title="Detail" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057 -5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </Link>
                                            <button @click="deleteInvoice(inv.id)" title="Hapus" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </Deferred>
    </AppLayout>
</template>