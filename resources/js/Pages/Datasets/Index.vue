<script setup>
import { Link } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';
import PageHeader from '../../Components/Ui/PageHeader.vue';
import Badge from '../../Components/Ui/Badge.vue';
import Icon from '../../Components/Ui/Icon.vue';
import Pagination from '../../Components/Ui/Pagination.vue';

const props = defineProps({
    datasets: { type: Object, required: true },
});

function relativeTime(value) {
    if (!value) {
        return 'Never';
    }

    const minutes = Math.round((Date.now() - new Date(value).getTime()) / 60000);

    return minutes < 60 ? `${minutes}m ago` : minutes < 1440 ? `${Math.floor(minutes / 60)}h ago` : `${Math.floor(minutes / 1440)}d ago`;
}
</script>

<template>
    <AppShell>
        <PageHeader
            eyebrow="Canonical data"
            title="Datasets"
            subtitle="Governed, unified data produced by your pipelines."
        />

        <div class="sx-card overflow-hidden">
            <div v-if="datasets.data.length" class="overflow-x-auto">
                <table class="sx-table">
                    <thead>
                        <tr>
                            <th class="sx-th">Dataset</th>
                            <th class="sx-th">Source</th>
                            <th class="sx-th">Fields</th>
                            <th class="sx-th text-right">Records</th>
                            <th class="sx-th">Freshness</th>
                            <th class="sx-th text-right">Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="dataset in datasets.data" :key="dataset.id" class="sx-row-hover">
                            <td class="sx-td">
                                <Link :href="route('datasets.show', dataset.id)" class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-50 text-sky-600 dark:bg-sky-500/10">
                                        <Icon name="database" :size="17" />
                                    </span>
                                    <span>
                                        <span class="block text-sm font-semibold text-slate-800 dark:text-slate-200">{{ dataset.name }}</span>
                                        <span class="block max-w-md truncate text-[11px] text-slate-400">{{ dataset.description }}</span>
                                    </span>
                                </Link>
                            </td>
                            <td class="sx-td text-xs text-slate-600 dark:text-slate-300">{{ dataset.connection?.name ?? '—' }}</td>
                            <td class="sx-td sx-num text-slate-500">{{ dataset.schema_json?.length ?? 0 }}</td>
                            <td class="sx-td sx-num text-right font-semibold text-slate-800 dark:text-slate-200">{{ dataset.record_count.toLocaleString() }}</td>
                            <td class="sx-td"><Badge :label="relativeTime(dataset.freshness_at)" tone="success" dot /></td>
                            <td class="sx-td text-right text-xs text-slate-400">{{ relativeTime(dataset.updated_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="px-5 py-14 text-center text-sm text-slate-400">No datasets yet. Run a pipeline to create one.</div>
            <Pagination :links="datasets.links" />
        </div>
    </AppShell>
</template>
