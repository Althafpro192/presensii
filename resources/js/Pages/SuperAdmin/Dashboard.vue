<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
    BuildingOfficeIcon, 
    UsersIcon, 
    AcademicCapIcon,
    CheckBadgeIcon
} from '@heroicons/vue/24/outline';

defineProps({
    stats: Object,
    schools: Array,
});
</script>

<template>
    <AppLayout>
        <Head title="Super Admin Dashboard" />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Dashboard Super Admin</h1>
            <p class="text-slate-500">Ringkasan sistem presensi multi-sekolah.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="glass p-6 rounded-2xl flex items-center gap-4 border-l-4 border-l-primary">
                <div class="p-3 bg-primary/10 rounded-xl text-primary">
                    <BuildingOfficeIcon class="w-8 h-8" />
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Total Sekolah</p>
                    <p class="text-2xl font-bold text-slate-800">{{ stats.total_schools }}</p>
                </div>
            </div>
            
            <div class="glass p-6 rounded-2xl flex items-center gap-4 border-l-4 border-l-green-500">
                <div class="p-3 bg-green-500/10 rounded-xl text-green-500">
                    <CheckBadgeIcon class="w-8 h-8" />
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Sekolah Aktif</p>
                    <p class="text-2xl font-bold text-slate-800">{{ stats.active_schools }}</p>
                </div>
            </div>

            <div class="glass p-6 rounded-2xl flex items-center gap-4 border-l-4 border-l-blue-500">
                <div class="p-3 bg-blue-500/10 rounded-xl text-blue-500">
                    <UsersIcon class="w-8 h-8" />
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Total Siswa</p>
                    <p class="text-2xl font-bold text-slate-800">{{ stats.total_students }}</p>
                </div>
            </div>

            <div class="glass p-6 rounded-2xl flex items-center gap-4 border-l-4 border-l-orange-500">
                <div class="p-3 bg-orange-500/10 rounded-xl text-orange-500">
                    <AcademicCapIcon class="w-8 h-8" />
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Total Guru</p>
                    <p class="text-2xl font-bold text-slate-800">{{ stats.total_teachers }}</p>
                </div>
            </div>
        </div>

        <div class="glass rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800">Daftar Sekolah</h3>
                <Link href="/super-admin/schools" class="text-primary text-sm font-medium hover:underline">Lihat Semua</Link>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Sekolah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Subdomain</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Siswa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <tr v-for="school in schools" :key="school.id" class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-slate-200 flex-shrink-0 flex items-center justify-center font-bold text-slate-500 overflow-hidden">
                                        <img v-if="school.logo" :src="school.logo" class="h-10 w-10 object-cover" />
                                        <span v-else>{{ school.name.charAt(0) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-slate-900">{{ school.name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ school.slug }}.domain.com
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ school.students_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                      :class="school.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                    {{ school.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="schools.length === 0">
                            <td colspan="4" class="px-6 py-10 text-center text-slate-500">
                                Belum ada data sekolah.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
