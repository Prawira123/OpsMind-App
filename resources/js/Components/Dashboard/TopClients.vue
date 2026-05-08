<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    topClients: { type: Array, default: () => [] },
})

const formatCurrency = (val) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val || 0)

const formatShort = (val) => {
    const n = parseFloat(val || 0)
    if (n >= 1_000_000_000) return `Rp ${(n / 1_000_000_000).toFixed(1)}M`
    if (n >= 1_000_000) return `Rp ${(n / 1_000_000).toFixed(1)}Jt`
    if (n >= 1_000) return `Rp ${(n / 1_000).toFixed(0)}Rb`
    return formatCurrency(n)
}
</script>

<template>
    <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Top Klien</h2>
            <Link :href="route('clients.index')" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                Lihat Semua →
            </Link>
        </div>

        <div class="divide-y divide-gray-50 dark:divide-gray-800/50">
            <div v-if="!topClients?.length" class="flex flex-col items-center gap-2 py-12 text-center">
                <div class="h-12 w-12 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-2xl">👥</div>
                <p class="text-sm text-gray-400">Belum ada data klien</p>
            </div>

            <div v-for="(client, index) in topClients" :key="client.client_id"
                 class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                <div :class="[
                    'h-7 w-7 rounded-lg flex items-center justify-center text-xs font-extrabold shrink-0',
                    index === 0 ? 'bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400' :
                    index === 1 ? 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400' :
                    index === 2 ? 'bg-orange-100 dark:bg-orange-900/40 text-orange-600 dark:text-orange-400' :
                    'bg-gray-50 dark:bg-gray-800/50 text-gray-400',
                ]">
                    {{ index + 1 }}
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                        {{ client.client?.name ?? 'Klien #' + client.client_id }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ formatShort(client.total_spent) }} total</p>
                </div>

                <span v-if="index === 0" class="text-base">🥇</span>
                <span v-else-if="index === 1" class="text-base">🥈</span>
                <span v-else-if="index === 2" class="text-base">🥉</span>
            </div>
        </div>

        <!-- Quick links -->
        <div class="border-t border-gray-100 dark:border-gray-800 p-4 space-y-2">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Akses Cepat</p>
            <div class="grid grid-cols-2 gap-2">
                <Link :href="route('invoices.create')" class="flex items-center gap-1.5 rounded-lg bg-gray-50 dark:bg-gray-800 px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Invoice
                </Link>
                <Link :href="route('reports.income')" class="flex items-center gap-1.5 rounded-lg bg-gray-50 dark:bg-gray-800 px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Laporan
                </Link>
                <Link :href="route('accounts.index')" class="flex items-center gap-1.5 rounded-lg bg-gray-50 dark:bg-gray-800 px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Rekening
                </Link>
                <Link :href="route('clients.index')" class="flex items-center gap-1.5 rounded-lg bg-gray-50 dark:bg-gray-800 px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Klien
                </Link>
            </div>
        </div>
    </div>
</template>
