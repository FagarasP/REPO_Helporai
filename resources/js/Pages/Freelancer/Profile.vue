<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    freelancer: Object,
    projects: Array,
    comments: Array,
    rating: Number
});
</script>

<template>
    <Head title="Freelancer Profile" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Profile Header -->
                        <div class="flex flex-col md:flex-row items-center md:items-start mb-8">
                            <!-- Avatar -->
                            <div class="flex-shrink-0 mb-4 md:mb-0 md:mr-6">
                                <div class="bg-gray-200 border-2 border-dashed rounded-xl w-32 h-32 flex items-center justify-center overflow-hidden">
                                    <img v-if="freelancer.avatar" :src="freelancer.avatar" :alt="freelancer.name" class="w-full h-full object-cover rounded-full">
                                    <div v-else class="bg-gray-300 border-2 border-dashed rounded-full w-32 h-32 flex items-center justify-center">
                                        <span class="text-4xl font-bold text-gray-600">{{ freelancer.name.charAt(0) }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Freelancer Info -->
                            <div class="text-center md:text-left">
                                <h1 class="text-3xl font-bold">{{ freelancer.name }}</h1>
                                <div class="mt-2">
                                    <p v-if="freelancer.languages && freelancer.languages.length" class="text-gray-600 dark:text-gray-400">
                                        Languages: <span v-for="(lang, index) in freelancer.languages" :key="index">{{ lang.language }} ({{ lang.level }})<span v-if="index < freelancer.languages.length - 1">, </span></span>
                                    </p>
                                    <p v-if="freelancer.years_of_experience" class="text-gray-600 dark:text-gray-400">
                                        Years of Experience: {{ freelancer.years_of_experience }}
                                    </p>
                                    <div class="flex items-center">
                                        <!-- Rating Stars -->
                                        <div class="flex">
                                            <svg v-for="n in 5" :key="n" class="w-5 h-5" :class="n <= rating ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </div>
                                        <span class="ml-2 text-gray-600 dark:text-gray-400">{{ rating }}/5</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Projects Table -->
                        <div class="mt-8">
                            <h2 class="text-2xl font-bold mb-4">Previous Projects</h2>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Project Title</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Company</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-for="project in projects" :key="project.id">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ project.title }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ project.company.name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    Completed
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="#" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View Details</a>
                                            </td>
                                        </tr>
                                        <tr v-if="projects.length === 0">
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                No projects found
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Comments Section -->
                        <div class="mt-8">
                            <h2 class="text-2xl font-bold mb-4">Company Comments</h2>
                            <div class="space-y-4">
                                <div v-for="(comment, index) in comments" :key="index" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <div class="flex justify-between">
                                        <span class="font-medium text-gray-900 dark:text-white">{{ comment.company }}</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ comment.date }}</span>
                                    </div>
                                    <p class="mt-2 text-gray-700 dark:text-gray-300">{{ comment.text }}</p>
                                </div>
                                <div v-if="comments.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
                                    No comments yet
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>