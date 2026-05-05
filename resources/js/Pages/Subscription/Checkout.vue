<script setup>
import { onMounted, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    plan: Object,
    snap_token: String,
    midtrans_client_key: String
});

const isLoading = ref(true);
const error = ref(null);

const payWithMidtrans = () => {
    if (!props.snap_token) {
        error.value = 'Snap token tidak ditemukan. Silakan coba lagi.';
        return;
    }

    if (!window.snap) {
        error.value = 'Midtrans Snap tidak dimuat. Periksa koneksi internet Anda.';
        return;
    }

    window.snap.pay(props.snap_token, {
        onSuccess: function(result) {
            console.log(result);
            window.location.href = '/dashboard';
        },
        onPending: function(result) {
            console.log(result);
            window.location.href = '/dashboard';
        },
        onError: function(result) {
            console.log(result);
            error.value = 'Pembayaran gagal. Silakan coba lagi.';
        },
        onClose: function() {
            error.value = 'Anda menutup popup pembayaran tanpa menyelesaikan pembayaran.';
        }
    });
};

onMounted(() => {
    if (window.snap) {
        isLoading.value = false;
        setTimeout(() => {
            payWithMidtrans();
        }, 500);
    } else {
        error.value = 'Midtrans Snap tidak dimuat. Pastikan script Midtrans dimuat dengan benar.';
        isLoading.value = false;
    }
});
</script>

<template>
    <Head title="Checkout" />

    <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Menyelesaikan Pembayaran
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Silakan selesaikan pembayaran untuk paket <strong>{{ plan.name }}</strong>
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 text-center">
                <!-- Loading State -->
                <div v-if="isLoading" class="py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
                    <p class="mt-4 text-gray-600">Memuat halaman pembayaran...</p>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="py-4">
                    <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
                        <p class="text-red-800">{{ error }}</p>
                    </div>
                    <button
                        @click="payWithMidtrans"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mb-2"
                    >
                        Coba Bayar Lagi
                    </button>
                </div>

                <!-- Success/Ready State -->
                <div v-else>
                    <p class="mb-4">Jika popup Midtrans tidak muncul otomatis, klik tombol di bawah ini:</p>
                    <button
                        @click="payWithMidtrans"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Bayar Sekarang
                    </button>
                </div>

                <div class="mt-4">
                    <Link href="/SubscriptionPlan" class="text-sm text-indigo-600 hover:text-indigo-500">
                        Batal
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
