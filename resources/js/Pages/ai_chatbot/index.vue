<script setup>
import { ref, computed, nextTick, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import axios from 'axios'

// =========================================================
// USER CONTEXT
// =========================================================
const page   = usePage()
const user   = computed(() => page.props.auth?.user)
const tenant = computed(() => user.value?.tenant)

const avatarInitial = computed(() =>
    user.value?.name?.charAt(0)?.toUpperCase() ?? 'U'
)

// =========================================================
// CHAT STATE
// =========================================================
const messages  = ref([])
const input     = ref('')
const isLoading = ref(false)
const chatEnd   = ref(null)
const inputRef  = ref(null)

// =========================================================
// SUGGESTED PROMPTS
// =========================================================
const suggestions = [
    { icon: '📊', text: 'Analisis keuangan bisnis saya bulan ini' },
    { icon: '💡', text: 'Tips meningkatkan arus kas bisnis' },
    { icon: '📄', text: 'Cara membuat invoice yang profesional' },
    { icon: '📈', text: 'Strategi mengurangi pengeluaran bisnis' },
    { icon: '🧾', text: 'Apa itu double-entry bookkeeping?' },
    { icon: '💰', text: 'Cara mengelola piutang usaha dengan baik' },
]

// =========================================================
// SEND MESSAGE
// =========================================================
const sendMessage = async (text = null) => {
    const content = (text ?? input.value).trim()
    if (!content || isLoading.value) return

    input.value = ''

    // Add user message lokaly
    messages.value.push({ role: 'user', content, id: Date.now() })

    await scrollToBottom()

    isLoading.value = true

    try {
        // Panggil backend kita (Gemini di server-side, aman & tenant-aware)
        const response = await axios.post(route('ai.chat'), {
            message: content,
        })

        messages.value.push({
            role:    'assistant',
            content: response.data.reply ?? 'Maaf, tidak ada jawaban dari AI.',
            id:      Date.now() + 1,
        })

    } catch (err) {
        console.error('AI Chat Error:', err)
        messages.value.push({
            role:    'assistant',
            content: 'Maaf, saya sedang mengalami kendala teknis. Harap coba lagi nanti.',
            id:      Date.now() + 1,
        })
    } finally {
        isLoading.value = false
        await scrollToBottom()
        inputRef.value?.focus()
    }
}

const scrollToBottom = async () => {
    await nextTick()
    chatEnd.value?.scrollIntoView({ behavior: 'smooth' })
}

const handleEnter = (e) => {
    if (e.shiftKey) return
    e.preventDefault()
    sendMessage()
}

const clearChat = () => {
    messages.value = []
    inputRef.value?.focus()
}

onMounted(() => inputRef.value?.focus())

// =========================================================
// FORMAT — simple markdown-like rendering
// =========================================================
const formatMessage = (text) => {
    return text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/`(.*?)`/g, '<code style="background:rgba(99,102,241,0.1);padding:1px 5px;border-radius:4px;font-family:monospace;font-size:0.85em">$1</code>')
        .replace(/^### (.*$)/gm, '<h3 style="font-size:0.9rem;font-weight:600;margin:0.75rem 0 0.3rem;color:inherit">$1</h3>')
        .replace(/^## (.*$)/gm, '<h2 style="font-size:1rem;font-weight:600;margin:0.75rem 0 0.3rem;color:inherit">$1</h2>')
        .replace(/^- (.*$)/gm, '<div style="display:flex;gap:6px;margin:2px 0"><span style="color:#6366f1;margin-top:2px">•</span><span>$1</span></div>')
        .replace(/^\d+\. (.*$)/gm, '<div style="display:flex;gap:6px;margin:2px 0"><span style="color:#6366f1;font-weight:600;min-width:16px">·</span><span>$1</span></div>')
        .replace(/\n\n/g, '<br/><br/>')
        .replace(/\n/g, '<br/>')
}
</script>

<template>
    <AppLayout title="AI Assistant">
        <div class="flex flex-col h-[calc(100vh-8rem)] max-w-full mx-auto">

            <!-- HEADER -->
            <div class="flex items-center justify-between mb-4 shrink-0">
                <div class="flex items-center gap-3">
                    <!-- AI Avatar -->
                    <div class="relative">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600
                                    flex items-center justify-center shadow-lg shadow-indigo-500/20">
                            <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/>
                            </svg>
                        </div>
                        <!-- Online dot -->
                        <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full
                                     bg-emerald-500 border-2 border-white dark:border-gray-900"/>
                    </div>
                    <div>
                        <h1 class="text-base font-bold text-gray-900 dark:text-white leading-none">
                            AI Assistant
                        </h1>
                        <p class="text-xs text-emerald-500 mt-0.5 flex items-center gap-1">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"/>
                            Online · Siap membantu
                        </p>
                    </div>
                </div>

                <!-- Clear chat -->
                <button v-if="messages.length > 0"
                    @click="clearChat"
                    class="flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-gray-700
                           px-3 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-400
                           hover:text-red-600 hover:border-red-200 dark:hover:border-red-800
                           hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0
                                 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1
                                 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Chat
                </button>
            </div>

            <!-- CHAT AREA -->
            <div class="flex-1 overflow-y-auto rounded-2xl bg-white dark:bg-gray-900
                        border border-gray-200 dark:border-gray-800 shadow-sm
                        flex flex-col min-h-0">

                <!-- Empty state / Suggestions -->
                <div v-if="messages.length === 0"
                     class="flex-1 flex flex-col items-center justify-center p-8">

                    <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-violet-600 to-indigo-600
                                flex items-center justify-center mb-4 shadow-xl shadow-indigo-500/20">
                        <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/>
                        </svg>
                    </div>

                    <h2 class="text-lg font-bold text-gray-900 dark:text-white text-center mb-1">
                        Halo, {{ user?.name?.split(' ')[0] ?? 'Pengguna' }}! 👋
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center max-w-sm mb-8">
                        Saya siap membantu analisis keuangan dan pertanyaan bisnis kamu.
                        Tanyakan apa saja!
                    </p>

                    <!-- Suggestion chips -->
                    <div class="grid grid-cols-1 gap-2 w-full max-w-lg sm:grid-cols-2">
                        <button
                            v-for="s in suggestions"
                            :key="s.text"
                            @click="sendMessage(s.text)"
                            class="flex items-center gap-3 rounded-xl border border-gray-200
                                   dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-4 py-3
                                   text-left text-sm text-gray-700 dark:text-gray-300
                                   hover:border-indigo-300 dark:hover:border-indigo-700
                                   hover:bg-indigo-50 dark:hover:bg-indigo-900/20
                                   hover:text-indigo-700 dark:hover:text-indigo-300
                                   transition-all duration-150 active:scale-98 group"
                        >
                            <span class="text-base shrink-0">{{ s.icon }}</span>
                            <span class="text-xs leading-snug">{{ s.text }}</span>
                        </button>
                    </div>
                </div>

                <!-- Messages -->
                <div v-else class="flex-1 overflow-y-auto p-5 space-y-5">

                    <div v-for="msg in messages" :key="msg.id"
                         :class="['flex gap-3', msg.role === 'user' ? 'justify-end' : 'justify-start']">

                        <!-- AI Avatar -->
                        <div v-if="msg.role === 'assistant'"
                             class="h-8 w-8 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600
                                    flex items-center justify-center shrink-0 mt-0.5
                                    shadow-md shadow-indigo-500/20">
                            <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/>
                            </svg>
                        </div>

                        <!-- Message bubble -->
                        <div :class="[
                            'max-w-[75%] rounded-2xl px-4 py-3 text-sm leading-relaxed',
                            msg.role === 'user'
                                ? 'bg-indigo-600 text-white rounded-tr-sm'
                                : 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded-tl-sm',
                        ]">
                            <div v-if="msg.role === 'assistant'"
                                 v-html="formatMessage(msg.content)"/>
                            <p v-else>{{ msg.content }}</p>
                        </div>

                        <!-- User Avatar -->
                        <div v-if="msg.role === 'user'"
                             class="h-8 w-8 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600
                                    flex items-center justify-center shrink-0 mt-0.5 text-white
                                    text-xs font-bold">
                            {{ avatarInitial }}
                        </div>
                    </div>

                    <!-- Loading indicator -->
                    <div v-if="isLoading" class="flex gap-3 justify-start">
                        <div class="h-8 w-8 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600
                                    flex items-center justify-center shrink-0
                                    shadow-md shadow-indigo-500/20">
                            <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/>
                            </svg>
                        </div>
                        <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl rounded-tl-sm px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <span class="h-2 w-2 rounded-full bg-indigo-400 animate-bounce"
                                      style="animation-delay: 0ms"/>
                                <span class="h-2 w-2 rounded-full bg-indigo-400 animate-bounce"
                                      style="animation-delay: 150ms"/>
                                <span class="h-2 w-2 rounded-full bg-indigo-400 animate-bounce"
                                      style="animation-delay: 300ms"/>
                            </div>
                        </div>
                    </div>

                    <div ref="chatEnd"/>
                </div>
            </div>

            <!-- INPUT AREA -->
            <div class="mt-3 shrink-0">
                <div class="flex items-end gap-3 rounded-2xl bg-white dark:bg-gray-900
                            border border-gray-200 dark:border-gray-800 p-3 shadow-sm
                            focus-within:border-indigo-400 dark:focus-within:border-indigo-600
                            transition-colors">

                    <textarea
                        ref="inputRef"
                        v-model="input"
                        @keydown.enter="handleEnter"
                        placeholder="Tanyakan sesuatu tentang keuangan bisnis kamu..."
                        rows="1"
                        :disabled="isLoading"
                        class="flex-1 resize-none bg-transparent text-sm text-gray-900
                               dark:text-white placeholder-gray-400 outline-none focus:outline-none
                               leading-relaxed py-1 max-h-32 disabled:opacity-50"
                        style="field-sizing: content; min-height: 24px;"
                    />

                    <!-- Send button -->
                    <button
                        @click="sendMessage()"
                        :disabled="!input.trim() || isLoading"
                        :class="[
                            'flex h-9 w-9 shrink-0 items-center justify-center rounded-xl transition-all',
                            input.trim() && !isLoading
                                ? 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-500/20 active:scale-95'
                                : 'bg-gray-100 dark:bg-gray-800 text-gray-300 dark:text-gray-600 cursor-not-allowed',
                        ]"
                    >
                        <svg v-if="!isLoading" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27
                                     20.876L5.999 12zm0 0h7.5"/>
                        </svg>
                        <svg v-else class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </button>
                </div>

                <p class="text-center text-xs text-gray-400 dark:text-gray-600 mt-2">
                    Tekan Enter untuk kirim · Shift+Enter untuk baris baru
                </p>
            </div>

        </div>
    </AppLayout>
</template>