<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { Head, usePage, router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import axios from 'axios'

const props = defineProps({
    conversations: Array,
    activeConversationId: [Number, String, null],
    messages: Array
})

const page = usePage()
const currentUser = computed(() => page.props.auth.user)

const localConversations = ref([...props.conversations])
const localMessages = ref([...props.messages])
const newMessageText = ref('')
const searchGroup = ref('')
const messagesContainer = ref(null)
const isSending = ref(false)

// UI state for popups/dropdowns
const showDocPopup = ref(false)
const activeMessageMenuId = ref(null)
const activeSidebarMenuId = ref(null)

const handleDocAction = (type) => {
    alert(`Kirim ${type} terpilih.`)
    showDocPopup.value = false
}

const toggleMessageMenu = (messageId) => {
    activeMessageMenuId.value = activeMessageMenuId.value === messageId ? null : messageId
}

const toggleSidebarMenu = (e, convoId) => {
    // Prevent navigating to the chat room when clicking the 3-dot button
    e.preventDefault()
    e.stopPropagation()
    activeSidebarMenuId.value = activeSidebarMenuId.value === convoId ? null : convoId
}

const handleMessageAction = (action, message) => {
    if (action === 'copy') {
        navigator.clipboard.writeText(message.body)
    } else {
        alert(`Aksi "${action}" untuk pesan ini terpilih.`)
    }
    activeMessageMenuId.value = null
}

const handleSidebarAction = (e, action, convo) => {
    e.preventDefault()
    e.stopPropagation()
    alert(`Aksi "${action}" untuk obrolan ${convo.name} terpilih.`)
    activeSidebarMenuId.value = null
}

// Watch props to keep local state synced
watch(() => props.conversations, (newVal) => {
    localConversations.value = [...newVal]
}, { deep: true })

watch(() => props.messages, (newVal) => {
    localMessages.value = [...newVal]
    scrollToBottom()
}, { deep: true })

// Find active conversation object
const activeConversation = computed(() => {
    return localConversations.value.find(c => c.id === props.activeConversationId) || null
})

// Filter conversations based on search query
const filteredConversations = computed(() => {
    if (!searchGroup.value) return localConversations.value
    const query = searchGroup.value.toLowerCase()
    return localConversations.value.filter(c => c.name.toLowerCase().includes(query))
})

// Scroll messages container to bottom
const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
        }
    })
}

// Format message size
const formatSize = (bytes) => {
    if (!bytes) return ''
    const kb = bytes / 1024
    if (kb < 1024) return `${kb.toFixed(1)} KB`
    const mb = kb / 1024
    return `${mb.toFixed(1)} MB`
}

// Send a message
const sendMessage = async () => {
    if (!newMessageText.value.trim() || isSending.value || !props.activeConversationId) return

    const textToSend = newMessageText.value.trim()
    newMessageText.value = ''
    isSending.value = true

    // Optimistic UI update
    const tempId = 'temp-' + Date.now()
    const optimisticMessage = {
        id: tempId,
        body: textToSend,
        type: 'text',
        user_id: currentUser.value.id,
        user_name: currentUser.value.name,
        time: 'Baru saja',
        is_me: true,
        status: 'sending'
    }
    localMessages.value.push(optimisticMessage)
    scrollToBottom()

    try {
        const response = await axios.post(route('chat.messages.store'), {
            conversation_id: props.activeConversationId,
            body: textToSend,
            type: 'text'
        })

        if (response.data.success) {
            // Replace optimistic message with actual message
            const index = localMessages.value.findIndex(m => m.id === tempId)
            if (index !== -1) {
                const apiMsg = response.data.message
                localMessages.value[index] = {
                    id: apiMsg.id,
                    body: apiMsg.body,
                    type: apiMsg.type,
                    user_id: apiMsg.user_id,
                    user_name: currentUser.value.name,
                    time: 'Baru saja',
                    is_me: true,
                    status: 'sent'
                }
            }
            
            // Local update of conversation preview
            const convoIndex = localConversations.value.findIndex(c => c.id === props.activeConversationId)
            if (convoIndex !== -1) {
                localConversations.value[convoIndex].last_message = textToSend
                localConversations.value[convoIndex].last_message_time = 'Baru saja'
            }
        }
    } catch (err) {
        console.error('Gagal mengirim pesan:', err)
        // Mark optimistic message as failed
        const index = localMessages.value.findIndex(m => m.id === tempId)
        if (index !== -1) {
            localMessages.value[index].status = 'failed'
        }
    } finally {
        isSending.value = false
        scrollToBottom()
    }
}

