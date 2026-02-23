<script setup>
import { ref } from 'vue';

const props = defineProps({
    conversationId: Number,
    peerUserId: Number,
    turn: Object,
});

const active = ref(false);
const muted = ref(false);
const cameraEnabled = ref(true);
const hasMediaError = ref(false);

const startCall = async (type = 'video') => {
    active.value = true;
    try {
        await navigator.mediaDevices.getUserMedia({
            audio: true,
            video: type === 'video',
        });

        await fetch(route('calls.offer', props.conversationId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({
                to_user_id: props.peerUserId,
                call_type: type,
                sdp: { placeholder: true, type },
            }),
        });
    } catch (error) {
        hasMediaError.value = true;
    }
};

const hangup = async () => {
    active.value = false;
    await fetch(route('calls.hangup', props.conversationId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ log_id: 1 }),
    });
};
</script>

<template>
    <div class="flex items-center gap-2">
        <button class="px-3 py-2 rounded-lg bg-emerald-600 text-white" @click="startCall('audio')">Audio Call</button>
        <button class="px-3 py-2 rounded-lg bg-indigo-600 text-white" @click="startCall('video')">Video Call</button>

        <div v-if="active" class="fixed right-6 bottom-6 w-80 bg-white border rounded-xl shadow-xl p-4 z-50">
            <div class="font-semibold mb-2">Call aktiv</div>
            <div class="flex gap-2 mb-3">
                <button class="px-2 py-1 text-sm rounded border" @click="muted = !muted">{{ muted ? 'Unmute' : 'Mute' }}</button>
                <button class="px-2 py-1 text-sm rounded border" @click="cameraEnabled = !cameraEnabled">{{ cameraEnabled ? 'Camera Off' : 'Camera On' }}</button>
            </div>
            <div v-if="hasMediaError" class="text-xs text-red-600 mb-2">Kein Zugriff auf Kamera/Mikrofon. Bitte Berechtigungen prüfen.</div>
            <button class="w-full px-3 py-2 rounded bg-red-600 text-white" @click="hangup">Hangup</button>
        </div>
    </div>
</template>
