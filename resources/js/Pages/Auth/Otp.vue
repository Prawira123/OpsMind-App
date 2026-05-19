<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'

const props = defineProps({
    type:  String,
    email: String,
})

const form = useForm({
    code: '',
    type: props.type,
})

const typeLabel = computed(() => ({
    'email_verification' : 'Verifikasi Email',
    'forgot_password'    : 'Reset Password',
    'two_factor'         : 'Login Dua Faktor',
    'sensitive_action'   : 'Konfirmasi Aksi',
    'reset_password'   : 'Reset Password',
}[props.type] ?? 'Verifikasi OTP'))

// COUNTDOWN TIMER
const countdown = ref(60)
const canResend  = computed(() => countdown.value === 0)
const resending  = ref(false)
let countdownInterval = null

const countdownDisplay = computed(() => {
    const minutes = Math.floor(countdown.value / 60)
    const seconds = countdown.value % 60
    return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
})

const startCountdown = () => {
    countdown.value = 60
    if (countdownInterval) clearInterval(countdownInterval)
    countdownInterval = setInterval(() => {
        if (countdown.value > 0) {
            countdown.value--
        } else {
            clearInterval(countdownInterval)
        }
    }, 1000)
}

onMounted(() => startCountdown())
onUnmounted(() => {
    if (countdownInterval) clearInterval(countdownInterval)
})

// HANYA IZINKAN ANGKA
const onInput = (event) => {
    form.code = event.target.value.replace(/\D/g, '').slice(0, 6)
}

// SUBMIT
const submit = () => {
    if(props.type === 'forgot_password'){
        form.post(route('otp.store.guest', { type: props.type }), {
            onError: () => { form.code = '' }
        })
    }else if(props.type === 'two_factor'){
        form.post(route('otp.store.guest', { type: props.type }), {
            onError: () => { form.code = '' }
        })
    }else{
        form.post(route('otp.store', { type: props.type }), {
            onError: () => { form.code = '' }
        })
    }
}

// RESEND
const resend = () => {
    if (!canResend.value || resending.value) return
    resending.value = true
    form.code = ''
    if(props.type === 'forgot_password'){
        form.post(route('otp.resend.guest', { type: props.type }), {
            preserveState: true,
            onSuccess: () => startCountdown(),
            onFinish:  () => { resending.value = false }
        })
    }else if(props.type === 'two_factor'){
        form.post(route('otp.resend.guest', { type: props.type }), {
            preserveState: true,
            onSuccess: () => startCountdown(),
            onFinish:  () => { resending.value = false }
        })
    }else{
        form.post(route('otp.resend', { type: props.type }), {
            preserveState: true,
            onSuccess: () => startCountdown(),
            onFinish:  () => { resending.value = false }
        })
    }
}
</script>

<template>
    <GuestLayout footerText="Enter the verification code sent to your email">
        <Head title="OTP Code" />

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Header -->
            <div class="text-center space-y-3 mb-4">
                <div class="mx-auto h-14 w-14 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ typeLabel }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Kode verifikasi telah dikirim ke<br>
                    <span class="font-semibold text-gray-700 dark:text-gray-200">{{ email }}</span>
                </p>
            </div>

            <!-- Flash Success -->
            <div v-if="$page.props.flash?.success" class="p-4 rounded-lg bg-green-50 border border-green-200">
                <p class="text-sm text-center font-medium text-green-700 dark:text-green-400">
                    ✓ {{ $page.props.flash.success }}
                </p>
            </div>

            <!-- OTP Input -->
            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode OTP</label>
                <input
                    v-model="form.code"
                    @input="onInput"
                    type="text"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    maxlength="6"
                    placeholder="123456"
                    autofocus
                    :class="[
                        'w-full rounded-xl border-2 px-4 py-4 text-center text-2xl text-white font-bold tracking-widest',
                        'transition-all duration-200 focus:outline-none',
                        form.errors.code
                            ? 'border-red-400 bg-red-50/50 focus:border-red-500 focus:ring-2 focus:ring-red-200'
                            : 'border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50'
                    ]"
                />
                <div v-if="form.errors.code" class="flex items-center gap-2 text-sm text-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ form.errors.code }}
                </div>
                <p class="text-xs text-gray-400 dark:text-white">Kode berlaku selama 10 menit</p>
            </div>

            <!-- Verify Button -->
            <div class="pt-2">
                <button
                    type="submit"
                    :disabled="form.code.length < 6 || form.processing"
                    class="w-full justify-center py-3 rounded-xl text-base font-semibold bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 border-0 shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                >
                    <span v-if="form.processing" class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5 animate-spin text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span class="text-white">Memverifikasi...</span>
                    </span>
                    <span v-else class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-white">Verifikasi</span>
                    </span>
                </button>
            </div>

            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200 dark:border-gray-600"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white dark:bg-gray-800 text-gray-500">atau</span>
                </div>
            </div>

            <!-- Resend -->
            <div class="text-center space-y-3">
                <p v-if="!canResend" class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="hidden sm:inline">Belum menerima kode?</span>
                    <span class="ml-1">
                        Kirim ulang dalam <span class="font-mono font-semibold text-indigo-600">{{ countdownDisplay }}</span>
                    </span>
                </p>
                <button
                    v-else
                    type="button"
                    @click="resend"
                    :disabled="resending"
                    class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors disabled:opacity-50 inline-flex items-center gap-1"
                >
                    <svg v-if="resending" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    {{ resending ? 'Mengirim...' : 'Kirim Ulang Kode' }}
                </button>
            </div>

            <!-- Back to login -->
            <div class="pt-4 text-center">
                <Link :href="route('login')" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Login
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
