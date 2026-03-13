<script setup>
import { router } from '@inertiajs/vue3'

// =========================================================
// PROPS
// =========================================================
const props = defineProps({
    notification: { type: Object, required: true },
})

// =========================================================
// TYPE CONFIG
// =========================================================
const typeConfig = {
    free_plan:               { icon: '🎉', label: 'Langganan Gratis',       color: 'bg-emerald-100 dark:bg-emerald-900/30', text: 'text-emerald-700 dark:text-emerald-400', border: 'border-emerald-200 dark:border-emerald-800' },
    pro_monthly_plan:        { icon: '⭐', label: 'Pro Plan Bulanan',        color: 'bg-indigo-100 dark:bg-indigo-900/30',   text: 'text-indigo-700 dark:text-indigo-400',   border: 'border-indigo-200 dark:border-indigo-800'   },
    pro_yearly_plan:         { icon: '⭐', label: 'Pro Plan Tahunan',        color: 'bg-indigo-100 dark:bg-indigo-900/30',   text: 'text-indigo-700 dark:text-indigo-400',   border: 'border-indigo-200 dark:border-indigo-800'   },
    enterprise_monthly_plan: { icon: '👑', label: 'Enterprise Plan Bulanan', color: 'bg-amber-100 dark:bg-amber-900/30',     text: 'text-amber-700 dark:text-amber-400',     border: 'border-amber-200 dark:border-amber-800'     },
    enterprise_yearly_plan:  { icon: '👑', label: 'Enterprise Plan Tahunan', color: 'bg-amber-100 dark:bg-amber-900/30',     text: 'text-amber-700 dark:text-amber-400',     border: 'border-amber-200 dark:border-amber-800'     },
    invoice_due:             { icon: '📄', label: 'Invoice Jatuh Tempo',     color: 'bg-orange-100 dark:bg-orange-900/30',   text: 'text-orange-700 dark:text-orange-400',   border: 'border-orange-200 dark:border-orange-800'   },
    invoice_paid:            { icon: '✅', label: 'Invoice Dibayar',         color: 'bg-green-100 dark:bg-green-900/30',     text: 'text-green-700 dark:text-green-400',     border: 'border-green-200 dark:border-green-800'     },
    invoice_overdue:         { icon: '⚠️', label: 'Invoice Terlambat',       color: 'bg-red-100 dark:bg-red-900/30',         text: 'text-red-700 dark:text-red-400',         border: 'border-red-200 dark:border-red-800'         },
    system:                  { icon: '🔔', label: 'Sistem',                  color: 'bg-gray-100 dark:bg-gray-800',           text: 'text-gray-700 dark:text-gray-400',       border: 'border-gray-200 dark:border-gray-700'       },
    subscription:            { icon: '💳', label: 'Langganan',               color: 'bg-violet-100 dark:bg-violet-900/30',   text: 'text-violet-700 dark:text-violet-400',   border: 'border-violet-200 dark:border-violet-800'   },
}

const config = typeConfig[props.notification.data?.type]
    ?? { icon: '🔔', label: 'Notifikasi', color: 'bg-gray-100 dark:bg-gray-800', text: 'text-gray-700 dark:text-gray-400', border: 'border-gray-200 dark:border-gray-700' }

// =========================================================
// ACTIONS
// =========================================================
const deleteNotification = () => {
    router.delete(route('notifications.destroy', props.notification.id), {
        onSuccess: () => router.visit(route('notifications.index')),
    })
}
</script>

<template>
    <div class="max-w-full mx-auto">

        <!-- BACK -->
        <div class="mb-6 flex items-center gap-3">
            <a
                :href="route('notifications.index')"
                class="flex h-9 w-9 items-center justify-center rounded-lg
                       border border-gray-200 dark:border-gray-700
                       text-gray-500 dark:text-gray-400
                       hover:bg-gray-100 dark:hover:bg-gray-800
                       hover:text-gray-900 dark:hover:text-white transition"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                Detail Notifikasi
            </h1>
        </div>

        <!-- CARD -->
        <div class="rounded-2xl bg-white dark:bg-gray-900
                    border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

            <!-- Top accent bar -->
            <div :class="['h-1.5 w-full', config.color.split(' ')[0]]"/>

            <div class="p-6">

                <!-- Icon + Type badge -->
                <div class="flex items-center gap-3 mb-6">
                    <div :class="[
                        'h-14 w-14 rounded-2xl flex items-center justify-center text-3xl shrink-0',
                        config.color,
                    ]">
                        {{ config.icon }}
                    </div>

                    <div>
                        <span :class="[
                            'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold border',
                            config.color, config.text, config.border,
                        ]">
                            {{ config.label }}
                        </span>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">
                            {{ notification.time }}
                            <span v-if="notification.read_at"
                                  class="ml-2 text-emerald-500">
                                · Sudah dibaca
                            </span>
                            <span v-else class="ml-2 text-indigo-500 font-medium">
                                · Belum dibaca
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-100 dark:border-gray-800 mb-6"/>

                <!-- Title -->
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3">
                    {{ notification.data?.title ?? 'Notifikasi' }}
                </h2>

                <!-- Message -->
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    {{ notification.data?.message }}
                </p>

                <!-- CTA Link -->
                <div v-if="notification.data?.url" class="mt-6">
                    <a
                        :href="notification.data.url"
                        :class="[
                            'inline-flex items-center gap-2 rounded-lg px-5 py-2.5',
                            'text-sm font-semibold text-white transition',
                            'bg-indigo-600 hover:bg-indigo-500',
                        ]"
                    >
                        Lihat Selengkapnya
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800
                        bg-gray-50 dark:bg-gray-800/50 flex justify-end">
                <button
                    @click="deleteNotification"
                    class="inline-flex items-center gap-2 rounded-lg px-4 py-2
                           text-sm font-medium text-red-600 dark:text-red-400
                           hover:bg-red-50 dark:hover:bg-red-900/20 transition"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Notifikasi
                </button>
            </div>
        </div>

    </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>