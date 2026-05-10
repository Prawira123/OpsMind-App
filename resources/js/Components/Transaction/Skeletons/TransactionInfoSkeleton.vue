<template>
    <div class="rounded-xl bg-white dark:bg-gray-900 border
                border-gray-200 dark:border-gray-800 shadow-sm p-6 animate-pulse">
        <div class="w-32 h-4 bg-gray-200 dark:bg-gray-700 rounded mb-4"/>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div v-for="i in 6" :key="i" class="rounded-xl border border-gray-100 dark:border-gray-800
                        bg-gray-50/50 dark:bg-gray-800/30 px-4 py-3">
                <div class="w-16 h-3 bg-gray-100 dark:bg-gray-800 rounded mb-1.5"/>
                <div class="w-24 h-4 bg-gray-200 dark:bg-gray-700 rounded"/>
            </div>
        </div>
    </div>
</template>
