<script setup>
import { useForm, Link } from '@inertiajs/vue3'

// =========================================================
// PROPS — dikirim dari AccountController::edit()
// =========================================================
const props = defineProps({
    account: Object,
})

// =========================================================
// FORM — pre-fill dengan data yang ada
// =========================================================
const form = useForm({
    name:           props.account.name           ?? '',
    type:           props.account.type           ?? 'cash',
    bank_name:      props.account.bank_name      ?? '',
    account_number: props.account.account_number ?? '',
    balance:        props.account.balance        ?? 0,
    is_active:      props.account.is_active      ?? true,
})

const submit = () => {
    form.put(route('accounts.update', props.account.id))
}

// =========================================================
// TIPE REKENING
// =========================================================
const accountTypes = [
    {
        value: 'cash',
        label: 'Kas Tunai',
        desc:  'Uang tunai di tangan',
        icon:  `<path stroke-linecap="round" stroke-linejoin="round"
                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2
                         2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2
                         2 0 11-4 0 2 2 0 014 0z"/>`,
        color: 'text-emerald-600 bg-emerald-50 border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800',
        activeColor: 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/30 ring-2 ring-emerald-500/20',
    },
    {
        value: 'bank',
        label: 'Bank',
        desc:  'Rekening bank',
        icon:  `<path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 6l9-4 9 4M3 10h18M5 10v8m4-8v8m4-8v8m4-8v8M19 10v8M3 18h18"/>`,
        color: 'text-blue-600 bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800',
        activeColor: 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 ring-2 ring-blue-500/20',
    },
    {
        value: 'ewallet',
        label: 'E-Wallet',
        desc:  'Dompet digital',
        icon:  `<path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2
                         2 0 002 2z"/>`,
        color: 'text-violet-600 bg-violet-50 border-violet-200 dark:bg-violet-900/20 dark:border-violet-800',
        activeColor: 'border-violet-500 bg-violet-50 dark:bg-violet-900/30 ring-2 ring-violet-500/20',
    },
    {
        value: 'other',
        label: 'Lainnya',
        desc:  'Tipe lainnya',
        icon:  `<path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3
                         3 0 00-3 3v8a3 3 0 003 3z"/>`,
        color: 'text-gray-600 bg-gray-50 border-gray-200 dark:bg-gray-800 dark:border-gray-700',
        activeColor: 'border-gray-500 bg-gray-50 dark:bg-gray-800 ring-2 ring-gray-500/20',
    },
]

// Format angka saat input balance
const formatBalance = (e) => {
    const raw = e.target.value.replace(/\D/g, '')
    form.balance = parseInt(raw) || 0
}

const displayBalance = () => {
    if (!form.balance) return ''
    return new Intl.NumberFormat('id-ID').format(form.balance)
}
</script>

