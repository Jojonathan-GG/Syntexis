<script setup>
import { Link } from '@inertiajs/vue3';
import Icon from './Icon.vue';

defineProps({
    links: { type: Array, default: () => [] },
});
</script>

<template>
    <div v-if="links.length > 3" class="flex flex-wrap items-center justify-between gap-2 border-t px-4 py-3 dark:border-slate-800">
        <div class="flex flex-wrap gap-1">
            <template v-for="(link, index) in links" :key="index">
                <span
                    v-if="!link.url"
                    class="cursor-not-allowed rounded-md px-2.5 py-1.5 text-xs text-slate-300 dark:text-slate-600"
                    v-html="link.label"
                />
                <Link
                    v-else
                    :href="link.url"
                    class="rounded-md px-2.5 py-1.5 text-xs font-medium transition"
                    :class="link.active
                        ? 'bg-indigo-600 text-white'
                        : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'"
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>
