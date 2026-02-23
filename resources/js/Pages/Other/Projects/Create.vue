<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({ project_languages: Array, job_types: Array, payment_offers: Array, companies: Array });
const form = useForm({ company_id:'', title:'', description:'', project_language:'', job_type:'', payment_offer:'', payment_amount:'', status:'not_published', start_date:'', end_date:'' });
const submit = ()=> form.post(route('other.projects.store'));
</script>
<template>
  <Head title="Projekt erstellen"/>
  <AuthenticatedLayout>
    <div class="max-w-4xl mx-auto p-6">
      <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow space-y-3">
        <h1 class="text-xl font-bold">Projekt erstellen (Admin)</h1>
        <select v-model="form.company_id" class="w-full rounded-lg border" required>
          <option disabled value="">Firma wählen</option>
          <option v-for="c in companies" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
        <input v-model="form.title" class="w-full rounded-lg border" placeholder="Titel" required>
        <textarea v-model="form.description" class="w-full rounded-lg border" rows="4" placeholder="Beschreibung" required />
        <div class="grid grid-cols-3 gap-3">
          <select v-model="form.project_language" class="rounded-lg border" required><option v-for="i in project_languages" :key="i.id" :value="i.value">{{ i.value }}</option></select>
          <select v-model="form.job_type" class="rounded-lg border" required><option v-for="i in job_types" :key="i.id" :value="i.value">{{ i.value }}</option></select>
          <select v-model="form.payment_offer" class="rounded-lg border" required><option v-for="i in payment_offers" :key="i.id" :value="i.value">{{ i.value }}</option></select>
        </div>
        <input v-model="form.payment_amount" type="number" class="w-full rounded-lg border" placeholder="Budget" required>
        <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg">Speichern</button>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
