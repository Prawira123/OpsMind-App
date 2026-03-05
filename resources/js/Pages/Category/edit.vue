<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

// =========================================================
// PROPS — dikirim dari CategoryController::edit()
// =========================================================
const props = defineProps({
    category: Object,
})

// =========================================================
// FORM — pre-fill dengan data yang ada
// =========================================================
const form = useForm({
    name:  props.category.name  ?? '',
    type:  props.category.type  ?? '',
    color: props.category.color ?? '#6366f1',
    icon:  props.category.icon  ?? 'tag',
})

const submit = () => {
    form.put(route('categories.update', props.category.id))
}

// =========================================================
// PRESET COLORS
// =========================================================
const presetColors = [
    '#6366f1', '#8b5cf6', '#ec4899', '#ef4444',
    '#f97316', '#eab308', '#22c55e', '#10b981',
    '#14b8a6', '#06b6d4', '#3b82f6', '#64748b',
]

// =========================================================
// ICON LIST
// =========================================================
const icons = [
    { name: 'tag',           label: 'Tag',          path: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z' },
    { name: 'shopping-bag',  label: 'Belanja',      path: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' },
    { name: 'home',          label: 'Rumah',        path: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'car',           label: 'Transportasi', path: 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0' },
    { name: 'food',          label: 'Makanan',      path: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z' },
    { name: 'heart',         label: 'Kesehatan',    path: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' },
    { name: 'academic',      label: 'Pendidikan',   path: 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z' },
    { name: 'briefcase',     label: 'Bisnis',       path: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
    { name: 'mobile',        label: 'Elektronik',   path: 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z' },
    { name: 'gift',          label: 'Hadiah',       path: 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7' },
    { name: 'trending-up',   label: 'Investasi',    path: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' },
    { name: 'currency',      label: 'Keuangan',     path: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'lightning',     label: 'Utilitas',     path: 'M13 10V3L4 14h7v7l9-11h-7z' },
    { name: 'globe',         label: 'Perjalanan',   path: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'music',         label: 'Hiburan',      path: 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3' },
    { name: 'office',        label: 'Kantor',       path: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
    { name: 'dots',          label: 'Lainnya',      path: 'M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z' },
]

const iconSearch     = ref('')
const showIconPicker = ref(false)

const filteredIcons = computed(() => {
    if (!iconSearch.value) return icons
    return icons.filter(i =>
        i.label.toLowerCase().includes(iconSearch.value.toLowerCase())
    )
})

const selectedIcon = computed(() =>
    icons.find(i => i.name === form.icon) ?? icons[0]
)

const selectIcon = (name) => {
    form.icon        = name
    showIconPicker.value = false
    iconSearch.value = ''
}
</script>

<template>
        <!-- HEADER -->
        <div class="mb-6 flex items-center gap-4">
            <Link :href="route('categories.index')"
                  class="flex h-9 w-9 items-center justify-center rounded-lg
                         border border-gray-200 dark:border-gray-700
                         text-gray-500 dark:text-gray-400
                         hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 19l-7-7 7-7"/>
                </svg>
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Edit Kategori
                </h1>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                    {{ category.name }}
                </p>
            </div>
        </div>

        <!-- BANNER PERUBAHAN BELUM DISIMPAN -->
        <div v-if="form.isDirty"
             class="mb-6 max-w-2xl flex items-center gap-3 rounded-lg
                    bg-amber-50 dark:bg-amber-900/20 border border-amber-200
                    dark:border-amber-800 px-4 py-3">
            <svg class="h-4 w-4 text-amber-500 shrink-0" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667
                         1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34
                         16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-sm text-amber-700 dark:text-amber-400">
                Ada perubahan yang belum disimpan
            </p>
        </div>

        <div class="max-w-full">
            <form @submit.prevent="submit" class="space-y-6">

                <!-- =========================================== -->
                <!-- PREVIEW                                      -->
                <!-- =========================================== -->
                <div class="rounded-xl bg-white dark:bg-gray-900 border
                            border-gray-200 dark:border-gray-800 p-6">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400
                               uppercase tracking-wider mb-4">
                        Preview
                    </p>
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 rounded-2xl flex items-center justify-center
                                    shadow-sm transition-all duration-200"
                             :style="{ backgroundColor: form.color + '20',
                                       border: `2px solid ${form.color}40` }">
                            <svg class="h-7 w-7 transition-all duration-200"
                                 :style="{ color: form.color }"
                                 fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      :d="selectedIcon.path"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">
                                {{ form.name || 'Nama Kategori' }}
                            </p>
                            <span class="inline-block mt-1 rounded-full px-2 py-0.5
                                         text-xs font-medium"
                                  :style="{ backgroundColor: form.color + '20',
                                            color: form.color }">
                                {{ form.type || 'Tipe' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- =========================================== -->
                <!-- INFORMASI KATEGORI                          -->
                <!-- =========================================== -->
                <div class="rounded-xl bg-white dark:bg-gray-900 border
                            border-gray-200 dark:border-gray-800 p-6 space-y-5">

                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Informasi Kategori
                    </h2>

                    <!-- Nama -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700
                                      dark:text-gray-300 mb-1.5">
                            Nama Kategori <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="contoh: Makanan, Gaji, Transportasi..."
                            :class="[
                                'w-full rounded-lg border px-3.5 py-2.5 text-sm',
                                'text-gray-900 dark:text-white placeholder-gray-400',
                                'bg-white dark:bg-gray-800 transition',
                                'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                                form.errors.name
                                    ? 'border-red-400 dark:border-red-500'
                                    : 'border-gray-200 dark:border-gray-700',
                            ]"
                        />
                        <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-500">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Tipe -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700
                                      dark:text-gray-300 mb-1.5">
                            Tipe Kategori <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.type"
                            type="text"
                            placeholder="contoh: Pengeluaran, Pemasukan, Transfer..."
                            :class="[
                                'w-full rounded-lg border px-3.5 py-2.5 text-sm',
                                'text-gray-900 dark:text-white placeholder-gray-400',
                                'bg-white dark:bg-gray-800 transition',
                                'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                                form.errors.type
                                    ? 'border-red-400 dark:border-red-500'
                                    : 'border-gray-200 dark:border-gray-700',
                            ]"
                        />
                        <p v-if="form.errors.type" class="mt-1.5 text-xs text-red-500">
                            {{ form.errors.type }}
                        </p>
                    </div>
                </div>

                <!-- =========================================== -->
                <!-- TAMPILAN: COLOR + ICON                      -->
                <!-- =========================================== -->
                <div class="rounded-xl bg-white dark:bg-gray-900 border
                            border-gray-200 dark:border-gray-800 p-6 space-y-6">

                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Tampilan
                    </h2>

                    <!-- COLOR PICKER -->
                    <div class="flex flex-col gap-4">
                        <label class="block text-sm font-medium text-gray-700
                                      dark:text-gray-300 mb-3">
                            Warna
                        </label>

                        <div class="flex flex-wrap gap-2 mb-3">
                            <button
                                v-for="color in presetColors" :key="color"
                                type="button"
                                @click="form.color = color"
                                :style="{ backgroundColor: color }"
                                :class="[
                                    'h-8 w-8 rounded-lg transition-all duration-150',
                                    form.color === color
                                        ? 'ring-2 ring-offset-2 ring-gray-500 dark:ring-offset-gray-900 scale-110'
                                        : 'hover:scale-105 opacity-80 hover:opacity-100',
                                ]"
                            >
                                <svg v-if="form.color === color"
                                     class="h-4 w-4 text-white mx-auto" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <input
                                v-model="form.color"
                                type="color"
                                class="h-9 w-9 rounded-lg border border-gray-200
                                       dark:border-gray-700 cursor-pointer p-0.5
                                       bg-white dark:bg-gray-800"
                            />
                            <input
                                v-model="form.color"
                                type="text"
                                placeholder="#6366f1"
                                class="w-32 rounded-lg border border-gray-200 dark:border-gray-700
                                       bg-white dark:bg-gray-800 px-3 py-2 text-sm font-mono
                                       text-gray-900 dark:text-white
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500
                                       focus:border-transparent transition"
                            />
                            <span class="text-xs text-gray-400">Kustom warna</span>
                        </div>
                    </div>

                    <!-- ICON PICKER -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700
                                      dark:text-gray-300 mb-3">
                            Icon
                        </label>

                        <button
                            type="button"
                            @click="showIconPicker = !showIconPicker"
                            class="flex items-center gap-3 rounded-lg border
                                   border-gray-200 dark:border-gray-700
                                   bg-white dark:bg-gray-800 px-4 py-2.5
                                   hover:bg-gray-50 dark:hover:bg-gray-700
                                   transition w-full sm:w-64"
                        >
                            <div class="h-7 w-7 rounded-lg flex items-center justify-center shrink-0"
                                 :style="{ backgroundColor: form.color + '20' }">
                                <svg class="h-4 w-4" :style="{ color: form.color }"
                                     fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          :d="selectedIcon.path"/>
                                </svg>
                            </div>
                            <span class="text-sm text-gray-700 dark:text-gray-300
                                         flex-1 text-left">
                                {{ selectedIcon.label }}
                            </span>
                            <svg class="h-4 w-4 text-gray-400 transition-transform shrink-0"
                                 :class="showIconPicker ? 'rotate-180' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                 stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <Transition
                            enter-from-class="opacity-0 -translate-y-2"
                            enter-active-class="transition duration-200"
                            leave-to-class="opacity-0 -translate-y-2"
                            leave-active-class="transition duration-150"
                        >
                            <div v-show="showIconPicker"
                                 class="mt-2 rounded-xl border border-gray-200 dark:border-gray-700
                                        bg-white dark:bg-gray-800 p-4 shadow-lg">

                                <div class="relative mb-3">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2
                                                h-4 w-4 text-gray-400"
                                         fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                                    </svg>
                                    <input
                                        v-model="iconSearch"
                                        type="text"
                                        placeholder="Cari icon..."
                                        class="w-full rounded-lg border border-gray-200
                                               dark:border-gray-700 bg-gray-50 dark:bg-gray-900
                                               pl-9 pr-3 py-2 text-sm text-gray-900 dark:text-white
                                               placeholder-gray-400 focus:outline-none
                                               focus:ring-2 focus:ring-indigo-500
                                               focus:border-transparent transition"
                                    />
                                </div>

                                <div class="grid grid-cols-6 gap-1.5 max-h-52 overflow-y-auto pr-1">
                                    <button
                                        v-for="icon in filteredIcons" :key="icon.name"
                                        type="button"
                                        @click="selectIcon(icon.name)"
                                        :title="icon.label"
                                        :class="[
                                            'flex flex-col items-center gap-1 rounded-lg p-2',
                                            'transition-all duration-150',
                                            form.icon === icon.name
                                                ? 'ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/30'
                                                : 'hover:bg-gray-100 dark:hover:bg-gray-700',
                                        ]"
                                    >
                                        <div class="h-8 w-8 rounded-lg flex items-center justify-center"
                                             :style="form.icon === icon.name
                                                 ? { backgroundColor: form.color + '20' } : {}">
                                            <svg class="h-4 w-4"
                                                 :style="form.icon === icon.name
                                                     ? { color: form.color } : {}"
                                                 :class="form.icon !== icon.name
                                                     ? 'text-gray-500 dark:text-gray-400' : ''"
                                                 fill="none" viewBox="0 0 24 24"
                                                 stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      :d="icon.path"/>
                                            </svg>
                                        </div>
                                        <span class="text-xs text-gray-400 truncate
                                                     w-full text-center leading-tight">
                                            {{ icon.label }}
                                        </span>
                                    </button>
                                </div>

                                <p v-if="filteredIcons.length === 0"
                                   class="text-center text-sm text-gray-400 py-6">
                                    Icon tidak ditemukan
                                </p>
                            </div>
                        </Transition>

                        <p v-if="form.errors.icon" class="mt-1.5 text-xs text-red-500">
                            {{ form.errors.icon }}
                        </p>
                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="flex items-center justify-between pt-2">
                    <Link :href="route('categories.index')"
                          class="rounded-lg border border-gray-200 dark:border-gray-700
                                 px-4 py-2.5 text-sm font-medium text-gray-700
                                 dark:text-gray-300 hover:bg-gray-50
                                 dark:hover:bg-gray-800 transition">
                        Batal
                    </Link>

                    <div class="flex items-center gap-3">
                        <button
                            v-if="form.isDirty"
                            type="button"
                            @click="form.reset()"
                            class="rounded-lg px-4 py-2.5 text-sm font-medium
                                   text-gray-500 dark:text-gray-400
                                   hover:text-gray-700 dark:hover:text-gray-200
                                   hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                        >
                            Reset
                        </button>

                        <button
                            type="submit"
                            :disabled="form.processing || !form.isDirty"
                            class="flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-2.5
                                   text-sm font-semibold text-white shadow-sm hover:bg-indigo-700
                                   disabled:opacity-60 disabled:cursor-not-allowed transition-colors"
                        >
                            <svg v-if="form.processing" class="h-4 w-4 animate-spin"
                                 fill="none" viewBox="0 0 24 24">
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
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
export default { layout: AppLayout }
</script>