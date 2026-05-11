<template>
    <div class="rounded-2xl bg-white dark:bg-gray-900 border
                border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden animate-pulse">
        <div class="h-1.5 bg-gray-200 dark:bg-gray-800"/>
        <div class="p-6">
            <div class="flex items-start justify-between gap-6 flex-wrap">
                <div>
                    <div class="h-3 w-24 bg-gray-200 dark:bg-gray-800 rounded mb-4"></div>
                    <div class="h-6 w-48 bg-gray-200 dark:bg-gray-800 rounded mb-2"></div>
                    <div class="h-4 w-40 bg-gray-100 dark:bg-gray-800/50 rounded mb-1"></div>
                    <div class="h-4 w-32 bg-gray-100 dark:bg-gray-800/50 rounded"></div>
                </div>
                <div class="text-right space-y-4">
                    <div v-for="i in 3" :key="i">
                        <div class="h-3 w-20 bg-gray-200 dark:bg-gray-800 rounded ml-auto mb-1"></div>
                        <div class="h-4 w-32 bg-gray-100 dark:bg-gray-800/50 rounded ml-auto"></div>
                    </div>
                </div>
            </div>
            <div class="mt-6 h-16 bg-gray-50 dark:bg-gray-800/50 rounded-xl"></div>
        </div>
    </div>
</template>
