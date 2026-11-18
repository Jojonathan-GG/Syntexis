<script setup>
import Icon from '../Components/Ui/Icon.vue';
</script>

<template>
    <div class="flex min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-slate-950 p-12 text-white lg:flex">
            <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-indigo-600/20 blur-3xl" />
            <div class="absolute -bottom-32 -left-20 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl" />

            <div class="relative flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 shadow-lg shadow-indigo-500/30">
                    <Icon name="layers" :size="20" />
                </span>
                <div>
                    <p class="text-lg font-extrabold tracking-tight">Syntexis</p>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-indigo-300">Unified EAI</p>
                </div>
            </div>

            <div class="relative max-w-md">
                <h2 class="text-4xl font-bold leading-tight tracking-tight">
                    Fuse every system.<br />
                    Surface every insight.
                </h2>
                <p class="mt-5 text-sm leading-relaxed text-slate-400">
                    Connect your ERP, CRM, databases and SaaS tools. Normalize complex data into a canonical model and turn it into decisions — from one workspace.
                </p>
                <div class="mt-8 grid grid-cols-3 gap-4 border-t border-white/10 pt-6">
                    <div>
                        <p class="text-2xl font-bold">8+</p>
                        <p class="text-[11px] text-slate-400">Connectors</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold">Real-time</p>
                        <p class="text-[11px] text-slate-400">Metrics</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold">RBAC</p>
                        <p class="text-[11px] text-slate-400">By design</p>
                    </div>
                </div>
            </div>

            <p class="relative text-xs text-slate-600">© {{ new Date().getFullYear() }} Syntexis. Enterprise integration intelligence.</p>
        </div>

        <div class="flex flex-1 items-center justify-center px-6 py-10">
            <div class="w-full max-w-md">
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-white">
                        <Icon name="layers" :size="18" />
                    </span>
                    <p class="text-lg font-extrabold text-slate-900 dark:text-white">Syntexis</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-xl shadow-slate-200/50 dark:border-slate-800 dark:bg-slate-900 dark:shadow-none">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
