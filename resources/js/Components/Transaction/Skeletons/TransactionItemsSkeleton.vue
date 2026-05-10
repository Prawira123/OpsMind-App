<template>
    <div class="space-y-5 animate-pulse">
        <!-- Item Skeleton -->
        <div class="rounded-xl bg-white dark:bg-gray-900 border
                    border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div class="w-24 h-4 bg-gray-200 dark:bg-gray-700 rounded"/>
                <div class="w-12 h-5 bg-gray-100 dark:bg-gray-800 rounded-full"/>
            </div>
            <div class="p-6 space-y-4">
                <div v-for="i in 3" :key="i" class="flex justify-between items-center">
                    <div class="flex gap-4 items-center">
                        <div class="w-8 h-8 bg-gray-100 dark:bg-gray-800 rounded-full"/>
                        <div class="space-y-2">
                            <div class="w-32 h-4 bg-gray-200 dark:bg-gray-700 rounded"/>
                            <div class="w-48 h-3 bg-gray-100 dark:bg-gray-800 rounded"/>
                        </div>
                    </div>
                    <div class="w-24 h-4 bg-gray-200 dark:bg-gray-700 rounded"/>
                </div>
            </div>
        </div>
        <!-- Journal Skeleton -->
        <div class="rounded-xl bg-white dark:bg-gray-900 border
                    border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div class="space-y-1">
                    <div class="w-24 h-4 bg-gray-200 dark:bg-gray-700 rounded"/>
                    <div class="w-32 h-3 bg-gray-100 dark:bg-gray-800 rounded"/>
                </div>
                <div class="w-16 h-5 bg-gray-100 dark:bg-gray-800 rounded-full"/>
            </div>
            <div class="p-6 space-y-3">
                <div v-for="i in 2" :key="i" class="flex justify-between">
                    <div class="w-40 h-4 bg-gray-200 dark:bg-gray-700 rounded"/>
                    <div class="w-24 h-4 bg-gray-200 dark:bg-gray-700 rounded"/>
                </div>
            </div>
        </div>
    </div>
</template>
