<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    quickLinks: Array,
});

const statCards = [
    { key: 'users', label: 'Users', color: 'from-indigo-500 to-indigo-700' },
    { key: 'projects', label: 'Projects', color: 'from-cyan-500 to-cyan-700' },
    { key: 'openTickets', label: 'Open Tickets', color: 'from-amber-500 to-amber-700' },
    { key: 'chatRooms', label: 'Chat Rooms', color: 'from-emerald-500 to-emerald-700' },
];
</script>

<template>
    <Head title="Admin Panel" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-6 space-y-6">
            <div class="bg-gradient-to-r from-slate-900 to-indigo-700 rounded-2xl p-6 text-white shadow-lg">
                <h1 class="text-2xl font-bold">Admin Panel</h1>
                <p class="opacity-90">Dein zentraler Bereich für User, Projekte, Tickets und Kommunikation.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div
                    v-for="card in statCards"
                    :key="card.key"
                    class="rounded-xl p-4 text-white shadow bg-gradient-to-r"
                    :class="card.color"
                >
                    <p class="text-sm opacity-90">{{ card.label }}</p>
                    <p class="text-3xl font-bold mt-2">{{ stats?.[card.key] ?? 0 }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-5">
                <h2 class="text-lg font-semibold mb-4">Schnellzugriff</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <Link
                        v-for="item in quickLinks"
                        :key="item.route"
                        :href="route(item.route)"
                        class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-gray-700 transition"
                    >
                        <div class="font-semibold">{{ item.label }}</div>
                        <p class="text-sm opacity-75 mt-1">{{ item.description }}</p>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
