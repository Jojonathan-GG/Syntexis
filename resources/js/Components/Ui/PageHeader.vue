<script setup>
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
    eyebrow: { type: String, default: '' },
});
</script>

<template>
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p v-if="eyebrow" class="mb-1 text-xs font-bold uppercase tracking-[0.18em] text-indigo-500">
                {{ eyebrow }}
            </p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ title }}</h1>
            <p v-if="subtitle" class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ subtitle }}</p>
        </div>
        <div class="flex items-center gap-2">
            <slot name="actions" />
        </div>
    </div>
</template>
