<script setup>
import Icon from './Icon.vue';

defineProps({
    icon: { type: String, default: 'layers' },
    title: { type: String, required: true },
    description: { type: String, default: '' },
});
</script>

<template>
    <div class="flex flex-col items-center justify-center px-6 py-14 text-center">
        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400 dark:bg-slate-800">
            <Icon :name="icon" :size="22" />
        </div>
        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ title }}</h3>
        <p v-if="description" class="mt-1 max-w-sm text-sm text-slate-500 dark:text-slate-400">
            {{ description }}
        </p>
        <div class="mt-5"><slot /></div>
    </div>
</template>
