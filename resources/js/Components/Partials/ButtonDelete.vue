<script setup>

import LogoCard from './LogoCard.vue';

const props = defineProps({
    btnDesc : {type : String, required : true},
})

</script>

<template>
    <button
        class="flex items-center gap-2 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-3 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 transition"
    >
        <LogoCard :logo-path="'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'" :class="'h-4 w-4'"/>
        {{ props.btnDesc }}
    </button>
</template>