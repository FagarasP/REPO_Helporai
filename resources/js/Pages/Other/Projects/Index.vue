<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
const props = defineProps({ projects: Array });
const destroyProject = (id) => {
  if (confirm('Projekt löschen?')) useForm({}).delete(route('other.projects.destroy', id));
};
</script>

<template>
  <Head title="Admin Project Management" />
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto p-6 space-y-4">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Project Management</h1>
        <Link :href="route('other.projects.create')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg">Projekt hinzufügen</Link>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow overflow-x-auto">
        <table class="w-full text-sm">
          <thead><tr class="border-b"><th>Titel</th><th>Firma</th><th>Status</th><th></th></tr></thead>
          <tbody>
            <tr v-for="project in projects" :key="project.id" class="border-b">
              <td class="py-2">{{ project.title }}</td>
              <td>{{ project.company?.name }}</td>
              <td>{{ project.status }}</td>
              <td class="text-right space-x-3">
                <Link :href="route('other.projects.edit', project.id)" class="text-indigo-600">Bearbeiten</Link>
                <button class="text-red-500" @click="destroyProject(project.id)">Löschen</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
