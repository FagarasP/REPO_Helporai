<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    project_languages: Array,
    job_types: Array,
    payment_offers: Array,
});

const form = useForm({
    title: '',
    description: '',
    project_language: '',
    job_type: '',
    payment_offer: '',
    payment_amount: '',
    status: 'not_published',
    start_date: '',
    end_date: '',
});

const submit = () => {
    form.post(route('company.projects.store'));
};
</script>

<template>
    <Head title="Create Project" />

    <AuthenticatedLayout>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-background-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-text-900 dark:text-text-100">
                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="title" value="Title" />
                                <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required autofocus />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="description" value="Description" />
                                <textarea id="description" class="mt-1 block w-full border-gray-300 dark:border-background-700 dark:bg-background-950 dark:text-text-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" v-model="form.description" required></textarea>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="project_language" value="Project Language" />
                                <select id="project_language" class="mt-1 block w-full border-gray-300 dark:border-background-700 dark:bg-background-950 dark:text-text-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" v-model="form.project_language" required>
                                    <option v-for="language in project_languages" :key="language.id" :value="language.value">{{ language.value }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.project_language" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="job_type" value="Job Type" />
                                <select id="job_type" class="mt-1 block w-full border-gray-300 dark:border-background-700 dark:bg-background-950 dark:text-text-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" v-model="form.job_type" required>
                                    <option v-for="job_type in job_types" :key="job_type.id" :value="job_type.value">{{ job_type.value }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.job_type" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="payment_offer" value="Payment Offer" />
                                <select id="payment_offer" class="mt-1 block w-full border-gray-300 dark:border-background-700 dark:bg-background-950 dark:text-text-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" v-model="form.payment_offer" required>
                                    <option v-for="payment_offer in payment_offers" :key="payment_offer.id" :value="payment_offer.value">{{ payment_offer.value }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.payment_offer" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="payment_amount" value="Payment Amount" />
                                <TextInput id="payment_amount" type="number" class="mt-1 block w-full" v-model="form.payment_amount" required />
                                <InputError class="mt-2" :message="form.errors.payment_amount" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="status" value="Status" />
                                <select id="status" class="mt-1 block w-full border-gray-300 dark:border-background-700 dark:bg-background-950 dark:text-text-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" v-model="form.status" required>
                                    <option value="not_published">Not Published</option>
                                    <option value="published">Published</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="start_date" value="Start Date" />
                                <TextInput id="start_date" type="date" class="mt-1 block w-full" v-model="form.start_date" />
                                <InputError class="mt-2" :message="form.errors.start_date" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="end_date" value="End Date" />
                                <TextInput id="end_date" type="date" class="mt-1 block w-full" v-model="form.end_date" />
                                <InputError class="mt-2" :message="form.errors.end_date" />
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
