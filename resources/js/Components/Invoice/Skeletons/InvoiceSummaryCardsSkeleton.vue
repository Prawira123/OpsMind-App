<template>
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-5">
        <div v-for="i in 5" :key="i"
             class="rounded-xl bg-white dark:bg-gray-900 border
                    border-gray-200 dark:border-gray-800 p-5 shadow-sm animate-pulse">
            <div class="h-3 w-20 bg-gray-200 dark:bg-gray-800 rounded mb-3"></div>
            <div class="h-8 w-12 bg-gray-200 dark:bg-gray-800 rounded mb-2"></div>
            <div class="h-3 w-24 bg-gray-100 dark:bg-gray-800/50 rounded"></div>
        </div>
    </div>
</template>
