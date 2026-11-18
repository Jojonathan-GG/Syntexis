<script setup>
import { computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';
import PageHeader from '../../Components/Ui/PageHeader.vue';
import Badge from '../../Components/Ui/Badge.vue';
import Icon from '../../Components/Ui/Icon.vue';
import BarChart from '../../Components/Ui/BarChart.vue';
import Sparkline from '../../Components/Ui/Sparkline.vue';
import Pagination from '../../Components/Ui/Pagination.vue';

const props = defineProps({
    metrics: { type: Array, default: () => [] },
    alerts: { type: Object, required: true },
    runVolume: { type: Array, default: () => [] },
    statusMix: { type: Object, default: () => ({}) },
    datasets: { type: Array, default: () => [] },
});

const colors = ['#6366f1', '#10b981', '#f59e0b', '#0ea5e9', '#8b5cf6', '#ec4899'];
const icons = ['database', 'check', 'clock', 'plug', 'activity', 'bolt'];

function formatValue(value) {
    const number = Number(value) || 0;

    return new Intl.NumberFormat('en-US', { notation: number > 9999 ? 'compact' : 'standard', maximumFractionDigits: 1 }).format(number);
}

function relativeTime(value) {
    if (!value) {
        return '—';
    }

    const minutes = Math.round((Date.now() - new Date(value).getTime()) / 60000);

    return minutes < 60 ? `${minutes}m ago` : minutes < 1440 ? `${Math.floor(minutes / 60)}h ago` : `${Math.floor(minutes / 1440)}d ago`;
}

const statusEntries = computed(() => Object.entries(props.statusMix ?? {}).filter(([, value]) => Number(value) > 0));
const statusTotal = computed(() => statusEntries.value.reduce((sum, [, value]) => sum + Number(value), 0));
const statusColors = ['#10b981', '#f59e0b', '#f43f5e', '#6366f1', '#0ea5e9'];

function statusPercentage(value) {
    return statusTotal.value ? Math.round((Number(value) / statusTotal.value) * 100) : 0;
}
</script>

<template>
    <AppShell>
        <PageHeader
            eyebrow="Intelligence layer"
            title="Insights"
            subtitle="Metrics, anomalies and the health signals behind your data."
        >
            <template #actions>
                <span class="rounded-lg border bg-white px-3 py-2 text-xs font-medium text-slate-500 dark:bg-slate-900">
                    {{ metrics.length }} tracked metrics
                </span>
            </template>
        </PageHeader>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div v-for="(metric, index) in metrics" :key="metric.id" class="sx-card-pad">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ metric.name }}</p>
                        <p class="sx-num mt-2 text-2xl font-bold text-slate-900 dark:text-white">
                            {{ formatValue(metric.value) }}
                            <span v-if="metric.unit" class="text-xs font-medium text-slate-400">{{ metric.unit }}</span>
                        </p>
                    </div>
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-lg"
                        :style="{ background: `${colors[index % colors.length]}14`, color: colors[index % colors.length] }"
                    >
                        <Icon :name="icons[index % icons.length]" :size="16" />
                    </span>
                </div>
                <div class="mt-4 flex items-end justify-between gap-3">
                    <span
                        class="text-xs font-semibold"
                        :class="Number(metric.change_percent) >= 0 ? 'text-emerald-600' : 'text-rose-600'"
                    >
                        {{ Number(metric.change_percent) > 0 ? '+' : '' }}{{ metric.change_percent }}%
                        <span class="ml-1 font-normal text-slate-400">vs last period</span>
                    </span>
                    <div class="w-24"><Sparkline :values="metric.series ?? []" :color="colors[index % colors.length]" /></div>
                </div>
                <p class="mt-2 text-[10px] font-semibold uppercase tracking-wider text-slate-400">{{ metric.category }}</p>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-3">
            <div class="sx-card-pad xl:col-span-2">
                <h2 class="mb-1 text-sm font-bold text-slate-800 dark:text-white">Pipeline reliability</h2>
                <p class="mb-5 text-xs text-slate-400">Successful and failed runs over the last 14 days</p>
                <BarChart :data="runVolume" :height="200" />
            </div>

            <div class="sx-card-pad">
                <h2 class="mb-5 text-sm font-bold text-slate-800 dark:text-white">Record status mix</h2>
                <div class="flex items-center gap-5">
                    <div
                        class="relative h-32 w-32 shrink-0 rounded-full"
                        :style="{
                            background: statusEntries.length
                                ? `conic-gradient(${statusEntries.map(([label, value], index) => `${statusColors[index % statusColors.length]} ${statusPercentage(value)}%`).join(', ')})`
                                : '#e2e8f0',
                        }"
                    >
                        <div class="absolute inset-4 flex flex-col items-center justify-center rounded-full bg-white dark:bg-slate-900">
                            <span class="sx-num text-lg font-bold text-slate-800 dark:text-white">{{ statusTotal }}</span>
                            <span class="text-[10px] text-slate-400">records</span>
                        </div>
                    </div>
                    <div class="min-w-0 flex-1 space-y-2">
                        <div v-for="([label, value], index) in statusEntries" :key="label" class="flex items-center gap-2 text-xs">
                            <span class="h-2 w-2 shrink-0 rounded-full" :style="{ background: statusColors[index % statusColors.length] }" />
                            <span class="flex-1 truncate capitalize text-slate-600 dark:text-slate-300">{{ label }}</span>
                            <span class="sx-num font-semibold text-slate-800 dark:text-slate-200">{{ statusPercentage(value) }}%</span>
                        </div>
                        <p v-if="!statusEntries.length" class="text-xs text-slate-400">No data yet.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-3">
            <div class="sx-card overflow-hidden xl:col-span-2">
                <div class="border-b px-5 py-4 dark:border-slate-800">
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Alerts & anomalies</h2>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    <div v-for="alert in alerts.data" :key="alert.id" class="flex items-start gap-3 px-5 py-4">
                        <span
                            class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg"
                            :class="alert.severity === 'critical'
                                ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/10'
                                : alert.severity === 'warning'
                                    ? 'bg-amber-50 text-amber-600 dark:bg-amber-500/10'
                                    : 'bg-sky-50 text-sky-600 dark:bg-sky-500/10'"
                        >
                            <Icon :name="alert.severity === 'info' ? 'info' : 'alert'" :size="15" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ alert.title }}</p>
                                <Badge :label="alert.severity" :tone="alert.severity === 'critical' ? 'danger' : alert.severity === 'warning' ? 'warning' : 'info'" />
                                <Badge v-if="alert.status === 'acknowledged'" label="acknowledged" tone="neutral" />
                            </div>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ alert.message }}</p>
                            <p class="mt-1.5 text-[11px] text-slate-400">{{ alert.source }} · {{ relativeTime(alert.created_at) }}</p>
                        </div>
                        <button
                            v-if="alert.status === 'open'"
                            type="button"
                            class="sx-btn-secondary shrink-0 px-2.5 py-1.5 text-xs"
                            @click="router.post(route('alerts.acknowledge', alert.id), {}, { preserveScroll: true })"
                        >
                            <Icon name="check" :size="13" /> Acknowledge
                        </button>
                    </div>
                    <p v-if="!alerts.data.length" class="px-5 py-12 text-center text-sm text-slate-400">No alerts — everything looks healthy.</p>
                </div>
                <Pagination :links="alerts.links" />
            </div>

            <aside class="sx-card-pad h-fit">
                <h2 class="mb-4 text-sm font-bold text-slate-800 dark:text-white">Dataset coverage</h2>
                <div class="space-y-4">
                    <div v-for="dataset in datasets" :key="dataset.id">
                        <div class="mb-1.5 flex items-center justify-between gap-2">
                            <Link :href="route('datasets.show', dataset.id)" class="truncate text-xs font-semibold text-slate-700 hover:text-indigo-600 dark:text-slate-200">
                                {{ dataset.name }}
                            </Link>
                            <span class="sx-num text-[11px] text-slate-400">{{ dataset.record_count.toLocaleString() }}</span>
                        </div>
                        <div class="h-1.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                            <div class="h-full rounded-full bg-emerald-500" :style="{ width: `${Math.min(100, dataset.record_count / 1500)}%` }" />
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </AppShell>
</template>
