<script setup>
    const props = defineProps({
        item : {type : Object, required : true},
    })
</script>


<template>
    <option :value="item.key">{{ item.value }}</option>
</template>