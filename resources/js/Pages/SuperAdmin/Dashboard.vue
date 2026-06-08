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
            <h1 class="text-[22px] font-medium text-slate-900">Dashboard Super Admin</h1>
            <p class="text-[13px] text-slate-500 mt-1">Ringkasan sistem presensi multi-sekolah.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white border border-slate-200 rounded-[12px] p-5 flex items-center gap-4">
                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-500">
                    <BuildingOfficeIcon class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[12px] text-slate-500 font-medium uppercase tracking-wide">Total Sekolah</p>
                    <p class="text-[28px] font-medium text-slate-900 leading-none mt-1">{{ stats.total_schools }}</p>
                </div>
            </div>
            
            <div class="bg-white border border-slate-200 rounded-[12px] p-5 flex items-center gap-4">
                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-500">
                    <CheckBadgeIcon class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[12px] text-slate-500 font-medium uppercase tracking-wide">Sekolah Aktif</p>
                    <p class="text-[28px] font-medium text-slate-900 leading-none mt-1">{{ stats.active_schools }}</p>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-[12px] p-5 flex items-center gap-4">
                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-500">
                    <UsersIcon class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[12px] text-slate-500 font-medium uppercase tracking-wide">Total Siswa</p>
                    <p class="text-[28px] font-medium text-slate-900 leading-none mt-1">{{ stats.total_students }}</p>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-[12px] p-5 flex items-center gap-4">
                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-500">
                    <AcademicCapIcon class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[12px] text-slate-500 font-medium uppercase tracking-wide">Total Guru</p>
                    <p class="text-[28px] font-medium text-slate-900 leading-none mt-1">{{ stats.total_teachers }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-[12px] overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-[16px] font-medium text-slate-900">Daftar Sekolah</h3>
                <Link href="/super-admin/schools" class="text-indigo-500 text-[13px] hover:underline">Lihat Semua</Link>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="py-3 px-5 font-medium text-[12px] text-slate-700 border-b border-slate-200">NAMA SEKOLAH</th>
                            <th class="py-3 px-5 font-medium text-[12px] text-slate-700 border-b border-slate-200">SUBDOMAIN</th>
                            <th class="py-3 px-5 font-medium text-[12px] text-slate-700 border-b border-slate-200">SISWA</th>
                            <th class="py-3 px-5 font-medium text-[12px] text-slate-700 border-b border-slate-200">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="school in schools" :key="school.id" class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-5 text-[14px] text-slate-900">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-[12px] font-medium text-slate-600">
                                        <img v-if="school.logo" :src="school.logo" class="w-full h-full rounded-full object-cover" />
                                        <span v-else>{{ school.name.charAt(0).toUpperCase() }}</span>
                                    </div>
                                    {{ school.name }}
                                </div>
                            </td>
                            <td class="py-3 px-5 text-[14px] text-slate-500">
                                {{ school.slug }}.domain.com
                            </td>
                            <td class="py-3 px-5 text-[14px] text-slate-500">
                                {{ school.students_count }}
                            </td>
                            <td class="py-3 px-5">
                                <span class="px-2.5 py-0.5 rounded-full text-[12px] font-medium" 
                                      :class="school.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                    {{ school.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="schools.length === 0">
                            <td colspan="4" class="py-8 text-center text-[13px] text-slate-400">
                                Belum ada data sekolah.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
