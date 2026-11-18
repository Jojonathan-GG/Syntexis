<script setup>
import { Link } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';
import PageHeader from '../../Components/Ui/PageHeader.vue';
import Badge from '../../Components/Ui/Badge.vue';
import Icon from '../../Components/Ui/Icon.vue';

const props = defineProps({
    pipeline: { type: Object, required: true },
    runs: { type: Array, default: () => [] },
});

function formatDate(value) {
    return value ? new Date(value).toLocaleString() : '—';
}

const stats = {
    total: props.runs.length,
    success: props.runs.filter((run) => run.status === 'success').length,
    failed: props.runs.filter((run) => run.status === 'failed').length,
    rows: props.runs.reduce((sum, run) => sum + run.rows_processed, 0),
};
</script>

<template>
    <AppShell>
        <PageHeader
            eyebrow="Pipeline detail"
            :title="pipeline.name"
            :subtitle="`${pipeline.connection?.name ?? 'No source'} → ${pipeline.dataset?.name ?? 'Auto-created dataset'}`"
        >
            <template #actions>
                <Link :href="route('pipelines.index')" class="sx-btn-secondary"><Icon name="x" :size="15" /> Back</Link>
                <Link :href="route('pipelines.toggle', pipeline.id)" method="post" as="button" class="sx-btn-secondary">
                    <Icon :name="pipeline.status === 'active' ? 'pause' : 'play'" :size="15" />
                    {{ pipeline.status === 'active' ? 'Pause' : 'Resume' }}
                </Link>
                <Link
                    v-if="pipeline.status === 'active'"
                    :href="route('pipelines.run', pipeline.id)"
                    method="post"
                    as="button"
                    class="sx-btn-primary"
                >
                    <Icon name="play" :size="15" /> Run now
                </Link>
            </template>
        </PageHeader>

        <div class="mb-5 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="sx-card-pad">
                <p class="sx-label">Total runs</p>
                <p class="sx-num mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ stats.total }}</p>
            </div>
            <div class="sx-card-pad">
                <p class="sx-label">Successful</p>
                <p class="sx-num mt-2 text-2xl font-bold text-emerald-600">{{ stats.success }}</p>
            </div>
            <div class="sx-card-pad">
                <p class="sx-label">Failed</p>
                <p class="sx-num mt-2 text-2xl font-bold text-rose-600">{{ stats.failed }}</p>
            </div>
            <div class="sx-card-pad">
                <p class="sx-label">Rows processed</p>
                <p class="sx-num mt-2 text-2xl font-bold text-indigo-600">{{ stats.rows.toLocaleString() }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            <div class="sx-card-pad lg:col-span-2">
                <h2 class="mb-4 text-sm font-bold text-slate-800 dark:text-white">Run history</h2>
                <div class="overflow-x-auto">
                    <table class="sx-table">
                        <thead>
                            <tr>
                                <th class="sx-th">Status</th>
                                <th class="sx-th text-right">Rows</th>
                                <th class="sx-th text-right">Failed</th>
                                <th class="sx-th text-right">Duration</th>
                                <th class="sx-th">Started</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="run in runs" :key="run.id" class="sx-row-hover">
                                <td class="sx-td"><Badge :label="run.status" :tone="run.status === 'success' ? 'success' : 'danger'" dot /></td>
                                <td class="sx-td sx-num text-right text-slate-700 dark:text-slate-300">{{ run.rows_processed.toLocaleString() }}</td>
                                <td class="sx-td sx-num text-right" :class="run.rows_failed ? 'text-rose-600' : 'text-slate-400'">{{ run.rows_failed }}</td>
                                <td class="sx-td sx-num text-right text-slate-500">{{ (run.duration_ms / 1000).toFixed(1) }}s</td>
                                <td class="sx-td text-xs text-slate-400">{{ formatDate(run.started_at) }}</td>
                            </tr>
                            <tr v-if="!runs.length">
                                <td colspan="5" class="sx-td py-10 text-center text-sm text-slate-400">No runs recorded yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="sx-card-pad">
                    <h2 class="mb-4 text-sm font-bold text-slate-800 dark:text-white">Configuration</h2>
                    <dl class="space-y-3 text-xs">
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-400">Status</dt>
                            <dd><Badge :label="pipeline.status" :tone="pipeline.status === 'active' ? 'success' : 'neutral'" /></dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-400">Schedule</dt>
                            <dd class="font-semibold capitalize text-slate-700 dark:text-slate-200">{{ pipeline.schedule }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-400">Rows / run</dt>
                            <dd class="sx-num font-semibold text-slate-700 dark:text-slate-200">{{ pipeline.config?.rows ?? 250 }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-400">Mapping</dt>
                            <dd class="font-semibold capitalize text-slate-700 dark:text-slate-200">{{ pipeline.config?.mapping_mode ?? 'auto' }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-400">Last run</dt>
                            <dd class="text-slate-700 dark:text-slate-200">{{ formatDate(pipeline.last_run_at) }}</dd>
                        </div>
                    </dl>
                    <Link :href="route('pipelines.edit', pipeline.id)" class="sx-btn-secondary mt-5 w-full">
                        <Icon name="edit" :size="15" /> Edit configuration
                    </Link>
                </div>

                <div class="sx-card-pad">
                    <h2 class="mb-4 text-sm font-bold text-slate-800 dark:text-white">Pipeline steps</h2>
                    <ol class="space-y-3">
                        <li v-for="(step, index) in ['Extract from source', 'Validate schema', 'Map to canonical model', 'Load into dataset']" :key="step" class="flex items-center gap-3 text-xs text-slate-600 dark:text-slate-300">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-50 text-[10px] font-bold text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">{{ index + 1 }}</span>
                            {{ step }}
                        </li>
                    </ol>
                </div>
            </aside>
        </div>
    </AppShell>
</template>
