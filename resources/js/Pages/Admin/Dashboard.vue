<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
    UsersIcon, 
    BookOpenIcon, 
    GraduationCapIcon, 
    FileTextIcon,
    CheckCircleIcon,
    XCircleIcon,
    AlertCircleIcon,
    ClockIcon,
    ActivityIcon
} from 'lucide-vue-next';

const props = defineProps({
    stats: Object,
    todayAttendance: Object,
    monthlyTrend: Array,
});

const getStatusBadge = (status) => {
    switch (status) {
        case 'hadir': return 'bg-green-100 text-green-800';
        case 'sakit': return 'bg-indigo-100 text-indigo-800';
        case 'izin': return 'bg-yellow-100 text-yellow-800';
        case 'alpha': return 'bg-red-100 text-red-800';
        case 'terlambat': return 'bg-orange-100 text-orange-800';
        default: return 'bg-slate-100 text-slate-600';
    }
};

const getStatusIcon = (status) => {
    switch (status) {
        case 'hadir': return CheckCircleIcon;
        case 'sakit': return ActivityIcon;
        case 'izin': return FileTextIcon;
        case 'alpha': return XCircleIcon;
        case 'terlambat': return ClockIcon;
        default: return AlertCircleIcon;
    }
};

const getStatusName = (status) => {
    switch (status) {
        case 'hadir': return 'Hadir';
        case 'sakit': return 'Sakit';
        case 'izin': return 'Izin';
        case 'alpha': return 'Tanpa Keterangan';
        case 'terlambat': return 'Terlambat';
        default: return status;
    }
};

const totalAttendanceToday = Object.values(props.todayAttendance).reduce((a, b) => a + b, 0);
const statusKeys = ['hadir', 'sakit', 'izin', 'alpha', 'terlambat'];

</script>

<template>
    <AppLayout>
        <Head title="Admin Dashboard" />

        <div class="mb-6">
            <h1 class="text-[22px] font-medium text-slate-900">Dashboard Sekolah</h1>
            <p class="text-[13px] text-slate-500 mt-1">Ringkasan aktivitas dan kehadiran siswa hari ini.</p>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white border border-slate-200 rounded-[12px] p-5 flex items-center gap-4">
                <div class="p-2 bg-indigo-50 text-indigo-500 rounded-lg">
                    <UsersIcon class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[12px] font-medium text-slate-500 uppercase tracking-wide">Total Siswa</p>
                    <h3 class="text-[28px] font-medium text-slate-900 leading-none mt-1">{{ stats.total_students }}</h3>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-[12px] p-5 flex items-center gap-4">
                <div class="p-2 bg-indigo-50 text-indigo-500 rounded-lg">
                    <BookOpenIcon class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[12px] font-medium text-slate-500 uppercase tracking-wide">Total Kelas</p>
                    <h3 class="text-[28px] font-medium text-slate-900 leading-none mt-1">{{ stats.total_classrooms }}</h3>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-[12px] p-5 flex items-center gap-4">
                <div class="p-2 bg-indigo-50 text-indigo-500 rounded-lg">
                    <GraduationCapIcon class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[12px] font-medium text-slate-500 uppercase tracking-wide">Total Guru</p>
                    <h3 class="text-[28px] font-medium text-slate-900 leading-none mt-1">{{ stats.total_teachers }}</h3>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-[12px] p-5 flex items-center gap-4">
                <div class="p-2 bg-indigo-50 text-indigo-500 rounded-lg">
                    <FileTextIcon class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-[12px] font-medium text-slate-500 uppercase tracking-wide">Izin Pending</p>
                    <h3 class="text-[28px] font-medium text-slate-900 leading-none mt-1">{{ stats.pending_leaves }}</h3>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Today's Attendance Summary -->
            <div class="bg-white border border-slate-200 rounded-[12px] p-5 lg:col-span-1">
                <div class="flex items-center justify-between mb-5 border-b border-slate-100 pb-3">
                    <h2 class="text-[16px] font-medium text-slate-900">Kehadiran Hari Ini</h2>
                    <span class="px-2.5 py-0.5 bg-slate-100 text-slate-600 text-[12px] font-medium rounded-full">
                        {{ totalAttendanceToday }} Tercatat
                    </span>
                </div>

                <div v-if="totalAttendanceToday === 0" class="py-8 flex flex-col items-center text-slate-400 text-center">
                    <ClockIcon class="w-8 h-8 mb-2 text-slate-300" />
                    <p class="text-[13px]">Belum ada data kehadiran hari ini.</p>
                </div>

                <div v-else class="space-y-3">
                    <div v-for="key in statusKeys" :key="key">
                        <div v-if="todayAttendance[key] !== undefined" class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-[12px]" :class="getStatusBadge(key)">
                                    <component :is="getStatusIcon(key)" class="w-4 h-4" />
                                </div>
                                <div>
                                    <p class="text-[14px] font-medium text-slate-900">{{ getStatusName(key) }}</p>
                                </div>
                            </div>
                            <div class="text-[16px] font-medium text-slate-900">
                                {{ todayAttendance[key] }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-5">
                    <Link :href="route('admin.students.index')" class="flex items-center justify-center py-2 px-4 border border-slate-200 hover:bg-slate-50 text-slate-700 text-[13px] font-medium rounded-lg transition-colors">
                        Lihat Data Siswa
                    </Link>
                </div>
            </div>

            <!-- Monthly Trend -->
            <div class="bg-white border border-slate-200 rounded-[12px] overflow-hidden lg:col-span-2">
                <div class="p-5 border-b border-slate-100">
                    <h2 class="text-[16px] font-medium text-slate-900">Tren Kehadiran (6 Bulan Terakhir)</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="py-3 px-5 font-medium text-[12px] uppercase tracking-wide text-slate-700 border-b border-slate-200">Bulan</th>
                                <th v-for="key in statusKeys" :key="key" class="py-3 px-5 font-medium text-[12px] uppercase tracking-wide text-slate-700 border-b border-slate-200">
                                    {{ getStatusName(key) }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="trend in monthlyTrend" :key="trend.month" class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 px-5 text-[14px] font-medium text-slate-900">{{ trend.month }}</td>
                                <td v-for="key in statusKeys" :key="key" class="py-3 px-5 text-[14px] text-slate-500">
                                    <span v-if="trend.data[key] > 0" class="px-2 py-0.5 rounded-full text-[12px] font-medium" :class="getStatusBadge(key)">
                                        {{ trend.data[key] }}
                                    </span>
                                    <span v-else>-</span>
                                </td>
                            </tr>
                            <tr v-if="monthlyTrend.length === 0">
                                <td colspan="6" class="py-10 text-center text-[13px] text-slate-400">Belum ada data historis kehadiran.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
