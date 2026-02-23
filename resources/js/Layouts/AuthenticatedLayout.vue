<script setup>
import { ref, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import SideMenu from '@/Components/SideMenu.vue';
import useTheme from '@/Utils/useTheme';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const { isDark, toggleTheme } = useTheme();

onMounted(() => {
    const page = usePage();
    if (!page.props.auth.user) {
        // Clear local storage
        localStorage.clear();

        // Remove cookies (example, adjust as needed)
        document.cookie.split(';').forEach(function(c) {
            document.cookie = c.replace(/^ +/, '').replace(/=.*/, '=;expires=' + new Date().toUTCString() + ';path=/');
        });

        // Redirect to login page
        window.location.href = '/login';
    }
});
</script>

<template>
    <div class="min-h-screen flex bg-background-50 dark:bg-background-900 dark:border-background-700">
        <!-- Top Navigation -->
        <nav
            class="fixed top-0 w-full z-50 bg-white/95 dark:bg-background-800 backdrop-blur-sm border-b border-primary-100 dark:border-background-700 h-16">
            <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <!-- Logo -->
                    <div class="flex shrink-0 items-center">
                        <Link :href="route('dashboard')" class="flex items-center space-x-3">
                        <ApplicationLogo class="block h-9 w-auto fill-current text-text-800 dark:text-text-200" />
                        </Link>
                    </div>
                    <header class="bg-white dark:bg-background-800 shadow-sm border-b border-primary-100 dark:border-background-700"
                        v-if="$slots.header">
                        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                            <slot name="header" />
                        </div>
                    </header>
                    <!-- Right Side Actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Theme Toggle -->
                        <button @click="toggleTheme"
                            class="p-2 rounded-lg  dark:bg-background-800 dark:text-text-400 dark:hover:text-text-300 text-text-600  hover:bg-primary-50 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-200"
                            title="Toggle Theme">
                            <!-- Light Mode Icon -->
                            <svg v-if="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <!-- Dark Mode Icon -->
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                        <!-- User Dropdown -->
                        <div v-if="$page.props.auth.user" class="relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button type="button"
                                        class="inline-flex items-center px-4 py-2 dark:text-text-400 dark:hover:text-text-300 text-text-600 rounded-lg text-sm font-medium text-text-700 hover:bg-primary-50  hover:text-primary-600 transition-all duration-200">
                                        <div
                                            class="w-8 h-8 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-white font-semibold text-sm">
                                                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                        <span>{{ $page.props.auth.user.name }}</span>
                                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="py-1">
                                        <DropdownLink :href="route('profile.edit')" class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span>Profile</span>
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button"
                                            class="flex items-center space-x-2 w-full">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            <span>Log Out</span>
                                        </DropdownLink>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                        <div v-else class="relative">
                            <Link :href="route('login')" 
                                class="inline-flex items-center px-4 py-2 dark:text-text-400 dark:hover:text-text-300 text-text-600 rounded-lg text-sm font-medium text-text-700 hover:bg-primary-50  hover:text-primary-600 transition-all duration-200">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                Login
                            </Link>
                        </div>

                        <!-- Mobile Menu Button -->
                        <div class="flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="p-2 rounded-lg  dark:text-text-400 dark:hover:text-text-300 text-text-600  hover:bg-primary-50 dark:hover:bg-background-900/30 transition-all duration-200">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path
                                        :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar -->
        <SideMenu @close="showingNavigationDropdown = false" 
            :class="{ 'translate-x-0 ease-out': showingNavigationDropdown, '-translate-x-full ease-in': !showingNavigationDropdown }"
            class="fixed inset-y-0 left-0 z-40 pt-16 transition-transform duration-300 ease-in-out sm:translate-x-0 shadow-lg" />

        <!-- Main Content -->
        <div class="flex-1 ml-0 sm:ml-64 pt-16 bg-background-50 dark:bg-background-950 min-h-screen">
            <!-- Page Header -->
            <header
                class="bg-white dark:bg-background-900 shadow-sm border-b border-primary-100 dark:border-background-800"
                v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="p-6">
                <slot />
            </main>
        </div>
    </div>
</template>