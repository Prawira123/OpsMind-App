<script setup>
const columns = 6
const rows = 5
</script>

<template>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-800">
                    <th class="w-10 px-4 py-3">
                        <div class="h-4 w-4 rounded bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
                    </th>
                    <th v-for="i in columns" :key="i" class="px-4 py-3">
                        <div class="h-4 w-20 rounded bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
                    </th>
                    <th class="px-4 py-3 text-right">
                        <div class="ml-auto h-4 w-16 rounded bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800/50">
                <tr v-for="row in rows" :key="row" class="transition-colors">
                    <td class="px-4 py-3.5">
                        <div class="h-4 w-4 rounded bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
                    </td>
                    <td v-for="i in columns" :key="i" class="px-4 py-3.5">
                        <div
                            class="h-4 rounded bg-gray-200 dark:bg-gray-700 animate-pulse"
                            :class="i === columns ? 'w-24' : i === 1 ? 'w-32' : 'w-24'"
                        ></div>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1">
                            <div class="h-8 w-8 rounded-lg bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
                            <div class="h-8 w-8 rounded-lg bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
                            <div class="h-8 w-8 rounded-lg bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
