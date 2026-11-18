<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Icon from './Icon.vue';

const page = usePage();
const message = ref('');
const tone = ref('success');
const visible = ref(false);

let timer = null;

watch(
    () => [page.props.flash?.success, page.props.flash?.error],
    ([success, error]) => {
        if (!success && !error) {
            return;
        }

        message.value = success || error;
        tone.value = success ? 'success' : 'error';
        visible.value = true;

        if (timer) {
            clearTimeout(timer);
        }

        timer = setTimeout(() => {
            visible.value = false;
        }, 5000);
    },
    { immediate: true }
);
</script>

<template>
    <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="translate-y-2 opacity-0"
        leave-active-class="transition duration-150 ease-in"
        leave-to-class="translate-y-2 opacity-0"
    >
        <div
            v-if="visible"
            class="fixed bottom-5 right-5 z-50 flex max-w-sm items-start gap-3 rounded-xl border bg-white px-4 py-3 shadow-xl dark:bg-slate-900"
            :class="tone === 'success' ? 'border-emerald-200 dark:border-emerald-900' : 'border-rose-200 dark:border-rose-900'"
        >
            <div
                class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full"
                :class="tone === 'success' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'"
            >
                <Icon :name="tone === 'success' ? 'check' : 'alert'" :size="14" />
            </div>
            <p class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ message }}</p>
            <button
                type="button"
                class="ml-2 text-slate-400 transition hover:text-slate-600"
                aria-label="Dismiss notification"
                @click="visible = false"
            >
                <Icon name="x" :size="15" />
            </button>
        </div>
    </Transition>
</template>
