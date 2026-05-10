<script setup>
</script>

<template>
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div v-for="i in 3" :key="i" class="rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
            <div class="h-3 w-24 rounded bg-gray-200 dark:bg-gray-700 animate-pulse mb-3"></div>
            <div class="h-8 w-40 rounded bg-gray-200 dark:bg-gray-700 animate-pulse mb-3"></div>
            <div class="mt-2 h-1 w-full rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                <div class="h-full rounded-full bg-gray-200 dark:bg-gray-700 animate-pulse" style="width: 60%"></div>
            </div>
        </div>
    </div>
</template>
