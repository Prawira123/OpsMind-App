<script setup>
import { ref, computed, watch } from 'vue'
import { useForm, usePage, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import BadgeSuccess from '@/Components/Partials/BadgeSuccess.vue'
// =========================================================
// PROPS
// =========================================================
const props = defineProps({
    user:   { type: Object, required: true },
    tenant: { type: Object, required: true },
    status : {type: String, required: false},
})

// =========================================================
// ACTIVE TAB
// =========================================================
const activeTab = ref('profile')

const tabs = [
    { key: 'profile',  label: 'Profil Saya',    icon: 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z' },
    { key: 'tenant',   label: 'Data Bisnis',     icon: 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21' },
    { key: 'security', label: 'Keamanan',        icon: 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z' },
]

// =========================================================
// AVATAR INITIAL
// =========================================================
const avatarInitial = computed(() =>
    props.user?.name?.charAt(0)?.toUpperCase() ?? 'U'
)

// =========================================================
// FORM: PROFILE (user)
// =========================================================
const profileForm = useForm({
    name:     props.user.name     ?? '',
    email:    props.user.email    ?? '',
})

const submitProfile = () => profileForm.put(route('profile.update_user', props.user.id))

// =========================================================
// FORM: TENANT
// =========================================================
const tenantForm = useForm({
    name:     props.tenant.name     ?? '',
    email:    props.tenant.email    ?? '',
    phone:    props.tenant.phone    ?? '',
    address:  props.tenant.address  ?? '',
    currency: props.tenant.currency ?? 'IDR',
    timezone: props.tenant.timezone ?? 'Asia/Jakarta',
    logo:     props.tenant.logo     ?? '',
})

const submitTenant = () => tenantForm.post(route('profile.update_tenant'))

// =========================================================
// FORM: PASSWORD
// =========================================================
const passwordForm = useForm({
    current_password:      '',
    password:              '',
    password_confirmation: '',
    email :          props.user.email,
})

const twoFactorEnabled = useForm({
    enabled: props.user.two_factor_enabled,
})

const submitPassword = () => {
    passwordForm.put(route('profile.update_password'), {
        onSuccess: () => passwordForm.reset(),
    })
}

const submit2Factor = () => {
    twoFactorEnabled.put(route('profile.update_2fa'))
}

console.log(twoFactorEnabled.enabled)



// =========================================================
// LOGO UPLOAD
// =========================================================
const logoPreview  = ref(props.tenant.logo ?? null)
const logoInput    = ref(null)

const handleLogoChange = (e) => {
    const file = e.target.files[0]
    if (!file) return
    const reader = new FileReader()
    reader.onload = (ev) => { logoPreview.value = ev.target.result }
    reader.readAsDataURL(file)
    tenantForm.logo = file
}

// =========================================================
// CURRENCY & TIMEZONE OPTIONS
// =========================================================
const currencies = ['IDR', 'USD', 'SGD', 'MYR', 'EUR']
const timezones  = [
    'Asia/Jakarta',
    'Asia/Makassar',
    'Asia/Jayapura',
    'Asia/Singapore',
    'Asia/Kuala_Lumpur',
]

const showStatus = ref(false)

watch(() => props.status, (val)=>{
    if(val){
        showStatus.value = true
        setTimeout(() => {
            showStatus.value = false
        }, 3000)
    }
}, {immediate: true})

</script>

<template>
    <div class="max-w-8xl mx-auto">

        <!-- ============================================ -->
        <!-- PROFILE HEADER CARD -->
        <!-- ============================================ -->
        <div class="mb-6 rounded-2xl bg-white dark:bg-gray-900
                    border border-gray-200 dark:border-gray-800
                    overflow-hidden shadow-sm">

            <!-- Cover -->
            <div class="h-24 bg-gradient-to-r from-indigo-500 via-violet-500 to-purple-600"/>

            <!-- Avatar + Info -->
            <div class="px-6 pb-5">
                <div class="flex items-end justify-between -mt-10 mb-4">
                    <!-- Avatar -->
                    <div v-if="!props.tenant.logo" class="h-20 w-20 rounded-2xl ring-4 ring-white dark:ring-gray-900
                                bg-gradient-to-br from-indigo-500 to-violet-600
                                flex items-center justify-center
                                text-white text-3xl font-bold shadow-lg">
                        {{ avatarInitial }}
                    </div>
                    <div v-if="props.tenant.logo" class="h-20 w-20 rounded-2xl ring-4 ring-white dark:ring-gray-900
                    flex items-center justify-center bg-cover bg-center shadow-lg" :style="{ backgroundImage: `url(${props.tenant.logo})` }">
                    </div>

                    <!-- Status badge -->
                    <span :class="[
                        'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold',
                        user.is_active
                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                            : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                    ]">
                        <span :class="[
                            'h-1.5 w-1.5 rounded-full',
                            user.is_active ? 'bg-emerald-500' : 'bg-red-500',
                        ]"/>
                        {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ user.name }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ user.email }}
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                    {{ tenant.name }}
                </p>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- TABS -->
        <!-- ============================================ -->
        <div class="mb-6 flex gap-1 rounded-xl bg-gray-100 dark:bg-gray-800/50 p-1">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                @click="activeTab = tab.key"
                :class="[
                    'flex flex-1 items-center justify-center gap-2 rounded-lg px-4 py-2.5',
                    'text-sm font-medium transition-all duration-200',
                    activeTab === tab.key
                        ? 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm'
                        : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200',
                ]"
            >
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" :d="tab.icon"/>
                </svg>
                <span class="hidden sm:block">{{ tab.label }}</span>
            </button>
        </div>

        <div class="" v-if="showStatus">
            <BadgeSuccess :status="props.status"/>
        </div>

        <!-- ============================================ -->
        <!-- TAB: PROFIL SAYA -->
        <!-- ============================================ -->
        <div v-if="activeTab === 'profile'">
            <form @submit.prevent="submitProfile" class="space-y-5">
                <div class="rounded-xl bg-white dark:bg-gray-900
                            border border-gray-200 dark:border-gray-800 p-6 shadow-sm">

                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-5">
                        Informasi Pribadi
                    </h2>

                    <div class="space-y-4">

                        <!-- Nama -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700
                                            dark:text-gray-300 mb-1.5">
                                Nama Lengkap
                                <span class="text-red-500 ml-0.5">*</span>
                            </label>
                            <input
                                v-model="profileForm.name"
                                type="text"
                                placeholder="Nama lengkap kamu"
                                :class="[
                                    'w-full rounded-lg border px-3.5 py-2.5 text-sm',
                                    'text-gray-900 dark:text-white placeholder-gray-400',
                                    'bg-white dark:bg-gray-800 transition',
                                    'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                                    profileForm.errors.name
                                        ? 'border-red-400'
                                        : 'border-gray-200 dark:border-gray-700',
                                ]"
                            />
                            <p v-if="profileForm.errors.name"
                                class="mt-1.5 text-xs text-red-500">
                                {{ profileForm.errors.name }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700
                                            dark:text-gray-300 mb-1.5">
                                Email
                                <span class="text-red-500 ml-0.5">*</span>
                            </label>
                            <input
                                v-model="profileForm.email"
                                type="email"
                                placeholder="email@kamu.com"
                                :class="[
                                    'w-full rounded-lg border px-3.5 py-2.5 text-sm',
                                    'text-gray-900 dark:text-white placeholder-gray-400',
                                    'bg-white dark:bg-gray-800 transition',
                                    'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                                    profileForm.errors.email
                                        ? 'border-red-400'
                                        : 'border-gray-200 dark:border-gray-700',
                                ]"
                            />
                            <p v-if="profileForm.errors.email"
                                class="mt-1.5 text-xs text-red-500">
                                {{ profileForm.errors.email }}
                            </p>
                        </div>

                        <!-- Email verified info -->
                        <div :class="[
                            'flex items-center gap-2 rounded-lg px-3.5 py-2.5 text-xs',
                            user.email_verified_at
                                ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400'
                                : 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400',
                        ]">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                <path v-if="user.email_verified_at"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                <path v-else stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                            </svg>
                            {{
                                user.email_verified_at
                                    ? 'Email sudah terverifikasi'
                                    : 'Email belum terverifikasi — cek inbox kamu'
                            }}
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <button
                        type="submit"
                        :disabled="profileForm.processing"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600
                                px-5 py-2.5 text-sm font-semibold text-white
                                hover:bg-indigo-500 disabled:opacity-60
                                disabled:cursor-not-allowed transition"
                    >
                        <svg v-if="profileForm.processing"
                                class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ profileForm.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- ============================================ -->
        <!-- TAB: DATA BISNIS (TENANT) -->
        <!-- ============================================ -->
        <div v-if="activeTab === 'tenant'">
            <form @submit.prevent="submitTenant" class="space-y-5">

                <!-- Logo -->
                <div class="rounded-xl bg-white dark:bg-gray-900
                            border border-gray-200 dark:border-gray-800 p-6 shadow-sm">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-5">
                        Logo Bisnis
                    </h2>

                    <div class="flex items-center gap-5">
                        <!-- Preview -->
                        <div class="h-16 w-16 rounded-xl border-2 border-dashed
                                    border-gray-200 dark:border-gray-700
                                    bg-gray-50 dark:bg-gray-800
                                    flex items-center justify-center overflow-hidden shrink-0">
                            <img v-if="logoPreview" :src="props.tenant.logo"
                                    class="h-full w-full object-cover"/>
                            <svg v-else class="h-6 w-6 text-gray-300" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                            </svg>
                        </div>

                        <div>
                            <button
                                type="button"
                                @click="logoInput.click()"
                                class="rounded-lg border border-gray-200 dark:border-gray-700
                                        px-4 py-2 text-sm font-medium text-gray-700
                                        dark:text-gray-300 hover:bg-gray-50
                                        dark:hover:bg-gray-800 transition"
                            >
                                Ganti Logo
                            </button>
                            <input ref="logoInput" type="file" accept="image/*"
                                    class="hidden" @change="handleLogoChange"/>
                            <p class="mt-1.5 text-xs text-gray-400">
                                PNG, JPG, SVG. Maks 2MB.
                            </p>
                        </div>
                        <p v-if="tenantForm.errors.name" class="mt-1.5 text-xs text-red-500">
                            {{ tenantForm.errors.name }}
                        </p>
                    </div>
                </div>

                <!-- Info Bisnis -->
                <div class="rounded-xl bg-white dark:bg-gray-900
                            border border-gray-200 dark:border-gray-800 p-6 shadow-sm space-y-4">

                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Informasi Bisnis
                    </h2>

                    <!-- Nama Bisnis -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700
                                        dark:text-gray-300 mb-1.5">
                            Nama Bisnis
                            <span class="text-red-500 ml-0.5">*</span>
                        </label>
                        <input
                            v-model="tenantForm.name"
                            type="text"
                            placeholder="Nama bisnis atau perusahaan"
                            :class="[
                                'w-full rounded-lg border px-3.5 py-2.5 text-sm',
                                'text-gray-900 dark:text-white placeholder-gray-400',
                                'bg-white dark:bg-gray-800 transition',
                                'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                                tenantForm.errors.name
                                    ? 'border-red-400' : 'border-gray-200 dark:border-gray-700',
                            ]"
                        />
                        <p v-if="tenantForm.errors.name" class="mt-1.5 text-xs text-red-500">
                            {{ tenantForm.errors.name }}
                        </p>
                    </div>

                    <!-- Email Bisnis -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700
                                        dark:text-gray-300 mb-1.5">
                            Email Bisnis
                            <span class="text-red-500 ml-0.5">*</span>
                        </label>
                        <input
                            v-model="tenantForm.email"
                            type="email"
                            placeholder="email@bisnis.com"
                            :class="[
                                'w-full rounded-lg border px-3.5 py-2.5 text-sm',
                                'text-gray-900 dark:text-white placeholder-gray-400',
                                'bg-white dark:bg-gray-800 transition',
                                'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                                tenantForm.errors.email
                                    ? 'border-red-400' : 'border-gray-200 dark:border-gray-700',
                            ]"
                        />
                        <p v-if="tenantForm.errors.email" class="mt-1.5 text-xs text-red-500">
                            {{ tenantForm.errors.email }}
                        </p>
                    </div>

                    <!-- No Telepon -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700
                                        dark:text-gray-300 mb-1.5">
                            No. Telepon
                        </label>
                        <input
                            v-model="tenantForm.phone"
                            type="tel"
                            placeholder="08xx-xxxx-xxxx"
                            :class="[
                                'w-full rounded-lg border px-3.5 py-2.5 text-sm',
                                'text-gray-900 dark:text-white placeholder-gray-400',
                                'bg-white dark:bg-gray-800 transition',
                                'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                                tenantForm.errors.phone
                                    ? 'border-red-400' : 'border-gray-200 dark:border-gray-700',
                            ]"
                        />
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700
                                        dark:text-gray-300 mb-1.5">
                            Alamat
                        </label>
                        <textarea
                            v-model="tenantForm.address"
                            rows="3"
                            placeholder="Alamat lengkap bisnis..."
                            :class="[
                                'w-full rounded-lg border px-3.5 py-2.5 text-sm resize-none',
                                'text-gray-900 dark:text-white placeholder-gray-400',
                                'bg-white dark:bg-gray-800 transition',
                                'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                                tenantForm.errors.address
                                    ? 'border-red-400' : 'border-gray-200 dark:border-gray-700',
                            ]"
                        />
                    </div>
                </div>

                <!-- Pengaturan Regional -->
                <div class="rounded-xl bg-white dark:bg-gray-900
                            border border-gray-200 dark:border-gray-800 p-6 shadow-sm space-y-4">

                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Pengaturan Regional
                    </h2>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <!-- Mata Uang -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700
                                            dark:text-gray-300 mb-1.5">
                                Mata Uang
                            </label>
                            <select
                                v-model="tenantForm.currency"
                                class="w-full rounded-lg border border-gray-200 dark:border-gray-700
                                        bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm
                                        text-gray-900 dark:text-white
                                        focus:outline-none focus:ring-2 focus:ring-indigo-500
                                        focus:border-transparent transition"
                            >
                                <option v-for="c in currencies" :key="c" :value="c">
                                    {{ c }}
                                </option>
                            </select>
                        </div>

                        <!-- Timezone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700
                                            dark:text-gray-300 mb-1.5">
                                Zona Waktu
                            </label>
                            <select
                                v-model="tenantForm.timezone"
                                class="w-full rounded-lg border border-gray-200 dark:border-gray-700
                                        bg-white dark:bg-gray-800 px-3.5 py-2.5 text-sm
                                        text-gray-900 dark:text-white
                                        focus:outline-none focus:ring-2 focus:ring-indigo-500
                                        focus:border-transparent transition"
                            >
                                <option v-for="tz in timezones" :key="tz" :value="tz">
                                    {{ tz }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <button
                        type="submit"
                        :disabled="tenantForm.processing"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600
                                px-5 py-2.5 text-sm font-semibold text-white
                                hover:bg-indigo-500 disabled:opacity-60
                                disabled:cursor-not-allowed transition"
                    >
                        <svg v-if="tenantForm.processing"
                                class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ tenantForm.processing ? 'Menyimpan...' : 'Simpan Data Bisnis' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- ============================================ -->
        <!-- TAB: KEAMANAN -->
        <!-- ============================================ -->
        <div v-if="activeTab === 'security'">
            <form @submit.prevent="submitPassword" class="space-y-5 mb-5">
                <div class="rounded-xl bg-white dark:bg-gray-900
                            border border-gray-200 dark:border-gray-800 p-6 shadow-sm space-y-4">

                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Ubah Password
                    </h2>

                    <!-- Password Lama -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700
                                        dark:text-gray-300 mb-1.5">
                            Password Saat Ini
                            <span class="text-red-500 ml-0.5">*</span>
                        </label>
                        <input
                            v-model="passwordForm.current_password"
                            type="password"
                            placeholder="••••••••"
                            :class="[
                                'w-full rounded-lg border px-3.5 py-2.5 text-sm',
                                'text-gray-900 dark:text-white placeholder-gray-400',
                                'bg-white dark:bg-gray-800 transition',
                                'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                                passwordForm.errors.current_password
                                    ? 'border-red-400' : 'border-gray-200 dark:border-gray-700',
                            ]"
                        />
                        <p v-if="passwordForm.errors.current_password"
                            class="mt-1.5 text-xs text-red-500">
                            {{ passwordForm.errors.current_password }}
                        </p>
                    </div>

                    <!-- Password Baru -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700
                                        dark:text-gray-300 mb-1.5">
                            Password Baru
                            <span class="text-red-500 ml-0.5">*</span>
                        </label>
                        <input
                            v-model="passwordForm.password"
                            type="password"
                            placeholder="••••••••"
                            :class="[
                                'w-full rounded-lg border px-3.5 py-2.5 text-sm',
                                'text-gray-900 dark:text-white placeholder-gray-400',
                                'bg-white dark:bg-gray-800 transition',
                                'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                                passwordForm.errors.password
                                    ? 'border-red-400' : 'border-gray-200 dark:border-gray-700',
                            ]"
                        />
                        <p v-if="passwordForm.errors.password"
                            class="mt-1.5 text-xs text-red-500">
                            {{ passwordForm.errors.password }}
                        </p>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700
                                        dark:text-gray-300 mb-1.5">
                            Konfirmasi Password Baru
                            <span class="text-red-500 ml-0.5">*</span>
                        </label>
                        <input
                            v-model="passwordForm.password_confirmation"
                            type="password"
                            placeholder="••••••••"
                            :class="[
                                'w-full rounded-lg border px-3.5 py-2.5 text-sm',
                                'text-gray-900 dark:text-white placeholder-gray-400',
                                'bg-white dark:bg-gray-800 transition',
                                'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                                passwordForm.errors.password_confirmation
                                    ? 'border-red-400' : 'border-gray-200 dark:border-gray-700',
                            ]"
                        />
                    </div>
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        :disabled="passwordForm.processing"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600
                                px-5 py-2.5 text-sm font-semibold text-white
                                hover:bg-indigo-500 disabled:opacity-60
                                disabled:cursor-not-allowed transition"
                    >
                        <svg v-if="passwordForm.processing"
                                class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ passwordForm.processing ? 'Menyimpan...' : 'Ubah Password' }}
                    </button>
                </div>
            </form>
            <form @submit.prevent="submit2Factor" class="space-y-5">
                                <div class="rounded-xl bg-white dark:bg-gray-900
                            border border-gray-200 dark:border-gray-800 p-6 shadow-sm">

                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                Two-Factor Authentication
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Tambah lapisan keamanan ekstra pada akun kamu
                            </p>
                        </div>
                        <div class="flex gap-2 items-center justify-end">
                            <div class="inline-flex items-center gap-1.5 rounded-full px-3 py-1">
                                <ToggleSwitch @click="twoFactorEnabled.enabled = !twoFactorEnabled.enabled" :data_toggle="twoFactorEnabled.enabled"/>
                            </div>
                            <span :class="[
                                'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold',
                                twoFactorEnabled.enabled == true
                                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                    : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                            ]">
                                {{ twoFactorEnabled.enabled ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <button
                        type="submit"
                        :disabled="twoFactorEnabled.processing && !twoFactorEnabled.isDirty"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600
                                px-5 py-2.5 text-sm font-semibold text-white
                                hover:bg-indigo-500 disabled:opacity-60
                                disabled:cursor-not-allowed transition"
                    >
                        <svg v-if="twoFactorEnabled.processing"
                                class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ twoFactorEnabled.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </button>
                </div>
            </form>
        </div>

    </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import ToggleSwitch from '@/Components/Partials/ToggleSwitch.vue'
import { Badge } from 'lucide-vue-next'
import BadgeSuccess from '@/Components/Partials/BadgeSuccess.vue'
export default { layout: AppLayout }
</script>