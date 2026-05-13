<script setup>
</script>

<template>
    <div class="fixed inset-0 z-[60] flex flex-col items-center justify-center bg-gray-50/80 dark:bg-gray-950/80 backdrop-blur-md">
        <div class="relative">
            <!-- Outer spinning ring -->
            <div class="h-20 w-20 rounded-full border-4 border-indigo-500/20 border-t-indigo-600 animate-spin"></div>
            
            <!-- Inner pulsing logo background -->
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="h-12 w-12 rounded-xl bg-indigo-600 shadow-lg shadow-indigo-500/50 flex items-center justify-center animate-pulse">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="mt-8 text-center">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">
                Menyiapkan Paket Terbaik...
            </h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-[200px]">
                Mohon tunggu sejenak sementara kami mengambil rincian paket untuk Anda.
            </p>
        </div>
        
        <!-- Animated dots -->
        <div class="mt-6 flex gap-1.5">
            <div class="h-1.5 w-1.5 rounded-full bg-indigo-600 animate-bounce [animation-delay:-0.3s]"></div>
            <div class="h-1.5 w-1.5 rounded-full bg-indigo-600 animate-bounce [animation-delay:-0.15s]"></div>
            <div class="h-1.5 w-1.5 rounded-full bg-indigo-600 animate-bounce"></div>
        </div>
    </div>
</template>

<style scoped>
@keyframes pulse-soft {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(0.95); }
}
</style>
