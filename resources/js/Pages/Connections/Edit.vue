<script setup>
import { computed, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';
import PageHeader from '../../Components/Ui/PageHeader.vue';
import Icon from '../../Components/Ui/Icon.vue';

const props = defineProps({
    connection: { type: Object, required: true },
    catalog: { type: Array, required: true },
});

const form = useForm({
    name: props.connection.name,
    type: props.connection.type,
    status: props.connection.status,
    config: { ...(props.connection.config ?? {}) },
});

const definition = computed(() => props.catalog.find((item) => item.type === form.type));

watch(
    () => form.type,
    (type) => {
        const selected = props.catalog.find((item) => item.type === type);

        for (const field of selected?.fields ?? []) {
            if (form.config[field.key] === undefined) {
                form.config[field.key] = '';
            }
        }
    }
);

function submit() {
    form.put(route('connections.update', props.connection.id));
}
</script>

<template>
    <AppShell>
        <PageHeader eyebrow="Connectivity" title="Edit connection" :subtitle="connection.name">
            <template #actions>
                <Link :href="route('connections.index')" class="sx-btn-secondary"><Icon name="x" :size="15" /> Cancel</Link>
            </template>
        </PageHeader>

        <form class="sx-card-pad max-w-3xl" @submit.prevent="submit">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="sx-label" for="name">Display name</label>
                    <input id="name" v-model="form.name" type="text" class="sx-input" />
                    <p v-if="form.errors.name" class="mt-1 text-xs text-rose-600">{{ form.errors.name }}</p>
                </div>
                <div>
                    <label class="sx-label" for="type">Connector type</label>
                    <select id="type" v-model="form.type" class="sx-input">
                        <option v-for="item in catalog" :key="item.type" :value="item.type">{{ item.label }}</option>
                    </select>
                </div>
                <div>
                    <label class="sx-label" for="status">Status</label>
                    <select id="status" v-model="form.status" class="sx-input">
                        <option value="active">Active</option>
                        <option value="paused">Paused</option>
                    </select>
                </div>
            </div>

            <div v-if="definition" class="mt-6 grid gap-4 border-t pt-5 sm:grid-cols-2 dark:border-slate-800">
                <div v-for="field in definition.fields" :key="field.key">
                    <label class="sx-label" :for="`field-${field.key}`">{{ field.label }}</label>
                    <input :id="`field-${field.key}`" v-model="form.config[field.key]" :type="field.type" class="sx-input" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2 border-t pt-5 dark:border-slate-800">
                <Link :href="route('connections.index')" class="sx-btn-secondary">Cancel</Link>
                <button type="submit" class="sx-btn-primary" :disabled="form.processing">
                    <Icon name="check" :size="16" /> Save changes
                </button>
            </div>
        </form>
    </AppShell>
</template>
