<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    incomePerMonth: { type: Object, default: () => ({ labels: [], totals: [] }) },
})

const chartPoints = computed(() => {
    const totals = props.incomePerMonth?.totals ?? []
    if (totals.length < 2) return ''
    const w = 600, h = 120
    const max = Math.max(...totals, 1)
    const min = Math.min(...totals, 0)
    const range = max - min || 1
    return totals.map((val, i) => {
        const x = (i / (totals.length - 1)) * w
        const y = h - ((val - min) / range) * h
        return `${x},${y}`
    }).join(' ')
})

const chartArea = computed(() => {
    const totals = props.incomePerMonth?.totals ?? []
    if (totals.length < 2) return ''
    const w = 600, h = 120
    const max = Math.max(...totals, 1)
    const min = Math.min(...totals, 0)
    const range = max - min || 1
    const pts = totals.map((val, i) => {
        const x = (i / (totals.length - 1)) * w
        const y = h - ((val - min) / range) * h
        return `${x},${y}`
    })
    return `0,${h} ${pts.join(' ')} ${w},${h}`
})

const getChartX = (index) => {
    const totals = props.incomePerMonth?.totals ?? []
    if (totals.length < 2) return 0
    return (index / (totals.length - 1)) * 600
}

const getChartY = (index) => {
    const totals = props.incomePerMonth?.totals ?? []
    if (totals.length < 2) return 120
    const max = Math.max(...totals, 1)
    const min = Math.min(...totals, 0)
    const range = max - min || 1
    return 120 - ((totals[index] - min) / range) * 120
}

const formatShort = (val) => {
    const n = parseFloat(val || 0)
    if (n >= 1_000_000_000) return `Rp ${(n / 1_000_000_000).toFixed(1)}M`
    if (n >= 1_000_000) return `Rp ${(n / 1_000_000).toFixed(1)}Jt`
    if (n >= 1_000) return `Rp ${(n / 1_000).toFixed(0)}Rb`
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n)
}

const hoverIndex = computed(() => hoverIndexRef.value)
const hoverIndexRef = { value: null }
</script>

<template>
    <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-sm font-bold text-gray-900 dark:text-white">Tren Pemasukan</h2>
                <p class="text-xs text-gray-400 mt-0.5">Per bulan</p>
            </div>
            <Link :href="route('reports.income')" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                Lihat Laporan →
            </Link>
        </div>

        <div v-if="(incomePerMonth?.totals?.length ?? 0) > 1" class="relative">
            <svg viewBox="0 0 600 130" class="w-full h-36 overflow-visible" @mouseleave="hoverIndexRef.value = null">
                <line x1="0" y1="0" x2="600" y2="0" stroke="currentColor" stroke-opacity="0.05" class="text-gray-500"/>
                <line x1="0" y1="40" x2="600" y2="40" stroke="currentColor" stroke-opacity="0.05" class="text-gray-500"/>
                <line x1="0" y1="80" x2="600" y2="80" stroke="currentColor" stroke-opacity="0.05" class="text-gray-500"/>
                <line x1="0" y1="120" x2="600" y2="120" stroke="currentColor" stroke-opacity="0.05" class="text-gray-500"/>

                <defs>
                    <linearGradient id="incomeGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#6366f1" stop-opacity="0.3"/>
                        <stop offset="100%" stop-color="#6366f1" stop-opacity="0.01"/>
                    </linearGradient>
                </defs>
                <polygon :points="chartArea" fill="url(#incomeGrad)"/>

                <polyline :points="chartPoints" fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>

                <g v-for="(val, i) in (incomePerMonth?.totals ?? [])" :key="i">
                    <circle :cx="getChartX(i)" :cy="getChartY(i)" r="12" fill="transparent" class="cursor-pointer" @mouseenter="hoverIndexRef.value = i"/>
                    <circle v-if="hoverIndexRef.value === i" :cx="getChartX(i)" :cy="getChartY(i)" r="4" fill="#6366f1" stroke="white" stroke-width="2"/>
                </g>

                <g v-if="hoverIndexRef.value !== null">
                    <rect :x="Math.min(getChartX(hoverIndexRef.value) - 50, 500)" :y="getChartY(hoverIndexRef.value) - 38" width="100" height="30" rx="6" fill="#1e1b4b" fill-opacity="0.95"/>
                    <text :x="Math.min(getChartX(hoverIndexRef.value), 550)" :y="getChartY(hoverIndexRef.value) - 20" text-anchor="middle" font-size="9" fill="white" font-family="monospace">
                        {{ incomePerMonth?.labels?.[hoverIndexRef.value] }}
                    </text>
                    <text :x="Math.min(getChartX(hoverIndexRef.value), 550)" :y="getChartY(hoverIndexRef.value) - 10" text-anchor="middle" font-size="10" fill="#a5b4fc" font-weight="bold" font-family="monospace">
                        {{ formatShort(incomePerMonth?.totals?.[hoverIndexRef.value]) }}
                    </text>
                </g>
            </svg>

            <div class="flex justify-between mt-1 px-0.5">
                <span v-for="(label, i) in (incomePerMonth?.labels ?? [])" :key="i" class="text-xs text-gray-400 truncate" style="max-width:60px; font-size:10px">
                    {{ label?.split(' ')[0] }}
                </span>
            </div>
        </div>

        <div v-else class="flex flex-col items-center justify-center h-36 gap-2">
            <div class="h-12 w-12 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-2xl">📈</div>
            <p class="text-sm text-gray-400">Belum ada data pemasukan</p>
        </div>
    </div>
</template>
