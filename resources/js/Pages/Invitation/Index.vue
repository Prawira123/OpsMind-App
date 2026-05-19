<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { Head, useForm, router, Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

const props = defineProps({
    members: Array
})

const page = usePage()
const currentUser = computed(() => page.props.auth.user)
const localMembers = ref([...props.members])

watch(() => props.members, (newVal) => {
    localMembers.value = [...newVal]
}, { deep: true })

const showInviteModal = ref(false)
const inviteForm = useForm({
    email: '',
    role: 'staff'
})

const roles = [
    {
        id : 'owner',
        name: 'Admin',
        description: 'Akses penuh ke seluruh fitur dan pengaturan tenant.',
        icon: 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z'
    },
    {
        id : 'manager',
        name: 'Manager',
        description: 'Mengelola transaksi, invoice, dan klien tanpa akses pengaturan.',
        icon: 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'
    },
    {
        id : 'staff',
        name: 'Staff',
        description: 'Dapat membuat dan mengubah transaksi, invoice, dan klien.',
        icon: 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10'
    },
    {
        id : 'accountant',
        name: 'Accountant',
        description: 'Dapat melihat Laporan keuangan dan Invoice',
        icon: 'M2.036 12.322a1.012 1.012 0 010-.644C3.412 8.784 7.087 6 12 6c4.913 0 8.588 2.385 10.024 5.678a1.012 1.012 0 010 .644C20.588 15.216 16.913 18 12 18c-4.913 0-8.588-2.385-10.024-5.678z M12 10.5a1.5 1.5 0 100 3 1.5 1.5 0 000-3z'
    }
]

const submitInvitation = () => {
    inviteForm.post(route('invitations.store'), {
        onSuccess: () => {
            showInviteModal.value = false
            inviteForm.reset()
        }
    })
}

const deleteMember = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus anggota ini?')) {
        router.delete(route('invitations.destroy', id))
    }
}

const getStatusColor = (status) => {
    return status === 'active' 
        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' 
        : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'
}

let presenceChannel = null

const setupPresence = () => {
    if (!window.Echo) {
        window.Pusher = Pusher
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: import.meta.env.VITE_REVERB_HOST === 'localhost' ? '127.0.0.1' : (import.meta.env.VITE_REVERB_HOST || window.location.hostname),
            wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
            wssPort: import.meta.env.VITE_REVERB_PORT || 8080,
            forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],
        })
    }

    const tenantId = currentUser.value.tenant_id
    if (tenantId) {
        presenceChannel = window.Echo.join(`tenant.${tenantId}.presence`)

        presenceChannel.here((users) => {
            const onlineIds = users.map(u => u.id)
            localMembers.value.forEach(m => {
                if (m.status === 'active') {
                    const isOnline = onlineIds.includes(m.id)
                    m.is_online = isOnline
                    if (isOnline) {
                        m.last_seen = '-'
                    }
                }
            })
        })

        presenceChannel.joining((user) => {
            const idx = localMembers.value.findIndex(m => m.id === user.id)
            if (idx !== -1) {
                localMembers.value[idx].is_online = true
                localMembers.value[idx].last_seen = '-'
            }
        })

        presenceChannel.leaving((user) => {
            const idx = localMembers.value.findIndex(m => m.id === user.id)
            if (idx !== -1) {
                localMembers.value[idx].is_online = false
                localMembers.value[idx].last_seen = 'Baru saja'
            }
        })
    }
}

onMounted(() => {
    setupPresence()
})

onUnmounted(() => {
    if (presenceChannel) {
        const tenantId = currentUser.value.tenant_id
        if (tenantId) {
            window.Echo.leave(`tenant.${tenantId}.presence`)
        }
        presenceChannel = null
    }
})
</script>