// Websocket setup
let activeChannel = null
let subscribedChannels = []
let presenceChannel = null

const setupWebSocket = () => {
    // Initialize Echo if not already done
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

    // Leave existing channels before subscribing
    leaveAllChannels()

    // 1. Subscribe to active conversation channel (messages & receipts)
    if (props.activeConversationId) {
        const channelName = `conversation.${props.activeConversationId}`
        activeChannel = window.Echo.private(channelName)

        activeChannel.listen('.message-sent', (payload) => {
            // Only append if message is from other user and not already in list
            if (payload.user_id !== currentUser.value.id) {
                const exists = localMessages.value.some(m => m.id === payload.id)
                if (!exists) {
                    localMessages.value.push({
                        id: payload.id,
                        body: payload.body,
                        type: payload.type,
                        user_id: payload.user_id,
                        user_name: payload.user_name,
                        time: payload.time,
                        is_me: false,
                        attachment_path: payload.attachment_path,
                        attachment_name: payload.attachment_name,
                        attachment_size: payload.attachment_size,
                        attachment_mime: payload.attachment_mime,
                    })
                    scrollToBottom()

                    // Send delivery and read status to server immediately
                    axios.post(route('chat.messages.delivery'), { message_id: payload.id })
                    axios.post(route('chat.messages.read'), { message_id: payload.id })
                }
            }
        })

        activeChannel.listen('.message-read', (payload) => {
            const msg = localMessages.value.find(m => m.id === payload.messageId)
            if (msg && msg.is_me) {
                msg.status = 'read'
            }
        })

        activeChannel.listen('.message-delivered', (payload) => {
            const msg = localMessages.value.find(m => m.id === payload.messageId)
            if (msg && msg.is_me && msg.status !== 'read') {
                msg.status = 'delivered'
            }
        })
    }

    // 2. Subscribe to all conversation channels for real-time sidebar & unread count updates
    localConversations.value.forEach(convo => {
        // Skip active conversation channel because it's handled by activeChannel
        if (convo.id === props.activeConversationId) return

        const channel = window.Echo.private(`conversation.${convo.id}`)
        subscribedChannels.push(convo.id)

        channel.listen('.message-sent', (payload) => {
            const convoIndex = localConversations.value.findIndex(c => c.id === convo.id)
            if (convoIndex !== -1) {
                // Update sidebar details
                localConversations.value[convoIndex].last_message = payload.body
                localConversations.value[convoIndex].last_message_time = payload.time || 'Baru saja'

                // Increment unread count if message is from partner
                if (payload.user_id !== currentUser.value.id) {
                    localConversations.value[convoIndex].unread_count = (localConversations.value[convoIndex].unread_count || 0) + 1
                    
                    // Send delivery ACK since receiver received it
                    axios.post(route('chat.messages.delivery'), { message_id: payload.id })
                }
            }
        })
    })

    // 3. Subscribe to tenant presence channel for online/offline tracking
    const tenantId = currentUser.value.tenant_id
    if (tenantId) {
        presenceChannel = window.Echo.join(`tenant.${tenantId}.presence`)

        presenceChannel.here((users) => {
            const onlineIds = users.map(u => u.id)
            localConversations.value.forEach(convo => {
                if (convo.partner_id) {
                    const isOnline = onlineIds.includes(convo.partner_id)
                    convo.is_online = isOnline
                    if (isOnline) {
                        convo.last_seen = '-'
                    }
                }
            })
        })

        presenceChannel.joining((user) => {
            const convoIndex = localConversations.value.findIndex(c => c.partner_id === user.id)
            if (convoIndex !== -1) {
                localConversations.value[convoIndex].is_online = true
                localConversations.value[convoIndex].last_seen = '-'
            }
        })

        presenceChannel.leaving((user) => {
            const convoIndex = localConversations.value.findIndex(c => c.partner_id === user.id)
            if (convoIndex !== -1) {
                localConversations.value[convoIndex].is_online = false
                localConversations.value[convoIndex].last_seen = 'Baru saja'
            }
        })
    }
}

