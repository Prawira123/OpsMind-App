<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

// =========================================================
// PROPS
// =========================================================
const props = defineProps({
    notifications: { type: Array, default: () => [] },
})

// =========================================================
// FILTER
// =========================================================
const activeFilter = ref('all')

const filters = [
    { key: 'all',          label: 'Semua' },
    { key: 'unread',       label: 'Belum Dibaca' },
    { key: 'subscription', label: 'Langganan' },
    { key: 'invoice',      label: 'Invoice' },
    { key: 'system',       label: 'Sistem' },
]

const filteredNotifications = computed(() => {
    if (activeFilter.value === 'all')    return props.notifications
    if (activeFilter.value === 'unread') return props.notifications.filter(n => !n.read_at)

    return props.notifications.filter(n => {
        const type = n.type ?? ''
        if (activeFilter.value === 'subscription') return type.includes('plan') || type.includes('subscription')
        if (activeFilter.value === 'invoice')      return type.includes('invoice')
        if (activeFilter.value === 'system')       return type.includes('system') || type.includes('login')
        return true
    })
})

const unreadCount = computed(() => props.notifications.filter(n => !n.read_at).length)

const typeConfig = {
    free_plan:                 { icon: '🎉', color: 'bg-emerald-100 dark:bg-emerald-900/30', dot: 'bg-emerald-500' },
    pro_monthly_plan:          { icon: '⭐', color: 'bg-indigo-100 dark:bg-indigo-900/30',   dot: 'bg-indigo-500'  },
    pro_yearly_plan:           { icon: '⭐', color: 'bg-indigo-100 dark:bg-indigo-900/30',   dot: 'bg-indigo-500'  },
    enterprise_monthly_plan:   { icon: '👑', color: 'bg-amber-100 dark:bg-amber-900/30',     dot: 'bg-amber-500'   },
    enterprise_yearly_plan:    { icon: '👑', color: 'bg-amber-100 dark:bg-amber-900/30',     dot: 'bg-amber-500'   },
    invoice_due:               { icon: '📄', color: 'bg-orange-100 dark:bg-orange-900/30',   dot: 'bg-orange-500'  },
    invoice_paid:              { icon: '✅', color: 'bg-green-100 dark:bg-green-900/30',     dot: 'bg-green-500'   },
    invoice_overdue:           { icon: '⚠️', color: 'bg-red-100 dark:bg-red-900/30',         dot: 'bg-red-500'     },
    system:                    { icon: '🔔', color: 'bg-gray-100 dark:bg-gray-800',           dot: 'bg-gray-500'    },
    subscription:              { icon: '💳', color: 'bg-violet-100 dark:bg-violet-900/30',   dot: 'bg-violet-500'  },
}

const getConfig = (type) => typeConfig[type] ?? { icon: '🔔', color: 'bg-gray-100 dark:bg-gray-800', dot: 'bg-gray-400' }

const markAsRead = (id) => {
    router.post(route('notifications.markAsRead', id), {}, {
        preserveScroll: true,
        preserveState:  true,  // ← tidak reload halaman
    })
}

const markAllAsRead = () => {
    router.post(route('notifications.markAllAsRead'), {}, {
        preserveScroll: true,
    })
}

const deleteNotification = (id) => {
    router.delete(route('notifications.destroy', id), {
        preserveScroll: true,
    })
}

const handleNotificationClick = (notif) => {
    router.get(route('notifications.show', notif.id), {
        preserveScroll: true
    })
}
</script>

