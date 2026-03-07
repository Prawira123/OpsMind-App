<script setup>
    const props = defineProps({
        btnText : {type : String, required : true},
        form : {type : Object, required : true},
    })
</script>


<template>
    <button
        type="submit"
        :disabled="props.form.processing"
        class="flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-2.5
                text-sm font-semibold text-white shadow-sm
                hover:bg-indigo-700 disabled:opacity-60
                disabled:cursor-not-allowed transition-colors"
    >
        <!-- Spinner saat loading -->
        <svg v-if="props.form.processing"
                class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10"
                    stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                    d="M5 13l4 4L19 7"/>
        </svg>
        {{ props.btnText }}
    </button>
</template>