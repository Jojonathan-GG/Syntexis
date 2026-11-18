<script setup>
import { computed, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';
import PageHeader from '../../Components/Ui/PageHeader.vue';
import Icon from '../../Components/Ui/Icon.vue';

const props = defineProps({
    catalog: { type: Array, required: true },
});

const form = useForm({
    name: '',
    type: 'rest_api',
    status: 'active',
    config: {},
});

const definition = computed(() => props.catalog.find((item) => item.type === form.type));

watch(
    () => form.type,
    (type) => {
        const selected = props.catalog.find((item) => item.type === type);
        form.config = Object.fromEntries((selected?.fields ?? []).map((field) => [field.key, '']));
    },
    { immediate: true }
);

function submit() {
    form.post(route('connections.store'));
}
</script>

<template>
    <AppShell>
        <PageHeader
            eyebrow="Connectivity"
            title="New connection"
            subtitle="Register an external system that pipelines can read from."
        >
            <template #actions>
                <Link :href="route('connections.index')" class="sx-btn-secondary">
                    <Icon name="x" :size="15" /> Cancel
                </Link>
            </template>
        </PageHeader>

        <form class="grid grid-cols-1 gap-5 lg:grid-cols-3" @submit.prevent="submit">
            <div class="sx-card-pad lg:col-span-2">
                <h2 class="mb-5 text-sm font-bold text-slate-800 dark:text-white">Connection details</h2>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="sx-label" for="name">Display name</label>
                        <input id="name" v-model="form.name" type="text" class="sx-input" placeholder="e.g. ERP Core (PostgreSQL)" />
                        <p v-if="form.errors.name" class="mt-1 text-xs text-rose-600">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="sx-label" for="type">Connector type</label>
                        <select id="type" v-model="form.type" class="sx-input">
                            <optgroup v-for="(items, category) in catalog.reduce((groups, item) => {
                                groups[item.category] = groups[item.category] || [];
                                groups[item.category].push(item);
                                return groups;
                            }, {})" :key="category" :label="category">
                                <option v-for="item in items" :key="item.type" :value="item.type">{{ item.label }}</option>
                            </optgroup>
                        </select>
                        <p v-if="form.errors.type" class="mt-1 text-xs text-rose-600">{{ form.errors.type }}</p>
                    </div>

                    <div>
                        <label class="sx-label" for="status">Status</label>
                        <select id="status" v-model="form.status" class="sx-input">
                            <option value="active">Active</option>
                            <option value="paused">Paused</option>
                        </select>
                    </div>
                </div>

                <div v-if="definition" class="mt-6 border-t pt-5 dark:border-slate-800">
                    <div class="mb-4 flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10">
                            <Icon :name="definition.icon" :size="17" />
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 dark:text-white">{{ definition.label }}</h3>
                            <p class="text-xs text-slate-400">{{ definition.description }}</p>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div v-for="field in definition.fields" :key="field.key">
                            <label class="sx-label" :for="`field-${field.key}`">{{ field.label }}</label>
                            <input
                                :id="`field-${field.key}`"
                                v-model="form.config[field.key]"
                                :type="field.type"
                                :placeholder="field.placeholder"
                                class="sx-input"
                            />
                            <p v-if="form.errors[`config.${field.key}`]" class="mt-1 text-xs text-rose-600">
                                {{ form.errors[`config.${field.key}`] }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2 border-t pt-5 dark:border-slate-800">
                    <Link :href="route('connections.index')" class="sx-btn-secondary">Cancel</Link>
                    <button type="submit" class="sx-btn-primary" :disabled="form.processing">
                        <Icon name="check" :size="16" /> {{ form.processing ? 'Saving...' : 'Create connection' }}
                    </button>
                </div>
            </div>

            <aside class="sx-card-pad h-fit">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white">How it works</h3>
                <ol class="mt-4 space-y-4 text-xs text-slate-500 dark:text-slate-400">
                    <li class="flex gap-3">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-[10px] font-bold text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">1</span>
                        Register credentials or endpoints here. Secrets are stored encrypted.
                    </li>
                    <li class="flex gap-3">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-[10px] font-bold text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">2</span>
                        Build a pipeline that reads from this source.
                    </li>
                    <li class="flex gap-3">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-[10px] font-bold text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">3</span>
                        Data lands in a canonical dataset and powers your insights.
                    </li>
                </ol>
            </aside>
        </form>
    </AppShell>
</template>
