<template>
  <label :for="id" class="flex items-center cursor-pointer">
    <!-- Toggle -->
    <div class="relative">
      <!-- Input -->
      <input
        :id="id"
        type="checkbox"
        class="sr-only"
        :checked="modelValue"
        @change="handleChange"
      />
      <!-- Line -->
      <div
        :class="[
          'block w-10 h-6 rounded-full transition-colors duration-300 ease-in-out',
          modelValue ? 'bg-blue-600' : 'bg-gray-300',
        ]"
      ></div>
      <!-- Dot -->
      <div
        :class="[
          'dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-300 ease-in-out',
          modelValue ? 'translate-x-full' : 'translate-x-0',
        ]"
      ></div>
    </div>
    <!-- Label text (optional) -->
    <div v-if="label" class="ml-3 text-gray-700 dark:text-text-200 font-medium">
      {{ label }}
    </div>
  </label>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  id: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['update:modelValue']);

const handleChange = (event) => {
  emit('update:modelValue', event.target.checked);
};
</script>

<style scoped>
/* No additional styles needed beyond Tailwind CSS */
</style>
