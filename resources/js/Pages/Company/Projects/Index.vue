<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import { ref } from 'vue';
import axios from 'axios';
import { CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    projects: Array,
});

const showApplicantsModal = ref(false);
const selectedProject = ref(null);
const projectApplications = ref([]);

const openApplicantsModal = async (project) => {
    selectedProject.value = project;
    const response = await axios.get(route('company.projects.applicants', project.id));
    projectApplications.value = response.data;
    showApplicantsModal.value = true;
};

const closeApplicantsModal = () => {
    showApplicantsModal.value = false;
    selectedProject.value = null;
    projectApplications.value = [];
};

const rejectApplication = async (applicationId) => {
    await axios.post(route('company.projects.applications.reject', applicationId));
    const response = await axios.get(route('company.projects.applicants', selectedProject.value.id));
    projectApplications.value = response.data;
};

</script>

<template>
    <Head title="Projects" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-end mb-4">
                            <Link :href="route('company.projects.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Project
                            </Link>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Applicants</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                <tr v-for="project in projects" :key="project.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ project.title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ project.status }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button @click="openApplicantsModal(project)" class="text-indigo-600 hover:text-indigo-900">View Applicants ({{ project.applications_count }})</button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Link :href="route('company.projects.edit', project.id)" class="text-indigo-600 hover:text-indigo-900">Edit</Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <Modal :show="showApplicantsModal" @close="closeApplicantsModal">
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900  dark:text-gray-300">Applicants for {{ selectedProject?.title }}</h2>
                                <div class="mt-4">
                                    <ul v-if="projectApplications.length">
                                        <li v-for="application in projectApplications" :key="application.id" class="flex items-center justify-between py-2 border-b">
                                            <span>
                                                <Link :href="route('freelancer.public.profile', application.user.id)" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ application.user.name }}
                                                </Link>
                                                - {{ application.status }}
                                            </span>
                                            <div class="flex space-x-2">
                                                <button v-if="application.status === 'pending'" class="flex items-center text-green-600 hover:text-green-900">
                                                    <CheckCircleIcon class="h-5 w-5 mr-1" />
                                                    Approve
                                                </button>
                                                <button v-if="application.status === 'pending'" @click="rejectApplication(application.id)" class="flex items-center text-red-600 hover:text-red-900">
                                                    <XCircleIcon class="h-5 w-5 mr-1" />
                                                    Reject
                                                </button>
                                            </div>
                                        </li>
                                    </ul>
                                    <p v-else class=" dark:text-gray-300">No applicants for this project yet.</p>
                                </div>
                                <div class="mt-6 flex justify-end">
                                    <button @click="closeApplicantsModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </Modal>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
