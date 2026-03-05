<script setup>

    const props = defineProps({
        subDesc : {type : Number, required : true},
        mainDesc : {type : Number, required : true},
        titleCard : {type : String, required : true},
        subTitle : {type : String, required : true},
    })

</script>

<template>
    <div class="col-span-2 rounded-xl bg-gradient-to-br from-indigo-600
                        to-violet-600 p-5 text-white shadow-md
                        shadow-indigo-500/20 lg:col-span-1">
        <p class="text-xs font-medium text-indigo-200 uppercase tracking-wider">
            {{ props.titleCard }} 
        </p>
        <p class="mt-2 text-2xl font-bold truncate">
            {{ props.mainDesc }}
        </p>
        <p class="mt-1 text-xs text-indigo-200">
            {{props.subDesc}} {{ props.subTitle }}
        </p>
    </div>
</template>