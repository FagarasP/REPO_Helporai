<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    projects: Array,
    freelancers: Array,
    project_id: Number,
});

const form = useForm({
    name: '',
    project_id: props.project_id,
    description: '',
    internal_notes: '',
    freelancers: [],
});

// Keep a reference to the current freelancers
const currentFreelancers = ref(props.freelancers);

// Watch for project_id changes and fetch freelancers who applied to that project
watch(() => form.project_id, (newProjectId) => {
    if (newProjectId) {
        // Fetch freelancers who applied to the selected project
        router.reload({
            only: ['freelancers'],
            data: {
                project_id: newProjectId
            },
            onSuccess: (page) => {
                currentFreelancers.value = page.props.freelancers;
                // Clear selected freelancers when project changes
                form.freelancers = [];
            }
        });
    } else {
        // If no project selected, show all freelancers who applied to any project
        router.reload({
            only: ['freelancers'],
            data: {
                project_id: null
            },
            onSuccess: (page) => {
                currentFreelancers.value = page.props.freelancers;
                // Clear selected freelancers when project changes
                form.freelancers = [];
            }
        });
    }
});

const submit = () => {
    form.post(route('company.teams.store'));
};
</script>

<template>
    <Head title="Create Team" />

    <AuthenticatedLayout>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-background-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-text-900 dark:text-text-100">
                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="name" value="Name" />
                                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="project_id" value="Project" />
                                <select id="project_id" class="mt-1 block w-full border-gray-300 dark:border-background-700 dark:bg-background-950 dark:text-text-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" v-model="form.project_id" required>
                                    <option v-for="project in projects" :key="project.id" :value="project.id">{{ project.title }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.project_id" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="description" value="Description" />
                                <textarea id="description" class="mt-1 block w-full border-gray-300 dark:border-background-700 dark:bg-background-950 dark:text-text-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" v-model="form.description"></textarea>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="internal_notes" value="Internal Notes" />
                                <textarea id="internal_notes" class="mt-1 block w-full border-gray-300 dark:border-background-700 dark:bg-background-950 dark:text-text-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" v-model="form.internal_notes"></textarea>
                                <InputError class="mt-2" :message="form.errors.internal_notes" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="freelancers" value="Freelancers (who applied to the selected project)" />
                                <select multiple id="freelancers" class="mt-1 block w-full border-gray-300 dark:border-background-700 dark:bg-background-950 dark:text-text-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" v-model="form.freelancers">
                                    <option v-for="freelancer in currentFreelancers" :key="freelancer.id" :value="freelancer.id">{{ freelancer.name }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.freelancers" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Create
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
