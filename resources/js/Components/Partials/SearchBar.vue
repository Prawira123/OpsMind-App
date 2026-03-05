<script setup>
defineProps({
    modelValue: String,
    type: { type: String, default: 'text' },
    placeholder: { type: String, default: '' }
})
defineEmits(['update:modelValue'])
</script>

<template>
    <input
        :type="type"
        :placeholder="placeholder"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    />
</template>
