<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Footer from '@/Components/Footer.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ToggleSwitch from '@/Components/ToggleSwitch.vue';

const props = defineProps({
    freelancers: Array,
    uniqueLanguages: Array,
});

const search = ref('');
const languages = ref([]);
const experience = ref(0);
const onlyTalentsWithoutOngoingProjects = ref(false);
const onlyActiveTalents = ref(false);
const selectedHelpers = ref([]);

const filteredFreelancers = computed(() => {
    return props.freelancers.data.filter(freelancer => {
        const languageMatch = languages.value.length === 0 || 
            (freelancer.languages && Array.isArray(freelancer.languages) && 
             freelancer.languages.some(lang => 
                 lang && typeof lang.language !== 'undefined' && 
                 languages.value.some(selectedLang => 
                     selectedLang && 
                     String(selectedLang).trim() === String(lang.language).trim()
                 )
             ));
        const experienceMatch = freelancer.years_of_experience >= experience.value;
        const searchMatch = freelancer.name.toLowerCase().includes(search.value.toLowerCase()) || freelancer.email.toLowerCase().includes(search.value.toLowerCase());
        const withoutOngoingProjectsMatch = !onlyTalentsWithoutOngoingProjects.value || freelancer.projects_count === 0;
        const activeTalentsMatch = !onlyActiveTalents.value || freelancer.is_active;

        return languageMatch && experienceMatch && searchMatch && withoutOngoingProjectsMatch && activeTalentsMatch;
    });
});

const areActionsDisabled = computed(() => {
    return selectedHelpers.value.length === 0;
});

const selectAll = (event) => {
    if (event.target.checked) {
        selectedHelpers.value = filteredFreelancers.value.map(freelancer => freelancer.id);
    } else {
        selectedHelpers.value = [];
    }
};

const resetFilters = () => {
    search.value = '';
    languages.value = [];
    experience.value = 0;
    onlyTalentsWithoutOngoingProjects.value = false;
    onlyActiveTalents.value = false;
};

</script>

<template>

    <Head title="Scounting" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-background-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-text-900 dark:text-text-100">
                        <div class="mb-4">
                            <input type="text" v-model="search" placeholder="Search..." class="px-4 py-2 border rounded-md w-full border-gray-300 dark:border-background-700 dark:bg-background-950 dark:text-text-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                        </div>
                        <div class="flex mb-4">
                            <div class="w-1/2 pr-2">
                                <label for="languages" class="block mb-2">Languages</label>
                                <select id="languages" v-model="languages" multiple class="px-4 py-2 border rounded-md w-full border-gray-300 dark:border-background-700 dark:bg-background-950 dark:text-text-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                    <option v-for="lang in uniqueLanguages" :key="lang" :value="lang">{{ lang }}</option>
                                </select>
                            </div>
                            <div class="w-1/2 pl-2">
                                <label for="experience" class="block mb-2">Years of Experience: {{ experience }}</label>
                                <input type="range" v-model="experience" id="experience" min="0" max="25" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-background-700">
                            </div>
                        </div>
                        <div class="flex justify-start mb-4">
                            <div class="inline-flex rounded-md shadow-sm" role="group">
                                <button type="button" :disabled="areActionsDisabled" class="py-2 px-4 text-sm font-medium text-gray-900 bg-white rounded-l-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-background-700 dark:border-background-600 dark:text-text-100 dark:hover:text-text-100 dark:hover:bg-background-600 dark:focus:ring-blue-500 dark:focus:text-text-100 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Recruit
                                </button>
                                <button type="button" :disabled="areActionsDisabled" class="py-2 px-4 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-background-700 dark:border-background-600 dark:text-text-100 dark:hover:text-text-100 dark:hover:bg-background-600 dark:focus:ring-blue-500 dark:focus:text-text-100 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Set on watchlist
                                </button>
                                <button type="button" :disabled="areActionsDisabled" class="py-2 px-4 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-background-700 dark:border-background-600 dark:text-text-100 dark:hover:text-text-100 dark:hover:bg-background-600 dark:focus:ring-blue-500 dark:focus:text-text-100 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Invite to training
                                </button>
                                <button type="button" :disabled="areActionsDisabled" class="py-2 px-4 text-sm font-medium text-gray-900 bg-white rounded-r-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-background-700 dark:border-background-600 dark:text-text-100 dark:hover:text-text-100 dark:hover:bg-background-600 dark:focus:ring-blue-500 dark:focus:text-text-100 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Send Message
                                </button>
                            </div>
                        </div>
                        <div class="flex mb-4">
                            <div class="w-1/2 pr-2">
                                <ToggleSwitch id="ongoing-projects" v-model="onlyTalentsWithoutOngoingProjects" label="Only Helpers without ongoing projects" />
                            </div>
                            <div class="w-1/2 pl-2">
                                <ToggleSwitch id="active-talents" v-model="onlyActiveTalents" label="Only active Helpers" />
                            </div>
                        </div>
                        <div class="flex justify-end mb-4">
                            <button @click="resetFilters" class="py-2 px-4 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-background-700 dark:border-background-600 dark:text-text-100 dark:hover:text-text-100 dark:hover:bg-background-600 dark:focus:ring-blue-500 dark:focus:text-text-100">
                                Reset
                            </button>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 dark:bg-background-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-text-300 uppercase tracking-wider">
                                        <input type="checkbox" @change="selectAll" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:border-background-600 dark:bg-background-950">
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-text-300 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-text-300 uppercase tracking-wider">Helper since</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-text-300 uppercase tracking-wider">Languages</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-text-300 uppercase tracking-wider">Experience</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-text-300 uppercase tracking-wider">Notes</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-text-300 uppercase tracking-wider">Activity</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-background-800 divide-y divide-gray-200">
                                <tr v-for="freelancer in filteredFreelancers" :key="freelancer.id" class="hover:bg-gray-100 dark:hover:bg-background-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" :value="freelancer.id" v-model="selectedHelpers" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:border-background-600 dark:bg-background-950">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap dark:text-text-200">{{ freelancer.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap dark:text-text-200">{{ freelancer.helper_since }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap dark:text-text-200">
                                        <span v-for="(lang, index) in freelancer.languages" :key="index">
                                            {{ lang.language }} ({{ lang.level }})<span v-if="index < freelancer.languages.length - 1">, </span>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap dark:text-text-200">{{ freelancer.years_of_experience }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap dark:text-text-200">{{ freelancer.notes }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap dark:text-text-200">{{ freelancer.activity }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <Footer />

</template>