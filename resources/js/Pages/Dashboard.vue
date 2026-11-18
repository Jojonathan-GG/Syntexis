<script setup>
import { Link } from '@inertiajs/vue3';
import AppShell from '../Layouts/AppShell.vue';
import PageHeader from '../Components/Ui/PageHeader.vue';
import StatCard from '../Components/Ui/StatCard.vue';
import Badge from '../Components/Ui/Badge.vue';
import BarChart from '../Components/Ui/BarChart.vue';
import Icon from '../Components/Ui/Icon.vue';

const props = defineProps({
    stats: { type: Object, required: true },
    metrics: { type: Array, default: () => [] },
    runVolume: { type: Array, default: () => [] },
    recentRuns: { type: Array, default: () => [] },
    recentActivity: { type: Array, default: () => [] },
    alerts: { type: Array, default: () => [] },
    topDatasets: { type: Array, default: () => [] },
});

function formatNumber(value) {
    return new Intl.NumberFormat('en-US').format(Number(value) || 0);
}

function formatCompact(value) {
    return new Intl.NumberFormat('en-US', { notation: 'compact', maximumFractionDigits: 1 }).format(Number(value) || 0);
}

function relativeTime(value) {
    if (!value) {
        return '—';
    }

    const minutes = Math.round((Date.now() - new Date(value).getTime()) / 60000);

    if (minutes < 1) {
        return 'just now';
    }
    if (minutes < 60) {
        return `${minutes}m ago`;
    }
    if (minutes < 1440) {
        return `${Math.floor(minutes / 60)}h ago`;
    }

    return `${Math.floor(minutes / 1440)}d ago`;
}

function runTone(status) {
    return status === 'success' ? 'success' : status === 'failed' ? 'danger' : 'info';
}

const metricColors = ['#6366f1', '#10b981', '#f59e0b', '#0ea5e9', '#8b5cf6', '#ec4899'];
const metricIcons = ['database', 'check', 'clock', 'plug', 'activity', 'bolt'];

const primaryMetrics = ['records_ingested', 'pipeline_success', 'avg_latency', 'data_freshness'];
const headlineMetrics = props.metrics.filter((metric) => primaryMetrics.includes(metric.key));
const extraMetrics = props.metrics.filter((metric) => !primaryMetrics.includes(metric.key));
</script>

