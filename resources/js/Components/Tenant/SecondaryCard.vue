<script setup>
    import LogoCard from '@/Components/Partials/LogoCard.vue'

    const props = defineProps({
        titleCard : {type : String, required : true},
        subTitle : {type : String, required : true},
        mainDesc : {type : Number, required : true},
        logoPath : {type : String, required : true},
        classLogo : {type : String, required : true},
    })

</script>


<template>
    <div class="rounded-xl bg-white dark:bg-gray-900 border
                        border-gray-200 dark:border-gray-800 p-5">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400
                               uppercase tracking-wider">
                        {{ props.titleCard }}
                    </p>
                    <div class="h-8 w-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30
                        flex items-center justify-center">
                        <LogoCard :logoPath="props.logoPath" :class="props.classLogo"/>
                    </div>
                </div>
                <p class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                    {{ props.mainDesc }}
                </p>
                <p class="text-xs text-gray-400 mt-1">{{ props.subTitle }}</p>
            </div>
</template>