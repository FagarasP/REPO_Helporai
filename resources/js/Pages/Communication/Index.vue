<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    rooms: Array,
    activeRoomId: Number,
    messages: Array,
    canManageAllRooms: Boolean,
});

const form = useForm({
    message: '',
});

const sendMessage = () => {
    if (!props.activeRoomId) {
        return;
    }

    form.post(route('communication.messages.store', props.activeRoomId), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Communication Hub" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-6 space-y-6">
            <div class="bg-gradient-to-r from-slate-900 to-cyan-700 rounded-2xl p-6 text-white shadow-lg">
                <h1 class="text-2xl font-bold">Communication Hub</h1>
                <p class="opacity-90">Team-Chats wie in modernen Collaboration-Tools – für Admin und Company Teams.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-2xl shadow p-4">
                    <h2 class="font-semibold mb-3">Channels</h2>
                    <div class="space-y-2">
                        <Link
                            v-for="room in rooms"
                            :key="room.id"
                            :href="route('communication.index', { room_id: room.id })"
                            class="block px-3 py-2 rounded-lg border text-sm"
                            :class="room.id === activeRoomId ? 'bg-cyan-50 border-cyan-400 text-cyan-900' : 'hover:bg-gray-50 border-gray-200'"
                        >
                            <div class="font-medium">{{ room.name }}</div>
                            <div v-if="canManageAllRooms" class="text-xs opacity-70">{{ room.company?.name || 'No company' }}</div>
                        </Link>
                        <div v-if="rooms.length === 0" class="text-xs opacity-70">Noch keine Channels vorhanden.</div>
                    </div>
                </div>

                <div class="lg:col-span-3 bg-white dark:bg-gray-800 rounded-2xl shadow p-4 flex flex-col min-h-[450px]">
                    <div class="border-b pb-3 mb-3">
                        <h2 class="font-semibold text-lg">{{ rooms.find(r => r.id === activeRoomId)?.name || 'Kein Channel ausgewählt' }}</h2>
                    </div>

                    <div class="flex-1 overflow-y-auto space-y-3 pr-1">
                        <div v-for="message in messages" :key="message.id" class="rounded-xl border p-3">
                            <div class="text-sm font-semibold">{{ message.user?.name }} <span class="text-xs opacity-60">({{ message.user?.role }})</span></div>
                            <p class="text-sm mt-1 whitespace-pre-wrap">{{ message.message }}</p>
                            <div class="text-xs opacity-60 mt-1">{{ new Date(message.created_at).toLocaleString() }}</div>
                        </div>
                        <div v-if="messages.length === 0" class="text-sm opacity-60">Noch keine Nachrichten. Starte den Team-Chat 👋</div>
                    </div>

                    <form @submit.prevent="sendMessage" class="pt-4 mt-4 border-t flex gap-2">
                        <textarea v-model="form.message" rows="2" class="flex-1 rounded-lg border" placeholder="Nachricht schreiben..." required />
                        <button class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg disabled:opacity-50" :disabled="!activeRoomId || form.processing">
                            Senden
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
