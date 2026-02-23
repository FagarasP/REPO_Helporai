<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { DocumentTextIcon, BriefcaseIcon, CurrencyDollarIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    settings: Array,
});

const formatType = (type) => {
    const words = type.split('_');
    return words.map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
};

const getIcon = (type) => {
    switch (type) {
        case 'project_language':
            return DocumentTextIcon;
        case 'job_type':
            return BriefcaseIcon;
        case 'payment_offer':
            return CurrencyDollarIcon;
        default:
            return null;
    }
};
</script>

<template>
    <Head title="Project Settings" />

    <AuthenticatedLayout>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-end mb-4">
                            <Link :href="route('settings.project-settings.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Setting
                            </Link>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Value</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                <tr v-for="setting in settings" :key="setting.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ setting.value }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <component :is="getIcon(setting.type)" class="h-5 w-5 mr-2" />
                                            <span>{{ formatType(setting.type) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Link :href="route('settings.project-settings.edit', setting.id)" class="text-indigo-600 hover:text-indigo-900">Edit</Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
