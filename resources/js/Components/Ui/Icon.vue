<script setup>
import { computed } from 'vue';

const props = defineProps({
    name: { type: String, required: true },
    size: { type: [Number, String], default: 18 },
    stroke: { type: [Number, String], default: 1.8 },
});

const paths = {
    grid: ['M3 3h7v7H3z', 'M14 3h7v7h-7z', 'M14 14h7v7h-7z', 'M3 14h7v7H3z'],
    plug: ['M12 22v-5', 'M9 8V2', 'M15 8V2', 'M18 8v5a6 6 0 0 1-12 0V8z'],
    workflow: ['M3 3h5v5H3z', 'M16 16h5v5h-5z', 'M8 5.5h4a3 3 0 0 1 3 3V16', 'M8 18.5h4a3 3 0 0 0 3-3V8'],
    database: ['M12 3c4.4 0 8 1.3 8 3s-3.6 3-8 3-8-1.3-8-3 3.6-3 8-3z', 'M4 6v6c0 1.7 3.6 3 8 3s8-1.3 8-3V6', 'M4 12v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6'],
    chart: ['M3 3v18h18', 'M7 15l4-5 3 3 5-7'],
    shield: ['M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'],
    search: ['M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16z', 'M21 21l-4.3-4.3'],
    bell: ['M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9', 'M10 21h4'],
    'chevron-down': ['M6 9l6 6 6-6'],
    'chevron-right': ['M9 6l6 6-6 6'],
    sun: ['M12 17a5 5 0 1 0 0-10 5 5 0 0 0 0 10z', 'M12 1v2M12 21v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M1 12h2M21 12h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4'],
    moon: ['M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z'],
    plus: ['M12 5v14M5 12h14'],
    play: ['M5 3l14 9-14 9V3z'],
    pause: ['M6 4h4v16H6zM14 4h4v16h-4z'],
    check: ['M20 6L9 17l-5-5'],
    x: ['M18 6L6 18M6 6l12 12'],
    alert: ['M12 9v4M12 17h.01', 'M10.3 3.9L1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z'],
    clock: ['M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18z', 'M12 7v5l3 2'],
    'arrow-up': ['M12 19V5M5 12l7-7 7 7'],
    'arrow-down': ['M12 5v14M19 12l-7 7-7-7'],
    external: ['M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6', 'M15 3h6v6M10 14L21 3'],
    trash: ['M3 6h18M8 6V4h8v2M19 6l-1 15H6L5 6', 'M10 11v6M14 11v6'],
    edit: ['M12 20h9', 'M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z'],
    refresh: ['M20 11a8 8 0 1 0-2.3 6.7', 'M20 4v7h-7'],
    filter: ['M3 5h18l-7 8v6l-4 2v-8z'],
    logout: ['M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4', 'M16 17l5-5-5-5M21 12H9'],
    user: ['M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2', 'M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z'],
    settings: ['M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z', 'M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.2h-4v-.2a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1L4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H3v-4h.2a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7 7 4.2l.1.1a1.7 1.7 0 0 0 1.9.3 1.7 1.7 0 0 0 1-1.6V3h4v.2a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1L19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.2v4h-.2a1.7 1.7 0 0 0-1.6 1z'],
    bolt: ['M13 2L3 14h8l-1 8 10-12h-8z'],
    api: ['M8 3H5a2 2 0 0 0-2 2v3M16 3h3a2 2 0 0 1 2 2v3M8 21H5a2 2 0 0 1-2-2v-3M16 21h3a2 2 0 0 0 2-2v-3', 'M8 12h8M10 9l-3 3 3 3M14 9l3 3-3 3'],
    file: ['M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z', 'M14 2v6h6'],
    folder: ['M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'],
    cloud: ['M18 18H6a4 4 0 0 1-.6-8A6 6 0 0 1 17 9a4.5 4.5 0 0 1 1 9z'],
    card: ['M3 6h18v12H3z', 'M3 10h18'],
    activity: ['M22 12h-4l-3 9L9 3l-3 9H2'],
    layers: ['M12 2L2 7l10 5 10-5z', 'M2 17l10 5 10-5M2 12l10 5 10-5'],
    menu: ['M3 6h18M3 12h18M3 18h18'],
    command: ['M9 6a3 3 0 1 0-3 3h12a3 3 0 1 0-3-3v12a3 3 0 1 0 3-3H6a3 3 0 1 0 3 3z'],
    info: ['M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18z', 'M12 11v5M12 8h.01'],
};

const elements = computed(() => paths[props.name] ?? paths.info);
</script>

<template>
    <svg
        :width="size"
        :height="size"
        viewBox="0 0 24 24"
        fill="none"
        :stroke-width="stroke"
        stroke="currentColor"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
        class="shrink-0"
    >
        <path v-for="(element, index) in elements" :key="index" :d="element" />
    </svg>
</template>
