<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CallOverlay from '@/Components/Communication/CallOverlay.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    conversation: Object,
    messages: Array,
    turn: Object,
    maxAttachmentMb: Number,
});

const list = ref(props.messages || []);
const form = useForm({
    body: '',
    attachment: null,
});

const page = usePage();
const authUserId = page.props?.auth?.user?.id;
const peerUser = computed(() => props.conversation?.participants?.find((item) => item.user_id !== authUserId)?.user || props.conversation?.participants?.[0]?.user);

const send = () => {
    form.post(route('messages.sendMessage', props.conversation.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => form.reset('body', 'attachment'),
    });
};

const markRead = () => {
    useForm({}).post(route('messages.markRead', props.conversation.id));
};

onMounted(() => {
    markRead();
});
</script>

<template>
    <Head :title="`Chat - ${conversation.title}`" />
    <AuthenticatedLayout>
        <div class="max-w-6xl mx-auto p-6 space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-bold">{{ conversation.title }}</h1>
                    <p class="text-xs opacity-70">Read receipts + Attachments + Call-Signaling</p>
                </div>
                <CallOverlay :conversation-id="conversation.id" :peer-user-id="peerUser?.id" :turn="turn" />
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 space-y-3 min-h-[360px]">
                <div v-for="message in list" :key="message.id" class="border rounded-lg p-3">
                    <div class="text-sm font-semibold">{{ message.sender?.name }}</div>
                    <p class="text-sm mt-1 whitespace-pre-wrap">{{ message.body }}</p>
                    <div class="text-xs opacity-60 mt-1">{{ new Date(message.created_at).toLocaleString() }}</div>
                </div>
                <div v-if="list.length === 0" class="text-sm opacity-60">Noch keine Nachrichten.</div>
            </div>

            <form class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 space-y-2" @submit.prevent="send">
                <textarea v-model="form.body" class="w-full rounded-lg border" rows="3" placeholder="Nachricht schreiben..." />
                <input type="file" @input="form.attachment = $event.target.files[0]">
                <div class="text-xs opacity-70">Max Attachment: {{ maxAttachmentMb }} MB</div>
                <button class="px-4 py-2 rounded bg-indigo-600 text-white">Senden</button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
