<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Head, usePage } from '@inertiajs/vue3';


</script>

<template>
    <!-- Header/Navigation -->

    <Head title="Welcome" />
    <div>in work</div>


</template>

<style scoped>
.testimonials-wrapper {
    overflow: hidden;
    width: 100%;
}

.testimonials-track {
    display: flex;
    transition: transform 0.5s ease-in-out;
    width: 300%;
    /* 3 slides = 300% */
}

.testimonial-slide {
    flex: 0 0 100%;
    width: 100%;
}
</style>
