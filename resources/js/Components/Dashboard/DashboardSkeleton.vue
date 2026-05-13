<template>
    <div class="space-y-6">
        <!-- KPI CARDS SKELETON -->
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <!-- Total Balance Skeleton -->
            <div class="col-span-2 lg:col-span-1 rounded-2xl bg-indigo-100/50 dark:bg-indigo-900/20 p-5 border border-indigo-200/50 dark:border-indigo-800/50 animate-pulse">
                <div class="flex items-center gap-2 mb-3">
                    <div class="h-7 w-7 rounded-lg bg-indigo-200 dark:bg-indigo-800" />
                    <div class="h-3 w-20 bg-indigo-200 dark:bg-indigo-800 rounded" />
                </div>
                <div class="h-8 w-32 bg-indigo-300 dark:bg-indigo-700 rounded mb-2" />
                <div class="h-3 w-24 bg-indigo-200 dark:bg-indigo-800 rounded" />
            </div>

            <!-- Other 3 KPI Cards -->
            <div v-for="i in 3" :key="i" class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 animate-pulse">
                <div class="flex items-center gap-2 mb-3">
                    <div class="h-7 w-7 rounded-lg bg-gray-100 dark:bg-gray-800" />
                    <div class="h-3 w-20 bg-gray-100 dark:bg-gray-800 rounded" />
                </div>
                <div class="h-7 w-28 bg-gray-200 dark:bg-gray-700 rounded mb-2" />
                <div class="h-3 w-16 bg-gray-100 dark:bg-gray-800 rounded" />
            </div>
        </div>

        <!-- CHART + SUMMARY SKELETON -->
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            <!-- Chart Skeleton -->
            <div class="lg:col-span-2 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-6 animate-pulse">
                <div class="flex items-center justify-between mb-6">
                    <div class="h-4 w-32 bg-gray-100 dark:bg-gray-800 rounded" />
                    <div class="h-4 w-24 bg-gray-100 dark:bg-gray-800 rounded" />
                </div>
                <div class="h-48 w-full bg-gray-50 dark:bg-gray-800/50 rounded flex items-end gap-2 px-4 pb-4">
                    <div v-for="i in 12" :key="i" :class="['flex-1 bg-gray-200 dark:bg-gray-700 rounded-t', i % 2 === 0 ? 'h-3/4' : 'h-1/2']" />
                </div>
            </div>

            <!-- Summary Skeleton -->
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-6 animate-pulse">
                <div class="h-4 w-36 bg-gray-100 dark:bg-gray-800 rounded mb-6" />
                <div class="flex justify-center mb-6">
                    <div class="h-32 w-32 rounded-full border-8 border-gray-100 dark:border-gray-800 border-t-indigo-500" />
                </div>
                <div class="space-y-3">
                    <div v-for="i in 2" :key="i" class="flex items-center justify-between">
                        <div class="h-3 w-20 bg-gray-100 dark:bg-gray-800 rounded" />
                        <div class="h-3 w-24 bg-gray-100 dark:bg-gray-800 rounded" />
                    </div>
                </div>
            </div>
        </div>

        <!-- TRANSACTIONS + CLIENTS SKELETON -->
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            <!-- Transactions Skeleton -->
            <div class="lg:col-span-2 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 overflow-hidden animate-pulse">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="h-4 w-32 bg-gray-100 dark:bg-gray-800 rounded" />
                    <div class="h-3 w-16 bg-gray-100 dark:bg-gray-800 rounded" />
                </div>
                <div class="divide-y divide-gray-50 dark:divide-gray-800/50">
                    <div v-for="i in 5" :key="i" class="flex items-center gap-4 px-5 py-3.5">
                        <div class="h-9 w-9 rounded-xl bg-gray-100 dark:bg-gray-800 shrink-0" />
                        <div class="flex-1">
                            <div class="h-3.5 w-1/2 bg-gray-100 dark:bg-gray-800 rounded mb-2" />
                            <div class="h-2.5 w-1/4 bg-gray-50 dark:bg-gray-800/50 rounded" />
                        </div>
                        <div class="h-4 w-20 bg-gray-100 dark:bg-gray-800 rounded shrink-0" />
                    </div>
                </div>
            </div>

            <!-- Top Clients Skeleton -->
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 overflow-hidden animate-pulse">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="h-4 w-24 bg-gray-100 dark:bg-gray-800 rounded" />
                    <div class="h-3 w-16 bg-gray-100 dark:bg-gray-800 rounded" />
                </div>
                <div class="divide-y divide-gray-50 dark:divide-gray-800/50">
                    <div v-for="i in 5" :key="i" class="flex items-center gap-3 px-5 py-3.5">
                        <div class="h-7 w-7 rounded-lg bg-gray-100 dark:bg-gray-800 shrink-0" />
                        <div class="flex-1">
                            <div class="h-3.5 w-2/3 bg-gray-100 dark:bg-gray-800 rounded mb-1.5" />
                            <div class="h-2.5 w-1/3 bg-gray-50 dark:bg-gray-800/50 rounded" />
                        </div>
                        <div class="h-5 w-5 rounded bg-gray-100 dark:bg-gray-800 shrink-0" />
                    </div>
                </div>
                <!-- Quick Access Skeleton -->
                <div class="border-t border-gray-100 dark:border-gray-800 p-4">
                    <div class="h-3 w-20 bg-gray-100 dark:bg-gray-800 rounded mb-4" />
                    <div class="grid grid-cols-2 gap-2">
                        <div v-for="i in 4" :key="i" class="h-8 rounded-lg bg-gray-50 dark:bg-gray-800" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
