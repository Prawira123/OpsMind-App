<script setup>

    import { Link } from '@inertiajs/vue3'

    const props = defineProps({
        href: { type: String, required: true },
        title : {type : String, required : true},
        desc : {type : String, required : true},
        btnDesc : {type : String, required : true},
    })

</script>

<template>

    <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ props.title }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ props.desc }}
                </p>
            </div>

            <Link
                :href="props.href"
                class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5
                       text-sm font-semibold text-white shadow-sm
                       hover:bg-indigo-700 transition-colors"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 4v16m8-8H4"/>
                </svg>
                Tambah {{ props.btnDesc }}
            </Link>
        </div>

</template>