<template>
    <AppLayout title="My Team">
        <Head title="My Team" />

        <div class="max-w-full mx-auto py-6">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Anggota Tim</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola akses dan undangan anggota tim Anda.</p>
                </div>
                <button 
                    @click="showInviteModal = true"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 
                           text-sm font-semibold text-white hover:bg-indigo-500 
                           shadow-lg shadow-indigo-500/30 transition-all hover:shadow-indigo-500/50"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Undang Anggota
                </button>
            </div>

            <!-- Team Table -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-800">
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Anggota</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Role</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status Online</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Last Seen</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <tr v-for="member in localMembers" :key="member.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div v-if="member.avatar" class="h-10 w-10 rounded-full bg-cover bg-center" :style="{ backgroundImage: `url(${member.avatar})` }" />
                                        <div v-else class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold">
                                            {{ member.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ member.name }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ member.email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800">
                                        {{ member.role }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <span :class="[getStatusColor(member.status), 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium']">
                                        {{ member.status === 'active' ? 'Aktif' : 'Menunggu' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <span v-if="member.is_online" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/50">
                                        <span class="relative flex h-1.5 w-1.5">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                                        </span>
                                        Online
                                    </span>
                                    <span v-else class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                        Offline
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sm text-gray-500 dark:text-gray-400">
                                    {{ member.last_seen }}
                                </td>
                                <td class="px-6 py-5 text-right flex items-center justify-end gap-2">
                                    <Link 
                                        v-if="member.status === 'active' && member.id !== $page.props.auth.user.id" 
                                        :href="route('chat.start-with-user', member.id)" 
                                        class="text-gray-400 hover:text-indigo-600 transition-colors p-2"
                                        title="Chat"
                                    >
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                    </Link>
                                    <button @click="deleteMember(member.id)" class="text-gray-400 hover:text-red-500 transition-colors p-2">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Invite Modal -->
        <div v-if="showInviteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="showInviteModal = false" />
            
            <!-- Modal Content -->
            <div class="relative bg-white dark:bg-gray-900 w-full max-w-xl rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden animate-in fade-in zoom-in duration-300">
                <!-- Header -->
                <div class="p-6 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Undang Anggota Tim</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kirimkan undangan bergabung ke email calon anggota.</p>
                </div>

                <form @submit.prevent="submitInvitation" class="p-6 space-y-6">
                    <!-- Email Input -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alamat Email</label>
                        <input 
                            v-model="inviteForm.email"
                            type="email" 
                            required
                            placeholder="nama@email.com"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 
                                   text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all px-4 py-3"
                        />
                        <div v-if="inviteForm.errors.email" class="text-red-500 text-xs mt-1">{{ inviteForm.errors.email }}</div>
                    </div>

                    <!-- Role Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Pilih Role Akses</label>
                        <div class="grid grid-cols-1 gap-3">
                            <button 
                                v-for="role in roles" 
                                :key="role.id"
                                type="button"
                                @click="inviteForm.role = role.id"
                                :class="[
                                    'flex items-start gap-4 p-4 rounded-2xl border-2 transition-all text-left group',
                                    inviteForm.role === role.id 
                                        ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-900/20' 
                                        : 'border-gray-100 dark:border-gray-800 hover:border-indigo-200 dark:hover:border-indigo-900/50'
                                ]"
                            >
                                <div :class="[
                                    'h-10 w-10 shrink-0 rounded-xl flex items-center justify-center transition-colors',
                                    inviteForm.role === role.name 
                                        ? 'bg-indigo-600 text-white' 
                                        : 'bg-gray-100 dark:bg-gray-800 text-gray-500 group-hover:text-indigo-500'
                                ]">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" :d="role.icon" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ role.name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">{{ role.description }}</p>
                                </div>
                                <div v-if="inviteForm.role === role.id" class="ml-auto">
                                    <div class="h-5 w-5 rounded-full bg-indigo-600 flex items-center justify-center">
                                        <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button 
                            type="button" 
                            @click="showInviteModal = false"
                            class="px-5 py-2.5 text-sm font-semibold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
                        >
                            Batal
                        </button>
                        <PrimaryButton 
                            :class="{ 'opacity-50': inviteForm.processing }" 
                            :disabled="inviteForm.processing"
                        >
                            Kirim Undangan
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>

    </AppLayout>
</template>