<template>
    <div class="max-w-full mx-auto">

        <!-- ============================================ -->
        <!-- HEADER -->
        <!-- ============================================ -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Notifikasi
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ unreadCount > 0 ? `${unreadCount} belum dibaca` : 'Semua sudah dibaca' }}
                </p>
            </div>

            <!-- Tandai semua -->
            <button
                v-if="unreadCount > 0"
                @click="markAllAsRead"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-200
                       dark:border-gray-700 px-4 py-2 text-sm font-medium
                       text-gray-700 dark:text-gray-300 hover:bg-gray-50
                       dark:hover:bg-gray-800 transition"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tandai Semua Dibaca
            </button>
        </div>

        <!-- ============================================ -->
        <!-- FILTER TABS -->
        <!-- ============================================ -->
        <div class="mb-5 flex gap-2 overflow-x-auto pb-1">
            <button
                v-for="f in filters"
                :key="f.key"
                @click="activeFilter = f.key"
                :class="[
                    'shrink-0 rounded-full px-4 py-1.5 text-xs font-semibold transition-all duration-200',
                    activeFilter === f.key
                        ? 'bg-indigo-600 text-white shadow-sm'
                        : 'bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:border-indigo-300 hover:text-indigo-600',
                ]"
            >
                {{ f.label }}
                <span
                    v-if="f.key === 'unread' && unreadCount > 0"
                    :class="[
                        'ml-1.5 rounded-full px-1.5 py-0.5 text-xs',
                        activeFilter === 'unread'
                            ? 'bg-white/20 text-white'
                            : 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400',
                    ]"
                >
                    {{ unreadCount }}
                </span>
                <!-- <span
                    v-else
                    :class="[
                        'ml-1.5 rounded-full px-1.5 py-0.5 text-xs',
                        activeFilter === 'unread'
                            ? 'bg-white/20 text-white'
                            : 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400',
                    ]"
                >
                </span> -->
            </button>
        </div>

        <!-- ============================================ -->
        <!-- NOTIFICATION LIST -->
        <!-- ============================================ -->
        <div class="py-10 px-5 rounded-xl bg-white dark:bg-gray-900
                    border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

            <!-- Empty state -->
            <div v-if="filteredNotifications.length === 0"
                 class="flex flex-col items-center gap-3 py-20 px-4 text-center">
                <div class="h-16 w-16 rounded-2xl bg-gray-100 dark:bg-gray-800
                            flex items-center justify-center text-3xl">
                    🔔
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        Tidak ada notifikasi
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ activeFilter === 'unread' ? 'Semua notifikasi sudah dibaca' : 'Belum ada notifikasi masuk' }}
                    </p>
                </div>
            </div>

            <!-- List -->
            <div v-else  class="divide-y divide-gray-100 dark:divide-gray-800">
                <div
                    @click="handleNotificationClick(notif)"
                    v-for="(notif) in filteredNotifications"
                    :key="notif.id"
                    :class="[
                        'flex items-start gap-4 px-5 py-4 transition-colors group rounded-xl',
                        !notif.read_at
                            ? 'bg-indigo-50/40 dark:bg-indigo-900/10 hover:bg-indigo-50 dark:hover:bg-indigo-900/20'
                            : 'hover:bg-gray-50 dark:hover:bg-gray-800/50',
                    ]"
                >
                    <!-- Icon -->
                    <div :class="[
                        'h-10 w-10 rounded-xl flex items-center justify-center text-lg shrink-0 mt-0.5',
                        getConfig(notif.data?.type).color,
                    ]">
                        {{ getConfig(notif.data?.type).icon }}
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0 gap-1">

                                <!-- Unread dot + Title -->
                                <div class="flex items-center">
                                    <p :class="[
                                        'text-sm truncate',
                                        !notif.read_at
                                            ? 'font-semibold text-gray-900 dark:text-white'
                                            : 'font-medium text-gray-700 dark:text-gray-300',
                                    ]">
                                        {{ notif.title ?? 'Notifikasi' }}
                                    </p>
                                </div>

                                <!-- Message -->
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">
                                    {{ notif.message }}
                                </p>

                                <!-- Time + Link -->
                                <div class="flex items-center gap-3 mt-1.5">
                                    <span class="text-xs text-gray-400">
                                        {{ notif.time }}
                                    </span>
                                    <a
                                        v-if="notif.data?.url"
                                        :href="notif.data.url"
                                        class="text-xs text-indigo-600 dark:text-indigo-400
                                               hover:underline font-medium"
                                    >
                                        Lihat detail →
                                    </a>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-1 shrink-0 opacity-0
                                        group-hover:opacity-100 transition-opacity">

                                <!-- Mark as read -->
                                <button
                                    v-if="!notif.read_at"
                                    @click="markAsRead(notif.id)"
                                    title="Tandai sudah dibaca"
                                    class="flex h-7 w-7 items-center justify-center rounded-lg
                                           text-gray-400 hover:text-indigo-600
                                           hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>

                                <!-- Delete -->
                                <button
                                    @click="deleteNotification(notif.id)"
                                    title="Hapus"
                                    class="flex h-7 w-7 items-center justify-center rounded-lg
                                           text-gray-400 hover:text-red-600
                                           hover:bg-red-50 dark:hover:bg-red-900/20 transition"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>