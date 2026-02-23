<template>
    <aside
        class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-primary-100 dark:border-background-700 dark:bg-background-800 hidden sm:block">
        <div class="flex flex-col h-full">

            <!-- Navigation -->
            <div class="flex-1 overflow-y-auto py-4">
                <nav class="px-2 space-y-1">
                    <template v-for="item in menuItems" :key="item.name">
                        <!-- Simple link -->
                        <div v-if="!item.hasSubmenu">
                            <Link :href="route(item.route)"
                                class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200"
                                :class="[
                        route().current(item.route)
                            ? 'bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400 border-l-4 border-primary-600'
                            : 'text-text-700 hover:bg-primary-50 hover:text-primary-600 dark:text-text-400 dark:hover:bg-primary-900/30 dark:hover:text-text-300'
                    ]">
                            <svg class="mr-3 h-5 w-5 text-text-400 group-hover:text-primary-600 dark:group-hover:text-text-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                            </svg>
                            {{ item.name }}
                            </Link>
                        </div>

                        <!-- Menu with submenu -->
                        <div v-else>
                            <button @click="toggleSubmenu(item.key)"
                                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all duration-200"
                                :class="[
                        expandedMenus[item.key] || isSubmenuActive(item.submenu)
                            ? 'bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400'
                            : 'text-text-700 hover:bg-primary-50 hover:text-primary-600 dark:text-text-400 dark:hover:bg-primary-900/30 dark:hover:text-text-300'
                    ]">
                                <div class="flex items-center">
                                    <svg class="mr-3 h-5 w-5 text-text-400 group-hover:text-primary-600 dark:group-hover:text-text-300"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            :d="item.icon" />
                                    </svg>
                                    <span>{{ item.name }}</span>
                                </div>
                                <svg class="h-4 w-4 text-text-400 group-hover:text-primary-600 dark:hover:text-text-300 transition-all duration-200"
                                    :class="{ 'rotate-90': expandedMenus[item.key] }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

                            <!-- Submenu -->
                            <div v-show="expandedMenus[item.key]" class="mt-1 ml-8 space-y-1">
                                <Link v-for="subItem in item.submenu" :key="subItem.route" :href="route(subItem.route)"
                                    class="block px-3 py-2 text-sm dark:bg-background-800 dark:text-text-400 dark:hover:text-text-300 text-text-600 hover:text-primary-600 hover:bg-primary-50 rounded-md transition-all duration-200"
                                    :class="{ 'text-primary-600 dark:bg-background-600 dark:text-text-400 bg-primary-50 border-l-4 border-primary-600 dark:bg-primary-900/30 font-medium': route().current(subItem.route) }">
                                {{ subItem.name }}
                                </Link>
                            </div>
                        </div>
                    </template>
                </nav>
            </div>
        </div>
    </aside>
</template>

<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';

defineEmits(['close']);

// Get user role and permissions from Inertia page props
const page = usePage();
const user = page.props.auth.user;
const userRole = user ? user.role || 'other' : 'other';
const menuPermissions = user ? user.menu_permissions || [] : [];

// Expanded menu state for sub-menus
const expandedMenus = ref({});

const toggleSubmenu = (menuKey) => {
    expandedMenus.value[menuKey] = !expandedMenus.value[menuKey];
};

// Check if any submenu item is active
const isSubmenuActive = (submenu) => {
    return submenu.some(item => route().current(item.route));
};

// Function to check if a menu item should be visible based on permissions
const isMenuItemVisible = (item) => {
    // For 'other' role, check menu permissions
    if (userRole === 'other') {
        // If menu_permissions is null or empty, only show dashboard
        if (!menuPermissions || menuPermissions.length === 0) {
            return item.route === 'dashboard';
        }
        
        // If menu_permissions exists, check if item or any of its submenu items are allowed
        if (item.route) {
            // Simple menu item
            return menuPermissions.includes(item.route);
        } else if (item.submenu) {
            // Menu with submenu - check if any submenu item is allowed
            return item.submenu.some(subItem => menuPermissions.includes(subItem.route));
        }
        return false;
    }
    
    // For other roles, show all menu items
    return true;
};

