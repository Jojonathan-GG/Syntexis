<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Icon from '@/Components/Ui/Icon.vue';

defineProps({
    canResetPassword: { type: Boolean, default: false },
    status: { type: String, default: '' },
});

const form = useForm({
    email: 'admin@syntexis.test',
    password: 'password',
    remember: false,
});

function submit() {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <p class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-500">Welcome back</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Sign in to Syntexis</h1>
        <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">Use your workspace account to continue.</p>

        <div v-if="status" class="mt-4 rounded-lg bg-emerald-50 px-3 py-2 text-sm text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
            {{ status }}
        </div>

        <form class="mt-7 space-y-4" @submit.prevent="submit">
            <div>
                <label class="sx-label" for="email">Work email</label>
                <input id="email" v-model="form.email" type="email" class="sx-input" required autofocus autocomplete="username" />
                <p v-if="form.errors.email" class="mt-1 text-xs text-rose-600">{{ form.errors.email }}</p>
            </div>

            <div>
                <label class="sx-label" for="password">Password</label>
                <input id="password" v-model="form.password" type="password" class="sx-input" required autocomplete="current-password" />
                <p v-if="form.errors.password" class="mt-1 text-xs text-rose-600">{{ form.errors.password }}</p>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <input v-model="form.remember" type="checkbox" class="rounded border-slate-300 text-indigo-600" />
                    Remember me
                </label>
                <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                    Forgot password?
                </Link>
            </div>

            <button type="submit" class="sx-btn-primary w-full py-2.5" :disabled="form.processing">
                <Icon name="logout" :size="16" class="rotate-180" />
                {{ form.processing ? 'Signing in...' : 'Sign in' }}
            </button>
        </form>

        <div class="mt-6 rounded-lg border border-indigo-100 bg-indigo-50/60 p-3 text-xs text-indigo-800 dark:border-indigo-900/50 dark:bg-indigo-500/10 dark:text-indigo-300">
            <p class="font-semibold">Demo workspace</p>
            <p class="mt-1">admin@syntexis.test / password</p>
            <p class="mt-0.5">analyst@syntexis.test / password</p>
        </div>

        <p class="mt-6 text-center text-xs text-slate-500 dark:text-slate-400">
            New to Syntexis?
            <Link :href="route('register')" class="font-semibold text-indigo-600">Create an account</Link>
        </p>
    </GuestLayout>
</template>
