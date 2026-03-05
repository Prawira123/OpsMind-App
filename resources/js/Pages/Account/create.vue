<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import SelectCard from '@/Components/Partials/SelectCard.vue';
import TextInputForm from '@/Components/Partials/TextInputForm.vue';
import ToggleSwitch from '@/Components/Partials/ToggleSwitch.vue';

// FORM — field sesuai fillable di model Account
const form = useForm({
    name:           '',
    type:           'cash',
    bank_name:      '',
    account_number: '',
    balance:        0,
    is_active:      true,
})

watch(() => form.name, () => {
    console.log(form.name)
})
const submit = () => {
    form.post(route('accounts.store'))
}

// TIPE REKENING
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
        <!-- HEADER -->
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
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Tambah Rekening
                </h1>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                    Tambahkan rekening kas, bank, atau e-wallet baru
                </p>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="max-w-full">
            <form @submit.prevent="submit" class="space-y-6">

                <!-- Card: Tipe Rekening -->
                <div class="rounded-xl bg-white dark:bg-gray-900 border
                            border-gray-200 dark:border-gray-800 p-6">

                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                        Tipe Rekening
                    </h2>

                    <!-- Type selector — card grid -->
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <SelectCard :form="form" :dataTypes="accountTypes"/>
                    </div>

                    <!-- Error tipe -->
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Nama Rekening
                            <span class="text-red-500 ml-0.5">*</span>
                        </label>
                        <TextInputForm v-model="form.name" :form="form" :placeholder="'contoh: Kas Utama, BCA Operasional...'" :error="form.errors.name"/>
                        <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-500">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Nama Bank -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Nama Bank / Platform
                        </label>
                        <TextInputForm v-model="form.bank_name" :form="form" :placeholder="'contoh: BCA, Mandiri, OVO, GoPay...'" :error="form.errors.bank_name"/>
                        <p v-if="form.errors.bank_name" class="mt-1.5 text-xs text-red-500">
                            {{ form.errors.bank_name }}
                        </p>
                    </div>

                    <!-- Nomor Rekening -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Nomor Rekening / Nomor HP
                        </label>
                        <p v-if="form.errors.account_number" class="mt-1.5 text-xs text-red-500">
                            {{ form.errors.account_number }}
                        </p>
                        <TextInputForm v-model="form.account_number" :form="form" :placeholder="'contoh: 1234567890'" :error="form.errors.account_number"/>
                    </div>
                </div>

                <!-- Card: Saldo & Status -->
                <div class="rounded-xl bg-white dark:bg-gray-900 border
                            border-gray-200 dark:border-gray-800 p-6 space-y-5">

                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Saldo & Status
                    </h2>

                    <!-- Saldo Awal -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Saldo Awal
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5
                                        pointer-events-none">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
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
                        <p class="mt-1.5 text-xs text-gray-400">
                            Masukkan saldo saat ini. Bisa diubah melalui transaksi penyesuaian.
                        </p>
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
                        <!-- Toggle switch -->
                        <!-- <button
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
                        </button> -->
                        <ToggleSwitch :form="form" />
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

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-2.5
                               text-sm font-semibold text-white shadow-sm
                               hover:bg-indigo-700 disabled:opacity-60
                               disabled:cursor-not-allowed transition-colors"
                    >
                        <!-- Spinner saat loading -->
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
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Rekening' }}
                    </button>
                </div>

            </form>
        </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import { watch } from 'vue';
export default { layout: AppLayout }
</script>