<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import SelectCard from '@/Components/Partials/SelectCard.vue';
import TextInputForm from '@/Components/Partials/TextInputForm.vue';
import ToggleSwitch from '@/Components/Partials/ToggleSwitch.vue';
import ButtonSubmit from '@/Components/Partials/ButtonSubmit.vue';

// FORM — field sesuai fillable di model Account

const form = useForm({
    name:'',
    email:'',
    phone:'',
    address: '',
    company:'',
})

watch(() => form.name, () => {
    console.log(form.name)
})
const submit = () => {
    form.post(route('clients.store'))
}

</script>

<template>
        <!-- HEADER -->
        <div class="mb-6 flex items-center gap-4">
            <Link
                :href="route('clients.index')"
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
                    Tambah Client
                </h1>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                    Tambahkan Client Kamu
                </p>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="max-w-full">
            <form @submit.prevent="submit" class="space-y-6">            

                <!-- Card: Informasi Rekening -->
                <div class="rounded-xl bg-white dark:bg-gray-900 border
                            border-gray-200 dark:border-gray-800 p-6 space-y-5">

                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Informasi Client
                    </h2>

                    <!-- Nama Rekening -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Nama Client
                            <span class="text-red-500 ml-0.5">*</span>
                        </label>
                        <TextInputForm v-model="form.name" :form="form" :placeholder="'Masukkan nama client...'" :error="form.errors.name"/>
                        <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-500">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Email Client
                            <span class="text-red-500 ml-0.5">*</span>
                        </label>
                        <TextInputForm v-model="form.email" :form="form" :placeholder="'Masukkan email client...'" :error="form.errors.name"/>
                        <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-500">
                            {{ form.errors.email }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            No Telp
                            <span class="text-red-500 ml-0.5">*</span>
                        </label>
                        <TextInputForm v-model="form.phone" :form="form" :placeholder="'Masukkan no telp client...'" :error="form.errors.name"/>
                        <p v-if="form.errors.phone" class="mt-1.5 text-xs text-red-500">
                            {{ form.errors.phone }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Alamat
                            <span class="text-red-500 ml-0.5">*</span>
                        </label>
                        <TextInputForm v-model="form.address" :form="form" :placeholder="'Masukkan alamat client...'" :error="form.errors.name"/>
                        <p v-if="form.errors.address" class="mt-1.5 text-xs text-red-500">
                            {{ form.errors.address }}
                        </p>
                    </div>
                </div>

                <!-- Card: Saldo & Status -->
                <div class="rounded-xl bg-white dark:bg-gray-900 border
                            border-gray-200 dark:border-gray-800 p-6 space-y-5">

                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Informasi Luar
                    </h2>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Nama Perusahaan
                            <span class="text-red-500 ml-0.5">*</span>
                        </label>
                        <TextInputForm v-model="form.company" :form="form" :placeholder="'Masukkan nama perusahaan client...'" :error="form.errors.name"/>
                        <p v-if="form.errors.company" class="mt-1.5 text-xs text-red-500">
                            {{ form.errors.company }}
                        </p>
                    </div>

                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-2">
                    <Link
                        :href="route('clients.index')"
                        class="rounded-lg border border-gray-200 dark:border-gray-700
                               px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300
                               hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                    >
                        Batal
                    </Link>

                    <ButtonSubmit :btn-text="form.processing ? 'menyimpan...' : 'Kirim Data'" :form="form"/>
                </div>

            </form>
        </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import { watch } from 'vue';
export default { layout: AppLayout }
</script>