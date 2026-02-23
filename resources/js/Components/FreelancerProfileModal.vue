<script setup>
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { watch, ref, computed } from 'vue';
import { PlusIcon, MinusIcon } from '@heroicons/vue/24/solid';
import Slider from '@vueform/slider';
import '@vueform/slider/themes/default.css';
import useTheme from '@/Utils/useTheme';

const { isDark, toggleTheme } = useTheme();

const props = defineProps({
    show: Boolean,
    freelancer: Object, // Existing freelancer data if editing
});

const emit = defineEmits(['close']);

const form = useForm({
    languages: [],
    years_of_experience: 0,
    availability: '',
    preferred_shift: '',
    specializations: [],
    certifications: [],
    bio: '',
});

const availableLanguages = [
    'English', 'German', 'Spanish', 'Italian', 'French', 'Portuguese', 'Russian', 'Chinese', 'Japanese',
];

const proficiencyLevels = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];

watch(() => props.freelancer, (newFreelancer) => {
    if (newFreelancer) {
        form.languages = newFreelancer.languages || [];
        form.years_of_experience = newFreelancer.years_of_experience || 0;
        form.availability = newFreelancer.availability || '';
        form.preferred_shift = newFreelancer.preferred_shift || '';
        form.specializations = newFreelancer.specializations || [];
        form.certifications = newFreelancer.certifications || [];
        form.bio = newFreelancer.bio || '';
    }
}, { immediate: true });

const specializationsInput = computed({
    get: () => form.specializations.join(', '),
    set: (value) => {
        form.specializations = value.split(',').map(item => item.trim()).filter(item => item.length > 0);
    },
});

const certificationsInput = computed({
    get: () => form.certifications.join(', '),
    set: (value) => {
        form.certifications = value.split(',').map(item => item.trim()).filter(item => item.length > 0);
    },
});

const addLanguage = () => {
    form.languages.push({ language: '', level: 'A1' });
};

const removeLanguage = (index) => {
    form.languages.splice(index, 1);
};

const submit = () => {
    if (props.freelancer) {
        form.post(route('freelancer.profile.update', props.freelancer.id), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
            onError: () => {},
        });
    } else {
        form.post(route('freelancer.profile.store'), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
            onError: () => {},
        });
    }
};

const closeModal = () => {
    form.reset();
    emit('close');
};
</script>

<template>
    <Modal :show="show" @close="closeModal">
        <form @submit.prevent="submit" class="p-6">
            <h2 class="text-lg font-medium text-text-900 dark:text-text-100">
                {{ freelancer ? 'Edit Freelancer Profile' : 'Add Freelancer Profile' }}
            </h2>

            <div class="mt-6">
                <InputLabel value="Languages" />
                <div v-for="(lang, index) in form.languages" :key="index" class="flex items-center mt-2 space-x-2">
                    <div class="w-1/2">
                        <select v-model="lang.language" class="mt-1 block w-full border-gray-300 dark:border-background-700 dark:bg-background-950 dark:text-text-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="">Select Language</option>
                            <option v-for="al in availableLanguages" :key="al" :value="al">{{ al }}</option>
                        </select>
                    </div>
                    <div class="w-1/2">
                        <select v-model="lang.level" class="mt-1 block w-full border-gray-300 dark:border-background-700 dark:bg-background-950 dark:text-text-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option v-for="level in proficiencyLevels" :key="level" :value="level">{{ level }}</option>
                        </select>
                    </div>
                    <MinusIcon @click="removeLanguage(index)" class="h-5 w-5 text-red-500 cursor-pointer" />
                </div>
                <PrimaryButton type="button" @click="addLanguage" class="mt-2">
                    <PlusIcon class="h-5 w-5 mr-1" /> Add Language
                </PrimaryButton>
                <InputError :message="form.errors.languages" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="years_of_experience" value="Years of Experience" />
                <TextInput
                    id="years_of_experience"
                    v-model="form.years_of_experience"
                    type="number"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.years_of_experience" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="availability" value="Availability" />
                <select
                    id="availability"
                    v-model="form.availability"
                    class="mt-1 block w-full border-gray-300 dark:border-background-700 dark:bg-background-950 dark:text-text-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                >
                    <option value="">Select Availability</option>
                    <option value="full-time">Full-time</option>
                    <option value="part-time">Part-time</option>
                    <option value="hourly">Hourly</option>
                </select>
                <InputError :message="form.errors.availability" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="preferred_shift" value="Preferred Shift" />
                <select
                    id="preferred_shift"
                    v-model="form.preferred_shift"
                    class="mt-1 block w-full border-gray-300 dark:border-background-700 dark:bg-background-950 dark:text-text-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                >
                    <option value="">Select Preferred Shift</option>
                    <option value="morning">Morning</option>
                    <option value="afternoon">Afternoon</option>
                    <option value="night">Night</option>
                </select>
                <InputError :message="form.errors.preferred_shift" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="specializations" value="Specializations (comma separated)" />
                <TextInput
                    id="specializations"
                    v-model="specializationsInput"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.specializations" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="certifications" value="Certifications (comma separated)" />
                <TextInput
                    id="certifications"
                    v-model="certificationsInput"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.certifications" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="bio" value="Bio" />
                <textarea
                    id="bio"
                    v-model="form.bio"
                    class="mt-1 block w-full border-gray-300 dark:border-background-700 dark:bg-background-950 dark:text-text-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                ></textarea>
                <InputError :message="form.errors.bio" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <PrimaryButton :disabled="form.processing">
                    {{ freelancer ? 'Update' : 'Add' }}
                </PrimaryButton>
            </div>
        </form>
    </Modal>
</template>