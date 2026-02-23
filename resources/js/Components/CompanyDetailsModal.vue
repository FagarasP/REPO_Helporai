<script setup>
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import { XMarkIcon } from '@heroicons/vue/24/solid';

const URL = window.URL;

const props = defineProps({
    show: Boolean,
    company: Object, // Existing company data if editing
});

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
    legal_name: '',
    address: '',
    city: '',
    state: '',
    postal_code: '',
    country: 'DE',
    phone: '',
    email: '',
    website: '',
    tax_number: '',
    vat_id: '',
    commercial_register_number: '',
    currency: 'EUR',
    financial_year_start: '',
    financial_year_end: '',
    logo: null,
});

watch(() => props.company, (newCompany) => {
    if (newCompany) {
        form.name = newCompany.name;
        form.legal_name = newCompany.legal_name;
        form.address = newCompany.address;
        form.city = newCompany.city;
        form.state = newCompany.state;
        form.postal_code = newCompany.postal_code;
        form.country = newCompany.country;
        form.phone = newCompany.phone;
        form.email = newCompany.email;
        form.website = newCompany.website;
        form.tax_number = newCompany.tax_number;
        form.vat_id = newCompany.vat_id;
        form.commercial_register_number = newCompany.commercial_register_number;
        form.currency = newCompany.currency;
        form.financial_year_start = newCompany.financial_year_start;
        form.financial_year_end = newCompany.financial_year_end;
    }
}, { immediate: true });

const submit = () => {
    if (props.company) {
        form.post(route('company.details.update', props.company.id), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
            onError: () => {},
        });
    } else {
        form.post(route('company.details.store'), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
            onError: () => {},
        });
    }
};

const handleLogoChange = (e) => {
    form.logo = e.target.files[0];
};

const removeLogo = async () => {
    if (!props.company) return;

    await form.post(route('company.details.removeLogo', props.company.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
            // Optionally, refresh the page to reflect the logo removal
            window.location.reload();
        },
        onError: () => {},
    });
};

const closeModal = () => {
    form.reset();
    emit('close');
};
</script>

<template>
    <Modal :show="show" @close="closeModal">
        <form @submit.prevent="submit" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ company ? 'Edit Company Details' : 'Add Company Details' }}
            </h2>

            <div class="mt-6">
                <InputLabel for="name" value="Company Name" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="legal_name" value="Legal Name" />
                <TextInput
                    id="legal_name"
                    v-model="form.legal_name"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.legal_name" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="address" value="Address" />
                <TextInput
                    id="address"
                    v-model="form.address"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.address" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="city" value="City" />
                <TextInput
                    id="city"
                    v-model="form.city"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.city" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="state" value="State" />
                <TextInput
                    id="state"
                    v-model="form.state"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.state" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="postal_code" value="Postal Code" />
                <TextInput
                    id="postal_code"
                    v-model="form.postal_code"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.postal_code" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="country" value="Country" />
                <TextInput
                    id="country"
                    v-model="form.country"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.country" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="phone" value="Phone" />
                <TextInput
                    id="phone"
                    v-model="form.phone"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.phone" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.email" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="website" value="Website" />
                <TextInput
                    id="website"
                    v-model="form.website"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.website" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="tax_number" value="Tax Number" />
                <TextInput
                    id="tax_number"
                    v-model="form.tax_number"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.tax_number" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="vat_id" value="VAT ID" />
                <TextInput
                    id="vat_id"
                    v-model="form.vat_id"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.vat_id" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="commercial_register_number" value="Commercial Register Number" />
                <TextInput
                    id="commercial_register_number"
                    v-model="form.commercial_register_number"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.commercial_register_number" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="currency" value="Currency" />
                <TextInput
                    id="currency"
                    v-model="form.currency"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.currency" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="financial_year_start" value="Financial Year Start" />
                <TextInput
                    id="financial_year_start"
                    v-model="form.financial_year_start"
                    type="date"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.financial_year_start" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="financial_year_end" value="Financial Year End" />
                <TextInput
                    id="financial_year_end"
                    v-model="form.financial_year_end"
                    type="date"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.financial_year_end" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="logo" value="Company Logo" />
                <input
                    id="logo"
                    type="file"
                    @change="handleLogoChange"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                />
                <InputError :message="form.errors.logo" class="mt-2" />
                <div v-if="form.logo || (company && company.logo_path)" class="mt-4 flex items-center space-x-4">
                    <img :src="form.logo ? URL.createObjectURL(form.logo) : '/storage/' + company.logo_path" alt="Company Logo" class="h-20 w-20 object-cover rounded-full">
                    <div @click="removeLogo" class="cursor-pointer bg-red-500 hover:bg-red-700 rounded-full w-8 h-8 flex items-center justify-center">
                        <XMarkIcon class="h-5 w-5 text-white" />
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <PrimaryButton :disabled="form.processing">
                    {{ company ? 'Update' : 'Add' }}
                </PrimaryButton>
            </div>
        </form>
    </Modal>
</template>
