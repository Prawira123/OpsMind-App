<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="relative min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 p-4 sm:p-6 lg:p-8">
        <!-- Background decorative elements with blur effect -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-indigo-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-pink-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
        </div>

        <!-- Logo area with glassmorphism effect -->
        <div class="relative z-10 mb-8 sm:mb-10">
            <div class="flex flex-col items-center space-y-3">
                <div class="relative">
                    <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full blur opacity-30"></div>
                    <div class="relative bg-white dark:bg-gray-800 rounded-full p-3 shadow-lg">
                        <ApplicationLogo class="h-16 w-16 fill-current text-indigo-600 dark:text-indigo-400" />
                    </div>
                </div>
                <Link
                    href="/"
                    class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400 hover:from-indigo-700 hover:to-purple-700 transition-colors"
                >
                    {{ $page.props.appName || 'OpsMind' }}
                </Link>
            </div>
        </div>

        <!-- Login card with glassmorphism effect -->
        <div class="relative z-10 w-full max-w-md">
            <div class="backdrop-blur-xl bg-white/80 dark:bg-gray-800/80 rounded-2xl shadow-2xl shadow-indigo-500/10 dark:shadow-purple-500/10 border border-white/50 dark:border-gray-700/50 overflow-hidden">
                <!-- Card header gradient -->
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

                <div class="p-8 sm:p-10">
                    <slot />
                </div>
            </div>

            <!-- Subtle footer text -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Welcome back to your dashboard
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes blob {
    0%, 100% {
        transform: translate(0, 0) scale(1);
    }
    25% {
        transform: translate(20px, -20px) scale(1.05);
    }
    50% {
        transform: translate(-15px, 20px) scale(0.95);
    }
    75% {
        transform: translate(15px, 15px) scale(1.02);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>
