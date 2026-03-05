<script setup>
    const props = defineProps({
        logoPath : {type : String, required : true},
        class : {type : String, required : true}, 
    })
</script>


<template>
    
        <svg :class="props.class" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                    :d="props.logoPath"/>
        </svg>
</template>