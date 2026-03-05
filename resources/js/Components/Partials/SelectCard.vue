<script setup>
    const props = defineProps({
        dataTypes : {type : Array, required : false},
        form : {type : Object, required : true},
    })
</script>

<template>
    <button
        v-for="type in props.dataTypes"
        :key="type.value"
        type="button"
        @click="props.form.type = type.value"
        :class="[
            'relative flex flex-col items-center gap-2 rounded-xl border-2 p-4',
            'text-sm font-medium transition-all duration-150 cursor-pointer',
            props.form.type === type.value
                ? type.activeColor
                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 bg-white dark:bg-gray-900',
        ]"
    >
        <!-- Check icon saat aktif -->
        <div v-if="props.form.type === type.value"
                class="absolute top-2 right-2 h-4 w-4 rounded-full
                    bg-indigo-600 flex items-center justify-center">
            <svg class="h-2.5 w-2.5 text-white" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <div :class="[
            'h-10 w-10 rounded-lg flex items-center justify-center',
            type.color,
        ]">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.5"
                    v-html="type.icon"/>
        </div>

        <div class="text-center">
            <p class="text-xs font-semibold text-gray-900 dark:text-white">
                {{ type.label }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">
                {{ type.desc }}
            </p>
        </div>
    </button>
</template>