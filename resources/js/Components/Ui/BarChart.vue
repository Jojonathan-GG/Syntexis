<script setup>
import { computed } from 'vue';

const props = defineProps({
    data: { type: Array, default: () => [] },
    height: { type: Number, default: 180 },
});

const max = computed(() => {
    const values = props.data.flatMap((item) => [Number(item.success) || 0, Number(item.failed) || 0]);
    return Math.max(1, ...values);
});

function barHeight(value) {
    return `${Math.max(2, ((Number(value) || 0) / max.value) * 100)}%`;
}
</script>

<template>
    <div class="w-full">
        <div class="flex items-end gap-1.5" :style="{ height: `${height}px` }">
            <div v-for="(item, index) in data" :key="index" class="group flex h-full flex-1 flex-col justify-end">
                <div class="flex h-full items-end justify-center gap-0.5">
                    <div
                        class="w-1/3 rounded-t bg-indigo-500 transition group-hover:bg-indigo-600"
                        :style="{ height: barHeight(item.success) }"
                        :title="`${item.success} successful`"
                    />
                    <div
                        v-if="item.failed"
                        class="w-1/3 rounded-t bg-rose-400 transition group-hover:bg-rose-500"
                        :style="{ height: barHeight(item.failed) }"
                        :title="`${item.failed} failed`"
                    />
                </div>
                <span class="mt-2 truncate text-center text-[10px] text-slate-400">{{ item.label }}</span>
            </div>
        </div>
        <div class="mt-3 flex items-center gap-4 border-t border-slate-100 pt-3 text-xs text-slate-500 dark:border-slate-800">
            <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded bg-indigo-500" /> Successful</span>
            <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded bg-rose-400" /> Failed</span>
        </div>
    </div>
</template>
