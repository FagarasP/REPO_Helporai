<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Footer from '@/Components/Footer.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>

    <Head title="Settings" />

    <AuthenticatedLayout>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-background-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-text-900 dark:text-text-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <Link :href="route('settings.user-management')"
                                class="bg-background-200 dark:bg-background-700 p-6 rounded-lg text-center hover:bg-background-300 dark:hover:bg-background-600">
                            <h3 class="text-lg font-medium">User Management</h3>
                            </Link>

                            <Link :href="route('settings.project-settings.index')"
                                class="bg-background-200 dark:bg-background-700 p-6 rounded-lg text-center hover:bg-background-300 dark:hover:bg-background-600">
                            <h3 class="text-lg font-medium">Project Settings</h3>
                            </Link>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <Footer />
</template>