<template>
  <div class="p-6 space-y-2">
    <h1 class="text-2xl font-semibold">{{ titleKey }}</h1>
    <p class="text-sm text-gray-600">{{ descriptionKey }}</p>
  </div>
</template>

<script setup>
defineProps({
  portal: { type: String, required: true },
  module: { type: String, required: true },
  titleKey: { type: String, required: true },
  descriptionKey: { type: String, required: true },
});
</script>
