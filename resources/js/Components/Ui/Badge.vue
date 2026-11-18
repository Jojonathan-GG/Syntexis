<script setup>
import { computed } from 'vue';

const props = defineProps({
    label: { type: [String, Number], required: true },
    tone: { type: String, default: 'neutral' },
    dot: { type: Boolean, default: false },
});

const tones = {
    success: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400',
    warning: 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400',
    danger: 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-400',
    info: 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-500/10 dark:text-sky-400',
    brand: 'bg-indigo-50 text-indigo-700 ring-indigo-600/20 dark:bg-indigo-500/10 dark:text-indigo-400',
    neutral: 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300',
};

const dots = {
    success: 'bg-emerald-500',
    warning: 'bg-amber-500',
    danger: 'bg-rose-500',
    info: 'bg-sky-500',
    brand: 'bg-indigo-500',
    neutral: 'bg-slate-400',
};

const classes = computed(() => tones[props.tone] ?? tones.neutral);
const dotClass = computed(() => dots[props.tone] ?? dots.neutral);
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset"
        :class="classes"
    >
        <span v-if="dot" class="h-1.5 w-1.5 rounded-full" :class="dotClass" />
        {{ label }}
    </span>
</template>
