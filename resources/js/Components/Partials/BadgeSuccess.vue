<script setup>
    const proops = defineProps({
        status : {type : String, required : true},
    })
</script>

<template>
    <div class="mb-6 rounded-xl bg-white dark:bg-green-900/30
                border border-green-200 dark:border-green-800 p-6 shadow-sm">
        <p class="text-sm text-green-500 dark:text-green-400">
            {{ status }}
        </p>
    </div>
</template>