<template>
        <div class="mb-6 flex items-center gap-4">
            <Link
                :href="route('accounts.index')"
                class="flex h-9 w-9 items-center justify-center rounded-lg
                       border border-gray-200 dark:border-gray-700
                       text-gray-500 dark:text-gray-400
                       hover:bg-gray-100 dark:hover:bg-gray-800
                       hover:text-gray-900 dark:hover:text-white transition"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 19l-7-7 7-7"/>
                </svg>
            </Link>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Edit Rekening - {{ account.name }}
                    </h1>
                    <!-- Badge status -->
                    <span :class="[
                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                        account.is_active
                            ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                            : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                    ]">
                        {{ account.is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                    {{ account.name }}
                </p>
            </div>
        </div>

        <!-- ================================================ -->
        <!-- INFO PERUBAHAN (hanya di Edit) -->
        <!-- ================================================ -->
        <div v-if="form.isDirty"
             class="mb-6 max-w-2xl flex items-center gap-3 rounded-lg
                    bg-amber-50 dark:bg-amber-900/20 border border-amber-200
                    dark:border-amber-800 px-4 py-3">
            <svg class="h-4 w-4 text-amber-500 shrink-0" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667
                         1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34
                         16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-sm text-amber-700 dark:text-amber-400">
                Ada perubahan yang belum disimpan
            </p>
        </div>

        <!-- ================================================ -->
        <!-- FORM CARD -->
        <!-- ================================================ -->
        <div class="max-w-full">
            <form @submit.prevent="submit" class="space-y-6">

                <!-- Card: Tipe Rekening -->
                <div class="rounded-xl bg-white dark:bg-gray-900 border
                            border-gray-200 dark:border-gray-800 p-6">

                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                        Tipe Rekening
                    </h2>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <button
                            v-for="type in accountTypes"
                            :key="type.value"
                            type="button"
                            @click="form.type = type.value"
                            :class="[
                                'relative flex flex-col items-center gap-2 rounded-xl border-2 p-4',
                                'text-sm font-medium transition-all duration-150 cursor-pointer',
                                form.type === type.value
                                    ? type.activeColor
                                    : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 bg-white dark:bg-gray-900',
                            ]"
                        >
                            <div v-if="form.type === type.value"
                                 class="absolute top-2 right-2 h-4 w-4 rounded-full
                                        bg-indigo-600 flex items-center justify-center">
                                <svg class="h-2.5 w-2.5 text-white" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>

                            <div :class="[
                                'h-10 w-10 rounded-lg flex items-center justify-center',
                                type.color,
                            ]">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="1.5"
                                     v-html="type.icon"/>
                            </div>

                            <div class="text-center">
                                <p class="text-xs font-semibold text-gray-900 dark:text-white">
                                    {{ type.label }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ type.desc }}</p>
                            </div>
                        </button>
                    </div>

                    <p v-if="form.errors.type" class="mt-2 text-xs text-red-500">
                        {{ form.errors.type }}
                    </p>
                </div>

                <!-- Card: Informasi Rekening -->
                <div class="rounded-xl bg-white dark:bg-gray-900 border
                            border-gray-200 dark:border-gray-800 p-6 space-y-5">

                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Informasi Rekening
                    </h2>

                    <!-- Nama Rekening -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700
                                      dark:text-gray-300 mb-1.5">
                            Nama Rekening
                            <span class="text-red-500 ml-0.5">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="contoh: Kas Utama, BCA Operasional..."
                            :class="[
                                'w-full rounded-lg border px-3.5 py-2.5 text-sm',
                                'text-gray-900 dark:text-white placeholder-gray-400',
                                'bg-white dark:bg-gray-800 transition',
                                'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                                form.errors.name
                                    ? 'border-red-400 dark:border-red-500'
                                    : 'border-gray-200 dark:border-gray-700',
                            ]"
                        />
                        <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-500">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Nama Bank -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700
                                      dark:text-gray-300 mb-1.5">
                            Nama Bank / Platform
                        </label>
                        <input
                            v-model="form.bank_name"
                            type="text"
                            placeholder="contoh: BCA, Mandiri, OVO, GoPay..."
                            :class="[
                                'w-full rounded-lg border px-3.5 py-2.5 text-sm',
                                'text-gray-900 dark:text-white placeholder-gray-400',
                                'bg-white dark:bg-gray-800 transition',
                                'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                                form.errors.bank_name
                                    ? 'border-red-400 dark:border-red-500'
                                    : 'border-gray-200 dark:border-gray-700',
                            ]"
                        />
                        <p v-if="form.errors.bank_name" class="mt-1.5 text-xs text-red-500">
                            {{ form.errors.bank_name }}
                        </p>
                    </div>

                    <!-- Nomor Rekening -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700
                                      dark:text-gray-300 mb-1.5">
                            Nomor Rekening / Nomor HP
                        </label>
                        <input
                            v-model="form.account_number"
                            type="text"
                            placeholder="contoh: 1234567890"
                            :class="[
                                'w-full rounded-lg border px-3.5 py-2.5 text-sm font-mono',
                                'text-gray-900 dark:text-white placeholder-gray-400',
                                'bg-white dark:bg-gray-800 transition',
                                'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                                form.errors.account_number
                                    ? 'border-red-400 dark:border-red-500'
                                    : 'border-gray-200 dark:border-gray-700',
                            ]"
                        />
                        <p v-if="form.errors.account_number"
                           class="mt-1.5 text-xs text-red-500">
                            {{ form.errors.account_number }}
                        </p>
                    </div>
                </div>

                <!-- Card: Saldo & Status -->
                <div class="rounded-xl bg-white dark:bg-gray-900 border
                            border-gray-200 dark:border-gray-800 p-6 space-y-5">

                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Saldo & Status
                    </h2>

                    <!-- Saldo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700
                                      dark:text-gray-300 mb-1.5">
                            Saldo Saat Ini
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center
                                        pl-3.5 pointer-events-none">
                                <span class="text-sm font-medium text-gray-500
                                             dark:text-gray-400">
                                    Rp
                                </span>
                            </div>
                            <input
                                :value="displayBalance()"
                                @input="formatBalance"
                                type="text"
                                inputmode="numeric"
                                placeholder="0"
                                :class="[
                                    'w-full rounded-lg border pl-10 pr-3.5 py-2.5 text-sm',
                                    'text-gray-900 dark:text-white placeholder-gray-400',
                                    'bg-white dark:bg-gray-800 transition',
                                    'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                                    form.errors.balance
                                        ? 'border-red-400 dark:border-red-500'
                                        : 'border-gray-200 dark:border-gray-700',
                                ]"
                            />
                        </div>
                        <!-- Info peringatan edit saldo -->
                        <div class="mt-2 flex items-start gap-2">
                            <svg class="h-3.5 w-3.5 text-amber-500 shrink-0 mt-0.5"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                 stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-xs text-gray-400">
                                Mengubah saldo di sini akan langsung memperbarui nilai rekening.
                                Untuk mencatat perubahan saldo, gunakan fitur transaksi.
                            </p>
                        </div>
                        <p v-if="form.errors.balance" class="mt-1 text-xs text-red-500">
                            {{ form.errors.balance }}
                        </p>
                    </div>

                    <!-- Status Aktif -->
                    <div class="flex items-center justify-between rounded-lg border
                                border-gray-200 dark:border-gray-700 px-4 py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                Status Rekening
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Rekening nonaktif tidak bisa digunakan untuk transaksi
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="form.is_active = !form.is_active"
                            :class="[
                                'relative inline-flex h-6 w-11 shrink-0 cursor-pointer',
                                'rounded-full border-2 border-transparent transition-colors duration-200',
                                'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2',
                                form.is_active
                                    ? 'bg-indigo-600'
                                    : 'bg-gray-200 dark:bg-gray-700',
                            ]"
                        >
                            <span :class="[
                                'pointer-events-none inline-block h-5 w-5 rounded-full',
                                'bg-white shadow transform transition duration-200',
                                form.is_active ? 'translate-x-5' : 'translate-x-0',
                            ]"/>
                        </button>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-2">
                    <Link
                        :href="route('accounts.index')"
                        class="rounded-lg border border-gray-200 dark:border-gray-700
                               px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300
                               hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                    >
                        Batal
                    </Link>

                    <div class="flex items-center gap-3">
                        <!-- Reset form -->
                        <button
                            v-if="form.isDirty"
                            type="button"
                            @click="form.reset()"
                            class="rounded-lg px-4 py-2.5 text-sm font-medium
                                   text-gray-500 dark:text-gray-400
                                   hover:text-gray-700 dark:hover:text-gray-200
                                   hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                        >
                            Reset
                        </button>

                        <button
                            type="submit"
                            :disabled="form.processing || !form.isDirty"
                            class="flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-2.5
                                   text-sm font-semibold text-white shadow-sm
                                   hover:bg-indigo-700 disabled:opacity-60
                                   disabled:cursor-not-allowed transition-colors"
                        >
                            <svg v-if="form.processing"
                                 class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>