<script setup>
const props = defineProps({
    totalBalance:    { type: Number, default: 0 },
    monthlyStats:    { type: Object, default: () => ({ income: 0, expense: 0 }) },
    invoicePending:  { type: Number, default: 0 },
})

const formatShort = (val) => {
    const n = parseFloat(val || 0)
    if (n >= 1_000_000_000) return `Rp ${(n / 1_000_000_000).toFixed(1)}M`
    if (n >= 1_000_000) return `Rp ${(n / 1_000_000).toFixed(1)}Jt`
    if (n >= 1_000) return `Rp ${(n / 1_000).toFixed(0)}Rb`
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n)
}
</script>

<template>
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <!-- Total Saldo -->
        <div class="col-span-2 lg:col-span-1 relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-700 p-5 shadow-xl shadow-indigo-500/20">
            <div class="absolute -right-6 -top-6 h-28 w-28 rounded-full bg-white/10 blur-sm"/>
            <div class="absolute -right-2 -bottom-8 h-20 w-20 rounded-full bg-white/5"/>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-3">
                    <div class="h-7 w-7 rounded-lg bg-white/20 flex items-center justify-center">
                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-white/70 uppercase tracking-wider">Total Saldo</p>
                </div>
                <p class="text-2xl font-extrabold text-white leading-none">{{ formatShort(totalBalance) }}</p>
                <p class="text-xs text-white/60 mt-1.5">Semua rekening aktif</p>
            </div>
        </div>

        <!-- Pemasukan Bulan Ini -->
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-3">
                <div class="h-7 w-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                    <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pemasukan</p>
            </div>
            <p class="text-xl font-extrabold text-gray-900 dark:text-white leading-none">{{ formatShort(monthlyStats?.income) }}</p>
            <p class="text-xs text-gray-400 mt-1.5">Bulan ini</p>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-500 rounded-b-2xl opacity-60"/>
        </div>

        <!-- Pengeluaran Bulan Ini -->
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-3">
                <div class="h-7 w-7 rounded-lg bg-rose-100 dark:bg-rose-900/40 flex items-center justify-center">
                    <svg class="h-4 w-4 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pengeluaran</p>
            </div>
            <p class="text-xl font-extrabold text-gray-900 dark:text-white leading-none">{{ formatShort(monthlyStats?.expense) }}</p>
            <p class="text-xs text-gray-400 mt-1.5">Bulan ini</p>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-rose-500 rounded-b-2xl opacity-60"/>
        </div>

        <!-- Invoice Pending -->
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-3">
                <div class="h-7 w-7 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center">
                    <svg class="h-4 w-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Invoice</p>
            </div>
            <p class="text-xl font-extrabold text-gray-900 dark:text-white leading-none">{{ invoicePending }}</p>
            <p class="text-xs text-gray-400 mt-1.5">Menunggu pembayaran</p>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-amber-500 rounded-b-2xl opacity-60"/>
        </div>
    </div>
</template>