<template>
    <AppShell>
        <PageHeader
            eyebrow="Workspace overview"
            title="Good to see you, Avery"
            subtitle="Here is what is happening across your integration landscape."
        >
            <template #actions>
                <span class="hidden items-center gap-2 rounded-lg border bg-white px-3 py-2 text-xs font-medium text-slate-500 dark:bg-slate-900 sm:flex">
                    <Icon name="clock" :size="14" /> Last 14 days
                </span>
                <Link :href="route('pipelines.create')" class="sx-btn-primary">
                    <Icon name="plus" :size="16" /> New pipeline
                </Link>
            </template>
        </PageHeader>

        <!-- Headline stats -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard
                label="Active connections"
                :value="stats.connections"
                :change="extraMetrics.find((m) => m.key === 'active_sources')?.change_percent ?? 0"
                icon="plug"
                color="#6366f1"
                :series="extraMetrics.find((m) => m.key === 'active_sources')?.series ?? []"
            />
            <StatCard
                label="Running pipelines"
                :value="stats.activePipelines"
                icon="workflow"
                color="#10b981"
                :series="runVolume.map((item) => item.success)"
            />
            <StatCard
                label="Canonical records"
                :value="formatNumber(stats.records)"
                :change="headlineMetrics.find((m) => m.key === 'records_ingested')?.change_percent"
                icon="database"
                color="#0ea5e9"
                :series="headlineMetrics.find((m) => m.key === 'records_ingested')?.series ?? []"
            />
            <StatCard
                label="Pipeline success"
                :value="`${stats.successRate}%`"
                :change="headlineMetrics.find((m) => m.key === 'pipeline_success')?.change_percent"
                icon="check"
                color="#f59e0b"
                :series="headlineMetrics.find((m) => m.key === 'pipeline_success')?.series ?? []"
            />
        </div>

        <!-- Charts + alerts -->
        <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-3">
            <div class="sx-card-pad xl:col-span-2">
                <div class="mb-5 flex items-start justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 dark:text-white">Pipeline activity</h2>
                        <p class="mt-0.5 text-xs text-slate-400">Runs per day across all pipelines</p>
                    </div>
                    <Badge label="Live" tone="success" dot />
                </div>
                <BarChart :data="runVolume" :height="190" />
            </div>

            <div class="sx-card-pad">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Attention needed</h2>
                    <Link :href="route('insights.index')" class="text-xs font-semibold text-indigo-600">View all</Link>
                </div>
                <div class="space-y-3">
                    <div
                        v-for="alert in alerts.slice(0, 4)"
                        :key="alert.id"
                        class="flex gap-3 rounded-lg border border-slate-100 p-3 dark:border-slate-800"
                    >
                        <div
                            class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg"
                            :class="alert.severity === 'critical'
                                ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/10'
                                : alert.severity === 'warning'
                                    ? 'bg-amber-50 text-amber-600 dark:bg-amber-500/10'
                                    : 'bg-sky-50 text-sky-600 dark:bg-sky-500/10'"
                        >
                            <Icon :name="alert.severity === 'info' ? 'info' : 'alert'" :size="14" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-semibold text-slate-800 dark:text-slate-200">{{ alert.title }}</p>
                            <p class="mt-0.5 text-[11px] text-slate-400">{{ relativeTime(alert.created_at) }} · {{ alert.source }}</p>
                        </div>
                    </div>
                    <p v-if="!alerts.length" class="py-8 text-center text-xs text-slate-400">All clear — no alerts.</p>
                </div>
            </div>
        </div>

        <!-- Metric catalog -->
        <div v-if="headlineMetrics.length" class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div v-for="(metric, index) in headlineMetrics" :key="metric.id" class="sx-card-pad">
                <div class="flex items-start justify-between">
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ metric.name }}</p>
                    <Icon :name="metricIcons[index] ?? 'activity'" :size="15" :style="{ color: metricColors[index] }" />
                </div>
                <p class="sx-num mt-2 text-xl font-bold text-slate-900 dark:text-white">
                    {{ formatCompact(metric.value) }}<span v-if="metric.unit" class="ml-1 text-xs font-medium text-slate-400">{{ metric.unit }}</span>
                </p>
                <div class="mt-3">
                    <span
                        class="text-xs font-semibold"
                        :class="Number(metric.change_percent) >= 0 ? 'text-emerald-600' : 'text-rose-600'"
                    >
                        {{ Number(metric.change_percent) > 0 ? '+' : '' }}{{ metric.change_percent }}%
                    </span>
                </div>
            </div>
        </div>

        <!-- Tables -->
        <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-3">
            <div class="sx-card overflow-hidden xl:col-span-2">
                <div class="border-b px-5 py-4 dark:border-slate-800">
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Recent pipeline runs</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="sx-table">
                        <thead>
                            <tr>
                                <th class="sx-th">Pipeline</th>
                                <th class="sx-th">Status</th>
                                <th class="sx-th text-right">Rows</th>
                                <th class="sx-th text-right">Duration</th>
                                <th class="sx-th text-right">When</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="run in recentRuns" :key="run.id" class="sx-row-hover">
                                <td class="sx-td font-medium text-slate-800 dark:text-slate-200">
                                    <Link :href="route('pipelines.show', run.pipeline_id)" class="sx-link">
                                        {{ run.pipeline?.name ?? `Pipeline #${run.pipeline_id}` }}
                                    </Link>
                                </td>
                                <td class="sx-td"><Badge :label="run.status" :tone="runTone(run.status)" dot /></td>
                                <td class="sx-td sx-num text-right text-slate-600 dark:text-slate-300">{{ formatNumber(run.rows_processed) }}</td>
                                <td class="sx-td sx-num text-right text-slate-500">{{ (run.duration_ms / 1000).toFixed(1) }}s</td>
                                <td class="sx-td text-right text-xs text-slate-400">{{ relativeTime(run.started_at) }}</td>
                            </tr>
                            <tr v-if="!recentRuns.length">
                                <td colspan="5" class="sx-td py-10 text-center text-sm text-slate-400">No runs yet. Run a pipeline to see activity here.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-5">
                <div class="sx-card-pad">
                    <h2 class="mb-4 text-sm font-bold text-slate-800 dark:text-white">Top datasets</h2>
                    <div class="space-y-3.5">
                        <div v-for="dataset in topDatasets" :key="dataset.id">
                            <div class="mb-1 flex items-center justify-between gap-2">
                                <Link :href="route('datasets.show', dataset.id)" class="truncate text-xs font-semibold text-slate-700 hover:text-indigo-600 dark:text-slate-200">
                                    {{ dataset.name }}
                                </Link>
                                <span class="sx-num text-[11px] text-slate-400">{{ formatCompact(dataset.record_count) }}</span>
                            </div>
                            <div class="h-1.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                                <div class="h-full rounded-full bg-indigo-500" :style="{ width: `${Math.min(100, dataset.record_count / 1500)}%` }" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sx-card-pad">
                    <h2 class="mb-4 text-sm font-bold text-slate-800 dark:text-white">Activity feed</h2>
                    <div class="space-y-3">
                        <div v-for="log in recentActivity.slice(0, 5)" :key="log.id" class="flex gap-2.5">
                            <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-400" />
                            <div class="min-w-0">
                                <p class="truncate text-xs text-slate-700 dark:text-slate-300">{{ log.description }}</p>
                                <p class="text-[10px] text-slate-400">{{ log.user?.name ?? 'System' }} · {{ relativeTime(log.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppShell>
</template>
