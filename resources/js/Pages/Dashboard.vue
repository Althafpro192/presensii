<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    auth: Object,
    stats: Object
});

const page = usePage();
const user = page.props.auth?.user;

// Auto redirect berdasarkan role (Sesuai kode Anda sebelumnya)
if (user?.role === 'teacher') {
    router.visit('/teacher');
} else if (user?.role === 'student') {
    router.visit('/student');
} else if (user?.role === 'parent') {
    router.visit('/parent');
}

// Dummy data for visual presentation like Welcome.vue
const recentActivities = [
    { id: 1, name: 'Budi Santoso', time: '06:45 WIB', status: 'Hadir', class: 'X-IPA 1' },
    { id: 2, name: 'Siti Aminah', time: '06:50 WIB', status: 'Hadir', class: 'X-IPA 2' },
    { id: 3, name: 'Ahmad Dahlan', time: '07:15 WIB', status: 'Terlambat', class: 'XI-IPS 1' },
    { id: 4, name: 'Rina Nose', time: '07:20 WIB', status: 'Terlambat', class: 'XII-IPA 1' },
];
</script>

<template>
    <AppLayout>
        <Head title="Dashboard Utama" />

        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-[24px] font-medium text-slate-900 tracking-tight">Ringkasan Hari Ini</h1>
                <p class="text-[14px] text-slate-500 mt-1">Pantau statistik presensi dan aktivitas terbaru secara real-time.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-indigo-50 text-indigo-700 text-[13px] font-medium border border-indigo-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                    Sistem Aktif
                </span>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Total Siswa -->
            <div class="bg-white border border-slate-200 rounded-[12px] p-5 hover:border-slate-300 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-[10px] bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-[13px] font-medium text-slate-500 mb-1">Total Siswa</h3>
                    <div class="text-[28px] font-medium text-slate-900 tracking-tight">{{ stats?.total_students || '1,240' }}</div>
                </div>
            </div>

            <!-- Total Guru -->
            <div class="bg-white border border-slate-200 rounded-[12px] p-5 hover:border-slate-300 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-[10px] bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="m4 6 8-4 8 4"/><path d="m18 10 4 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-8l4-2"/><path d="M14 22v-4a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v4"/><path d="M18 5v17"/><path d="M6 5v17"/><circle cx="12" cy="9" r="2"/></svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-[13px] font-medium text-slate-500 mb-1">Total Guru</h3>
                    <div class="text-[28px] font-medium text-slate-900 tracking-tight">{{ stats?.total_teachers || '85' }}</div>
                </div>
            </div>

            <!-- Hadir Hari Ini -->
            <div class="bg-white border border-slate-200 rounded-[12px] p-5 hover:border-slate-300 transition-colors relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-24 h-24 text-green-500"><path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" /></svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-[10px] bg-green-50 text-green-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-[13px] font-medium text-slate-500 mb-1">Hadir Hari Ini</h3>
                        <div class="text-[28px] font-medium text-slate-900 tracking-tight">{{ stats?.today_attendance || '1,150' }}</div>
                    </div>
                </div>
            </div>

            <!-- Alfa / Terlambat -->
            <div class="bg-white border border-slate-200 rounded-[12px] p-5 hover:border-slate-300 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-[10px] bg-rose-50 text-rose-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-[13px] font-medium text-slate-500 mb-1">Alfa / Terlambat</h3>
                    <div class="text-[28px] font-medium text-slate-900 tracking-tight">{{ stats?.today_absent || '90' }}</div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Table -->
        <div class="bg-white border border-slate-200 rounded-[12px] overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-[15px] font-medium text-slate-900">Aktivitas Presensi Terbaru</h3>
                <button class="text-[13px] font-medium text-indigo-600 hover:text-indigo-700">Lihat Semua</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-[14px]">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-500 border-b border-slate-100">
                            <th class="px-5 py-3 font-medium">Siswa</th>
                            <th class="px-5 py-3 font-medium">Kelas</th>
                            <th class="px-5 py-3 font-medium">Waktu Tap</th>
                            <th class="px-5 py-3 font-medium text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="log in recentActivities" :key="log.id" class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3 text-slate-900 font-medium">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-[11px] font-medium text-slate-600">
                                        {{ log.name.split(' ').map(n => n[0]).join('').substring(0, 2) }}
                                    </div>
                                    {{ log.name }}
                                </div>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ log.class }}</td>
                            <td class="px-5 py-3 text-slate-600">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5 text-slate-400"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    {{ log.time }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <span v-if="log.status === 'Hadir'" class="inline-flex items-center px-2 py-1 rounded-md bg-green-50 text-green-700 text-[12px] font-medium border border-green-100">
                                    Hadir
                                </span>
                                <span v-else class="inline-flex items-center px-2 py-1 rounded-md bg-yellow-50 text-yellow-700 text-[12px] font-medium border border-yellow-100">
                                    Terlambat
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>