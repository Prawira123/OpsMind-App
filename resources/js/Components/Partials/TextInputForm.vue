<script setup>

import { defineEmits } from 'vue';

    const props = defineProps({
        form : {type : Object, required : true},
        modelValue : {type : String, required : true},
        placeholder : {type : String, required : true},
        inputMode : {type : String, required : false},
        error : {type : Text, required : false},
    });

    const emit = defineEmits(['update:modelValue']);
</script>


<template>
    <input
        type="text"
        :value="props.modelValue"
        @input="emit('update:modelValue', $event.target.value)"
        :placeholder="props.placeholder"
        :class="[
            'w-full rounded-lg border px-3.5 py-2.5 text-sm',
            'text-gray-900 dark:text-white placeholder-gray-400',
            'bg-white dark:bg-gray-800 transition',
            'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
            props.error
                ? 'border-red-400 dark:border-red-500'
                : 'border-gray-200 dark:border-gray-700',
        ]"
    />
</template>