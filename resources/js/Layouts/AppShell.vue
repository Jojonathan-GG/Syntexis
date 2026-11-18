<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Icon from '../Components/Ui/Icon.vue';
import Flash from '../Components/Ui/Flash.vue';

const page = usePage();
const navigation = computed(() => page.props.navigation ?? []);
const organization = computed(() => page.props.auth?.organization);
const user = computed(() => page.props.auth?.user);
const openAlertCount = computed(() => page.props.openAlertCount ?? 0);

const sidebarOpen = ref(false);
const paletteOpen = ref(false);
const userMenuOpen = ref(false);
const searchQuery = ref('');
const isDark = ref(false);

const currentPath = computed(() => page.url.split('?')[0]);

function hrefFor(item) {
    const definition = page.props.ziggy?.routes?.[item.route];

    if (!definition) {
        return '/';
    }

    return definition.uri === '/' ? '/' : `/${definition.uri.replace(/^\/+/, '')}`;
}

function isActive(item) {
    const href = hrefFor(item);

    if (href === '/') {
        return currentPath.value === '/';
    }

    return currentPath.value === href || currentPath.value.startsWith(`${href}/`);
}

const currentNav = computed(
    () => navigation.value.find((item) => isActive(item)) ?? { label: 'Overview', icon: 'grid' }
);

const paletteItems = computed(() => {
    const items = [
        ...navigation.value.map((item) => ({ ...item, hint: 'Go to' })),
        { label: 'New Connection', route: 'connections.create', icon: 'plus', hint: 'Create' },
        { label: 'New Pipeline', route: 'pipelines.create', icon: 'plus', hint: 'Create' },
    ];

    const query = searchQuery.value.trim().toLowerCase();

    return query ? items.filter((item) => item.label.toLowerCase().includes(query)) : items;
});

function openPalette() {
    paletteOpen.value = true;
    searchQuery.value = '';
}

function selectPaletteItem(item) {
    paletteOpen.value = false;
    router.visit(hrefFor(item));
}

function toggleTheme() {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle('dark', isDark.value);
    localStorage.setItem('syntexis-theme', isDark.value ? 'dark' : 'light');
}

function handleKeydown(event) {
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
        event.preventDefault();
        openPalette();
    }

    if (event.key === 'Escape') {
        paletteOpen.value = false;
        userMenuOpen.value = false;
    }
}

