<script setup>
import { computed } from 'vue';

const props = defineProps({
    values: { type: Array, default: () => [] },
    color: { type: String, default: '#6366f1' },
});

const points = computed(() => {
    const values = props.values.map((value) => Number(value) || 0);

    if (values.length === 0) {
        return '';
    }

    const min = Math.min(...values);
    const max = Math.max(...values);
    const range = max - min || 1;

    return values
        .map((value, index) => {
            const x = values.length === 1 ? 0 : (index / (values.length - 1)) * 100;
            const y = 28 - ((value - min) / range) * 24;
            return `${x.toFixed(2)},${y.toFixed(2)}`;
        })
        .join(' ');
});
</script>

<template>
    <svg viewBox="0 0 100 30" preserveAspectRatio="none" class="h-8 w-full" aria-hidden="true">
        <polyline
            v-if="points"
            :points="points"
            fill="none"
            :stroke="color"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            vector-effect="non-scaling-stroke"
        />
    </svg>
</template>
