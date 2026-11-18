<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';
import PageHeader from '../../Components/Ui/PageHeader.vue';
import Icon from '../../Components/Ui/Icon.vue';

const props = defineProps({
    pipeline: { type: Object, required: true },
    connections: { type: Array, required: true },
    datasets: { type: Array, required: true },
    rows: { type: Number, default: 250 },
    mappingMode: { type: String, default: 'auto' },
});

const form = useForm({
    name: props.pipeline.name,
    connection_id: props.pipeline.connection_id,
    dataset_id: props.pipeline.dataset_id ?? '',
    schedule: props.pipeline.schedule,
    status: props.pipeline.status,
    rows: props.rows,
    mapping_mode: props.mappingMode,
});

function submit() {
    form.put(route('pipelines.update', props.pipeline.id));
}
</script>

<template>
    <AppShell>
        <PageHeader eyebrow="Data pipelines" title="Edit pipeline" :subtitle="pipeline.name">
            <template #actions>
                <Link :href="route('pipelines.show', pipeline.id)" class="sx-btn-secondary"><Icon name="x" :size="15" /> Cancel</Link>
            </template>
        </PageHeader>

        <form class="sx-card-pad max-w-3xl" @submit.prevent="submit">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="sx-label" for="name">Pipeline name</label>
                    <input id="name" v-model="form.name" type="text" class="sx-input" />
                    <p v-if="form.errors.name" class="mt-1 text-xs text-rose-600">{{ form.errors.name }}</p>
                </div>
                <div>
                    <label class="sx-label" for="connection">Source connection</label>
                    <select id="connection" v-model="form.connection_id" class="sx-input">
                        <option v-for="connection in connections" :key="connection.id" :value="connection.id">{{ connection.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="sx-label" for="dataset">Destination dataset</label>
                    <select id="dataset" v-model="form.dataset_id" class="sx-input">
                        <option value="">Auto-create</option>
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
                    <label class="sx-label" for="status">Status</label>
                    <select id="status" v-model="form.status" class="sx-input">
                        <option value="active">Active</option>
                        <option value="paused">Paused</option>
                    </select>
                </div>
                <div>
                    <label class="sx-label" for="rows">Rows per run</label>
                    <input id="rows" v-model.number="form.rows" type="number" min="1" max="2000" class="sx-input sx-num" />
                </div>
                <div>
                    <label class="sx-label" for="mapping">Field mapping</label>
                    <select id="mapping" v-model="form.mapping_mode" class="sx-input">
                        <option value="auto">Auto-detect</option>
                        <option value="custom">Custom mapping</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2 border-t pt-5 dark:border-slate-800">
                <Link :href="route('pipelines.show', pipeline.id)" class="sx-btn-secondary">Cancel</Link>
                <button type="submit" class="sx-btn-primary" :disabled="form.processing">
                    <Icon name="check" :size="16" /> Save changes
                </button>
            </div>
        </form>
    </AppShell>
</template>
