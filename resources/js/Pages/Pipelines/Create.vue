<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';
import PageHeader from '../../Components/Ui/PageHeader.vue';
import Icon from '../../Components/Ui/Icon.vue';

const props = defineProps({
    connections: { type: Array, required: true },
    datasets: { type: Array, required: true },
});

const form = useForm({
    name: '',
    connection_id: props.connections[0]?.id ?? '',
    dataset_id: '',
    schedule: 'manual',
    status: 'active',
    rows: 250,
    mapping_mode: 'auto',
});

function submit() {
    form.post(route('pipelines.store'));
}
</script>

<template>
    <AppShell>
        <PageHeader
            eyebrow="Data pipelines"
            title="New pipeline"
            subtitle="Define how source data is validated, mapped and loaded."
        >
            <template #actions>
                <Link :href="route('pipelines.index')" class="sx-btn-secondary"><Icon name="x" :size="15" /> Cancel</Link>
            </template>
        </PageHeader>

        <form class="grid grid-cols-1 gap-5 lg:grid-cols-3" @submit.prevent="submit">
            <div class="space-y-5 lg:col-span-2">
                <div class="sx-card-pad">
                    <h2 class="mb-5 text-sm font-bold text-slate-800 dark:text-white">Pipeline configuration</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="sx-label" for="name">Pipeline name</label>
                            <input id="name" v-model="form.name" type="text" class="sx-input" placeholder="e.g. ERP Orders Sync" />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-rose-600">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label class="sx-label" for="connection">Source connection</label>
                            <select id="connection" v-model="form.connection_id" class="sx-input">
                                <option value="" disabled>Select a connection</option>
                                <option v-for="connection in connections" :key="connection.id" :value="connection.id">
                                    {{ connection.name }} ({{ connection.type }})
                                </option>
                            </select>
                            <p v-if="form.errors.connection_id" class="mt-1 text-xs text-rose-600">{{ form.errors.connection_id }}</p>
                        </div>
                        <div>
                            <label class="sx-label" for="dataset">Destination dataset</label>
                            <select id="dataset" v-model="form.dataset_id" class="sx-input">
                                <option value="">Auto-create from pipeline</option>
                                <option v-for="dataset in datasets" :key="dataset.id" :value="dataset.id">{{ dataset.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="sx-label" for="schedule">Schedule</label>
                            <select id="schedule" v-model="form.schedule" class="sx-input">
                                <option value="manual">Manual</option>
                                <option value="hourly">Hourly</option>
                                <option value="daily">Daily</option>
                            </select>
                        </div>
                        <div>
                            <label class="sx-label" for="status">Initial status</label>
                            <select id="status" v-model="form.status" class="sx-input">
                                <option value="active">Active</option>
                                <option value="paused">Paused</option>
                            </select>
                        </div>
                        <div>
                            <label class="sx-label" for="rows">Rows per run</label>
                            <input id="rows" v-model.number="form.rows" type="number" min="1" max="2000" class="sx-input sx-num" />
                            <p v-if="form.errors.rows" class="mt-1 text-xs text-rose-600">{{ form.errors.rows }}</p>
                        </div>
                        <div>
                            <label class="sx-label" for="mapping">Field mapping</label>
                            <select id="mapping" v-model="form.mapping_mode" class="sx-input">
                                <option value="auto">Auto-detect</option>
                                <option value="custom">Custom mapping</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="sx-card-pad">
                    <h2 class="mb-5 text-sm font-bold text-slate-800 dark:text-white">Pipeline flow</h2>
                    <div class="flex flex-wrap items-center gap-2">
                        <div v-for="(step, index) in [
                            { label: 'Extract', icon: 'plug', hint: 'Read source records' },
                            { label: 'Validate', icon: 'shield', hint: 'Check schema & types' },
                            { label: 'Map', icon: 'layers', hint: 'Map to canonical fields' },
                            { label: 'Load', icon: 'database', hint: 'Write to dataset' },
                        ]" :key="step.label" class="flex items-center gap-2">
                            <div class="rounded-lg border border-slate-200 px-3 py-2.5 text-center dark:border-slate-700">
                                <Icon :name="step.icon" :size="16" class="mx-auto text-indigo-500" />
                                <p class="mt-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200">{{ step.label }}</p>
                                <p class="text-[10px] text-slate-400">{{ step.hint }}</p>
                            </div>
                            <Icon v-if="index < 3" name="chevron-right" :size="16" class="text-slate-300" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <Link :href="route('pipelines.index')" class="sx-btn-secondary">Cancel</Link>
                    <button type="submit" class="sx-btn-primary" :disabled="form.processing">
                        <Icon name="check" :size="16" /> {{ form.processing ? 'Creating...' : 'Create pipeline' }}
                    </button>
                </div>
            </div>

            <aside class="sx-card-pad h-fit">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white">Before you run</h3>
                <ul class="mt-4 space-y-3 text-xs text-slate-500 dark:text-slate-400">
                    <li class="flex gap-2"><Icon name="check" :size="14" class="mt-0.5 text-emerald-500" /> Connection must be healthy.</li>
                    <li class="flex gap-2"><Icon name="check" :size="14" class="mt-0.5 text-emerald-500" /> Runs are queued in the background.</li>
                    <li class="flex gap-2"><Icon name="check" :size="14" class="mt-0.5 text-emerald-500" /> Each run writes rows to the destination dataset.</li>
                    <li class="flex gap-2"><Icon name="check" :size="14" class="mt-0.5 text-emerald-500" /> Metrics refresh automatically after a run.</li>
                </ul>
                <div v-if="!connections.length" class="mt-5 rounded-lg bg-amber-50 p-3 text-xs text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
                    No active connections found. <Link :href="route('connections.create')" class="font-semibold underline">Create one first</Link>.
                </div>
            </aside>
        </form>
    </AppShell>
</template>
