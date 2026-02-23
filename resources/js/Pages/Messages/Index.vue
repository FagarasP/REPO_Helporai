<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    conversations: Array,
});

const search = ref('');
const contacts = ref([]);

const form = useForm({ user_id: null });

const searchContacts = async () => {
    const response = await fetch(route('messages.contacts', { q: search.value }));
    const data = await response.json();
    contacts.value = data.data || [];
};

const startConversation = (id) => {
    form.user_id = id;
    form.post(route('messages.createConversation'));
};
</script>

<template>
    <Head title="Messages" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-6 space-y-4">
            <div class="bg-gradient-to-r from-slate-900 to-blue-700 rounded-2xl p-6 text-white shadow-lg">
                <h1 class="text-2xl font-bold">Messages</h1>
                <p class="opacity-90">1:1 Chat zwischen Company und Freelancern (MVP).</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <h2 class="font-semibold mb-3">Neuen Chat starten</h2>
                    <div class="flex gap-2 mb-3">
                        <input v-model="search" class="flex-1 rounded-lg border" placeholder="Nutzer suchen..." @keyup.enter="searchContacts">
                        <button class="px-3 py-2 rounded bg-indigo-600 text-white" @click="searchContacts">Suchen</button>
                    </div>

                    <div class="space-y-2 max-h-80 overflow-y-auto">
                        <button
                            v-for="contact in contacts"
                            :key="contact.id"
                            class="w-full text-left border rounded-lg p-3 hover:bg-gray-50"
                            @click="startConversation(contact.id)"
                        >
                            <div class="font-semibold">{{ contact.name }}</div>
                            <div class="text-xs opacity-70">{{ contact.role }}</div>
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <h2 class="font-semibold mb-3">Chats</h2>
                    <div class="space-y-2">
                        <Link
                            v-for="conversation in conversations"
                            :key="conversation.id"
                            :href="route('messages.show', conversation.id)"
                            class="block border rounded-lg p-3 hover:bg-gray-50"
                        >
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="font-semibold">{{ conversation.title }}</div>
                                    <div class="text-sm opacity-75">{{ conversation.last_message || 'Noch keine Nachricht' }}</div>
                                </div>
                                <span v-if="conversation.unread_count" class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ conversation.unread_count }}</span>
                            </div>
                        </Link>
                        <div v-if="conversations.length === 0" class="text-sm opacity-60">Noch keine Konversation vorhanden.</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
