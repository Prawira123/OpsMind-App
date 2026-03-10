<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import SelectCard from '@/Components/Partials/SelectCard.vue';
import TextInputForm from '@/Components/Partials/TextInputForm.vue';
import ToggleSwitch from '@/Components/Partials/ToggleSwitch.vue';
import ButtonSubmit from '@/Components/Partials/ButtonSubmit.vue';

// FORM — field sesuai fillable di model Account

const props = defineProps({
    chartOfAccount: Object,
    chartOfAccounts: Object,
})

const form = useForm({
    type:props.chartOfAccount.account_type_id ?? '',
    parent_id:props.chartOfAccount.parent_id ?? '',
    name:props.chartOfAccount.name ?? '',
    description: props.chartOfAccount.description ?? '',
    balance: props.chartOfAccount.balance ?? 0,
    is_locked:props.chartOfAccount.is_locked ?? true,
    is_active:props.chartOfAccount.is_active ?? true,
})

console.log(props.chartOfAccount)

watch(() => form.name, () => {
    console.log(form.name)
})
const submit = () => {
    form.put(route('chart-of-accounts.update', props.chartOfAccount.id))
}

// TIPE REKENING
const accountTypes = [
    {   
        value: 1,
        label: 'asset',
        desc:  'Uang tunai di tangan',
        icon:  `<path stroke-linecap="round" stroke-linejoin="round"
                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2
                         2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2
                         2 0 11-4 0 2 2 0 014 0z"/>`,
        color: 'text-emerald-600 bg-emerald-50 border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800',
        activeColor: 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/30 ring-2 ring-emerald-500/20',
    },
    {
        value: 2,
        label: 'liability',
        desc:  'Rekening bank',
        icon:  `<path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 6l9-4 9 4M3 10h18M5 10v8m4-8v8m4-8v8m4-8v8M19 10v8M3 18h18"/>`,
        color: 'text-blue-600 bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800',
        activeColor: 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 ring-2 ring-blue-500/20',
    },
    {
        value: 3,
        label: 'equity',
        desc:  'Dompet digital',
        icon:  `<path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2
                         2 0 002 2z"/>`,
        color: 'text-violet-600 bg-violet-50 border-violet-200 dark:bg-violet-900/20 dark:border-violet-800',
        activeColor: 'border-violet-500 bg-violet-50 dark:bg-violet-900/30 ring-2 ring-violet-500/20',
    },
    {
        value : 5,
        label: 'expense',
        desc:  'Tipe lainnya',
        icon:  `<path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3
                         3 0 00-3 3v8a3 3 0 003 3z"/>`,
        color: 'text-red-600 bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800',
        activeColor: 'border-red-500 bg-red-50 dark:bg-red-900/30 ring-2 ring-red-500/20',
    },
    {
        value : 4,
        label: 'revenue',
        desc:  'Tipe lainnya',
        icon:  `<path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3
                         3 0 00-3 3v8a3 3 0 003 3z"/>`,
        color: 'text-yellow-600 bg-yellow-50 border-yellow-200 dark:bg-yellow-800 dark:border-yellow-700',
        activeColor: 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/30 ring-2 ring-yellow-500/20',
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
                :href="route('chart-of-accounts.index')"
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
                        Edit Rekening - {{ props.chartOfAccount.name }}
                    </h1>
                    <!-- Badge status -->
                    <span :class="[
                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                        props.chartOfAccount.is_active
                            ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                            : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                    ]">
                        {{ props.chartOfAccount.is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                    {{ props.chartOfAccount.name }}
                </p>
            </div>
        </div>

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
                        Tipe Akun
                    </h2>

                    <!-- Type selector — card grid -->
                    <div class="grid grid-cols-2 gap-3 lg:grid-cols-3">
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
                            Nama Akun
                            <span class="text-red-500 ml-0.5">*</span>
                        </label>
                        <TextInputForm v-model="form.name" :form="form" :placeholder="'contoh: Kas Utama, BCA Operasional...'" :error="form.errors.name"/>
                        <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-500">
                            {{ form.errors.name }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Parent Akun
                            <span class="text-xs text-gray-400 ml-1">(opsional)</span>
                        </label>

                        <select
                            v-model="form.parent_id"
                            :class="[
                                'w-full rounded-lg border px-3.5 py-2.5 text-sm',
                                'text-gray-900 dark:text-white',
                                'bg-white dark:bg-gray-800 transition',
                                'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                                form.errors.parent_id
                                    ? 'border-red-400 dark:border-red-500'
                                    : 'border-gray-200 dark:border-gray-700',
                            ]"
                        >
                            <option value="">— Tidak ada (akun utama) —</option>
                            <option
                                v-for="account in props.chartOfAccounts"
                                :key="account.id"
                                :value="account.id"
                            >
                                {{ account.code }} — {{ account.name }}
                            </option>
                        </select>

                        <p v-if="form.errors.parent_id" class="mt-1.5 text-xs text-red-500">
                            {{ form.errors.parent_id }}
                        </p>
                    </div>

                    <!-- Nama Bank -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Deskripsi
                        </label>
                        <TextInputForm v-model="form.description" :form="form" :placeholder="'masukkan keterangan akun...'" :error="form.errors.description"/>
                        <p v-if="form.errors.description" class="mt-1.5 text-xs text-red-500">
                            {{ form.errors.description }}
                        </p>
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
                                Status Aktif
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Akun nonaktif tidak bisa digunakan untuk transaksi
                            </p>
                        </div>
                        <ToggleSwitch :form="form" :data_toggle="form.is_active" @click="form.is_active = !form.is_active"/>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border
                                border-gray-200 dark:border-gray-700 px-4 py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                Status Terkunci
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Akun terkunci tidak bisa dihapus
                            </p>
                        </div>
                        <ToggleSwitch :form="form" :data_toggle="form.is_locked" @click="form.is_locked = !form.is_locked"/>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-2">
                    <Link :href="route('chart-of-accounts.index')"
                          class="rounded-lg border border-gray-200 dark:border-gray-700
                                 px-4 py-2.5 text-sm font-medium text-gray-700
                                 dark:text-gray-300 hover:bg-gray-50
                                 dark:hover:bg-gray-800 transition">
                        Batal
                    </Link>

                    <div class="flex items-center gap-3">
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
                                   text-sm font-semibold text-white shadow-sm hover:bg-indigo-700
                                   disabled:opacity-60 disabled:cursor-not-allowed transition-colors"
                        >
                            <svg v-if="form.processing" class="h-4 w-4 animate-spin"
                                 fill="none" viewBox="0 0 24 24">
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
import { watch } from 'vue';
export default { layout: AppLayout }
</script>