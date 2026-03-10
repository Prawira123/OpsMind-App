<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted, computed } from 'vue'

defineProps({
    title: { type: String, default: '' },
})

const page = usePage()

// Data dari HandleInertiaRequests shared props
const user   = computed(() => page.props.auth?.user)

const avatarInitial = computed(() =>
    user.value?.name?.charAt(0)?.toUpperCase() ?? 'U'
)

// =========================================================
// CEK ROUTE AKTIF
// =========================================================
const isActive = (routeName) => {
    if (!routeName) return false
    try {
        if (route().current(routeName)) return true

        // Cek wildcard — ambil prefix sebelum titik terakhir
        // 'accounts.index' → cek 'accounts.*'
        const prefix = routeName.split('.').slice(0, -1).join('.')
        if (prefix) return route().current(`${prefix}.*`)

        return false
    } catch {
        return false
    }
}

// Parent aktif jika salah satu child aktif
const isChildActive = (children) => {
    if (!children) return false
    return children.some(child => isActive(child.routeName))
}

// =========================================================
// DARK MODE
// =========================================================
const isDark = ref(false)

const initDarkMode = () => {
    const saved = localStorage.getItem('theme')
    isDark.value = saved ? saved === 'dark'
                         : window.matchMedia('(prefers-color-scheme: dark)').matches
    document.documentElement.classList.toggle('dark', isDark.value)
}

const toggleDarkMode = () => {
    isDark.value = !isDark.value
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
    document.documentElement.classList.toggle('dark', isDark.value)
}

onMounted(initDarkMode)

// =========================================================
// SIDEBAR
// =========================================================
const sidebarOpen    = ref(true)
const mobileMenuOpen = ref(false)

const handleResize = () => {
    if (window.innerWidth >= 1024) mobileMenuOpen.value = false
}

onMounted(() => window.addEventListener('resize', handleResize))
onUnmounted(() => window.removeEventListener('resize', handleResize))

// =========================================================
// NAVIGATION
// routeName → untuk deteksi active via Ziggy route().current()
// href      → untuk navigasi, '#' jika route belum dibuat
// =========================================================
const navigation = [
    {
        name:      'Dashboard',
        routeName: 'dashboard',
        href:      route('dashboard'),
        icon:      `<path stroke-linecap="round" stroke-linejoin="round"
                         d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2
                            2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0
                            011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>`,
    },
    {
        name:      'Transaksi',
        routeName: 'transactions.index',
        href:      '#', // TODO: route('transactions.index')
        icon:      `<path stroke-linecap="round" stroke-linejoin="round"
                         d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>`,
    },
    {
        name:      'Invoice',
        routeName: 'invoices.index',
        href:      '#', // TODO: route('invoices.index')
        icon:      `<path stroke-linecap="round" stroke-linejoin="round"
                         d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                            a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2
                            0 01-2 2z"/>`,
    },
    {
        name:     'Laporan',
        href:     null,
        icon:     `<path stroke-linecap="round" stroke-linejoin="round"
                         d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2
                            2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0
                            002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2
                            2 0 01-2 2h-2a2 2 0 01-2-2z"/>`,
        children: [
            { name: 'Laba Rugi',  routeName: 'reports.income',   href: '#' },
            { name: 'Neraca',     routeName: 'reports.balance',  href: '#' },
            { name: 'Arus Kas',   routeName: 'reports.cashflow', href: '#' },
            { name: 'Buku Besar', routeName: 'reports.ledger',   href: '#' },
        ],
    },
    {
        name:     'Master Data',
        href:     null,
        icon:     `<path stroke-linecap="round" stroke-linejoin="round"
                         d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582
                            4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0
                            2.21-3.582 4-8 4s-8-1.79-8-4"/>`,
        children: [
            { name: 'Rekening',          routeName: 'accounts.index',    href: route('accounts.index') },
            { name: 'Kategori',          routeName: 'categories.index',  href: route('categories.index') },
            { name: 'Chart of Accounts', routeName: 'chart-of-accounts.index',         href: route('chart-of-accounts.index') },
            { name: 'Klien',             routeName: 'clients.index',     href: route('clients.index') },
        ],
    },
]

