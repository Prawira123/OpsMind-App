<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppLayout from '@/Layouts/AppLayoutStatic.vue';
import { Head } from '@inertiajs/vue3';
import AppLayoutStatic from '@/Layouts/AppLayoutStatic.vue';
</script>

<template>

    <!-- <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        You're logged in!
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout> -->

    <AppLayoutStatic title="Dashboard">
        <div class="rounded-xl bg-white dark:bg-gray-900 p-6
                    border border-gray-200 dark:border-gray-800">
            <p class="text-gray-600 dark:text-gray-400">
                Konten dashboard akan ada di sini.
            </p>
        </div>
    </AppLayoutStatic>
</template>
