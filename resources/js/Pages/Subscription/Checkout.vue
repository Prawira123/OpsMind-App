<script setup>
import { onMounted, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const props = defineProps({
    plan: Object,
    snap_token: String,
    midtrans_client_key: String
})

const isLoading = ref(true)
const error = ref(null)

const payWithMidtrans = () => {
    if (!props.snap_token) {
        error.value = 'Snap token tidak ditemukan. Silakan coba lagi.'
        return
    }

    if (!window.snap) {
        error.value = 'Midtrans Snap tidak dimuat. Periksa koneksi internet Anda.'
        return
    }

    window.snap.pay(props.snap_token, {
        onSuccess: function(result) {
            Swal.fire({
                title: 'Pembayaran Berhasil!',
                text: 'Terima kasih, pembayaran untuk paket ' + props.plan.name + ' telah berhasil.',
                icon: 'success',
                confirmButtonText: 'Lihat Langganan',
                confirmButtonColor: '#4F46E5',
                allowOutsideClick: false,
            }).then(() => {
                router.visit('/SubscriptionPlan')
            })
        },
        onPending: function(result) {
            Swal.fire({
                title: 'Pembayaran Pending',
                text: 'Pembayaran Anda sedang diproses. Kami akan menginformasikan setelah pembayaran dikonfirmasi.',
                icon: 'info',
                confirmButtonText: 'Lihat Langganan',
                confirmButtonColor: '#4F46E5',
                allowOutsideClick: false,
            }).then(() => {
                router.visit('/SubscriptionPlan')
            })
        },
        onError: function(result) {
            Swal.fire({
                title: 'Pembayaran Gagal',
                text: 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.',
                icon: 'error',
                confirmButtonText: 'Coba Lagi',
                confirmButtonColor: '#4F46E5',
            })
        },
        onClose: function() {
            Swal.fire({
                title: 'Pembayaran Dibatalkan',
                text: 'Anda menutup popup pembayaran tanpa menyelesaikan pembayaran.',
                icon: 'warning',
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#4F46E5',
            })
        }
    })
}

onMounted(() => {
    if (window.snap) {
        isLoading.value = false
        setTimeout(() => {
            payWithMidtrans()
        }, 500)
    } else {
        error.value = 'Midtrans Snap tidak dimuat. Pastikan script Midtrans dimuat dengan benar.'
        isLoading.value = false
    }
})
</script>

<template>
    <Head title="Checkout" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">

        <!-- Back to Subscription Link -->
        <div class="max-w-6xl mx-auto px-4 pt-6">
            <Link
                href="/SubscriptionPlan"
                class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Langganan
            </Link>
        </div>

        <!-- Header -->
        <div class="pt-14 pb-10 text-center px-4">
            <div class="flex items-center justify-center gap-2 mb-10">
                <div class="h-8 w-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span class="text-xl font-bold text-gray-900 dark:text-white">
                    OpsMind
                </span>
            </div>

            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-3">
                Menyelesaikan Pembayaran
            </h1>
            <p class="text-lg text-gray-500 dark:text-gray-400 max-w-xl mx-auto">
                Paket: <strong>{{ plan.name }}</strong> -
                Rp {{ new Intl.NumberFormat('id-ID').format(plan.price) }}
            </p>
        </div>

        <!-- Payment Section -->
        <div class="max-w-6xl mx-auto px-4 pb-20">
            <div class="max-w-md mx-auto">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6 md:p-8">

                    <!-- Plan Details Card -->
                    <div class="bg-indigo-50 dark:bg-indigo-900/30 rounded-xl p-4 mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ plan.name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Langganan Bulanan</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(plan.price) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div v-if="isLoading" class="py-8 text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">Memuat halaman pembayaran...</p>
                    </div>

                    <!-- Error State -->
                    <div v-else-if="error" class="py-4">
                        <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-4">
                            <p class="text-red-800 dark:text-red-200">{{ error }}</p>
                        </div>
                        <button
                            @click="payWithMidtrans"
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200"
                        >
                            Coba Bayar Lagi
                        </button>
                    </div>

                    <!-- Ready State -->
                    <div v-else class="text-center">
                        <div class="mb-6">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/50 mb-4">
                                <svg class="h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-21 5.25h19.5m-19.5 0a2.25 2.25 0 01-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25m19.5 0a2.25 2.25 0 002.25-2.25H18.75a2.25 2.25 0 00-2.25-2.25M5.25 16.5h13.5m-13.5 0a2.25 2.25 0 01-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25M18.75 16.5a2.25 2.25 0 002.25-2.25H18.75a2.25 2.25 0 00-2.25-2.25"/>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                                Popup pembayaran Midtrans akan segera muncul
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                Jika popup tidak muncul otomatis, klik tombol di bawah ini
                            </p>
                        </div>
                        <button
                            @click="payWithMidtrans"
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200"
                        >
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 16h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Bayar Sekarang
                        </button>
                    </div>

                    <div class="mt-6 text-center">
                        <Link href="/SubscriptionPlan" class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                            Batal
                        </Link>
                    </div>
                </div>

                <!-- Secure Payment Note -->
                <div class="mt-6 flex items-center justify-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Pembayaran Aman & Terenkripsi
                </div>
            </div>
        </div>
    </div>
</template>
