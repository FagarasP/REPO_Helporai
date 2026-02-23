<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({ users: Object, roles: Array, filters: Object });

const createForm = useForm({ name: '', email: '', role: 'other', role_alias: '' });
const submitCreate = () => createForm.post(route('users.store'));

const deleteUser = (id) => {
    if (confirm('User löschen?')) useForm({}).delete(route('users.destroy', id));
};
</script>

<template>
  <Head title="User Management" />
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto p-6 space-y-6">
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow">
        <h1 class="text-2xl font-bold">Admin User Management</h1>
        <p class="opacity-70">Benutzer erstellen, Rollen verwalten, Konten bereinigen.</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow">
          <h2 class="font-semibold mb-3">Neuen User anlegen</h2>
          <form class="space-y-3" @submit.prevent="submitCreate">
            <input v-model="createForm.name" class="w-full rounded-lg border" placeholder="Name" required>
            <input v-model="createForm.email" type="email" class="w-full rounded-lg border" placeholder="E-Mail" required>
            <select v-model="createForm.role" class="w-full rounded-lg border">
              <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
            </select>
            <input v-model="createForm.role_alias" class="w-full rounded-lg border" placeholder="Role Alias (optional)">
            <button class="w-full bg-indigo-600 text-white py-2 rounded-lg">Erstellen</button>
          </form>
        </div>

        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl p-5 shadow overflow-x-auto">
          <table class="w-full text-sm">
            <thead><tr class="text-left border-b"><th>Name</th><th>E-Mail</th><th>Rolle</th><th></th></tr></thead>
            <tbody>
              <tr v-for="user in users.data" :key="user.id" class="border-b">
                <td class="py-2">{{ user.name }}</td>
                <td>{{ user.email }}</td>
                <td>{{ user.role }}</td>
                <td class="text-right"><button class="text-red-500" @click="deleteUser(user.id)">Löschen</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
