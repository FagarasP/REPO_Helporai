<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import Translations from './Partials/Translation.vue';
import CompanyDetailsModal from '@/Components/CompanyDetailsModal.vue';
import FreelancerProfileModal from '@/Components/FreelancerProfileModal.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    company: {
        type: Object,
        default: null,
    },
});

const showCompanyDetailsModal = ref(false);
const showFreelancerProfileModal = ref(false);

const openCompanyDetailsModal = () => {
    showCompanyDetailsModal.value = true;
};

const closeCompanyDetailsModal = () => {
    showCompanyDetailsModal.value = false;
};

const openFreelancerProfileModal = () => {
    showFreelancerProfileModal.value = true;
};

const closeFreelancerProfileModal = () => {
    showFreelancerProfileModal.value = false;
};

const user = usePage().props.auth.user;

</script>

<template>

    <Head title="Profile" />

    <AuthenticatedLayout>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                    <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status"
                        class="max-w-xl" />
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800" v-if="user.role === 'company'">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Company Details</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Update your company's profile information.
                    </p>
                    <p> &nbsp;</p>
                    <PrimaryButton @click="openCompanyDetailsModal" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded cursor-pointer">
                        {{ company ? 'Edit Company Details' : 'Add Company Details' }}
                    </PrimaryButton>
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800" v-if="user.role === 'freelancer'">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Freelancer Profile</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Update your freelancer profile information.
                    </p>

                    <p> &nbsp;</p>
                    <PrimaryButton @click="openFreelancerProfileModal" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded cursor-pointer">
                        {{ user.languages || user.years_of_experience ? 'Edit Freelancer Profile' : 'Add Freelancer Profile' }}
                    </PrimaryButton>
                </div>

                <!-- <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                    <Translations />
                </div> -->

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>

        <CompanyDetailsModal :show="showCompanyDetailsModal" :company="company" @close="closeCompanyDetailsModal" />
        <FreelancerProfileModal :show="showFreelancerProfileModal" :freelancer="user" @close="closeFreelancerProfileModal" />
    </AuthenticatedLayout>
</template>
