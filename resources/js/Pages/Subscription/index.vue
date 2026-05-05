<script setup>
import { ref, computed, watch } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import dayjs from 'dayjs'

const props = defineProps({
    plans: { type: Array, default: () => [] },
    currentSubscription: { type: Object, default: null }
})

console.log(props.currentSubscription)

// =========================================================
// BILLING TOGGLE
// =========================================================
const billing = ref('monthly')

const monthlyPlans = computed(() =>
    props.plans.filter(p => p.billing_cycle === 'monthly')
)
const yearlyPlans = computed(() =>
    props.plans.filter(p => p.billing_cycle === 'yearly')
)
const displayPlans = computed(() =>
    billing.value === 'monthly' ? monthlyPlans.value : yearlyPlans.value
)

// =========================================================
// FORMAT
// =========================================================
const formatPrice = (price) => {
    if (parseFloat(price) === 0) return 'Gratis'
    return new Intl.NumberFormat('id-ID', {
        style:    'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price)
}

const yearlyDiscount = (planName) => {
    const monthly = monthlyPlans.value.find(p => p.name === planName)
    const yearly  = yearlyPlans.value.find(p => p.name === planName)
    if (!monthly || !yearly || parseFloat(monthly.price) === 0) return null
    const saved = (parseFloat(monthly.price) * 12) - parseFloat(yearly.price)
    return formatPrice(saved)
}

// =========================================================
// PLAN CONFIG — warna & fitur highlight per plan
// =========================================================
const planConfig = {
    Starter: {
        badge:       null,
        gradient:    'from-slate-50 to-slate-100 dark:from-slate-800/50 dark:to-slate-900',
        border:      'border-slate-200 dark:border-slate-700',
        btnClass:    'bg-slate-800 dark:bg-slate-200 text-white dark:text-slate-900 hover:bg-slate-700 dark:hover:bg-white',
        accentColor: 'text-slate-600 dark:text-slate-400',
        iconBg:      'bg-slate-100 dark:bg-slate-800',
        highlight:   false,
    },
    Pro: {
        badge:       'Paling Populer',
        gradient:    'from-indigo-600 to-violet-600',
        border:      'border-indigo-500',
        btnClass:    'bg-white text-indigo-600 hover:bg-indigo-50 font-bold',
        accentColor: 'text-indigo-200',
        iconBg:      'bg-white/20',
        highlight:   true,
    },
    Enterprise: {
        badge:       'Terlengkap',
        gradient:    'from-amber-500 to-orange-500',
        border:      'border-amber-400',
        btnClass:    'bg-white text-amber-600 hover:bg-amber-50 font-bold',
        accentColor: 'text-amber-200',
        iconBg:      'bg-white/20',
        highlight:   true,
    },
}

// Fitur highlight per plan
const planHighlights = {
    Starter: [
        { icon: '📊', text: 'Dashboard & Laporan Dasar' },
        { icon: '🏦', text: 'Kelola Rekening' },
        { icon: '🏷️', text: 'Kategori Transaksi' },
        { icon: '💳', text: 'Transaksi s/d 50/bulan' },
        { icon: '👤', text: '1 Pengguna' },
        { icon: '👥', text: 'Maks. 5 Klien' },
    ],
    Pro: [
        { icon: '✅', text: 'Semua fitur Starter' },
        { icon: '📄', text: 'Invoice & Penagihan' },
        { icon: '👥', text: 'Kelola Klien (maks. 50)' },
        { icon: '📈', text: 'Laporan Lanjutan & Export' },
        { icon: '🤖', text: 'AI Assistant' },
        { icon: '👨‍💼', text: 'Tim s/d 5 anggota' },
        { icon: '📋', text: 'Chart of Accounts' },
    ],
    Enterprise: [
        { icon: '✅', text: 'Semua fitur Pro' },
        { icon: '♾️', text: 'Transaksi & Klien Unlimited' },
        { icon: '👥', text: 'Anggota Tim Unlimited' },
        { icon: '🔍', text: 'Audit Log Lengkap' },
        { icon: '📊', text: 'Custom Reports' },
        { icon: '🔌', text: 'Akses API' },
        { icon: '⭐', text: 'Priority Support' },
    ],
}

// =========================================================
// MODAL STATE
// =========================================================
const showModal = ref(false)
const pendingPlan = ref(null)

// =========================================================
// FORM
// =========================================================
const form = useForm({
    plan_id: null,
    paid_at: dayjs().format('YYYY-MM-DD HH:mm:ss'),
    ends_at: null,
    expire_current: false,
})

watch(billing, (val) => {
    form.ends_at = val === 'monthly'
        ? dayjs().add(1, 'month').format('YYYY-MM-DD HH:mm:ss')
        : dayjs().add(1, 'year').format('YYYY-MM-DD HH:mm:ss')
}, { immediate: true })

// =========================================================
// CURRENT SUBSCRIPTION HELPERS
// =========================================================
const isCurrentPlan = (plan) => {
    return props.currentSubscription &&
        props.currentSubscription.status === 'active' &&
        props.currentSubscription.plan &&
        props.currentSubscription.plan.id === plan.id
}

const disableFreePlan = computed(() => {
    if (!props.currentSubscription) return false
    if (props.currentSubscription.status !== 'active') return false
    if (!props.currentSubscription.plan) return false
    // If current is paid, disable free button
    return parseFloat(props.currentSubscription.plan.price) > 0
})

// =========================================================
// SELECT PLAN
// =========================================================
const selectPlan = (plan) => {
    // If clicking current active plan, do nothing
    if (isCurrentPlan(plan)) return

    // If there is an active subscription and selecting a different plan, show confirmation
    if (props.currentSubscription && props.currentSubscription.status === 'active') {
        pendingPlan.value = plan
        Swal.fire({
            title: 'Konfirmasi Perubahan Paket',
            text: `Anda akan beralih ke paket ${plan.name}. Langganan saat ini (${props.currentSubscription.plan.name}) akan diakhiri dan statusnya akan menjadi expired. Setelah pembayaran berhasil, langganan baru akan aktif. Apakah Anda yakin ingin melanjutkan?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            result.isConfirmed ? confirmSwitch() : cancelSwitch()
        })

        return
    }

    submitPlan(plan)
}

const submitPlan = (plan) => {
    form.plan_id = plan.id
    form.expire_current = false // default
    form.post(route('subs.select'))
}

const confirmSwitch = () => {
    // showModal.value = false
    if (pendingPlan.value) {
        form.plan_id = pendingPlan.value.id
        form.expire_current = true
        form.post(route('subs.select'))
        pendingPlan.value = null
    }
}

const cancelSwitch = () => {
    // showModal.value = false
    pendingPlan.value = null
}

const getConfig = (planName) => planConfig[planName] ?? planConfig.Starter
const getHighlights = (planName) => planHighlights[planName] ?? []
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">

        <!-- Back to Dashboard Button -->
        <div class="max-w-6xl mx-auto px-4 pt-6">
            <Link
                href="/dashboard"
                class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Dashboard
            </Link>
        </div>

        <!-- Current Subscription Info -->
        <div v-if="currentSubscription && currentSubscription.status === 'active'" class="max-w-6xl mx-auto px-4 pt-6">
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Langganan Saat Ini
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Paket: <span class="font-medium text-gray-900 dark:text-white">{{ currentSubscription?.plan?.name || 'Tidak ditemukan' }}</span>
                            <span class="ml-2 inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:text-emerald-400">
                                Aktif
                            </span>
                        </p>
                        <p class="mt-1 text-xs text-gray-400">
                            Berakhir: {{ new Date(currentSubscription.ends_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                        </p>
                    </div>
                    <div class="text-left md:text-right">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ formatPrice(currentSubscription?.plan?.price || 0) }}
                            <span class="text-sm font-normal text-gray-500">/{{ currentSubscription?.plan?.billing_cycle === 'monthly' ? 'bulan' : 'tahun' }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================ -->
        <!-- HEADER -->
        <!-- ================================================ -->
        <div class="pt-14 pb-10 text-center px-4">

            <!-- Logo / Brand -->
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
                Pilih Paket yang Tepat
            </h1>
            <p class="text-lg text-gray-500 dark:text-gray-400 max-w-xl mx-auto">
                Mulai gratis, upgrade kapanpun bisnis kamu berkembang.
                Tidak ada biaya tersembunyi.
            </p>

            <!-- Billing Toggle -->
            <div class="mt-8 inline-flex items-center gap-3 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-1.5 shadow-sm">
                <button
                    @click="billing = 'monthly'"
                    :class="[
                        'rounded-xl px-5 py-2 text-sm font-semibold transition-all duration-200',
                        billing === 'monthly'
                            ? 'bg-indigo-600 text-white shadow-sm'
                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white',
                    ]"
                >
                    Bulanan
                </button>
                <button
                    @click="billing = 'yearly'"
                    :class="[
                        'flex items-center gap-2 rounded-xl px-5 py-2 text-sm font-semibold transition-all duration-200',
                        billing === 'yearly'
                            ? 'bg-indigo-600 text-white shadow-sm'
                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white',
                    ]"
                >
                    Tahunan
                    <span :class="[
                        'rounded-full px-2 py-0.5 text-xs font-bold transition-colors',
                        billing === 'yearly'
                            ? 'bg-white/20 text-white'
                            : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                    ]">
                        Hemat 2 bulan
                    </span>
                </button>
            </div>
        </div>

        <!-- ================================================ -->
        <!-- PLAN CARDS -->
        <!-- ================================================ -->
        <div class="max-w-6xl mx-auto px-4 pb-20">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

                <div
                    v-for="plan in displayPlans"
                    :key="plan.id"
                    :class="[
                        'relative rounded-2xl overflow-hidden transition-all duration-300',
                        'hover:-translate-y-1 hover:shadow-2xl',
                        getConfig(plan.name).highlight
                            ? 'ring-2 ring-offset-2 ring-offset-gray-50 dark:ring-offset-gray-950 shadow-xl'
                            : 'border shadow-sm',
                        getConfig(plan.name).highlight
                            ? (plan.name === 'Pro' ? 'ring-indigo-500' : 'ring-amber-400')
                            : getConfig(plan.name).border,
                    ]"
                >
                    <!-- Highlighted plan — gradient header -->
                    <div
                        v-if="getConfig(plan.name).highlight"
                        :class="[
                            'bg-gradient-to-br p-6 pb-8',
                            getConfig(plan.name).gradient,
                        ]"
                    >
                        <!-- Badge -->
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center rounded-full bg-white/20 backdrop-blur-sm px-3 py-1 text-xs font-bold text-white">
                                ⭐ {{ getConfig(plan.name).badge }}
                            </span>
                            <!-- Yearly saving badge -->
                            <span
                                v-if="billing === 'yearly' && yearlyDiscount(plan.name)"
                                class="inline-flex items-center rounded-full bg-white/20 px-2.5 py-1 text-xs font-semibold text-white"
                            >
                                Hemat {{ yearlyDiscount(plan.name) }}
                            </span>
                        </div>

                        <h2 class="text-2xl font-extrabold text-white mb-1">
                            {{ plan.name }}
                        </h2>

                        <div class="flex items-end gap-1 mt-3">
                            <span class="text-4xl font-black text-white">
                                {{ formatPrice(plan.price) }}
                            </span>
                            <span v-if="parseFloat(plan.price) > 0" :class="['text-sm pb-1.5', getConfig(plan.name).accentColor]">
                                /{{ billing === 'monthly' ? 'bulan' : 'tahun' }}
                            </span>
                        </div>

                        <p v-if="billing === 'yearly' && parseFloat(plan.price) > 0" class="text-xs text-white/70 mt-1">
                            ~{{ formatPrice(parseFloat(plan.price) / 12) }}/bulan
                        </p>
                    </div>

                    <!-- Regular plan — plain header -->
                    <div
                        v-else
                        :class="[
                            'bg-gradient-to-br p-6 pb-8',
                            getConfig(plan.name).gradient,
                        ]"
                    >
                        <div class="flex items-center justify-between mb-4 h-6">
                            <span
                                v-if="billing === 'yearly' && yearlyDiscount(plan.name)"
                                class="inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400"
                            >
                                Hemat {{ yearlyDiscount(plan.name) }}
                            </span>
                        </div>

                        <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white mb-1">
                            {{ plan.name }}
                        </h2>

                        <div class="flex items-end gap-1 mt-3">
                            <span class="text-4xl font-black text-gray-900 dark:text-white">
                                {{ formatPrice(plan.price) }}
                            </span>
                            <span v-if="parseFloat(plan.price) > 0" class="text-sm text-gray-500 dark:text-gray-400 pb-1.5">
                                /{{ billing === 'monthly' ? 'bulan' : 'tahun' }}
                            </span>
                        </div>

                        <p v-if="parseFloat(plan.price) === 0" class="text-sm text-gray-400 dark:text-gray-500 mt-1">
                            Untuk memulai, tanpa kartu kredit
                        </p>
                    </div>

                    <!-- Feature list -->
                    <div :class="[
                        'p-6 flex h-full flex-col gap-6',
                        getConfig(plan.name).highlight
                            ? 'bg-white dark:bg-gray-900'
                            : 'bg-white dark:bg-gray-900',
                    ]">

                        <ul class="space-y-3">
                            <li
                                v-for="feature in getHighlights(plan.name)"
                                :key="feature.text"
                                class="flex items-start gap-3"
                            >
                                <span class="text-base leading-none mt-0.5 shrink-0">
                                    {{ feature.icon }}
                                </span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ feature.text }}
                                </span>
                            </li>
                        </ul>

                        <!-- CTA Button -->
                        <button
                            v-if="!isCurrentPlan(plan)"
                            @click="selectPlan(plan)"
                            :disabled="(form.processing && form.plan_id === plan.id) || (parseFloat(plan.price) === 0 && disableFreePlan)"
                            :class="[
                                'w-full rounded-xl py-3 px-6 text-sm font-semibold',
                                'transition-all duration-200 flex items-center justify-center gap-2',
                                'disabled:opacity-60 disabled:cursor-not-allowed',
                                getConfig(plan.name).highlight
                                    ? getConfig(plan.name).btnClass
                                    : getConfig(plan.name).btnClass,
                                getConfig(plan.name).highlight
                                    ? 'shadow-lg shadow-indigo-500/25'
                                    : '',
                            ]"
                        >
                            <!-- Loading spinner -->
                            <svg
                                v-if="form.processing && form.plan_id === plan.id"
                                class="h-4 w-4 animate-spin"
                                fill="none" viewBox="0 0 24 24"
                            >
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>

                            <!-- Icon -->
                            <svg v-else-if="parseFloat(plan.price) === 0" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                            <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 16h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>

                            {{
                                form.processing && form.plan_id === plan.id
                                    ? 'Memproses...'
                                    : parseFloat(plan.price) === 0
                                        ? 'Mulai Gratis'
                                        : 'Pilih & Bayar'
                            }}
                        </button>

                        <!-- Current Plan Badge -->
                        <div
                            v-else
                            class="w-full rounded-xl py-3 px-6 text-sm font-semibold text-center bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400"
                        >
                            ✅ Paket Saat Ini
                        </div>

                        <!-- Note untuk free plan -->
                        <p v-if="parseFloat(plan.price) === 0 && !isCurrentPlan(plan)" class="text-center text-xs text-gray-400">
                            Tidak perlu kartu kredit
                        </p>
                        <p v-if="parseFloat(plan.price) === 0 && disableFreePlan" class="text-center text-xs text-red-500">
                            Nonaktif karena Anda memiliki langganan berbayar
                        </p>

                        <!-- Note untuk paid plan -->
                        <p v-if="parseFloat(plan.price) > 0 && !isCurrentPlan(plan)" class="text-center text-xs text-gray-400">
                            Pembayaran aman via Midtrans
                        </p>
                    </div>
                </div>

            </div>

            <!-- ============================================ -->
            <!-- FAQ / TRUST SECTION -->
            <!-- ============================================ -->
            <div class="mt-16 text-center">
                <div class="flex flex-wrap items-center justify-center gap-8">
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Pembayaran Aman & Terenkripsi
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Batalkan Kapanpun
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Support 7 Hari
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                        Upgrade / Downgrade Bebas
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================ -->
        <!-- CONFIRMATION MODAL -->
        <!-- ================================================ -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-black/50 transition-opacity" @click="cancelSwitch"></div>

            <!-- Modal -->
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-6">
                    <!-- Icon -->
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/30 mb-4">
                        <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.364 5.636A9 9 0 1117.364 4.636 9 9 0 013.636 17.364z"/>
                        </svg>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center">
                        Ubah Paket Langganan
                    </h3>

                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 text-center">
                        Anda akan beralih ke paket <span class="font-medium text-gray-900 dark:text-white">{{ pendingPlan?.name }}</span>.
                        Langganan saat ini (<span class="font-medium">{{ currentSubscription?.plan?.name }}</span>) akan diakhiri dan statusnya akan menjadi <span class="font-medium">expired</span>.
                    </p>

                    <p class="mt-4 text-xs text-gray-400 text-center">
                        Setelah pembayaran berhasil, langganan baru akan aktif.
                    </p>

                    <div class="mt-6 flex gap-3">
                        <button
                            @click="cancelSwitch"
                            class="flex-1 rounded-xl py-2.5 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            @click="confirmSwitch"
                            class="flex-1 rounded-xl py-2.5 px-4 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors"
                        >
                            Ya, Lanjutkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