const bottomNavigation = [
    {
        name:      'Langganan',
        routeName: 'subscription.index',
        href:      '#',
        icon:      `<path stroke-linecap="round" stroke-linejoin="round"
                         d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593
                            3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296
                            1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746
                            3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745
                            0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296
                            3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63
                            3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043
                            3.296A3.745 3.745 0 0121 12z"/>`,
    },
    {
        name:      'Pengaturan',
        routeName: 'settings.index',
        href:      '#',
        icon:      `<path stroke-linecap="round" stroke-linejoin="round"
                         d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398
                            1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22
                            .127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37
                            .49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24
                            -.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43
                            .99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125
                            0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57
                            6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09
                            .543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213
                            -1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127
                            c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01
                            -1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827
                            c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378
                            -.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297
                            -2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072
                            1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644
                            -.869l.214-1.281z"/>
                 <path stroke-linecap="round" stroke-linejoin="round"
                       d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>`,
    },
]

// =========================================================
// DROPDOWN
// Auto-buka saat halaman load jika child aktif
// =========================================================
const openDropdowns = ref({
    'Laporan':     false,
    'Master Data': false,
})

onMounted(() => {
    navigation.forEach(item => {
        if (item.children && isChildActive(item.children)) {
            openDropdowns.value[item.name] = true
        }
    })
})

const toggleDropdown = (name) => {
    openDropdowns.value[name] = !openDropdowns.value[name]
}

// =========================================================
// NOTIF & USER MENU
// =========================================================
const showNotifications = ref(false)
const showUserMenu      = ref(false)

const closeAll = (e) => {
    if (!e.target.closest('.notif-btn')) showNotifications.value = false
    if (!e.target.closest('.user-btn'))  showUserMenu.value = false
}

