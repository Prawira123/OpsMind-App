<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
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
}[props.type] ?? 'Verifikasi OTP'))

// =========================================================
// COUNTDOWN TIMER
// =========================================================
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

// =========================================================
// HANYA IZINKAN ANGKA
// =========================================================
const onInput = (event) => {
    // Hapus semua karakter selain angka
    form.code = event.target.value.replace(/\D/g, '').slice(0, 6)
}

// =========================================================
// SUBMIT
// =========================================================
const submit = () => {
    form.post(route('otp.store', { type: props.type }), {
        onError: () => {
            form.code = ''
        }
    })
}

// =========================================================
// RESEND
// =========================================================
const resend = () => {
    if (!canResend.value || resending.value) return
    resending.value = true
    form.code = ''
    form.post(route('otp.resend', { type: props.type }), {
        preserveState: true,
        onSuccess: () => startCountdown(),
        onFinish:  () => { resending.value = false }
    })
}
</script>

<template>
    <GuestLayout>
        <Head title="OTP Code" />
        <div class="w-full">

            <!-- HEADER -->
            <div class="mb-6 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center
                            justify-center rounded-full bg-indigo-100">
                    <svg class="h-7 w-7 text-indigo-600" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor"
                         stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75
                                 m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25
                                 v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25
                                 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0
                                 002.25 2.25z" />
                    </svg>
                </div>

                <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ typeLabel }}
                </h1>

                <p class="flex flex-col items-center justify-center mt-2 text-sm text-gray-600 dark:text-gray-500">
                    Kode verifikasi telah dikirim ke
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {{ email }}
                    </span>
                </p>
            </div>

            <!-- FLASH SUCCESS -->
            <div
                v-if="$page.props.flash?.success"
                class="mb-4 rounded-lg border border-green-200
                       bg-green-50 p-3 text-center text-sm text-white"
            >
                ✓ {{ $page.props.flash.success }}
            </div>

            <form @submit.prevent="submit" class="space-y-5 flex flex-col gap-5" >

                <!-- INPUT KODE OTP -->
                <div class="flex flex-col gap-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-white">
                        Kode OTP
                    </label>

                    <input
                        v-model="form.code"
                        @input="onInput"
                        type="text"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        maxlength="6"
                        placeholder="Masukkan 6 digit kode"
                        autofocus
                        :class="[
                            'w-full rounded-lg border px-4 py-3 text-center',
                            'text-2xl font-bold tracking-widest text-gray-900',
                            'transition focus:outline-none focus:ring-2',
                            form.errors.code
                                ? 'border-red-400 bg-red-50 focus:ring-red-200'
                                : 'border-gray-300 bg-white focus:border-indigo-500 focus:ring-indigo-200'
                        ]"
                    />

                    <!-- Error -->
                    <p v-if="form.errors.code"
                       class="mt-1.5 text-sm text-red-600">
                        {{ form.errors.code }}
                    </p>

                    <!-- Info expired -->
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                        Kode berlaku selama 10 menit
                    </p>
                </div>

                <!-- TOMBOL VERIFIKASI -->
                <button
                    type="submit"
                    :disabled="form.code.length < 6 || form.processing"
                    class="w-full rounded-lg cursor_pointer bg-indigo-600 px-4 py-2.5
                           text-sm font-semibold text-white shadow-sm
                           transition hover:bg-indigo-700
                           focus:outline-none focus:ring-2
                           focus:ring-indigo-500 focus:ring-offset-2
                           disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <span v-if="form.processing"
                          class="flex items-center justify-center gap-2">
                        <svg class="h-4 w-4 animate-spin"
                             viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12"
                                    r="10" stroke="currentColor"
                                    stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0
                                     5.373 0 12h4z"/>
                        </svg>
                        Memverifikasi...
                    </span>
                    <span v-else>Verifikasi</span>
                </button>

                <!-- RESEND -->
                <div class="text-center text-sm text-gray-600">
                    Tidak menerima kode?

                    <!-- Timer -->
                    <span v-if="!canResend" class="ml-1">
                        Kirim ulang dalam
                        <span class="font-mono font-semibold text-indigo-600">
                            {{ countdownDisplay }}
                        </span>
                    </span>

                    <!-- Tombol resend -->
                    <button
                        v-else
                        type="button"
                        @click="resend"
                        :disabled="resending"
                        class="ml-1 font-semibold text-indigo-600
                               hover:underline disabled:opacity-50"
                    >
                        {{ resending ? 'Mengirim...' : 'Kirim ulang' }}
                    </button>
                </div>

                <!-- KEMBALI KE LOGIN -->
                <div class="text-center">
                    <a :href="route('login')"
                       class="text-sm text-gray-500 hover:text-gray-700
                              hover:underline">
                        ← Kembali ke login
                    </a>
                </div>

            </form>
        </div>
    </GuestLayout>
</template>