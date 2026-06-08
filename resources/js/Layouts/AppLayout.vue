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
    <div class="min-h-screen bg-slate-100 flex font-sans">
        <!-- Sidebar -->
        <div 
            class="fixed inset-y-0 left-0 z-50 w-[240px] bg-slate-900 text-white transform transition-transform duration-150 ease-out sm:translate-x-0 sm:static sm:flex-shrink-0"
            :class="{ '-translate-x-full': !isSidebarOpen, 'translate-x-0': isSidebarOpen }"
        >
            <div class="h-[56px] flex items-center px-6 border-b border-slate-800">
                <span class="font-medium text-[16px]">Sistem Presensi</span>
            </div>

            <div class="p-4">
                <nav class="space-y-1">
                    <Link
                        v-for="item in filteredNavItems"
                        :key="item.name"
                        :href="item.href"
                        class="group flex items-center px-3 py-2 text-[14px] font-medium rounded-lg transition-colors duration-150"
                        :class="[
                            $page.url.startsWith(item.href) 
                                ? 'bg-indigo-600 text-white' 
                                : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                        ]"
                    >
                        <component 
                            :is="item.icon" 
                            class="mr-3 flex-shrink-0 h-5 w-5 transition-colors" 
                            :class="[
                                $page.url.startsWith(item.href) 
                                    ? 'text-white' 
                                    : 'text-slate-400 group-hover:text-white'
                            ]"
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
            <header class="bg-white sticky top-0 z-40 h-[56px] flex items-center justify-between px-4 sm:px-6 border-b border-slate-200">
                <button 
                    @click="isSidebarOpen = !isSidebarOpen" 
                    class="sm:hidden p-2 -ml-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-50 focus:outline-none"
                >
                    <Bars3Icon class="h-6 w-6" />
                </button>

                <div class="flex-1 flex justify-end items-center gap-4">
                    <span class="text-[14px] text-slate-900 hidden sm:block">
                        {{ user?.name }}
                    </span>
                    
                    <Link 
                        href="/logout" 
                        method="post" 
                        as="button"
                        class="p-2 -mr-2 rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                        title="Logout"
                    >
                        <ArrowRightOnRectangleIcon class="h-5 w-5" />
                    </Link>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6 sm:p-8 bg-slate-100">
                <slot />
            </main>
        </div>
        
        <!-- Mobile Sidebar Overlay -->
        <div 
            v-if="isSidebarOpen" 
            @click="isSidebarOpen = false"
            class="fixed inset-0 bg-slate-900/50 z-40 sm:hidden"
        ></div>
    </div>
</template>
