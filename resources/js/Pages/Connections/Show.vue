<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';
import PageHeader from '../../Components/Ui/PageHeader.vue';
import Badge from '../../Components/Ui/Badge.vue';
import Icon from '../../Components/Ui/Icon.vue';

const props = defineProps({
    connection: { type: Object, required: true },
    definition: { type: Object, default: null },
});

function formatDate(value) {
    return value ? new Date(value).toLocaleString() : 'Never';
}
</script>

<template>
    <AppShell>
        <PageHeader
            :eyebrow="definition?.category ?? 'Connection'"
            :title="connection.name"
            :subtitle="definition?.description ?? 'External data source'"
        >
            <template #actions>
                <Link :href="route('connections.index')" class="sx-btn-secondary"><Icon name="x" :size="15" /> Back</Link>
                <Link :href="route('connections.test', connection.id)" method="post" as="button" class="sx-btn-secondary">
                    <Icon name="activity" :size="15" /> Test connection
                </Link>
                <Link :href="route('connections.edit', connection.id)" class="sx-btn-primary">
                    <Icon name="edit" :size="15" /> Edit
                </Link>
            </template>
        </PageHeader>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            <div class="space-y-5 lg:col-span-2">
                <div class="sx-card-pad">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-800 dark:text-white">Configuration</h2>
                        <Badge :label="connection.status" :tone="connection.status === 'active' ? 'success' : 'neutral'" dot />
                    </div>
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="sx-label">Connector type</dt>
                            <dd class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ definition?.label ?? connection.type }}</dd>
                        </div>
                        <div>
                            <dt class="sx-label">Last tested</dt>
                            <dd class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ formatDate(connection.last_tested_at) }}</dd>
                        </div>
                        <div>
                            <dt class="sx-label">Created by</dt>
                            <dd class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ connection.creator?.name ?? 'System' }}</dd>
                        </div>
                        <div>
                            <dt class="sx-label">Added</dt>
                            <dd class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ formatDate(connection.created_at) }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 rounded-xl bg-slate-50 p-4 dark:bg-slate-950">
                        <p class="sx-label mb-3">Stored configuration</p>
                        <pre class="sx-num overflow-x-auto text-xs leading-relaxed text-slate-600 dark:text-slate-300">{{ JSON.stringify(connection.config ?? {}, null, 2) }}</pre>
                    </div>
                </div>

                <div class="sx-card overflow-hidden">
                    <div class="border-b px-5 py-4 dark:border-slate-800">
                        <h2 class="text-sm font-bold text-slate-800 dark:text-white">Downstream datasets</h2>
                    </div>
                    <div v-if="connection.datasets?.length" class="divide-y divide-slate-100 dark:divide-slate-800">
                        <Link
                            v-for="dataset in connection.datasets"
                            :key="dataset.id"
                            :href="route('datasets.show', dataset.id)"
                            class="flex items-center gap-3 px-5 py-3.5 transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
                        >
                            <Icon name="database" :size="16" class="text-indigo-500" />
                            <span class="flex-1 text-sm font-medium text-slate-700 dark:text-slate-200">{{ dataset.name }}</span>
                            <span class="sx-num text-xs text-slate-400">{{ dataset.record_count.toLocaleString() }} rows</span>
                            <Icon name="chevron-right" :size="14" class="text-slate-300" />
                        </Link>
                    </div>
                    <p v-else class="px-5 py-10 text-center text-sm text-slate-400">No datasets are using this connection yet.</p>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="sx-card-pad">
                    <h2 class="mb-4 text-sm font-bold text-slate-800 dark:text-white">Pipelines</h2>
                    <div class="space-y-2">
                        <Link
                            v-for="pipeline in connection.pipelines ?? []"
                            :key="pipeline.id"
                            :href="route('pipelines.show', pipeline.id)"
                            class="flex items-center justify-between rounded-lg border border-slate-100 px-3 py-2.5 text-sm transition hover:border-indigo-200 dark:border-slate-800"
                        >
                            <span class="font-medium text-slate-700 dark:text-slate-200">{{ pipeline.name }}</span>
                            <Badge :label="pipeline.status" :tone="pipeline.status === 'active' ? 'success' : 'neutral'" />
                        </Link>
                        <p v-if="!connection.pipelines?.length" class="py-6 text-center text-xs text-slate-400">No pipelines yet.</p>
                    </div>
                </div>

                <div class="sx-card-pad border-rose-100 dark:border-rose-950/60">
                    <h2 class="text-sm font-bold text-rose-700 dark:text-rose-400">Danger zone</h2>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Removing a connection cannot be undone. Pipelines using it will stop working.</p>
                    <button
                        type="button"
                        class="sx-btn-danger mt-4 w-full"
                        @click=" router.delete(route('connections.destroy', connection.id))"
                    >
                        <Icon name="trash" :size="15" /> Delete connection
                    </button>
                </div>
            </aside>
        </div>
    </AppShell>
</template>
