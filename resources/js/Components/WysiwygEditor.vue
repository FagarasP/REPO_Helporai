<template>
    <div>
        <input type="hidden" :id="id" :name="id" :value="modelValue" />
        <trix-editor :input="id" @trix-change="updateContent" @trix-file-accept="preventFileAttachment"
            class="trix-content bg-white dark:bg-background-700 dark:text-text-100"></trix-editor>
    </div>
</template>

<script setup>
defineProps({
    modelValue: String,
    id: String,
})
const emit = defineEmits(['update:modelValue'])

const updateContent = (event) => {
    emit('update:modelValue', event.target.innerHTML)
}

const preventFileAttachment = (event) => {
    event.preventDefault() // Prevent the default file attachment behavior
}
</script>
<style scoped>
/* Default Trix styles cleanup for dark mode */
/* .trix-content {
    background-color: #374151;
    color: #f3f4f6;
    border: 1px solid #4b5563;
    min-height: 8rem;
    padding: 1rem;
} */
span.trix-button-group--file-tools {
    display: none !important;
}

/* Toolbar button background */
.trix-button {
    background-color: #1f2937;
    /* gray-800 */
    color: #d1d5db;
    /* gray-300 */
    border-color: #4b5563;
}

.trix-button:hover,
.trix-button--icon:hover {
    background-color: #374151;
    /* darker gray */
}

/* Caret color and focused border */
.trix-content:focus {
    outline: none;
    border-color: #3b82f6;
    /* Tailwind blue-500 */
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

/* Placeholder color override */
.trix-content:empty:before {
    color: #9ca3af;
    /* gray-400 */
}
</style>