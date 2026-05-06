<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    address: '',
    phone: '',
});

const submit = () => {
    form.post(route('tenant.store'), {
        onFinish: () => { form.reset('name', 'address', 'phone') }
    });
};
</script>

<template>
    <GuestLayout footerText="Tell us about your business">
        <Head title="Business Data" />

        <form @submit.prevent="submit" class="space-y-6">
            <div class="text-center space-y-2 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Business Information</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Tell us about your business to get started
                </p>
            </div>

            <!-- Business Name -->
            <div class="space-y-2">
                <InputLabel for="name" value="Business Name" class="text-gray-700 dark:text-gray-300 font-medium" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <TextInput
                        id="name"
                        type="text"
                        class="pl-11 block w-full rounded-xl border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700/50 dark:text-gray-100 transition-all duration-200"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="organization"
                        placeholder="Your Business Name"
                    />
                </div>
                <InputError class="mt-1 text-sm" :message="form.errors.name" />
            </div>

            <!-- Phone -->
            <div class="space-y-2">
                <InputLabel for="phone" value="Phone Number" class="text-gray-700 dark:text-gray-300 font-medium" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.383c0-.93-.636-1.762-1.525-1.962a48.64 48.64 0 00-5.728-.186c-.373.04-.745.08-1.118.103a96.83 96.83 0 01-3.118-.56c-.65-.168-1.288-.455-1.62-.842a2.25 2.25 0 01-.278-2.543c.465-.98.78-2.04.93-3.135.096-.93-.036-1.86-.406-2.725" />
                        </svg>
                    </div>
                    <TextInput
                        id="phone"
                        type="tel"
                        class="pl-11 block w-full rounded-xl border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700/50 dark:text-gray-100 transition-all duration-200"
                        v-model="form.phone"
                        required
                        autocomplete="tel"
                        placeholder="+62 812 3456 7890"
                    />
                </div>
                <InputError class="mt-1 text-sm" :message="form.errors.phone" />
            </div>

            <!-- Address -->
            <div class="space-y-2">
                <InputLabel for="address" value="Business Address" class="text-gray-700 dark:text-gray-300 font-medium" />
                <div class="relative">
                    <div class="absolute top-3 left-0 pl-4 flex items-start pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                    </div>
                    <textarea
                        id="address"
                        v-model="form.address"
                        required
                        rows="3"
                        placeholder="Enter your complete business address"
                        class="pl-11 block w-full rounded-xl border border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 dark:bg-gray-700/50 dark:text-gray-100 transition-all duration-200 py-2.5"
                    ></textarea>
                </div>
                <InputError class="mt-1 text-sm" :message="form.errors.address" />
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center py-3 rounded-xl text-base font-semibold bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 border-0 shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-[1.02]"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="!form.processing" class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Save & Continue</span>
                    </span>
                    <span v-else class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Saving...</span>
                    </span>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