onMounted(() => document.addEventListener('click', closeAll))
onUnmounted(() => document.removeEventListener('click', closeAll))
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-950 transition-colors duration-300">

        <!-- MOBILE OVERLAY -->
        <div v-if="mobileMenuOpen"
             class="fixed inset-0 z-20 bg-black/50 backdrop-blur-sm lg:hidden"
             @click="mobileMenuOpen = false"/>

        <!-- SIDEBAR -->
        <aside :class="[
            'fixed top-0 left-0 z-30 h-full flex flex-col',
            'bg-white dark:bg-gray-900',
            'border-r border-gray-200 dark:border-gray-800',
            'transition-all duration-300 ease-in-out',
            sidebarOpen ? 'w-64' : 'w-16',
            mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        ]">

            <!-- Logo -->
            <div class="flex h-16 items-center justify-between px-3 shrink-0
                        border-b border-gray-200 dark:border-gray-800">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center
                                rounded-lg bg-gradient-to-br from-indigo-600 to-violet-600">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <Transition enter-from-class="opacity-0 -translate-x-2"
                                enter-active-class="transition duration-200"
                                leave-to-class="opacity-0 -translate-x-2"
                                leave-active-class="transition duration-100">
                        <div v-if="sidebarOpen" class="overflow-hidden">
                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                {{ user.tenant?.name ?? 'OpsMind' }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">OpsMind Suite</p>
                        </div>
                    </Transition>
                </div>

                <button @click="sidebarOpen = !sidebarOpen" :class="!sidebarOpen ? 'w-full' : ''"
                        class="hidden lg:flex h-7 w-7 items-center justify-center
                               rounded-md text-gray-400 hover:text-gray-600
                               dark:hover:text-gray-300 hover:bg-gray-100
                               dark:hover:bg-gray-800 transition shrink-0">
                    <svg class="h-4 w-4 transition-transform duration-300"
                         :class="sidebarOpen ? '' : 'rotate-180'"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>
            </div>

            <!-- Nav -->
            <nav class="flex-1 overflow-y-auto overflow-x-hidden py-4 px-2 space-y-0.5">
                <template v-for="item in navigation" :key="item.name">

                    <!-- Item tanpa children -->
                    <a v-if="!item.children" :href="item.href"
                       :class="[
                           'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm',
                           'font-medium transition-all duration-150 group relative',
                           isActive(item.routeName)
                               ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400'
                               : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white',
                       ]">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="1.5" v-html="item.icon"/>
                        <Transition enter-from-class="opacity-0"
                                    enter-active-class="transition duration-200 delay-75"
                                    leave-to-class="opacity-0"
                                    leave-active-class="transition duration-100">
                            <span v-if="sidebarOpen" class="truncate">{{ item.name }}</span>
                        </Transition>
                        <!-- Active bar -->
                        <div v-if="isActive(item.routeName)"
                             class="absolute right-0 top-1/2 -translate-y-1/2
                                    w-1 h-6 bg-indigo-600 rounded-l-full"/>
                        <!-- Tooltip collapsed -->
                        <span v-if="!sidebarOpen"
                              class="absolute left-full ml-2 px-2 py-1 rounded-md bg-gray-900
                                     text-white text-xs whitespace-nowrap opacity-0
                                     group-hover:opacity-100 pointer-events-none transition-opacity z-50">
                            {{ item.name }}
                        </span>
                    </a>

                    <!-- Item dengan children -->
                    <div v-else>
                        <button @click="toggleDropdown(item.name)"
                                :class="[
                                    'w-full flex items-center gap-3 rounded-lg px-3 py-2.5',
                                    'text-sm font-medium transition-all duration-150 group relative',
                                    // Parent highlight jika child aktif
                                    isChildActive(item.children)
                                        ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400'
                                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white',
                                ]">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="1.5" v-html="item.icon"/>
                            <Transition enter-from-class="opacity-0"
                                        enter-active-class="transition duration-200 delay-75"
                                        leave-to-class="opacity-0"
                                        leave-active-class="transition duration-100">
                                <span v-if="sidebarOpen" class="flex-1 text-left truncate">
                                    {{ item.name }}
                                </span>
                            </Transition>
                            <svg v-if="sidebarOpen"
                                 class="h-4 w-4 shrink-0 transition-transform duration-200"
                                 :class="openDropdowns[item.name] ? 'rotate-180' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                 stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 9l-7 7-7-7"/>
                            </svg>
                            <span v-if="!sidebarOpen"
                                  class="absolute left-full ml-2 px-2 py-1 rounded-md bg-gray-900
                                         text-white text-xs whitespace-nowrap opacity-0
                                         group-hover:opacity-100 pointer-events-none transition-opacity z-50">
                                {{ item.name }}
                            </span>
                        </button>

                        <!-- Children list -->
                        <Transition enter-from-class="opacity-0 -translate-y-1"
                                    enter-active-class="transition duration-200"
                                    leave-to-class="opacity-0 -translate-y-1"
                                    leave-active-class="transition duration-150">
                            <div v-if="openDropdowns[item.name] && sidebarOpen"
                                 class="mt-0.5 ml-4 pl-4 border-l-2
                                        border-gray-200 dark:border-gray-700 space-y-0.5">
                                <a v-for="child in item.children"
                                   :key="child.name"
                                   :href="child.href"
                                   :class="[
                                       'flex items-center gap-2 rounded-lg px-3 py-2 text-sm',
                                       'transition-all duration-150',
                                       isActive(child.routeName)
                                           ? 'text-indigo-600 dark:text-indigo-400 font-semibold bg-indigo-50 dark:bg-indigo-900/20'
                                           : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800',
                                   ]">
                                    <!-- Dot indikator child active -->
                                    <div :class="[
                                        'h-1.5 w-1.5 rounded-full shrink-0 transition-colors',
                                        isActive(child.routeName)
                                            ? 'bg-indigo-600 dark:bg-indigo-400'
                                            : 'bg-gray-300 dark:bg-gray-600'
                                    ]"/>
                                    {{ child.name }}
                                </a>
                            </div>
                        </Transition>
                    </div>
                </template>

                <!-- Divider -->
                <div class="my-3 border-t border-gray-200 dark:border-gray-800"/>

                <!-- Bottom nav -->
                <a v-for="item in bottomNavigation" :key="item.name" :href="item.href"
                   :class="[
                       'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm',
                       'font-medium transition-all duration-150 group relative',
                       isActive(item.routeName)
                           ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400'
                           : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white',
                   ]">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="1.5" v-html="item.icon"/>
                    <Transition enter-from-class="opacity-0"
                                enter-active-class="transition duration-200 delay-75"
                                leave-to-class="opacity-0"
                                leave-active-class="transition duration-100">
                        <span v-if="sidebarOpen" class="truncate">{{ item.name }}</span>
                    </Transition>
                    <div v-if="isActive(item.routeName)"
                         class="absolute right-0 top-1/2 -translate-y-1/2
                                w-1 h-6 bg-indigo-600 rounded-l-full"/>
                    <span v-if="!sidebarOpen"
                          class="absolute left-full ml-2 px-2 py-1 rounded-md bg-gray-900
                                 text-white text-xs whitespace-nowrap opacity-0
                                 group-hover:opacity-100 pointer-events-none transition-opacity z-50">
                        {{ item.name }}
                    </span>
                </a>
            </nav>

            <!-- User bottom sidebar -->
            <div class="shrink-0 border-t border-gray-200 dark:border-gray-800 p-3">
                <div class="flex items-center gap-3 rounded-lg px-2 py-2
                            hover:bg-gray-100 dark:hover:bg-gray-800 transition cursor-pointer">
                    <div v-if="!user.tenant?.logo" class="h-7 w-7 rounded-full bg-gradient-to-br from-indigo-500
                                to-violet-600 flex items-center justify-center
                                text-white text-xs font-bold shrink-0">
                        {{ avatarInitial }}
                    </div>
                    <div v-if="user.tenant?.logo" class="h-7 w-7 rounded-full bg-cover bg-center flex items-center justify-center" :style="{ backgroundImage: `url(${user.tenant?.logo})` }"></div>
                    <Transition enter-from-class="opacity-0"
                                enter-active-class="transition duration-200 delay-75"
                                leave-to-class="opacity-0"
                                leave-active-class="transition duration-100">
                        <div v-if="sidebarOpen" class="overflow-hidden">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ user?.name }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 truncate">
                                {{ user?.email }}
                            </p>
                        </div>
                    </Transition>
                </div>
            </div>
        </aside>

        <!-- ================================================ -->
        <!-- MAIN -->
        <!-- ================================================ -->
        <div :class="[
            'flex flex-col min-h-screen transition-all duration-300',
            sidebarOpen ? 'lg:pl-64' : 'lg:pl-16',
        ]">

            <!-- TOP NAVBAR -->
            <header class="sticky top-0 z-20 flex h-16 items-center gap-3 px-4
                           bg-white/80 dark:bg-gray-900/80 backdrop-blur-md
                           border-b border-gray-200 dark:border-gray-800">

                <button @click="mobileMenuOpen = true"
                        class="lg:hidden flex h-9 w-9 items-center justify-center
                               rounded-lg text-gray-500 hover:bg-gray-100
                               dark:hover:bg-gray-800 transition">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Search -->
                <div class="hidden lg:flex flex-1 max-w-md">
                    <div class="relative w-full">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                        <input type="text"
                               placeholder="Cari transaksi, invoice, klien..."
                               class="w-full rounded-lg border border-gray-200 dark:border-gray-700
                                      bg-gray-50 dark:bg-gray-800 pl-9 pr-16 py-2 text-sm
                                      text-gray-900 dark:text-white placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      focus:border-transparent transition"/>
                        <kbd class="absolute right-3 top-1/2 -translate-y-1/2 inline-flex
                                    items-center rounded border border-gray-200 dark:border-gray-600
                                    bg-white dark:bg-gray-700 px-1.5 py-0.5 text-xs text-gray-400">
                            ⌘K
                        </kbd>
                    </div>
                </div>

                <div class="flex-1"/>

                <div class="flex items-center gap-2">

                    <!-- AI ASSISTANT -->
                    <a href="#"
                       class="relative flex items-center gap-2 rounded-lg px-3 py-1.5
                              bg-gradient-to-r from-violet-600 to-indigo-600
                              hover:from-violet-500 hover:to-indigo-500
                              text-white text-sm font-semibold
                              shadow-md shadow-indigo-500/30 hover:shadow-lg
                              hover:shadow-indigo-500/50 transition-all duration-200
                              overflow-hidden group">
                        <span class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10
                                     to-white/0 -translate-x-full group-hover:translate-x-full
                                     transition-transform duration-700 ease-in-out"/>
                        <svg class="h-4 w-4 shrink-0 relative z-10" viewBox="0 0 24 24"
                             fill="currentColor">
                            <path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3
                                     2.4-7.4L2 9.4h7.6z"/>
                        </svg>
                        <span class="hidden sm:block whitespace-nowrap relative z-10">
                            AI Assistant
                        </span>
                        <span class="relative flex h-2 w-2 shrink-0 z-10">
                            <span class="animate-ping absolute inline-flex h-full w-full
                                         rounded-full bg-white/70"/>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-white"/>
                        </span>
                    </a>

                    <div class="h-6 w-px bg-gray-200 dark:bg-gray-700"/>

                    <!-- Dark mode -->
                    <button @click="toggleDarkMode" :title="isDark ? 'Light Mode' : 'Dark Mode'"
                            class="flex h-9 w-9 items-center justify-center rounded-lg
                                   text-gray-500 dark:text-gray-400 hover:bg-gray-100
                                   dark:hover:bg-gray-800 hover:text-gray-900
                                   dark:hover:text-white transition">
                        <svg v-if="isDark" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386
                                     6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591
                                     1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75
                                     3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                        </svg>
                        <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385
                                     0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753
                                     9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753
                                     0 009.002-5.998z"/>
                        </svg>
                    </button>

                    <!-- Notifikasi -->
                    <div class="relative notif-btn">
                        <button @click.stop="showNotifications = !showNotifications"
                                class="relative flex h-9 w-9 items-center justify-center
                                       rounded-lg text-gray-500 dark:text-gray-400
                                       hover:bg-gray-100 dark:hover:bg-gray-800
                                       hover:text-gray-900 dark:hover:text-white transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967
                                         8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967
                                         0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714
                                         0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                            </svg>
                            <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-red-500"/>
                        </button>

                        <Transition enter-from-class="opacity-0 translate-y-1"
                                    enter-active-class="transition duration-200"
                                    leave-to-class="opacity-0 translate-y-1"
                                    leave-active-class="transition duration-150">
                            <div v-if="showNotifications"
                                 class="absolute right-0 top-full mt-2 w-80 rounded-xl
                                        bg-white dark:bg-gray-900 border
                                        border-gray-200 dark:border-gray-700
                                        shadow-lg overflow-hidden z-50">
                                <div class="flex items-center justify-between px-4 py-3
                                            border-b border-gray-100 dark:border-gray-800">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        Notifikasi
                                    </p>
                                    <button class="text-xs text-indigo-600 hover:underline">
                                        Tandai semua dibaca
                                    </button>
                                </div>
                                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                                    <div class="flex gap-3 px-4 py-3 hover:bg-gray-50
                                                dark:hover:bg-gray-800 cursor-pointer transition">
                                        <div class="h-8 w-8 rounded-full bg-green-100
                                                    dark:bg-green-900/30 flex items-center
                                                    justify-center shrink-0">
                                            <svg class="h-4 w-4 text-green-600" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor"
                                                 stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-900 dark:text-white">
                                                Invoice #INV-001 telah dibayar
                                            </p>
                                            <p class="text-xs text-gray-400 mt-0.5">2 menit lalu</p>
                                        </div>
                                        <div class="h-2 w-2 rounded-full bg-indigo-500 shrink-0 mt-1.5"/>
                                    </div>
                                    <div class="flex gap-3 px-4 py-3 hover:bg-gray-50
                                                dark:hover:bg-gray-800 cursor-pointer transition">
                                        <div class="h-8 w-8 rounded-full bg-yellow-100
                                                    dark:bg-yellow-900/30 flex items-center
                                                    justify-center shrink-0">
                                            <svg class="h-4 w-4 text-yellow-600" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor"
                                                 stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0
                                                         2.502-1.667 1.732-3L13.732 4c-.77-1.333
                                                         -2.694-1.333-3.464 0L3.34 16c-.77 1.333
                                                         .192 3 1.732 3z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-900 dark:text-white">
                                                Invoice #INV-003 jatuh tempo besok
                                            </p>
                                            <p class="text-xs text-gray-400 mt-0.5">1 jam lalu</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-4 py-2.5 border-t border-gray-100
                                            dark:border-gray-800 text-center">
                                    <a href="#" class="text-xs text-indigo-600 hover:underline">
                                        Lihat semua notifikasi
                                    </a>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <div class="h-6 w-px bg-gray-200 dark:bg-gray-700"/>

                    <!-- User Menu -->
                    <div class="relative user-btn">
                        <button @click.stop="showUserMenu = !showUserMenu"
                                class="flex items-center gap-2 rounded-lg px-2 py-1.5
                                       hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                            <div v-if="!user.tenant?.logo" class="h-7 w-7 rounded-full bg-gradient-to-br from-indigo-500
                                        to-violet-600 flex items-center justify-center
                                        text-white text-xs font-bold shrink-0">
                                {{ avatarInitial }}
                            </div>
                            <div v-if="user.tenant?.logo" class="h-7 w-7 rounded-full bg-cover bg-center flex items-center justify-center" :style="{ backgroundImage: `url(${user.tenant?.logo})` }">
                            </div>
                            <span class="hidden sm:block text-sm font-medium text-gray-700
                                         dark:text-gray-300 max-w-28 truncate">
                                {{ user?.name }}
                            </span>
                            <svg class="h-4 w-4 text-gray-400 transition-transform duration-200"
                                 :class="showUserMenu ? 'rotate-180' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                 stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <Transition enter-from-class="opacity-0 translate-y-1"
                                    enter-active-class="transition duration-200"
                                    leave-to-class="opacity-0 translate-y-1"
                                    leave-active-class="transition duration-150">
                            <div v-if="showUserMenu"
                                 class="absolute right-0 top-full mt-2 w-56 rounded-xl
                                        bg-white dark:bg-gray-900 border
                                        border-gray-200 dark:border-gray-700
                                        shadow-lg overflow-hidden z-50">
                                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        {{ user?.name }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate">
                                        {{ user?.email }}
                                    </p>
                                </div>
                                <div class="py-1">
                                    <a :href="route('profiles.index')"
                                       class="flex items-center gap-3 px-4 py-2 text-sm
                                              text-gray-700 dark:text-gray-300
                                              hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0
                                                     017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933
                                                     17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                        </svg>
                                        Profil Saya
                                    </a>
                                    <a href="#"
                                       class="flex items-center gap-3 px-4 py-2 text-sm
                                              text-gray-700 dark:text-gray-300
                                              hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55
                                                     0 1.02.398 1.11.94l.213 1.281c.063.374.313.686
                                                     .645.87.074.04.147.083.22.127.324.196.72.257
                                                     1.075.124l1.217-.456a1.125 1.125 0 011.37.49
                                                     l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003
                                                     .827c-.293.24-.438.613-.431.992a6.759 6.759 0
                                                     010 .255c-.007.378.138.75.43.99l1.005.828c.424
                                                     .35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0
                                                     01-1.369.491l-1.217-.456c-.355-.133-.75-.072
                                                     -1.076.124a6.57 6.57 0 01-.22.128c-.331.183
                                                     -.581.495-.644.869l-.213 1.28c-.09.543-.56.941
                                                     -1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l
                                                     -.213-1.281c-.062-.374-.312-.686-.644-.87a6.52
                                                     6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076
                                                     -.124l-1.217.456a1.125 1.125 0 01-1.369-.49
                                                     l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004
                                                     -.827c.292-.24.437-.613.43-.992a6.932 6.932 0
                                                     010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828
                                                     a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125
                                                     1.125 0 011.37-.491l1.216.456c.356.133.751.072
                                                     1.076-.124.072-.044.146-.087.22-.128.332-.183
                                                     .582-.495.644-.869l.214-1.281z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Pengaturan
                                    </a>
                                </div>
                                <div class="border-t border-gray-100 dark:border-gray-800 py-1">
                                    <Link :href="route('logout')" method="post" as="button"
                                          class="flex w-full items-center gap-3 px-4 py-2
                                                 text-sm text-red-600 dark:text-red-400
                                                 hover:bg-red-50 dark:hover:bg-red-900/20">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25
                                                     2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5
                                                     21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0
                                                     0l3 3m-3-3h12.75"/>
                                        </svg>
                                        Keluar
                                    </Link>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>
            </header>

            <!-- PAGE CONTENT -->
            <main class="flex-1 p-6">
                <div v-if="title" class="mb-6 hidden lg:block">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
                        {{ title }}
                    </h1>
                </div>
                <slot/>
            </main>
        </div>
    </div>
</template>