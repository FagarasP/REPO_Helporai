<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    tickets: Array,
    isAdmin: Boolean,
});

const form = useForm({
    title: '',
    description: '',
    priority: 'medium',
    category: 'general',
});

const createTicket = () => {
    form.post(route('tickets.store'), {
        onSuccess: () => form.reset('title', 'description'),
    });
};

const changeStatus = (ticket, status) => {
    useForm({ status }).put(route('tickets.update', ticket.id));
};

const removeTicket = (ticket) => {
    if (confirm('Ticket wirklich löschen?')) {
        useForm({}).delete(route('tickets.destroy', ticket.id));
    }
};
</script>

<template>
    <Head title="Ticketsystem" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-6 space-y-6">
            <div class="bg-gradient-to-r from-indigo-600 to-cyan-600 rounded-2xl p-6 text-white shadow-lg">
                <h1 class="text-2xl font-bold">Modernes Ticketsystem</h1>
                <p class="opacity-90">Für alle Rollen: Probleme melden, Status verfolgen, schneller Support.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-2xl shadow p-5">
                    <h2 class="font-semibold text-lg mb-4">Neues Ticket</h2>
                    <form @submit.prevent="createTicket" class="space-y-3">
                        <input v-model="form.title" class="w-full rounded-lg border" placeholder="Titel" required>
                        <textarea v-model="form.description" class="w-full rounded-lg border" rows="4" placeholder="Beschreibung" required />
                        <select v-model="form.priority" class="w-full rounded-lg border">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                        <input v-model="form.category" class="w-full rounded-lg border" placeholder="Kategorie (z.B. billing)">
                        <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg">Ticket erstellen</button>
                    </form>
                </div>

                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow p-5">
                    <h2 class="font-semibold text-lg mb-4">Tickets</h2>
                    <div class="space-y-3">
                        <div v-for="ticket in tickets" :key="ticket.id" class="border rounded-xl p-4">
                            <div class="flex justify-between gap-3">
                                <div>
                                    <div class="font-semibold">{{ ticket.title }}</div>
                                    <div class="text-sm opacity-70">{{ ticket.description }}</div>
                                    <div class="text-xs mt-2">{{ ticket.category }} • {{ ticket.priority }}</div>
                                    <div v-if="isAdmin" class="text-xs mt-1">Von: {{ ticket.user?.name }} ({{ ticket.user?.role }})</div>
                                </div>
                                <div class="flex items-start gap-2">
                                    <select class="rounded-lg border text-sm" :value="ticket.status" @change="changeStatus(ticket, $event.target.value)">
                                        <option value="open">Open</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="resolved">Resolved</option>
                                        <option value="closed">Closed</option>
                                    </select>
                                    <button class="text-red-500 text-sm" @click="removeTicket(ticket)">Löschen</button>
                                </div>
                            </div>
                        </div>
                        <div v-if="tickets.length === 0" class="text-sm opacity-60">Noch keine Tickets vorhanden.</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
