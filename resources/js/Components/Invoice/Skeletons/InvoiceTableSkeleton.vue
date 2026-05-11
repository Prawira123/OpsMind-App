<template>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-800">
                    <th v-for="i in 7" :key="i" class="px-4 py-3">
                        <div class="h-3 w-20 bg-gray-100 dark:bg-gray-800 rounded animate-pulse"></div>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                <tr v-for="i in 5" :key="i">
                    <td v-for="j in 7" :key="j" class="px-4 py-4">
                        <div class="h-4 w-full bg-gray-50 dark:bg-gray-800/50 rounded animate-pulse"></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
