<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { 
    HomeIcon, 
    BuildingOfficeIcon, 
    UsersIcon, 
    Cog6ToothIcon,
    Bars3Icon,
    ArrowRightOnRectangleIcon
} from '@heroicons/vue/24/outline';

const page = usePage();
const user = page.props.auth?.user;
const role = user?.role;

const isSidebarOpen = ref(false);

const navItems = [
    { name: 'Dashboard', href: role === 'super_admin' ? '/super-admin/dashboard' : '/dashboard', icon: HomeIcon, show: true },
    { name: 'Sekolah', href: '/super-admin/schools', icon: BuildingOfficeIcon, show: role === 'super_admin' },
    { name: 'Siswa', href: '/admin/students', icon: UsersIcon, show: role === 'admin_sekolah' || role === 'kurikulum' },
    { name: 'Bulk Write RFID', href: '/admin/bulk-write', icon: Cog6ToothIcon, show: role === 'admin_sekolah' },
    { name: 'Kalender Akademik', href: '/kurikulum/calendar', icon: Cog6ToothIcon, show: role === 'kurikulum' || role === 'admin_sekolah' },
    { name: 'Kenaikan Kelas', href: '/kurikulum/promotion', icon: Cog6ToothIcon, show: role === 'kurikulum' || role === 'admin_sekolah' },
    { name: 'Pengaturan', href: '/admin/settings', icon: Cog6ToothIcon, show: role === 'admin_sekolah' },
    { name: 'Perangkat RFID', href: '/rfid-devices', icon: Cog6ToothIcon, show: role === 'admin_sekolah' },
];

const filteredNavItems = navItems.filter(item => item.show);
</script>

<template>
    <div class="min-h-screen bg-background flex">
        <!-- Sidebar -->
        <div 
            class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transform transition-transform duration-300 ease-in-out sm:translate-x-0 sm:static sm:flex-shrink-0"
            :class="{ '-translate-x-full': !isSidebarOpen, 'translate-x-0': isSidebarOpen }"
        >
            <div class="h-16 flex items-center px-6 bg-slate-950 font-bold text-xl tracking-wider">
                <span class="text-primary-light">Presensi</span>SaaS
            </div>

            <div class="p-4">
                <div class="mb-6 px-2">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Menu Utama</p>
                </div>

                <nav class="space-y-1">
                    <Link
                        v-for="item in filteredNavItems"
                        :key="item.name"
                        :href="item.href"
                        class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl hover:bg-slate-800 transition-colors"
                        :class="{ 'bg-primary text-white': $page.url.startsWith(item.href) }"
                    >
                        <component 
                            :is="item.icon" 
                            class="mr-3 flex-shrink-0 h-5 w-5 text-slate-400 group-hover:text-white transition-colors" 
                            :class="{ 'text-white': $page.url.startsWith(item.href) }"
                            aria-hidden="true" 
                        />
                        {{ item.name }}
                    </Link>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Topbar -->
            <header class="glass sticky top-0 z-40 h-16 flex items-center justify-between px-4 sm:px-6 shadow-sm border-b border-slate-200">
                <button 
                    @click="isSidebarOpen = !isSidebarOpen" 
                    class="sm:hidden p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none"
                >
                    <Bars3Icon class="h-6 w-6" />
                </button>

                <div class="flex-1 px-4 flex justify-between">
                    <div class="flex-1 flex">
                        <!-- Search or Breadcrumbs can go here -->
                    </div>
                    <div class="ml-4 flex items-center md:ml-6 gap-4">
                        <span class="text-sm font-medium text-slate-700 hidden sm:block">
                            Halo, {{ user?.name }}
                        </span>
                        
                        <Link 
                            href="/logout" 
                            method="post" 
                            as="button"
                            class="p-2 rounded-full text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors"
                        >
                            <ArrowRightOnRectangleIcon class="h-5 w-5" />
                        </Link>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 relative overflow-y-auto focus:outline-none p-4 sm:p-6 lg:p-8">
                <div v-motion :initial="{ opacity: 0, y: 10 }" :enter="{ opacity: 1, y: 0, transition: { duration: 300 } }">
                    <slot />
                </div>
            </main>
        </div>
        
        <!-- Mobile Sidebar Overlay -->
        <div 
            v-if="isSidebarOpen" 
            @click="isSidebarOpen = false"
            class="fixed inset-0 bg-slate-900/50 z-40 sm:hidden backdrop-blur-sm"
        ></div>
    </div>
</template>
