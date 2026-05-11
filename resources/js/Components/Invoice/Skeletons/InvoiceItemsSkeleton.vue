<template>
    <div class="rounded-xl bg-white dark:bg-gray-900 border
                border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden animate-pulse">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800
                    flex items-center justify-between">
            <div class="h-4 w-24 bg-gray-200 dark:bg-gray-800 rounded"></div>
            <div class="h-6 w-16 bg-gray-200 dark:bg-gray-800 rounded-full"></div>
        </div>
        <div class="p-6 space-y-4">
            <div v-for="i in 3" :key="i" class="flex gap-4">
                <div class="h-10 w-full bg-gray-50 dark:bg-gray-800/50 rounded"></div>
            </div>
        </div>
        <div class="p-6 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
            <div class="ml-auto w-48 space-y-3">
                <div class="h-4 w-full bg-gray-200 dark:bg-gray-800 rounded"></div>
                <div class="h-6 w-full bg-indigo-100 dark:bg-indigo-900/30 rounded"></div>
            </div>
        </div>
    </div>
</template>
