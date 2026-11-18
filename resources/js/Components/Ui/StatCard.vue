<script setup>
import Icon from './Icon.vue';
import Sparkline from './Sparkline.vue';

defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    change: { type: [String, Number], default: null },
    icon: { type: String, default: 'activity' },
    color: { type: String, default: '#6366f1' },
    series: { type: Array, default: () => [] },
});

function formatChange(change) {
    if (change === null || change === undefined) {
        return null;
    }

    const value = Number(change);
    return `${value > 0 ? '+' : ''}${value.toFixed(1)}%`;
}
</script>

<template>
    <div class="sx-card-pad relative overflow-hidden">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ label }}
                </p>
                <p class="sx-num mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    {{ value }}
                </p>
            </div>
            <div
                class="flex h-9 w-9 items-center justify-center rounded-lg"
                :style="{ background: `${color}14`, color }"
            >
                <Icon :name="icon" :size="18" />
            </div>
        </div>

        <div class="mt-3 flex items-end justify-between gap-3">
            <span
                v-if="formatChange(change)"
                class="inline-flex items-center gap-1 text-xs font-semibold"
                :class="Number(change) >= 0 ? 'text-emerald-600' : 'text-rose-600'"
            >
                <Icon :name="Number(change) >= 0 ? 'arrow-up' : 'arrow-down'" :size="13" />
                {{ formatChange(change) }}
                <span class="font-normal text-slate-400">vs last period</span>
            </span>
            <span v-else class="text-xs text-slate-400">Live</span>

            <div v-if="series.length" class="w-20 opacity-80">
                <Sparkline :values="series" :color="color" />
            </div>
        </div>
    </div>
</template>
