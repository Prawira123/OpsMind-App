<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'

// =========================================================
// NAVBAR SCROLL
// =========================================================
const scrolled = ref(false)
const onScroll = () => { scrolled.value = window.scrollY > 20 }
onMounted(() => window.addEventListener('scroll', onScroll))
onUnmounted(() => window.removeEventListener('scroll', onScroll))

// =========================================================
// FEATURES
// =========================================================
const features = [
    {
        icon: 'M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4',
        title: 'Manajemen Transaksi',
        desc: 'Catat pemasukan dan pengeluaran dengan sistem double-entry bookkeeping otomatis. Journal entry dibuat secara real-time.',
    },
    {
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        title: 'Invoice & Penagihan',
        desc: 'Buat invoice profesional, kirim ke klien, dan pantau status pembayaran. Notifikasi otomatis saat jatuh tempo.',
    },
    {
        icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        title: 'Laporan Keuangan',
        desc: 'Laba rugi, neraca, arus kas, dan buku besar tersedia otomatis — bisa diexport ke PDF kapanpun.',
    },
    {
        icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
        title: 'Manajemen Rekening',
        desc: 'Kelola semua rekening kas, bank, dan e-wallet dalam satu tempat. Saldo selalu update real-time.',
    },
    {
        icon: 'M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z',
        title: 'AI Financial Assistant',
        desc: 'Tanyakan apa saja tentang keuangan bisnis kamu ke AI Assistant. Analisis cerdas berbasis data nyata.',
    },
    {
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        title: 'Multi-Tenant & Tim',
        desc: 'Undang anggota tim dengan peran berbeda. Data bisnis terisolasi per tenant dengan keamanan enterprise.',
    },
]

// =========================================================
// STATS
// =========================================================
const stats = [
    { num: '2.4K+', label: 'Bisnis Aktif' },
    { num: 'Rp 2T+', label: 'Transaksi Diproses' },
    { num: '99.9%', label: 'Uptime SLA' },
    { num: '4.9★', label: 'Rating Pengguna' },
]

// =========================================================
// PRICING
// =========================================================
const plans = [
    {
        name: 'Starter',
        price: 'Gratis',
        period: 'Selamanya',
        featured: false,
        features: ['50 transaksi / bulan', '1 pengguna', '5 klien', 'Laporan dasar'],
    },
    {
        name: 'Pro',
        price: 'Rp 149K',
        period: 'per bulan',
        featured: true,
        badge: 'Paling Populer',
        features: ['Transaksi unlimited', '5 anggota tim', '50 klien', 'Invoice & laporan lengkap', 'AI Financial Assistant'],
    },
    {
        name: 'Enterprise',
        price: 'Rp 399K',
        period: 'per bulan',
        featured: false,
        features: ['Semua fitur Pro', 'Tim unlimited', 'Klien unlimited', 'API Access + Audit Log', 'Priority Support'],
    },
]

// =========================================================
// TRUST ITEMS
// =========================================================
const trustItems = [
    'Tidak perlu kartu kredit',
    'Setup dalam 5 menit',
    'Data aman & terenkripsi',
]
</script>

