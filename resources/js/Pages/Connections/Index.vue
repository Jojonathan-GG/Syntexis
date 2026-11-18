<script setup>
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';
import PageHeader from '../../Components/Ui/PageHeader.vue';
import Badge from '../../Components/Ui/Badge.vue';
import Icon from '../../Components/Ui/Icon.vue';
import EmptyState from '../../Components/Ui/EmptyState.vue';

const props = defineProps({
    connections: { type: Object, required: true },
    catalog: { type: Array, default: () => [] },
});

const search = ref('');
const statusFilter = ref('all');

const filtered = computed(() =>
    props.connections.data.filter((connection) => {
        const matchesSearch = connection.name.toLowerCase().includes(search.value.trim().toLowerCase());
        const matchesStatus = statusFilter.value === 'all' || connection.status === statusFilter.value;

        return matchesSearch && matchesStatus;
    })
);

function definitionFor(type) {
    return props.catalog.find((item) => item.type === type);
}

function relativeTime(value) {
    if (!value) {
        return 'Never';
    }

    const minutes = Math.round((Date.now() - new Date(value).getTime()) / 60000);

    return minutes < 60 ? `${minutes}m ago` : `${Math.floor(minutes / 60)}h ago`;
}

function destroyConnection(connection) {
    if (!window.confirm(`Delete "${connection.name}"? This cannot be undone.`)) {
        return;
    }

    router.delete(route('connections.destroy', connection.id), { preserveScroll: true });
}
</script>

<template>
    <AppShell>
        <PageHeader
            eyebrow="Connectivity"
            title="Connections"
            subtitle="External systems available to your pipelines."
        >
            <template #actions>
                <Link :href="route('connections.create')" class="sx-btn-primary">
                    <Icon name="plus" :size="16" /> New connection
                </Link>
            </template>
        </PageHeader>

        <div class="mb-5 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="sx-card-pad">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total sources</p>
                <p class="sx-num mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ connections.total }}</p>
            </div>
            <div class="sx-card-pad">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Active</p>
                <p class="sx-num mt-2 text-2xl font-bold text-emerald-600">{{ connections.data.filter((c) => c.status === 'active').length }}</p>
            </div>
            <div class="sx-card-pad">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Connector types</p>
                <p class="sx-num mt-2 text-2xl font-bold text-indigo-600">{{ new Set(connections.data.map((c) => c.type)).size }}</p>
            </div>
            <div class="sx-card-pad">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total pipelines</p>
                <p class="sx-num mt-2 text-2xl font-bold text-sky-600">{{ connections.data.reduce((sum, c) => sum + c.pipelines_count, 0) }}</p>
            </div>
        </div>

        <div class="sx-card overflow-hidden">
            <div class="flex flex-wrap items-center gap-3 border-b px-4 py-3 dark:border-slate-800">
                <div class="relative min-w-[220px] flex-1">
                    <Icon name="search" :size="15" class="absolute left-3 top-2.5 text-slate-400" />
                    <input v-model="search" type="search" placeholder="Search connections..." class="sx-input pl-9" />
                </div>
                <select v-model="statusFilter" class="sx-input w-auto">
                    <option value="all">All statuses</option>
                    <option value="active">Active</option>
                    <option value="paused">Paused</option>
                </select>
            </div>

            <div v-if="filtered.length" class="overflow-x-auto">
                <table class="sx-table">
                    <thead>
                        <tr>
                            <th class="sx-th">Source</th>
                            <th class="sx-th">Type</th>
                            <th class="sx-th">Datasets</th>
                            <th class="sx-th">Pipelines</th>
                            <th class="sx-th">Last tested</th>
                            <th class="sx-th">Status</th>
                            <th class="sx-th text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="connection in filtered" :key="connection.id" class="sx-row-hover">
                            <td class="sx-td">
                                <Link :href="route('connections.show', connection.id)" class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10">
                                        <Icon :name="definitionFor(connection.type)?.icon ?? 'plug'" :size="17" />
                                    </span>
                                    <span>
                                        <span class="block text-sm font-semibold text-slate-800 dark:text-slate-200">{{ connection.name }}</span>
                                        <span class="block text-[11px] text-slate-400">#{{ connection.id }}</span>
                                    </span>
                                </Link>
                            </td>
                            <td class="sx-td text-xs text-slate-600 dark:text-slate-300">{{ definitionFor(connection.type)?.label ?? connection.type }}</td>
                            <td class="sx-td sx-num text-slate-600 dark:text-slate-300">{{ connection.datasets_count }}</td>
                            <td class="sx-td sx-num text-slate-600 dark:text-slate-300">{{ connection.pipelines_count }}</td>
                            <td class="sx-td text-xs text-slate-400">{{ relativeTime(connection.last_tested_at) }}</td>
                            <td class="sx-td">
                                <Badge :label="connection.status" :tone="connection.status === 'active' ? 'success' : 'neutral'" dot />
                            </td>
                            <td class="sx-td">
                                <div class="flex items-center justify-end gap-1">
                                    <Link
                                        :href="route('connections.test', connection.id)"
                                        method="post"
                                        as="button"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-emerald-50 hover:text-emerald-600"
                                        title="Test connection"
                                    >
                                        <Icon name="activity" :size="15" />
                                    </Link>
                                    <Link :href="route('connections.edit', connection.id)" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-indigo-600" title="Edit">
                                        <Icon name="edit" :size="15" />
                                    </Link>
                                    <button type="button" class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" title="Delete" @click="destroyConnection(connection)">
                                        <Icon name="trash" :size="15" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <EmptyState v-else icon="plug" title="No connections found" description="Adjust your filters or add a new source to get started.">
                <Link :href="route('connections.create')" class="sx-btn-primary"><Icon name="plus" :size="15" /> Add connection</Link>
            </EmptyState>
        </div>
    </AppShell>
</template>