onMounted(() => {
    isDark.value = localStorage.getItem('syntexis-theme') === 'dark';
    document.documentElement.classList.toggle('dark', isDark.value);
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <div class="min-h-screen">
        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r bg-white transition-transform duration-200 dark:bg-slate-900 dark:border-slate-800 lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex h-16 items-center gap-2.5 border-b px-5 dark:border-slate-800">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-lg shadow-indigo-500/25">
                    <Icon name="layers" :size="18" />
                </div>
                <div class="leading-tight">
                    <p class="text-sm font-extrabold tracking-tight text-slate-900 dark:text-white">Syntexis</p>
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">Unified EAI</p>
                </div>
            </div>

            <div class="px-3 py-4">
                <div class="flex items-center gap-2.5 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 dark:border-slate-800 dark:bg-slate-950">
                    <div class="flex h-7 w-7 items-center justify-center rounded-md bg-indigo-100 text-[11px] font-bold text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">
                        {{ (organization?.name ?? 'S').slice(0, 1).toUpperCase() }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-semibold text-slate-700 dark:text-slate-200">
                            {{ organization?.name ?? 'Workspace' }}
                        </p>
                        <p class="text-[10px] text-slate-400">{{ organization?.plan ?? 'starter' }} plan</p>
                    </div>
                    <Icon name="chevron-down" :size="14" class="text-slate-400" />
                </div>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3">
                <p class="px-3 pb-2 pt-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Workspace</p>
                <Link
                    v-for="item in navigation"
                    :key="item.route"
                    :href="hrefFor(item)"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition"
                    :class="isActive(item)
                        ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300'
                        : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white'"
                >
                    <Icon :name="item.icon" :size="17" />
                    <span class="flex-1">{{ item.label }}</span>
                    <span
                        v-if="item.route === 'insights.index' && openAlertCount"
                        class="rounded-full bg-rose-500 px-1.5 py-0.5 text-[10px] font-bold text-white"
                    >
                        {{ openAlertCount }}
                    </span>
                </Link>
            </nav>

            <div class="border-t p-3 dark:border-slate-800">
                <Link
                    :href="hrefFor({ route: 'profile.edit' })"
                    class="flex items-center gap-3 rounded-lg px-2 py-2 transition hover:bg-slate-100 dark:hover:bg-slate-800"
                >
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold text-white"
                        :style="{ background: user?.avatar_color ?? '#6366f1' }"
                    >
                        {{ user?.name?.slice(0, 1).toUpperCase() ?? 'U' }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-semibold text-slate-800 dark:text-slate-200">{{ user?.name }}</p>
                        <p class="truncate text-[10px] capitalize text-slate-400">{{ user?.role }}</p>
                    </div>
                    <Icon name="settings" :size="15" class="text-slate-400" />
                </Link>
            </div>
        </aside>

        <div v-if="sidebarOpen" class="fixed inset-0 z-30 bg-slate-900/40 lg:hidden" @click="sidebarOpen = false" />

        <!-- Main -->
        <div class="lg:pl-64">
            <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b bg-white/90 px-4 backdrop-blur dark:bg-slate-900/90 dark:border-slate-800 sm:px-6">
                <button
                    type="button"
                    class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden"
                    aria-label="Toggle navigation"
                    @click="sidebarOpen = !sidebarOpen"
                >
                    <Icon name="menu" :size="20" />
                </button>

                <div class="hidden items-center gap-2 text-sm sm:flex">
                    <span class="text-slate-400">{{ organization?.name ?? 'Workspace' }}</span>
                    <Icon name="chevron-right" :size="14" class="text-slate-300" />
                    <span class="font-semibold text-slate-700 dark:text-slate-200">{{ currentNav.label }}</span>
                </div>

                <div class="flex-1" />

                <button
                    type="button"
                    class="hidden items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400 transition hover:border-indigo-300 hover:text-slate-600 dark:border-slate-700 dark:bg-slate-950 md:flex"
                    @click="openPalette"
                >
                    <Icon name="search" :size="15" />
                    <span>Search or jump to...</span>
                    <kbd class="ml-6 rounded border border-slate-300 px-1.5 py-0.5 text-[10px] dark:border-slate-600">⌘K</kbd>
                </button>

                <button
                    type="button"
                    class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 dark:hover:bg-slate-800"
                    :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                    @click="toggleTheme"
                >
                    <Icon :name="isDark ? 'sun' : 'moon'" :size="18" />
                </button>

                <Link
                    :href="hrefFor({ route: 'insights.index' })"
                    class="relative rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 dark:hover:bg-slate-800"
                    aria-label="Alerts"
                >
                    <Icon name="bell" :size="18" />
                    <span
                        v-if="openAlertCount"
                        class="absolute right-1 top-1 h-2 w-2 rounded-full bg-rose-500 ring-2 ring-white dark:ring-slate-900"
                    />
                </Link>

                <div class="relative">
                    <button
                        type="button"
                        class="flex items-center gap-2 rounded-lg p-1.5 transition hover:bg-slate-100 dark:hover:bg-slate-800"
                        @click="userMenuOpen = !userMenuOpen"
                    >
                        <div
                            class="flex h-7 w-7 items-center justify-center rounded-full text-[11px] font-bold text-white"
                            :style="{ background: user?.avatar_color ?? '#6366f1' }"
                        >
                            {{ user?.name?.slice(0, 1).toUpperCase() ?? 'U' }}
                        </div>
                        <Icon name="chevron-down" :size="14" class="hidden text-slate-400 sm:block" />
                    </button>
                    <div
                        v-if="userMenuOpen"
                        class="absolute right-0 top-11 w-48 rounded-xl border bg-white py-1 shadow-xl dark:border-slate-700 dark:bg-slate-900"
                    >
                        <Link
                            :href="hrefFor({ route: 'profile.edit' })"
                            class="flex items-center gap-2 px-3 py-2 text-sm text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800"
                            @click="userMenuOpen = false"
                        >
                            <Icon name="user" :size="15" /> Profile settings
                        </Link>
                        <Link
                            :href="hrefFor({ route: 'logout' })"
                            method="post"
                            class="flex items-center gap-2 px-3 py-2 text-sm text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10"
                            @click="userMenuOpen = false"
                        >
                            <Icon name="logout" :size="15" /> Sign out
                        </Link>
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-[1600px] p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>

        <!-- Command palette -->
        <div
            v-if="paletteOpen"
            class="fixed inset-0 z-50 flex items-start justify-center bg-slate-950/50 px-4 pt-[12vh] backdrop-blur-sm"
            @click.self="paletteOpen = false"
        >
            <div class="w-full max-w-lg overflow-hidden rounded-2xl border bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
                <div class="flex items-center gap-3 border-b px-4 dark:border-slate-800">
                    <Icon name="search" :size="18" class="text-slate-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        autofocus
                        placeholder="Search pages and actions..."
                        class="w-full bg-transparent py-4 text-sm outline-none placeholder:text-slate-400 dark:text-white"
                        @keydown.enter="paletteItems[0] && selectPaletteItem(paletteItems[0])"
                    />
                    <kbd class="rounded border border-slate-300 px-1.5 py-0.5 text-[10px] text-slate-400 dark:border-slate-600">ESC</kbd>
                </div>
                <div class="max-h-80 overflow-y-auto p-2">
                    <button
                        v-for="item in paletteItems"
                        :key="item.route"
                        type="button"
                        class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-slate-700 transition hover:bg-indigo-50 dark:text-slate-200 dark:hover:bg-slate-800"
                        @click="selectPaletteItem(item)"
                    >
                        <Icon :name="item.icon" :size="16" class="text-slate-400" />
                        <span class="flex-1 font-medium">{{ item.label }}</span>
                        <span class="text-[11px] uppercase tracking-wide text-slate-400">{{ item.hint }}</span>
                    </button>
                    <p v-if="!paletteItems.length" class="px-3 py-8 text-center text-sm text-slate-400">
                        No matches found.
                    </p>
                </div>
            </div>
        </div>

        <Flash />
    </div>
</template>
