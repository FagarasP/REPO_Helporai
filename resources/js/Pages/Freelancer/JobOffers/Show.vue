<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    project: Object,
    hasApplied: Boolean,
});

const form = useForm({});

const confirmingApplication = ref(false);
const applicationSubmitted = ref(props.hasApplied);

const confirmApply = () => {
    confirmingApplication.value = true;
};

const submitApplication = () => {
    form.post(route('freelancer.job-offers.apply', props.project.id), {
        onSuccess: () => {
            confirmingApplication.value = false;
            applicationSubmitted.value = true;
        },
    });
};

const closeModal = () => {
    confirmingApplication.value = false;
};
</script>

<template>
    <Head :title="project.title" />

    <AuthenticatedLayout>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex flex-wrap -mx-4">
                            <!-- Left Column (30%) -->
                            <div class="w-full lg:w-1/3 px-4 mb-6 lg:mb-0">
                                <!-- Company Info -->
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3 rounded-md shadow-xl p-4">
                                        <img v-if="project.company.logo_path" :src="'/storage/' + project.company.logo_path" alt="Company Logo" class="h-20 w-20 object-cover rounded-full">
                                        <h2 class="text-xl font-bold">{{ project.company.name }}</h2>
                                    </div>
                                    
                                </div>

                                <!-- Project Details -->
                                <div class="space-y-2">
                                    <p><strong>Status:</strong> Open</p>
                                    <p><strong>Job Type:</strong> Outbound</p>
                                    <p><strong>Task Category:</strong> Sales</p>
                                    <p><strong>Payment:</strong> {{ project.payment_amount }}</p>
                                    <p><strong>Billing Interval:</strong> Sec</p>
                                    <p><strong>Language:</strong> {{ project.project_language }}</p>
                                </div>
                            </div>

                            <!-- Right Column (70%) -->
                            <div class="w-full lg:w-2/3 px-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Project Description</h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ project.description }}</p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button @click="alert('Ask Question functionality coming soon!')" class="bg-gray-200 m-2 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                                        Ask Question
                            </button>
                            <button
                                v-if="!applicationSubmitted"
                                @click="confirmApply"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                            >
                                Apply for Project
                            </button>
                            
                            <div v-else class="text-green-500 font-medium">
                                You have already applied for this project.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div v-if="confirmingApplication" class="fixed inset-0 overflow-y-auto z-50">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" @click="closeModal">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                                    Confirm Application
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-300">
                                        Are you sure you want to apply for this project? Once submitted, you cannot withdraw your application.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button 
                            type="button" 
                            @click="submitApplication"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                            :disabled="form.processing"
                        >
                            Confirm Apply
                        </button>
                        <button 
                            type="button" 
                            @click="closeModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
