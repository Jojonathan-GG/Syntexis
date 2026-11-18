<script setup>
import { Link } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';
import PageHeader from '../../Components/Ui/PageHeader.vue';
import Badge from '../../Components/Ui/Badge.vue';
import Icon from '../../Components/Ui/Icon.vue';

const props = defineProps({
    dataset: { type: Object, required: true },
    records: { type: Array, default: () => [] },
    pipelines: { type: Array, default: () => [] },
});

function formatDate(value) {
    return value ? new Date(value).toLocaleString() : '—';
}

const columns = ['customer', 'region', 'amount', 'status', 'source'];
</script>

<template>
    <AppShell>
        <PageHeader
            eyebrow="Canonical data"
            :title="dataset.name"
            :subtitle="dataset.description ?? 'Unified dataset'"
        >
            <template #actions>
                <Link :href="route('datasets.index')" class="sx-btn-secondary"><Icon name="x" :size="15" /> Back</Link>
            </template>
        </PageHeader>

        <div class="mb-5 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="sx-card-pad">
                <p class="sx-label">Total records</p>
                <p class="sx-num mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ dataset.record_count.toLocaleString() }}</p>
            </div>
            <div class="sx-card-pad">
                <p class="sx-label">Canonical fields</p>
                <p class="sx-num mt-2 text-2xl font-bold text-indigo-600">{{ dataset.schema_json?.length ?? 0 }}</p>
            </div>
            <div class="sx-card-pad">
                <p class="sx-label">Source</p>
                <p class="mt-3 truncate text-sm font-semibold text-slate-800 dark:text-slate-200">{{ dataset.connection?.name ?? '—' }}</p>
            </div>
            <div class="sx-card-pad">
                <p class="sx-label">Freshness</p>
                <p class="mt-3"><Badge :label="formatDate(dataset.freshness_at)" tone="success" dot /></p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 xl:grid-cols-4">
            <div class="space-y-5 xl:col-span-3">
                <div class="sx-card overflow-hidden">
                    <div class="border-b px-5 py-4 dark:border-slate-800">
                        <h2 class="text-sm font-bold text-slate-800 dark:text-white">Record preview</h2>
                        <p class="mt-0.5 text-xs text-slate-400">Most recent normalized records</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="sx-table">
                            <thead>
                                <tr>
                                    <th class="sx-th">External ID</th>
                                    <th v-for="column in columns" :key="column" class="sx-th capitalize">{{ column }}</th>
                                    <th class="sx-th">Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="record in records" :key="record.id" class="sx-row-hover">
                                    <td class="sx-td sx-num text-xs font-semibold text-indigo-600">{{ record.external_id }}</td>
                                    <td v-for="column in columns" :key="column" class="sx-td text-xs text-slate-600 dark:text-slate-300">
                                        <Badge v-if="column === 'status'" :label="record.data?.status" :tone="record.data?.status === 'paid' ? 'success' : record.data?.status === 'failed' ? 'danger' : 'warning'" />
                                        <span v-else-if="column === 'amount'" class="sx-num">${{ Number(record.data?.amount ?? 0).toLocaleString() }}</span>
                                        <span v-else>{{ record.data?.[column] ?? '—' }}</span>
                                    </td>
                                    <td class="sx-td text-xs text-slate-400">{{ formatDate(record.created_at) }}</td>
                                </tr>
                                <tr v-if="!records.length">
                                    <td :colspan="columns.length + 2" class="sx-td py-12 text-center text-sm text-slate-400">No records yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="sx-card-pad">
                    <h2 class="mb-4 text-sm font-bold text-slate-800 dark:text-white">Canonical schema</h2>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <div v-for="field in dataset.schema_json ?? []" :key="field.name" class="flex items-center justify-between rounded-lg border border-slate-100 px-3 py-2.5 dark:border-slate-800">
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ field.name }}</span>
                            <Badge :label="field.type" tone="info" />
                        </div>
                    </div>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="sx-card-pad">
                    <h2 class="mb-4 text-sm font-bold text-slate-800 dark:text-white">Producing pipelines</h2>
                    <div class="space-y-2">
                        <Link
                            v-for="pipeline in pipelines"
                            :key="pipeline.id"
                            :href="route('pipelines.show', pipeline.id)"
                            class="flex items-center justify-between rounded-lg border border-slate-100 px-3 py-2.5 text-xs transition hover:border-indigo-200 dark:border-slate-800"
                        >
                            <span class="font-semibold text-slate-700 dark:text-slate-200">{{ pipeline.name }}</span>
                            <Badge :label="pipeline.status" :tone="pipeline.status === 'active' ? 'success' : 'neutral'" />
                        </Link>
                        <p v-if="!pipelines.length" class="py-6 text-center text-xs text-slate-400">No pipelines target this dataset.</p>
                    </div>
                </div>

                <div class="sx-card-pad">
                    <h2 class="mb-4 text-sm font-bold text-slate-800 dark:text-white">Dataset metadata</h2>
                    <dl class="space-y-3 text-xs">
                        <div class="flex justify-between"><dt class="text-slate-400">ID</dt><dd class="sx-num text-slate-700 dark:text-slate-200">#{{ dataset.id }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-400">Created</dt><dd class="text-slate-700 dark:text-slate-200">{{ formatDate(dataset.created_at) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-400">Source ID</dt><dd class="sx-num text-slate-700 dark:text-slate-200">#{{ dataset.connection_id ?? '—' }}</dd></div>
                    </dl>
                </div>
            </aside>
        </div>
    </AppShell>
</template>
