<script setup>

    import { defineEmits } from 'vue';
    import OptionSelect from './OptionSelect.vue'

    const props = defineProps({
        itemFilter : {type : Array, required : false},
        class : {type : String, required : true},
    })

    const emit = defineEmits(['update:modelValue'])

</script>


<template>
    <select
        v-model="modelValue"
        :class="props.class"
    >
    </select>
</template>