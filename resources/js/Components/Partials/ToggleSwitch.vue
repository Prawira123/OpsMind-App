<script setup>
    import { defineEmits } from 'vue';

    const props = defineProps({
        form : {type : Object, required : true},
    })

    const emit = defineEmits(['toggleValue']);

</script>

<template>
    <button
        type="button"
        @click="props.form.is_active = !props.form.is_active"
        :class="[
            'relative inline-flex h-6 w-11 shrink-0 cursor-pointer',
            'rounded-full border-2 border-transparent transition-colors duration-200',
            'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2',
            props.form.is_active
                ? 'bg-indigo-600'
                : 'bg-gray-200 dark:bg-gray-700',
        ]"
    >
        <span :class="[
            'pointer-events-none inline-block h-5 w-5 rounded-full',
            'bg-white shadow transform transition duration-200',
            props.form.is_active ? 'translate-x-5' : 'translate-x-0',
        ]"/>
    </button>
</template>