const leaveAllChannels = () => {
    if (activeChannel && props.activeConversationId) {
        window.Echo.leave(`conversation.${props.activeConversationId}`)
        activeChannel = null
    }
    subscribedChannels.forEach(convoId => {
        window.Echo.leave(`conversation.${convoId}`)
    })
    subscribedChannels = []

    if (presenceChannel) {
        const tenantId = currentUser.value.tenant_id
        if (tenantId) {
            window.Echo.leave(`tenant.${tenantId}.presence`)
        }
        presenceChannel = null
    }
}

// Watch active conversation ID to switch channels
watch(() => props.activeConversationId, () => {
    setupWebSocket()
    scrollToBottom()
})

onMounted(() => {
    setupWebSocket()
    scrollToBottom()
})

onUnmounted(() => {
    leaveAllChannels()
})
</script>

<template>
    <Head title="Pesan & Kolaborasi" />

    <AppLayout title="Pesan & Kolaborasi">
        <div class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Outer Card Container -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-800 overflow-hidden flex h-[calc(100vh-12rem)] min-h-[500px]">
                
                <!-- Sidebar (Left) -->
                <div class="w-full sm:w-80 shrink-0 border-r border-gray-200 dark:border-gray-800 flex flex-col bg-gray-50/30 dark:bg-gray-900/40"
                     :class="{ 'hidden sm:flex': activeConversationId }">
                    
                    <!-- Search Header -->
                    <div class="p-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50/80 dark:bg-gray-900/60 backdrop-blur-md">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Obrolan Tim</h2>
                        <div class="relative">
                            <input 
                                v-model="searchGroup"
                                type="text" 
                                placeholder="Cari percakapan..." 
                                class="w-full pl-10 pr-4 py-2 rounded-xl text-sm border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                            />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-gray-500">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Conversation List -->
                    <div class="flex-1 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-800/50 bg-white dark:bg-gray-900/20">
                        <div v-if="filteredConversations.length === 0" class="p-8 text-center text-gray-400 dark:text-gray-500 text-sm">
                            <svg class="h-10 w-10 mx-auto mb-2 text-gray-300 dark:text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 10.742h.01m3.999 0h.01m3.01 0h.01M9 16.5h.01m3.999 0h.01m3.01 0h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Tidak ada obrolan aktif.<br>
                            <Link :href="route('team.index')" class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold mt-2 inline-block">
                                Cari Anggota Tim
                            </Link>
                        </div>

                        <Link 
                            v-for="convo in filteredConversations" 
                            :key="convo.id"
                            :href="route('chat.show', convo.id)"
                            class="flex items-center gap-3 p-4 hover:bg-gray-100/50 dark:hover:bg-gray-800/40 transition-colors text-left w-full group relative border-b border-gray-100 dark:border-gray-800/40"
                            :class="convo.id === activeConversationId 
                                ? 'bg-indigo-50/70 dark:bg-indigo-950/20 border-l-4 border-indigo-600' 
                                : 'bg-white/40 dark:bg-transparent'"
                        >
                            <!-- Avatar / Status Dot -->
                            <div class="relative shrink-0">
                                <div v-if="convo.avatar" class="h-10 w-10 rounded-full bg-cover bg-center ring-1 ring-gray-200 dark:ring-gray-800" :style="{ backgroundImage: `url(${convo.avatar})` }" />
                                <div v-else class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-950/60 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold">
                                    {{ convo.name.charAt(0).toUpperCase() }}
                                </div>
                                <span v-if="convo.is_online" class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-gray-900 animate-pulse" />
                            </div>

                            <!-- Conversation Details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate" :class="{ 'font-bold': convo.unread_count > 0 }">
                                        {{ convo.name }}
                                    </p>
                                    <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                                        {{ convo.last_message_time }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate pr-4" :class="{ 'text-gray-900 dark:text-gray-200 font-medium': convo.unread_count > 0 }">
                                    {{ convo.last_message || 'Belum ada pesan' }}
                                </p>
                            </div>

                            <!-- Unread Badge & 3-Dot Actions -->
                            <div class="absolute right-4 bottom-3 flex items-center gap-1.5">
                                <span 
                                    v-if="convo.unread_count > 0" 
                                    class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-2xs font-bold bg-indigo-600 text-white"
                                >
                                    {{ convo.unread_count }}
                                </span>
                                
                                <div class="relative opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button
                                        type="button"
                                        @click="toggleSidebarMenu($event, convo.id)"
                                        class="h-6 w-6 rounded-md hover:bg-gray-200/80 dark:hover:bg-gray-800/80 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 flex items-center justify-center transition-colors"
                                        title="Pilihan obrolan"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
 
                                    <!-- Sidebar Dropdown Menu -->
                                    <div 
                                        v-if="activeSidebarMenuId === convo.id"
                                        class="absolute right-0 bottom-7 z-50 w-36 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-1 animate-in fade-in duration-100"
                                    >
                                        <button 
                                            type="button"
                                            @click="handleSidebarAction($event, 'read', convo)"
                                            class="w-full px-3 py-1.5 text-left text-2xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50"
                                        >
                                            Tandai Dibaca
                                        </button>
                                        <button 
                                            type="button"
                                            @click="handleSidebarAction($event, 'delete', convo)"
                                            class="w-full px-3 py-1.5 text-left text-2xs font-bold text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20"
                                        >
                                            Hapus Chat
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Chat Room Area (Right) -->
                <div class="flex-1 flex flex-col bg-slate-50 dark:bg-gray-950"
                     :class="{ 'hidden sm:flex': !activeConversationId, 'flex': activeConversationId }">
                    
                    <template v-if="activeConversation">
                        <!-- Chat Header -->
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800/80 flex items-center justify-between bg-white dark:bg-gray-900 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md">
                            <div class="flex items-center gap-3">
                                <!-- Back Button for mobile -->
                                <Link 
                                    :href="route('chat.index')" 
                                    class="sm:hidden p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg mr-1 transition-colors"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </Link>

                                <!-- Avatar -->
                                <div class="relative">
                                    <div v-if="activeConversation.avatar" class="h-10 w-10 rounded-full bg-cover bg-center ring-1 ring-gray-200 dark:ring-gray-800" :style="{ backgroundImage: `url(${activeConversation.avatar})` }" />
                                    <div v-else class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-950/60 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold">
                                        {{ activeConversation.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <span v-if="activeConversation.is_online" class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-gray-900" />
                                </div>

                                <!-- User Meta -->
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 leading-tight">
                                        {{ activeConversation.name }}
                                    </h3>
                                    <p class="text-2xs text-gray-400 dark:text-gray-500 flex items-center gap-1 mt-0.5">
                                        <span v-if="activeConversation.is_online" class="text-emerald-500 font-semibold flex items-center gap-1">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-ping" />
                                            Online
                                        </span>
                                        <span v-else>Aktif {{ activeConversation.last_seen }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Tutup Chat Button -->
                            <Link 
                                :href="route('chat.index')"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors mr-1 shrink-0"
                                title="Tutup Percakapan"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span class="hidden sm:inline">Tutup Chat</span>
                            </Link>
                        </div>

                        <!-- Messages Container -->
                        <div 
                            ref="messagesContainer"
                            class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50/20 dark:bg-gray-950/40"
                        >
                            <div v-if="localMessages.length === 0" class="flex flex-col items-center justify-center h-full text-gray-400 dark:text-gray-500">
                                <svg class="h-12 w-12 text-gray-300 dark:text-gray-800 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <p class="text-sm">Belum ada percakapan. Kirim pesan pertama untuk memulai!</p>
                            </div>

                            <div 
                                v-for="message in localMessages" 
                                :key="message.id"
                                class="flex"
                                :class="message.is_me ? 'justify-end' : 'justify-start'"
                            >
                                <div 
                                    class="max-w-[70%] group flex flex-col"
                                    :class="message.is_me ? 'items-end' : 'items-start'"
                                >
                                    <!-- Sender Name for group chats if needed -->
                                    <p v-if="!message.is_me && activeConversation.type === 'group'" class="text-2xs text-gray-400 dark:text-gray-500 mb-1 ml-2 font-medium">
                                        {{ message.user_name }}
                                    </p>
 
                                    <!-- Message Bubble Container with Actions -->
                                    <div 
                                        class="flex items-center gap-2 group/bubble relative"
                                        :class="message.is_me ? 'flex-row-reverse' : 'flex-row'"
                                    >
                                        <!-- Message Bubble -->
                                        <div 
                                            class="px-4 py-2.5 rounded-2xl text-sm shadow-sm leading-relaxed"
                                            :class="message.is_me 
                                                ? 'bg-indigo-600 text-white rounded-br-none' 
                                                : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-bl-none border border-gray-100 dark:border-gray-700/40'"
                                        >
                                            <!-- Text Body -->
                                            <p v-if="message.body" class="whitespace-pre-wrap select-text">{{ message.body }}</p>
 
                                            <!-- Attachment Rendering -->
                                            <div v-if="message.attachment_path" class="mt-2 pt-2 border-t border-white/20 dark:border-gray-700/50">
                                                <a 
                                                    :href="message.attachment_path" 
                                                    target="_blank"
                                                    class="flex items-center gap-2 hover:underline"
                                                    :class="message.is_me ? 'text-white' : 'text-indigo-600 dark:text-indigo-400'"
                                                >
                                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <span class="truncate max-w-[150px] font-medium text-xs">{{ message.attachment_name || 'Lampiran' }}</span>
                                                    <span class="text-2xs opacity-80">{{ formatSize(message.attachment_size) }}</span>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Message 3-dot Action Trigger -->
                                        <div class="opacity-0 group-hover/bubble:opacity-100 transition-opacity relative shrink-0">
                                            <button 
                                                type="button"
                                                @click="toggleMessageMenu(message.id)"
                                                class="h-7 w-7 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 flex items-center justify-center transition-colors"
                                                title="Pilihan pesan"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                </svg>
                                            </button>

                                            <!-- Dropdown Menu -->
                                            <div 
                                                v-if="activeMessageMenuId === message.id"
                                                class="absolute bottom-8 z-30 w-32 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-1 animate-in fade-in duration-100"
                                                :class="message.is_me ? 'right-0' : 'left-0'"
                                            >
                                                <button 
                                                    type="button"
                                                    @click="handleMessageAction('copy', message)"
                                                    class="w-full px-3 py-1.5 text-left text-2xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 flex items-center gap-2"
                                                >
                                                    Salin Pesan
                                                </button>
                                                <button 
                                                    type="button"
                                                    @click="handleMessageAction('reply', message)"
                                                    class="w-full px-3 py-1.5 text-left text-2xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 flex items-center gap-2"
                                                >
                                                    Balas Pesan
                                                </button>
                                                <button 
                                                    v-if="message.is_me"
                                                    type="button"
                                                    @click="handleMessageAction('delete', message)"
                                                    class="w-full px-3 py-1.5 text-left text-2xs font-bold text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20 flex items-center gap-2"
                                                >
                                                    Hapus Pesan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
 
                                    <!-- Time & Read Status -->
                                    <div 
                                        class="flex items-center gap-1.5 mt-1 px-1"
                                        :class="message.is_me ? 'justify-end' : 'justify-start'"
                                    >
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500 whitespace-nowrap">
                                            {{ message.time }}
                                        </span>
                                        
                                        <!-- Read Ticks (For Self Messages Only) -->
                                        <span v-if="message.is_me" class="flex items-center">
                                            <!-- Sending Spinner -->
                                            <span v-if="message.status === 'sending'" class="h-2.5 w-2.5 border border-indigo-400 border-t-transparent rounded-full animate-spin" />
                                            
                                            <!-- Failed Warning -->
                                            <span v-else-if="message.status === 'failed'" class="text-red-500 text-[10px] font-bold" title="Gagal mengirim">!</span>
                                            
                                            <!-- Read Double Check (Sky Blue) -->
                                            <svg v-else-if="message.status === 'read'" class="h-4 w-4 text-sky-500 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4 8-8" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 12l4 4 8-8" />
                                            </svg>
 
                                            <!-- Delivered Ticks (Gray Double Check) -->
                                            <svg v-else-if="message.status === 'delivered'" class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4 8-8" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 12l4 4 8-8" />
                                            </svg>
                                            
                                            <!-- Sent Tick (Gray Single Check) -->
                                            <svg v-else class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4 8-8" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Input Area -->
                        <div class="p-4 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
                            <form @submit.prevent="sendMessage" class="flex items-center gap-3">
                                <!-- Plus Button & Share Popup -->
                                <div class="relative shrink-0">
                                    <button 
                                        type="button"
                                        @click="showDocPopup = !showDocPopup"
                                        class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700/60 transition-colors"
                                        title="Bagikan dokumen/berkas"
                                    >
                                        <svg class="h-5 w-5 transition-transform duration-200" :class="showDocPopup ? 'rotate-45 text-indigo-500' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
 
                                    <!-- Document Popup Menu -->
                                    <div 
                                        v-if="showDocPopup" 
                                        class="absolute bottom-12 left-0 z-50 w-52 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700/80 p-2 py-3 animate-in slide-in-from-bottom-2 fade-in duration-150"
                                    >
                                        <button 
                                            type="button"
                                            @click="handleDocAction('Dokumen PDF/Word')"
                                            class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-left text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                        >
                                            <span class="h-7 w-7 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </span>
                                            Dokumen (.pdf, .doc)
                                        </button>
                                        <button 
                                            type="button"
                                            @click="handleDocAction('Gambar/Foto')"
                                            class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-left text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                        >
                                            <span class="h-7 w-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </span>
                                            Gambar / Foto
                                        </button>
                                        <button 
                                            type="button"
                                            @click="handleDocAction('Spreadsheet Excel')"
                                            class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-left text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors"
                                        >
                                            <span class="h-7 w-7 rounded-lg bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center text-teal-600 dark:text-teal-400">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </span>
                                            Spreadsheet (.xls)
                                        </button>
                                    </div>
                                </div>
 
                                <!-- Text Input -->
                                <div class="flex-1 relative">
                                    <textarea 
                                        v-model="newMessageText"
                                        rows="1"
                                        placeholder="Ketik pesan Anda di sini..." 
                                        @keydown.enter.prevent="sendMessage"
                                        class="w-full resize-none rounded-xl pl-4 pr-12 py-3 text-sm border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                                    ></textarea>
                                </div>
 
                                <!-- Send Button -->
                                <button 
                                    type="submit"
                                    :disabled="!newMessageText.trim() || isSending"
                                    class="shrink-0 inline-flex items-center justify-center h-10 w-10 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                >
                                    <svg class="h-5 w-5 rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </template>

                    <!-- Placeholder View if no active conversation -->
                    <template v-else>
                        <div class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-gray-50/10 dark:bg-gray-950/20">
                            <div class="h-16 w-16 bg-indigo-50 dark:bg-indigo-950/30 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-4 animate-bounce">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Ruang Obrolan Kolaborasi</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 max-w-sm">
                                Pilih percakapan aktif dari menu samping atau mulai obrolan baru dengan anggota tim Anda melalui halaman 
                                <Link :href="route('team.index')" class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">My Team</Link>.
                            </p>
                        </div>
                    </template>

                </div>

            </div>
        </div>
    </AppLayout>
</template>
