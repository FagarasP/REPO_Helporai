<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
const props = defineProps({ project: Object, project_languages: Array, job_types: Array, payment_offers: Array, companies: Array });
const form = useForm({ company_id: props.project.company_id, title: props.project.title, description: props.project.description, project_language: props.project.project_language, job_type: props.project.job_type, payment_offer: props.project.payment_offer, payment_amount: props.project.payment_amount, status: props.project.status, start_date: props.project.start_date, end_date: props.project.end_date });
const submit = ()=> form.put(route('other.projects.update', props.project.id));
</script>
<template>
  <Head title="Projekt bearbeiten"/>
  <AuthenticatedLayout><div class="max-w-4xl mx-auto p-6"><form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow space-y-3"><h1 class="text-xl font-bold">Projekt bearbeiten</h1><select v-model="form.company_id" class="w-full rounded-lg border" required><option v-for="c in companies" :key="c.id" :value="c.id">{{ c.name }}</option></select><input v-model="form.title" class="w-full rounded-lg border" required><textarea v-model="form.description" class="w-full rounded-lg border" rows="4" /><button class="bg-indigo-600 text-white px-4 py-2 rounded-lg">Aktualisieren</button></form></div></AuthenticatedLayout>
</template>
