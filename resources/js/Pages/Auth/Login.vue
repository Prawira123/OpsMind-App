<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ButtonOAuth from '@/Components/ButtonOAuth.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import SecondaryButton from '@/Components/SecondaryButton.vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Status message -->
            <div v-if="status" class="mb-4 p-4 rounded-lg bg-green-50 border border-green-200">
                <p class="text-sm font-medium text-green-700 dark:text-green-400">
                    {{ status }}
                </p>
            </div>

            <!-- Email field -->
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
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="you@example.com"
                    />
                </div>

                <InputError class="mt-1 text-sm" :message="form.errors.email" />
            </div>

            <!-- Password field -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <InputLabel for="password" value="Password" class="text-gray-700 dark:text-gray-300 font-medium" />

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors"
                    >
                        Forgot password?
                    </Link>
                </div>

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
                        autocomplete="current-password"
                        placeholder="Enter your password"
                    />
                </div>

                <InputError class="mt-1 text-sm" :message="form.errors.password" />
            </div>

            <!-- Remember me & buttons -->
            <div class="pt-2 space-y-4">
                <!-- Remember me checkbox -->
                <label class="flex items-center space-x-3 cursor-pointer group">
                    <div class="relative">
                        <Checkbox
                            name="remember"
                            v-model:checked="form.remember"
                            class="w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500"
                        />
                    </div>
                    <span class="text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition-colors">
                        Remember me
                    </span>
                </label>

                <!-- Action buttons -->
                <div class="space-y-3 pt-2">
                    <PrimaryButton
                        class="w-full justify-center py-3 rounded-xl text-base font-semibold bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 border-0 shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-[1.02]"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        <span v-if="!form.processing" class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span>Log in</span>
                        </span>
                        <span v-else class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Signing in...</span>
                        </span>
                    </PrimaryButton>

                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200 dark:border-gray-600"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white dark:bg-gray-800 text-gray-500">or continue with</span>
                        </div>
                    </div>

                    <!-- OAuth button -->
                    <div class="pt-2">
                        <ButtonOAuth />
                    </div>

                    <!-- Register link -->
                    <div class="pt-4 text-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            Don't have an account?
                        </span>
                        <Link
                            :href="route('register')"
                            class="ml-1 text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors inline-flex items-center"
                        >
                            Create account
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>