<template>
    <div class="min-h-screen bg-[#080b14] text-slate-100 font-sans antialiased overflow-x-hidden"
         style="font-family: 'DM Sans', sans-serif">

        <!-- Google Fonts -->
        <component :is="'link'" rel="preconnect" href="https://fonts.googleapis.com"/>
        <component :is="'link'" rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
        <component :is="'link'" href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet"/>

        <!-- Noise texture overlay -->
        <div class="pointer-events-none fixed inset-0 z-0 opacity-[0.025]"
             />

        <!-- ============================================
             NAVBAR
        ============================================ -->
        <nav :class="[
            'fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-6 md:px-10 h-16 transition-all duration-300',
            scrolled
                ? 'bg-[#080b14]/90 backdrop-blur-xl border-b border-white/[0.06]'
                : 'bg-transparent',
        ]">
            <!-- Logo -->
            <a href="/" class="flex items-center gap-2.5 no-underline group">
                <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600
                            flex items-center justify-center shadow-lg shadow-indigo-500/30
                            group-hover:shadow-indigo-500/50 transition-shadow">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <span class="text-base font-bold text-white tracking-tight"
                      style="font-family:'Syne',sans-serif">
                    OpsMind
                </span>
            </a>

            <!-- Nav links -->
            <ul class="hidden md:flex items-center gap-8 list-none">
                <li><a href="#fitur" class="text-sm text-slate-400 hover:text-white transition-colors no-underline">Fitur</a></li>
                <li><a href="#harga" class="text-sm text-slate-400 hover:text-white transition-colors no-underline">Harga</a></li>
                <li><a href="#tentang" class="text-sm text-slate-400 hover:text-white transition-colors no-underline">Tentang</a></li>
            </ul>

            <!-- CTA buttons -->
            <div class="flex items-center gap-3">
                <Link :href="route('login')"
                    class="hidden sm:inline-flex items-center px-4 py-2 rounded-lg border
                           border-white/10 text-sm font-medium text-slate-400
                           hover:text-white hover:border-white/20 hover:bg-white/[0.04]
                           transition-all duration-200 no-underline">
                    Masuk
                </Link>
                <Link :href="route('register')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm
                           font-medium text-white no-underline transition-all duration-200
                           bg-gradient-to-r from-indigo-600 to-violet-600
                           hover:from-indigo-500 hover:to-violet-500
                           shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40
                           hover:-translate-y-px">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Daftar Gratis
                </Link>
            </div>
        </nav>

        <!-- ============================================
             HERO
        ============================================ -->
        <section class="relative min-h-screen flex flex-col items-center justify-center
                        pt-16 pb-0 overflow-hidden">

            <!-- Orb glows -->
            <div class="pointer-events-none absolute -top-32 -left-32 w-[500px] h-[500px]
                        rounded-full opacity-100"
                 style="background:radial-gradient(circle,rgba(99,102,241,0.15) 0%,transparent 70%);filter:blur(60px)"/>
            <div class="pointer-events-none absolute top-40 -right-20 w-[400px] h-[400px]
                        rounded-full opacity-100"
                 style="background:radial-gradient(circle,rgba(139,92,246,0.12) 0%,transparent 70%);filter:blur(60px)"/>
            <div class="pointer-events-none absolute bottom-0 left-1/3 w-[300px] h-[300px]
                        rounded-full opacity-100"
                 style="background:radial-gradient(circle,rgba(99,102,241,0.08) 0%,transparent 70%);filter:blur(60px)"/>

            <div class="relative z-10 text-center px-6 max-w-4xl mx-auto">

                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full
                            border border-indigo-500/30 bg-indigo-500/[0.08]
                            text-xs font-medium text-indigo-300 tracking-wide mb-8">
                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-400 animate-pulse"/>
                    Platform keuangan bisnis terpadu
                </div>

                <!-- Headline -->
                <h1 class="text-5xl md:text-7xl font-extrabold leading-[1.06] tracking-[-0.04em] mb-6"
                    style="font-family:'Syne',sans-serif">
                    Kelola Keuangan<br/>
                    Bisnis dengan
                    <span class="bg-gradient-to-r from-indigo-400 via-violet-400 to-purple-400 bg-clip-text text-transparent">
                        Lebih Cerdas
                    </span>
                </h1>

                <!-- Description -->
                <p class="text-lg text-slate-400 font-light max-w-[540px] mx-auto leading-relaxed mb-10">
                    OpsMind menyederhanakan pencatatan keuangan, invoice, dan laporan bisnis
                    kamu dalam satu platform terintegrasi — dilengkapi AI Assistant.
                </p>

                <!-- CTA -->
                <div class="flex items-center justify-center gap-4 flex-wrap mb-10">
                    <Link :href="route('register')"
                        class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl text-base
                               font-medium text-white no-underline transition-all duration-200
                               bg-gradient-to-r from-indigo-600 to-violet-600
                               hover:from-indigo-500 hover:to-violet-500
                               shadow-xl shadow-indigo-500/25 hover:shadow-indigo-500/45
                               hover:-translate-y-0.5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        Mulai Gratis Sekarang
                    </Link>
                    <a href="#fitur"
                       class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl text-base
                              font-medium text-slate-400 no-underline transition-all duration-200
                              border border-white/[0.1] bg-white/[0.03]
                              hover:text-white hover:border-white/20 hover:bg-white/[0.05]">
                        Pelajari Fitur
                    </a>
                </div>

                <!-- Trust -->
                <div class="flex items-center justify-center gap-6 flex-wrap">
                    <div v-for="item in trustItems" :key="item"
                         class="flex items-center gap-1.5 text-xs text-slate-500">
                        <svg class="h-3.5 w-3.5 text-emerald-400 shrink-0" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ item }}
                    </div>
                </div>
            </div>

            <!-- DASHBOARD MOCKUP -->
            <div class="relative z-10 w-full max-w-4xl mx-auto mt-16 px-6">
                <div class="rounded-2xl border border-white/[0.08] bg-[#0d1120] overflow-hidden
                            shadow-[0_40px_80px_rgba(0,0,0,0.5),0_0_60px_rgba(99,102,241,0.08)]">

                    <!-- Window bar -->
                    <div class="flex items-center gap-2 px-4 py-3 bg-white/[0.03] border-b border-white/[0.06]">
                        <span class="h-2.5 w-2.5 rounded-full bg-red-500/70"/>
                        <span class="h-2.5 w-2.5 rounded-full bg-yellow-500/70"/>
                        <span class="h-2.5 w-2.5 rounded-full bg-green-500/70"/>
                        <div class="flex-1 flex justify-center">
                            <span class="text-[10px] text-slate-500 bg-white/[0.04] rounded
                                         px-3 py-0.5">
                                app.opsmind.id/dashboard
                            </span>
                        </div>
                    </div>

                    <!-- Mockup content -->
                    <div class="p-5 grid grid-cols-3 gap-3">
                        <!-- KPI cards -->
                        <div class="rounded-xl border border-white/[0.06] bg-white/[0.03] p-4">
                            <p class="text-[9px] text-slate-500 uppercase tracking-widest mb-2">Total Saldo</p>
                            <p class="text-xl font-bold text-white" style="font-family:'Syne',sans-serif">
                                Rp 48,2Jt
                            </p>
                            <p class="text-[10px] text-emerald-400 mt-1 flex items-center gap-1">
                                <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                </svg>
                                +12.4% bulan ini
                            </p>
                        </div>
                        <div class="rounded-xl border border-white/[0.06] bg-white/[0.03] p-4">
                            <p class="text-[9px] text-slate-500 uppercase tracking-widest mb-2">Pemasukan</p>
                            <p class="text-xl font-bold text-white" style="font-family:'Syne',sans-serif">
                                Rp 12,1Jt
                            </p>
                            <p class="text-[10px] text-slate-500 mt-1">Bulan ini</p>
                        </div>
                        <div class="rounded-xl border border-white/[0.06] bg-white/[0.03] p-4">
                            <p class="text-[9px] text-slate-500 uppercase tracking-widest mb-2">Invoice Pending</p>
                            <p class="text-xl font-bold text-white" style="font-family:'Syne',sans-serif">7</p>
                            <p class="text-[10px] text-rose-400 mt-1">Rp 4,3Jt pending</p>
                        </div>

                        <!-- Chart placeholder -->
                        <div class="col-span-3 rounded-xl border border-white/[0.06] bg-white/[0.03] p-4 h-24 overflow-hidden">
                            <p class="text-[9px] text-slate-500 uppercase tracking-widest mb-2">Tren Pendapatan</p>
                            <svg viewBox="0 0 800 40" class="w-full h-10" preserveAspectRatio="none">
                                <defs>
                                    <linearGradient id="gr" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#6366f1" stop-opacity="0.35"/>
                                        <stop offset="100%" stop-color="#6366f1" stop-opacity="0"/>
                                    </linearGradient>
                                </defs>
                                <polygon points="0,40 0,32 80,26 160,30 240,16 320,20 400,9 480,14 560,5 640,11 720,3 800,8 800,40"
                                         fill="url(#gr)"/>
                                <polyline points="0,32 80,26 160,30 240,16 320,20 400,9 480,14 560,5 640,11 720,3 800,8"
                                          fill="none" stroke="#6366f1" stroke-width="2"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================
             STATS
        ============================================ -->
        <section id="tentang"
                 class="border-y border-white/[0.06] bg-[#0d1120]">
            <div class="max-w-5xl mx-auto px-6">
                <div class="grid grid-cols-2 md:grid-cols-4">
                    <div v-for="(stat, i) in stats" :key="stat.num"
                         :class="[
                             'py-12 px-8 text-center',
                             i < stats.length - 1 ? 'border-r border-white/[0.06]' : '',
                         ]">
                        <div class="text-3xl font-extrabold tracking-[-0.04em] mb-1.5
                                    bg-gradient-to-r from-indigo-400 to-violet-400
                                    bg-clip-text text-transparent"
                             style="font-family:'Syne',sans-serif">
                            {{ stat.num }}
                        </div>
                        <div class="text-xs text-slate-500 font-light">{{ stat.label }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================
             FEATURES
        ============================================ -->
        <section id="fitur" class="py-24">
            <div class="max-w-5xl mx-auto px-6">

                <div class="mb-14">
                    <div class="flex items-center gap-3 text-xs font-semibold uppercase
                                tracking-[0.1em] text-indigo-400 mb-4">
                        <span class="h-px w-5 bg-indigo-500"/>
                        Fitur Unggulan
                    </div>
                    <h2 class="text-4xl font-bold tracking-[-0.03em] text-white mb-4"
                        style="font-family:'Syne',sans-serif">
                        Semua yang kamu butuhkan<br/>dalam satu platform
                    </h2>
                    <p class="text-slate-400 font-light max-w-md leading-relaxed">
                        Dari pencatatan transaksi hingga laporan keuangan lengkap —
                        OpsMind hadir untuk menyederhanakan proses bisnis kamu.
                    </p>
                </div>

                <!-- Feature grid -->
                <div class="border border-white/[0.07] rounded-2xl overflow-hidden
                            grid grid-cols-1 md:grid-cols-3 gap-px bg-white/[0.07]">
                    <div v-for="feature in features" :key="feature.title"
                         class="bg-[#0d1120] p-7 group hover:bg-indigo-500/[0.04]
                                transition-colors duration-200">
                        <div class="h-9 w-9 rounded-lg bg-indigo-500/[0.12] border border-indigo-500/20
                                    flex items-center justify-center mb-5
                                    group-hover:bg-indigo-500/[0.18] transition-colors">
                            <svg class="h-4.5 w-4.5 text-indigo-400" style="width:18px;height:18px"
                                 fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="feature.icon"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-white mb-2 tracking-[-0.01em]"
                            style="font-family:'Syne',sans-serif">
                            {{ feature.title }}
                        </h3>
                        <p class="text-[13px] text-slate-500 leading-relaxed font-light">
                            {{ feature.desc }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================
             PRICING
        ============================================ -->
        <section id="harga"
                 class="py-24 border-y border-white/[0.06] bg-[#0d1120]">
            <div class="max-w-5xl mx-auto px-6">

                <div class="text-center mb-14">
                    <div class="flex items-center justify-center gap-3 text-xs font-semibold
                                uppercase tracking-[0.1em] text-indigo-400 mb-4">
                        <span class="h-px w-5 bg-indigo-500"/>
                        Harga
                        <span class="h-px w-5 bg-indigo-500"/>
                    </div>
                    <h2 class="text-4xl font-bold tracking-[-0.03em] text-white mb-4"
                        style="font-family:'Syne',sans-serif">
                        Mulai gratis, bayar saat berkembang
                    </h2>
                    <p class="text-slate-400 font-light max-w-md mx-auto leading-relaxed">
                        Pilih paket yang sesuai dengan skala bisnis kamu.
                        Upgrade atau downgrade kapanpun.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-start">
                    <div v-for="plan in plans" :key="plan.name"
                         :class="[
                             'rounded-2xl border p-7 transition-all duration-200 relative overflow-hidden',
                             plan.featured
                                 ? 'border-indigo-500/40 bg-gradient-to-b from-indigo-500/[0.08] to-[#0d1120] shadow-[0_0_40px_rgba(99,102,241,0.12)]'
                                 : 'border-white/[0.07] bg-[#111827] hover:border-white/[0.12]',
                         ]">

                        <!-- Top shimmer line for featured -->
                        <div v-if="plan.featured"
                             class="absolute top-0 left-0 right-0 h-px
                                    bg-gradient-to-r from-transparent via-indigo-500/50 to-transparent"/>

                        <!-- Badge -->
                        <div v-if="plan.badge"
                             class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px]
                                    font-semibold uppercase tracking-[0.08em] text-indigo-300
                                    bg-indigo-500/[0.12] border border-indigo-500/20 mb-4">
                            {{ plan.badge }}
                        </div>

                        <h3 class="text-base font-bold text-white mb-1"
                            style="font-family:'Syne',sans-serif">
                            {{ plan.name }}
                        </h3>
                        <div class="text-3xl font-extrabold text-white tracking-[-0.04em] mb-1"
                             style="font-family:'Syne',sans-serif">
                            {{ plan.price }}
                        </div>
                        <p class="text-xs text-slate-500 mb-6">{{ plan.period }}</p>

                        <ul class="space-y-3 mb-7">
                            <li v-for="feat in plan.features" :key="feat"
                                class="flex items-center gap-2.5 text-[13px] text-slate-400">
                                <svg class="h-3.5 w-3.5 text-emerald-400 shrink-0" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ feat }}
                            </li>
                        </ul>

                        <Link :href="route('register')"
                            :class="[
                                'block w-full py-2.5 rounded-xl text-sm font-medium text-center no-underline transition-all duration-200',
                                plan.featured
                                    ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white hover:from-indigo-500 hover:to-violet-500 shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/35 hover:-translate-y-px'
                                    : 'border border-white/[0.1] text-slate-400 hover:text-white hover:border-white/20 hover:bg-white/[0.04]',
                            ]">
                            {{ plan.name === 'Starter' ? 'Mulai Gratis' : `Mulai ${plan.name}` }}
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================
             CTA
        ============================================ -->
        <section class="py-24">
            <div class="max-w-5xl mx-auto px-6">
                <div class="relative rounded-2xl border border-indigo-500/[0.18]
                            bg-gradient-to-b from-indigo-500/[0.07] to-transparent
                            p-16 text-center overflow-hidden">

                    <!-- Top line -->
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-64 h-px
                                bg-gradient-to-r from-transparent via-indigo-500/50 to-transparent"/>

                    <!-- Orb -->
                    <div class="pointer-events-none absolute top-1/2 left-1/2 -translate-x-1/2
                                -translate-y-1/2 w-72 h-72 rounded-full opacity-100"
                         style="background:radial-gradient(circle,rgba(99,102,241,0.1) 0%,transparent 70%);filter:blur(40px)"/>

                    <div class="relative z-10">
                        <h2 class="text-5xl font-extrabold tracking-[-0.04em] text-white mb-4"
                            style="font-family:'Syne',sans-serif">
                            Siap memulai<br/>perjalanan finansial?
                        </h2>
                        <p class="text-slate-400 font-light max-w-sm mx-auto mb-10 leading-relaxed">
                            Bergabung dengan ribuan pebisnis yang sudah mengelola keuangan
                            lebih cerdas bersama OpsMind.
                        </p>
                        <div class="flex items-center justify-center gap-4 flex-wrap">
                            <Link :href="route('register')"
                                class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl
                                       text-base font-medium text-white no-underline
                                       bg-gradient-to-r from-indigo-600 to-violet-600
                                       hover:from-indigo-500 hover:to-violet-500 transition-all
                                       shadow-xl shadow-indigo-500/25 hover:shadow-indigo-500/45
                                       hover:-translate-y-0.5">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                                Daftar Sekarang — Gratis
                            </Link>
                            <Link :href="route('login')"
                                class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl
                                       text-base font-medium text-slate-400 no-underline
                                       border border-white/[0.1] bg-white/[0.03]
                                       hover:text-white hover:border-white/20 transition-all">
                                Sudah punya akun?
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================
             FOOTER
        ============================================ -->
        <footer class="border-t border-white/[0.06] bg-[#0d1120] py-10">
            <div class="max-w-5xl mx-auto px-6 flex items-center justify-between flex-wrap gap-6">

                <a href="/" class="flex items-center gap-2 no-underline">
                    <div class="h-7 w-7 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600
                                flex items-center justify-center">
                        <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-white" style="font-family:'Syne',sans-serif">
                        OpsMind
                    </span>
                </a>

                <p class="text-xs text-slate-600">© 2025 OpsMind. Hak cipta dilindungi.</p>

                <nav class="flex items-center gap-6">
                    <a href="#" class="text-xs text-slate-500 hover:text-slate-300 no-underline transition-colors">Privasi</a>
                    <a href="#" class="text-xs text-slate-500 hover:text-slate-300 no-underline transition-colors">Syarat</a>
                    <a href="#" class="text-xs text-slate-500 hover:text-slate-300 no-underline transition-colors">Kontak</a>
                    <Link :href="route('login')" class="text-xs text-slate-500 hover:text-slate-300 no-underline transition-colors">Masuk</Link>
                    <Link :href="route('register')" class="text-xs text-indigo-400 hover:text-indigo-300 no-underline transition-colors">Daftar</Link>
                </nav>
            </div>
        </footer>

    </div>
</template>