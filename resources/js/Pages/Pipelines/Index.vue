<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';
import PageHeader from '../../Components/Ui/PageHeader.vue';
import Badge from '../../Components/Ui/Badge.vue';
import Icon from '../../Components/Ui/Icon.vue';
import EmptyState from '../../Components/Ui/EmptyState.vue';
import Pagination from '../../Components/Ui/Pagination.vue';

const props = defineProps({
    pipelines: { type: Object, required: true },
});

function relativeTime(value) {
    if (!value) {
        return 'Never';
    }

    const minutes = Math.round((Date.now() - new Date(value).getTime()) / 60000);

    return minutes < 60 ? `${minutes}m ago` : minutes < 1440 ? `${Math.floor(minutes / 60)}h ago` : `${Math.floor(minutes / 1440)}d ago`;
}

function destroyPipeline(pipeline) {
    if (!window.confirm(`Delete "${pipeline.name}"?`)) {
        return;
    }

    router.delete(route('pipelines.destroy', pipeline.id), { preserveScroll: true });
}

function dataRows(pipeline) {
    return pipeline.config?.rows ?? 250;
}
</script>

<template>
    <AppShell>
        <PageHeader
            eyebrow="Data pipelines"
            title="Pipelines"
            subtitle="Orchestrate ingestion, mapping and loading into canonical datasets."
        >
            <template #actions>
                <Link :href="route('pipelines.create')" class="sx-btn-primary"><Icon name="plus" :size="16" /> New pipeline</Link>
            </template>
        </PageHeader>

        <div class="mb-5 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="sx-card-pad">
                <p class="sx-label">Total pipelines</p>
                <p class="sx-num mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ pipelines.total }}</p>
            </div>
            <div class="sx-card-pad">
                <p class="sx-label">Active</p>
                <p class="sx-num mt-2 text-2xl font-bold text-emerald-600">{{ pipelines.data.filter((p) => p.status === 'active').length }}</p>
            </div>
            <div class="sx-card-pad">
                <p class="sx-label">Paused</p>
                <p class="sx-num mt-2 text-2xl font-bold text-amber-600">{{ pipelines.data.filter((p) => p.status === 'paused').length }}</p>
            </div>
            <div class="sx-card-pad">
                <p class="sx-label">Scheduled</p>
                <p class="sx-num mt-2 text-2xl font-bold text-indigo-600">{{ pipelines.data.filter((p) => p.schedule !== 'manual').length }}</p>
            </div>
        </div>

        <div class="sx-card overflow-hidden">
            <div v-if="pipelines.data.length" class="overflow-x-auto">
                <table class="sx-table">
                    <thead>
                        <tr>
                            <th class="sx-th">Pipeline</th>
                            <th class="sx-th">Source</th>
                            <th class="sx-th">Destination</th>
                            <th class="sx-th">Schedule</th>
                            <th class="sx-th">Last run</th>
                            <th class="sx-th">Status</th>
                            <th class="sx-th text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="pipeline in pipelines.data" :key="pipeline.id" class="sx-row-hover">
                            <td class="sx-td">
                                <Link :href="route('pipelines.show', pipeline.id)" class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10">
                                        <Icon name="workflow" :size="17" />
                                    </span>
                                    <span>
                                        <span class="block text-sm font-semibold text-slate-800 dark:text-slate-200">{{ pipeline.name }}</span>
                                        <span class="block text-[11px] text-slate-400">{{ dataRows(pipeline) }} rows / run</span>
                                    </span>
                                </Link>
                            </td>
                            <td class="sx-td text-xs text-slate-600 dark:text-slate-300">{{ pipeline.connection?.name ?? '—' }}</td>
                            <td class="sx-td text-xs text-slate-600 dark:text-slate-300">{{ pipeline.dataset?.name ?? 'Auto-create' }}</td>
                            <td class="sx-td"><Badge :label="pipeline.schedule" tone="info" /></td>
                            <td class="sx-td">
                                <div class="flex items-center gap-2">
                                    <Badge
                                        v-if="pipeline.latest_run"
                                        :label="pipeline.latest_run.status"
                                        :tone="pipeline.latest_run.status === 'success' ? 'success' : 'danger'"
                                        dot
                                    />
                                    <span class="text-[11px] text-slate-400">{{ relativeTime(pipeline.last_run_at) }}</span>
                                </div>
                            </td>
                            <td class="sx-td">
                                <Badge :label="pipeline.status" :tone="pipeline.status === 'active' ? 'success' : 'neutral'" dot />
                            </td>
                            <td class="sx-td">
                                <div class="flex items-center justify-end gap-1">
                                    <Link
                                        v-if="pipeline.status === 'active'"
                                        :href="route('pipelines.run', pipeline.id)"
                                        method="post"
                                        as="button"
                                        class="sx-btn-primary px-2.5 py-1.5 text-xs"
                                        title="Run now"
                                    >
                                        <Icon name="play" :size="13" /> Run
                                    </Link>
                                    <Link
                                        :href="route('pipelines.toggle', pipeline.id)"
                                        method="post"
                                        as="button"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-amber-50 hover:text-amber-600"
                                        :title="pipeline.status === 'active' ? 'Pause' : 'Resume'"
                                    >
                                        <Icon :name="pipeline.status === 'active' ? 'pause' : 'play'" :size="15" />
                                    </Link>
                                    <Link :href="route('pipelines.edit', pipeline.id)" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-indigo-600" title="Edit">
                                        <Icon name="edit" :size="15" />
                                    </Link>
                                    <button type="button" class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" title="Delete" @click="destroyPipeline(pipeline)">
                                        <Icon name="trash" :size="15" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <EmptyState v-else icon="workflow" title="No pipelines yet" description="Create a pipeline to start moving data into canonical datasets.">
                <Link :href="route('pipelines.create')" class="sx-btn-primary"><Icon name="plus" :size="15" /> New pipeline</Link>
            </EmptyState>
            <Pagination :links="pipelines.links" />
        </div>
    </AppShell>
</template>
