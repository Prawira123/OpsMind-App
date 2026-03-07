<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import SelectCard from '@/Components/Partials/SelectCard.vue';
import TextInputForm from '@/Components/Partials/TextInputForm.vue';
import ToggleSwitch from '@/Components/Partials/ToggleSwitch.vue';
import ButtonSubmit from '@/Components/Partials/ButtonSubmit.vue';
import ButtonReset from '@/Components/Partials/ButtonReset.vue';

// PROPS — dikirim dari AccountController::edit()
const props = defineProps({
    account: Object,
})

// FORM — pre-fill dengan data yang ada
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

        <!-- INFO PERUBAHAN (hanya di Edit) -->
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

        <!-- FORM CARD -->
        <div class="max-w-full">
            <form @submit.prevent="submit" class="space-y-6">

                <!-- Card: Tipe Rekening -->
                <div class="rounded-xl bg-white dark:bg-gray-900 border
                            border-gray-200 dark:border-gray-800 p-6">

                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                        Tipe Rekening
                    </h2>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <SelectCard :form="form" :dataTypes="accountTypes"/>
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
                        <TextInputForm v-model="form.name" :form="form" :placeholder="'contoh: Kas Utama, BCA Operasional...'" :error="form.errors.name"/>
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
                        <TextInputForm v-model="form.bank_name" :form="form" :placeholder="'contoh: BCA, Mandiri, OVO, GoPay...'" :error="form.errors.bank_name"/>
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
                        <TextInputForm v-model="form.account_number" :form="form" :placeholder="'contoh: 1234567890'" :error="form.errors.account_number"/>
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

                    <div class="flex items-center gap-3">
                        <!-- Reset form -->
                        <ButtonReset :form="form"/>
                        <ButtonSubmit :btn-text="form.processing ? 'menyimpan...' : 'Simpan Perubahan'" :form="form"/>
                    </div>
                </div>

            </form>
        </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>