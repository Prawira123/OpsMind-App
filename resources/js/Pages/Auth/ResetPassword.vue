<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    email: { type: String, required: true },
    token: { type: String, required: true },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout footerText="Set your new password">
        <Head title="Reset Password" />

        <form @submit.prevent="submit" class="space-y-6">
            <div class="text-center space-y-2 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Set New Password</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Please enter your new password below
                </p>
            </div>

            <!-- Email (read-only) -->
            <div class="space-y-2">
                <InputLabel for="email" value="Email" class="text-gray-700 dark:text-gray-300 font-medium" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                    <TextInput
                        id="email"
                        type="email"
                        class="pl-11 block w-full rounded-xl border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700/50 dark:text-gray-100 transition-all duration-200"
                        v-model="form.email"
                        disabled
                    />
                </div>
            </div>

            <!-- New Password -->
            <div class="space-y-2">
                <InputLabel for="password" value="New Password" class="text-gray-700 dark:text-gray-300 font-medium" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <TextInput
                        id="password"
                        type="password"
                        class="pl-11 block w-full rounded-xl border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700/50 dark:text-gray-100 transition-all duration-200"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="Enter new password"
                    />
                </div>
                <InputError class="mt-1 text-sm" :message="form.errors.password" />
            </div>

            <!-- Confirm Password -->
            <div class="space-y-2">
                <InputLabel for="password_confirmation" value="Confirm Password" class="text-gray-700 dark:text-gray-300 font-medium" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="pl-11 block w-full rounded-xl border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700/50 dark:text-gray-100 transition-all duration-200"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm new password"
                    />
                </div>
                <InputError class="mt-1 text-sm" :message="form.errors.password_confirmation" />
            </div>

            <!-- Submit button -->
            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center py-3 rounded-xl text-base font-semibold bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 border-0 shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-[1.02]"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="!form.processing" class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Reset Password</span>
                    </span>
                    <span v-else class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Resetting...</span>
                    </span>
                </PrimaryButton>
            </div>

            <!-- Back to login -->
            <div class="pt-4 text-center">
                <Link
                    :href="route('login')"
                    class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors inline-flex items-center"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Login
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
