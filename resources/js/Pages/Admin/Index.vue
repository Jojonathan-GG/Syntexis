<script setup>
import AppShell from '../../Layouts/AppShell.vue';
import PageHeader from '../../Components/Ui/PageHeader.vue';
import Badge from '../../Components/Ui/Badge.vue';
import Icon from '../../Components/Ui/Icon.vue';

defineProps({
    organization: { type: Object, default: null },
    users: { type: Array, default: () => [] },
    auditLogs: { type: Array, default: () => [] },
});

function formatDate(value) {
    return value ? new Date(value).toLocaleString() : '—';
}

function actionTone(action) {
    if (['created', 'ran', 'tested'].includes(action)) {
        return 'success';
    }
    if (['deleted'].includes(action)) {
        return 'danger';
    }

    return 'info';
}
</script>

<template>
    <AppShell>
        <PageHeader
            eyebrow="Administration"
            title="Admin console"
            subtitle="Workspace, team access and the immutable activity trail."
        />

        <div class="mb-5 grid grid-cols-1 gap-5 lg:grid-cols-3">
            <div class="sx-card-pad lg:col-span-2">
                <div class="flex items-start gap-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-lg font-bold text-white">
                        {{ organization?.name?.slice(0, 1) ?? 'S' }}
                    </span>
                    <div class="flex-1">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">{{ organization?.name ?? 'Workspace' }}</h2>
                        <p class="text-xs text-slate-400">slug: {{ organization?.slug ?? '—' }}</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <Badge :label="`${organization?.plan ?? 'starter'} plan`" tone="brand" />
                            <Badge label="SSO ready" tone="info" />
                            <Badge label="Audit enabled" tone="success" />
                        </div>
                    </div>
                </div>
                <dl class="mt-6 grid grid-cols-2 gap-4 border-t pt-5 text-xs dark:border-slate-800 sm:grid-cols-4">
                    <div><dt class="sx-label">Members</dt><dd class="sx-num font-bold text-slate-800 dark:text-slate-200">{{ users.length }}</dd></div>
                    <div><dt class="sx-label">Admins</dt><dd class="sx-num font-bold text-slate-800 dark:text-slate-200">{{ users.filter((u) => u.role === 'admin').length }}</dd></div>
                    <div><dt class="sx-label">Timezone</dt><dd class="font-semibold text-slate-800 dark:text-slate-200">{{ organization?.settings?.timezone ?? 'UTC' }}</dd></div>
                    <div><dt class="sx-label">Currency</dt><dd class="font-semibold text-slate-800 dark:text-slate-200">{{ organization?.settings?.currency ?? 'USD' }}</dd></div>
                </dl>
            </div>

            <div class="sx-card-pad">
                <h2 class="mb-4 text-sm font-bold text-slate-800 dark:text-white">Access policy</h2>
                <ul class="space-y-3 text-xs text-slate-500 dark:text-slate-400">
                    <li class="flex gap-2"><Icon name="check" :size="14" class="mt-0.5 text-emerald-500" /> Every tenant-scoped query is isolated.</li>
                    <li class="flex gap-2"><Icon name="check" :size="14" class="mt-0.5 text-emerald-500" /> Credentials are encrypted at rest.</li>
                    <li class="flex gap-2"><Icon name="check" :size="14" class="mt-0.5 text-emerald-500" /> Destructive actions are recorded.</li>
                    <li class="flex gap-2"><Icon name="check" :size="14" class="mt-0.5 text-emerald-500" /> Two-factor authentication available.</li>
                </ul>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div class="sx-card overflow-hidden">
                <div class="border-b px-5 py-4 dark:border-slate-800">
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Team members</h2>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    <div v-for="member in users" :key="member.id" class="flex items-center gap-3 px-5 py-3.5">
                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold text-white"
                            :style="{ background: member.avatar_color ?? '#6366f1' }"
                        >
                            {{ member.name?.slice(0, 1).toUpperCase() }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-800 dark:text-slate-200">{{ member.name }}</p>
                            <p class="truncate text-[11px] text-slate-400">{{ member.email }}</p>
                        </div>
                        <Badge :label="member.role" :tone="member.role === 'admin' ? 'brand' : 'neutral'" />
                    </div>
                </div>
            </div>

            <div class="sx-card overflow-hidden">
                <div class="border-b px-5 py-4 dark:border-slate-800">
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Audit trail</h2>
                    <p class="mt-0.5 text-xs text-slate-400">Most recent workspace events</p>
                </div>
                <div class="max-h-[520px] divide-y divide-slate-100 overflow-y-auto dark:divide-slate-800">
                    <div v-for="log in auditLogs" :key="log.id" class="flex items-start gap-3 px-5 py-3.5">
                        <span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-400" />
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs text-slate-700 dark:text-slate-300">{{ log.description }}</p>
                            <p class="mt-0.5 text-[10px] text-slate-400">{{ log.user?.name ?? 'System' }} · {{ formatDate(log.created_at) }}</p>
                        </div>
                        <Badge :label="log.action" :tone="actionTone(log.action)" />
                    </div>
                    <p v-if="!auditLogs.length" class="px-5 py-12 text-center text-sm text-slate-400">No activity recorded.</p>
                </div>
            </div>
        </div>
    </AppShell>
</template>
