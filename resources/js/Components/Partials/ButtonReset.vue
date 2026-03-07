<script setup>
    const props = defineProps({
        form : {type : Object, required : true}
    })
</script>


<template>
    <button
        type="button"
        @click="props.form.reset()"
        class="rounded-lg px-4 py-2.5 text-sm font-medium
                text-gray-500 dark:text-gray-400
                hover:text-gray-700 dark:hover:text-gray-200
                hover:bg-gray-100 dark:hover:bg-gray-800 transition"
    >
        Reset
    </button>  
</template>