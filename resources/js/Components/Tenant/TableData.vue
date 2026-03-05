<script setup>
import {Link} from '@inertiajs/vue3'

const props = defineProps({
    tableHeadData : {type : Array, required : true},
    filteredDatas : {type : Array, required : true},
    sortKey       : {type : String, required : true},
    sortDir       : {type : String, required : true},
    search        : {type : String, required : true},
    selected      : {type : Array, required : true},
    toggleSelect  : {type : Function, required : true},
    type          : {type : Object, required : false},
    deleteFunction: {type : Function, required : true},
    datas         : {type : Array, required : true},
    formatCurrency: {type : Function, required : false},
    toggleSort    : {type : Function, required : true},
    selectAll     : {type : Boolean, required : false},
    // Konfigurasi kolom khusus
    // Contoh: { balance: 'currency', type: 'badge', name: 'nameWithIcon' }
    columnTypes   : {type : Object, default: () => ({})},
    // Route untuk edit, contoh: 'accounts.edit' atau 'categories.edit'
    editRoute     : {type : String, required : true},
    // Route untuk delete
    deleteRoute   : {type : String, required : false},
})

const emit = defineEmits(['update:selectAll'])

function toggleSelectAll() {
    emit('update:selectAll', !props.selectAll)
}

// Helper: render nilai kolom berdasarkan tipe kolom
function getCellType(key) {
    return props.columnTypes[key] ?? 'text'
}
</script>

<template>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-800">
                    <th class="w-10 px-4 py-3">
                        <input
                            type="checkbox"
                            :checked="props.selectAll"
                            @change="toggleSelectAll"
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600
                                   focus:ring-indigo-500 cursor-pointer"
                        />
                    </th>

                    <th
                        v-for="col in props.tableHeadData"
                        :key="col.key"
                        @click="props.toggleSort(col.key)"
                        class="px-4 py-3 text-left text-xs font-semibold
                               text-gray-500 dark:text-gray-400 uppercase
                               tracking-wider cursor-pointer select-none
                               hover:text-gray-900 dark:hover:text-white transition-colors"
                    >
                        <div class="flex items-center gap-1">
                            {{ col.label }}
                            <span class="flex flex-col">
                                <svg class="h-3 w-3 transition-colors"
                                     :class="props.sortKey === col.key && props.sortDir === 'asc'
                                         ? 'text-indigo-600' : 'text-gray-300 dark:text-gray-600'"
                                     viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 4l8 8H4z"/>
                                </svg>
                                <svg class="h-3 w-3 transition-colors"
                                     :class="props.sortKey === col.key && props.sortDir === 'desc'
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
                <tr v-if="props.filteredDatas.length === 0">
                    <td :colspan="props.tableHeadData.length + 2" class="px-4 py-16 text-center">
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
                                    {{ props.search ? 'Data tidak ditemukan' : 'Belum ada data' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ props.search ? 'Coba kata kunci lain' : 'Tambahkan data pertama kamu' }}
                                </p>
                            </div>
                        </div>
                    </td>
                </tr>

                <!-- Data rows -->
                <tr
                    v-for="row in props.filteredDatas"
                    :key="row.id"
                    :class="[
                        'transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50',
                        props.selected.includes(row.id) && 'bg-indigo-50/50 dark:bg-indigo-900/10',
                    ]"
                >
                    <!-- Checkbox -->
                    <td class="px-4 py-3.5">
                        <input
                            type="checkbox"
                            :checked="props.selected.includes(row.id)"
                            @change="props.toggleSelect(row.id)"
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600
                                   focus:ring-indigo-500 cursor-pointer"
                        />
                    </td>

                    <!-- Dynamic columns -->
                    <td
                        v-for="col in props.tableHeadData"
                        :key="col.key"
                        class="px-4 py-3.5"
                    >
                        <!-- Tipe: nameWithIcon — nama + icon berdasarkan type -->
                        <div v-if="getCellType(col.key) === 'nameWithIcon'"
                             class="flex items-center gap-3">
                            <div :class="[
                                'h-9 w-9 rounded-lg flex items-center justify-center shrink-0',
                                row.type === 'cash'    && 'bg-emerald-100 dark:bg-emerald-900/30',
                                row.type === 'bank'    && 'bg-blue-100 dark:bg-blue-900/30',
                                row.type === 'ewallet' && 'bg-violet-100 dark:bg-violet-900/30',
                                row.type === 'income'  && 'bg-emerald-100 dark:bg-emerald-900/30',
                                row.type === 'expense' && 'bg-red-100 dark:bg-red-900/30',
                                (!row.type || row.type === 'other') && 'bg-gray-100 dark:bg-gray-800',
                            ]">
                                <svg class="h-4 w-4"
                                     :class="
                                         row.type === 'cash'    ? 'text-emerald-600' :
                                         row.type === 'bank'    ? 'text-blue-600' :
                                         row.type === 'ewallet' ? 'text-violet-600' :
                                         row.type === 'income'  ? 'text-emerald-600' :
                                         row.type === 'expense' ? 'text-red-600' :
                                         'text-gray-500'
                                     "
                                     fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ row[col.key] }}
                            </p>
                        </div>

                        <!-- Tipe: badge — menggunakan object `type` untuk label & warna -->
                        <span
                            v-else-if="getCellType(col.key) === 'badge'"
                            :class="[
                                'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                props.type?.[row[col.key]]?.color ?? 'bg-gray-100 text-gray-700'
                            ]"
                        >
                            {{ props.type?.[row[col.key]]?.label ?? row[col.key] }}
                        </span>

                        <!-- Tipe: currency -->
                        <p
                            v-else-if="getCellType(col.key) === 'currency'"
                            class="text-sm font-semibold text-gray-900 dark:text-white"
                        >
                            {{ props.formatCurrency?.(row[col.key]) ?? row[col.key] }}
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
                                :href="route(props.editRoute, row.id)"
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
                                @click="props.deleteFunction(row.id)"
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
    <div class="flex items-center justify-between px-4 py-3 border-t
                border-gray-200 dark:border-gray-800">
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Menampilkan
            <span class="font-medium text-gray-900 dark:text-white">
                {{ props.filteredDatas.length }}
            </span>
            dari
            <span class="font-medium text-gray-900 dark:text-white">
                {{ props.datas?.length ?? 0 }}
            </span>
            data
        </p>

        <p v-if="props.selected.length > 0"
           class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">
            {{ props.selected.length }} dipilih
        </p>
    </div>
</template>