// Define menu items for each role
const getAllMenuItems = () => {
    // Admin menu (has the same menu as other but with additional settings)
    if (userRole === 'admin') {
        const menuItems = [
            {
                name: 'Dashboard',
                route: 'dashboard',
                icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-3m-6 0a1 1 0 001-1v-6a1 1 0 011-1h2'
            },
            {
                name: 'Projects',
                key: 'projects',
                icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                hasSubmenu: true,
                submenu: [
                    { name: 'All Projects', route: 'other.project-management' },
                    { name: 'Create Project', route: 'other.projects.create' }
                ]
            },
            {
                name: 'Teams',
                key: 'teams',
                icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                hasSubmenu: true,
                submenu: [
                    { name: 'All Teams', route: 'other.teams.index' },
                    { name: 'Create Team', route: 'other.teams.create' }
                ]
            },
            {
                name: 'Workforce',
                key: 'freelancers',
                icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                hasSubmenu: true,
                submenu: [
                    { name: 'Scout Freelancers', route: 'other.workforce.scounting' },
                    { name: 'Recruit Freelancers', route: 'other.workforce.recruitment' }
                ]
            },
            {
                name: 'Shifts',
                key: 'shifts',
                icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                hasSubmenu: true,
                submenu: [
                    { name: 'Manage Shifts', route: 'other.shift-management' },
                    { name: 'Calendar View', route: 'other.shift-management' }
                ]
            },
            {
                name: 'Finance',
                key: 'finance',
                icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                hasSubmenu: true,
                submenu: [
                    { name: 'Payments', route: 'other.finance.payment-entry' },
                    { name: 'Invoices', route: 'other.finance.invoice-page' },
                    { name: 'Wallet', route: 'other.finance.wallet-page' }
                ]
            },
            {
                name: 'Settings',
                route: 'settings',
                icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'
            }
        ];
        return menuItems;
    }
    // Other role (same as admin but without settings)
    else if (userRole === 'other') {
        return [
            {
                name: 'Dashboard',
                route: 'other.dashboard',
                icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-3m-6 0a1 1 0 001-1v-6a1 1 0 011-1h2'
            },
            {
                name: 'Projects',
                key: 'projects',
                icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                hasSubmenu: true,
                submenu: [
                    { name: 'All Projects', route: 'other.project-management' },
                    { name: 'Create Project', route: 'other.projects.create' }
                ]
            },
            {
                name: 'Teams',
                key: 'teams',
                icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                hasSubmenu: true,
                submenu: [
                    { name: 'All Teams', route: 'other.teams.index' },
                    { name: 'Create Team', route: 'other.teams.create' }
                ]
            },
            {
                name: 'Workforce',
                key: 'freelancers',
                icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                hasSubmenu: true,
                submenu: [
                    { name: 'Scout Freelancers', route: 'other.workforce.scounting' },
                    { name: 'Recruit Freelancers', route: 'other.workforce.recruitment' }
                ]
            },
            {
                name: 'Shifts',
                key: 'shifts',
                icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                hasSubmenu: true,
                submenu: [
                    { name: 'Manage Shifts', route: 'other.shift-management' },
                    { name: 'Calendar View', route: 'other.shift-management' }
                ]
            },
            {
                name: 'Finance',
                key: 'finance',
                icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                hasSubmenu: true,
                submenu: [
                    { name: 'Payments', route: 'other.finance.payment-entry' },
                    { name: 'Invoices', route: 'other.finance.invoice-page' },
                    { name: 'Wallet', route: 'other.finance.wallet-page' }
                ]
            }
        ];
    }
    // Freelancer menu
    else if (userRole === 'freelancer') {
        return [
            {
                name: 'Dashboard',
                route: 'freelancer.dashboard',
                icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-3m-6 0a1 1 0 001-1v-6a1 1 0 011-1h2'
            },
            {
                name: 'Job Offers',
                key: 'job-offers',
                icon: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                hasSubmenu: true,
                submenu: [
                    { name: 'Available Jobs', route: 'freelancer.job-offers' },
                    { name: 'My Applications', route: 'freelancer.my-projects' }
                ]
            },
            {
                name: 'My Projects',
                key: 'my-projects',
                icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                hasSubmenu: true,
                submenu: [
                    { name: 'Active Projects', route: 'freelancer.my-projects' },
                    { name: 'Completed Projects', route: 'freelancer.my-projects' }
                ]
            },
            {
                name: 'Shifts',
                key: 'shifts',
                icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                hasSubmenu: true,
                submenu: [
                    { name: 'Available Shifts', route: 'freelancer.shifts' },
                    { name: 'My Shifts', route: 'freelancer.shifts' },
                    { name: 'Shift History', route: 'freelancer.shifts' }
                ]
            },
            {
                name: 'Finance',
                key: 'finance',
                icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                hasSubmenu: true,
                submenu: [
                    { name: 'Earnings', route: 'freelancer.finance' },
                    { name: 'Payment History', route: 'freelancer.finance' },
                    { name: 'Invoices', route: 'freelancer.finance' }
                ]
            }
        ];
    }
    // Company menu
    else if (userRole === 'company') {
        return [
            {
                name: 'Dashboard',
                route: 'company.dashboard',
                icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-3m-6 0a1 1 0 001-1v-6a1 1 0 011-1h2'
            },
            {
                name: 'Projects',
                key: 'projects',
                icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                hasSubmenu: true,
                submenu: [
                    { name: 'All Projects', route: 'company.project-management' },
                    { name: 'Create Project', route: 'company.projects.create' }
                ]
            },
            {
                name: 'Teams',
                key: 'teams',
                icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                hasSubmenu: true,
                submenu: [
                    { name: 'Manage Teams', route: 'company.teams.index' },
                    { name: 'Create Team', route: 'company.teams.create' }
                ]
            },
            {
                name: 'Workforce',
                key: 'freelancers',
                icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                hasSubmenu: true,
                submenu: [
                    { name: 'Scout Freelancers', route: 'company.workforce.scounting' },
                    { name: 'Manage Freelancers', route: 'company.workforce.recruitment' },
                    { name: 'Watchlist', route: 'company.workforce.watchlist' }
                ]
            },
            {
                name: 'Shift Management',
                key: 'shifts',
                icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                hasSubmenu: true,
                submenu: [
                    { name: 'Manage Shifts', route: 'company.shift-management' }
                ]
            },
            {
                name: 'Finance',
                key: 'finance',
                icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                hasSubmenu: true,
                submenu: [
                    { name: 'Payment Entry', route: 'company.finance.payment-entry' },
                    { name: 'Wallet', route: 'company.finance.wallet-page' },
                    { name: 'Invoices', route: 'company.finance.invoice-page' }
                ]
            }
        ];
    }
    // Default menu (should not happen but just in case)
    else {
        return [
            {
                name: 'Dashboard',
                route: 'dashboard',
                icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-3m-6 0a1 1 0 001-1v-6a1 1 0 011-1h2'
            }
        ];
    }
};

// Get filtered menu items based on user role and permissions
const menuItems = computed(() => {
    const allItems = getAllMenuItems();
    return allItems.filter(isMenuItemVisible);
});
</script>