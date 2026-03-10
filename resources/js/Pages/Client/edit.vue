<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import TextInputForm from '@/Components/Partials/TextInputForm.vue';
import ButtonSubmit from '@/Components/Partials/ButtonSubmit.vue';

// FORM — field sesuai fillable di model Account
const props = defineProps({
    client : Object,
})

const form = useForm({
    name:props.client.name ?? '',
    email:props.client.email ?? '',
    phone:props.client.phone ?? '',
    address: props.client.address ?? '',
    company:props.client.company ?? '',
})

watch(() => form.name, () => {
    console.log(form.name)
})

const submit = () => {
    form.put(route('clients.update', props.client.id))
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
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Edit Rekening - {{ props.client.name }}
                    </h1>
                </div>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                    {{ props.client.name }}
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
                    <Link :href="route('clients.index')"
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