<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
    UsersIcon, 
    FileTextIcon,
    CheckCircleIcon,
    XCircleIcon,
    AlertCircleIcon,
    ClockIcon,
    ActivityIcon,
    SchoolIcon,
    ArrowRightIcon
} from 'lucide-vue-next';

const props = defineProps({
    classroom: Object,
    stats: Object,
    todayAttendance: Object,
});

const getStatusBadge = (status) => {
    switch (status) {
        case 'hadir': return 'bg-green-100 text-green-800 border-green-200';
        case 'sakit': return 'bg-indigo-100 text-indigo-800 border-indigo-200';
        case 'izin': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
        case 'alpha': return 'bg-red-100 text-red-800 border-red-200';
        case 'terlambat': return 'bg-orange-100 text-orange-800 border-orange-200';
        default: return 'bg-slate-100 text-slate-600 border-slate-200';
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

// Calculate total attendance today
const totalAttendanceToday = props.todayAttendance ? Object.values(props.todayAttendance).reduce((a, b) => a + b, 0) : 0;
const statusKeys = ['hadir', 'sakit', 'izin', 'alpha', 'terlambat'];

</script>

<template>
    <AppLayout>
        <Head title="Guru Dashboard" />

        <div class="mb-6">
            <h1 class="text-[22px] font-medium text-slate-900">Dashboard Guru</h1>
            <p class="text-[13px] text-slate-500 mt-1">Pantau aktivitas siswa kelas perwalian Anda.</p>
        </div>

        <div v-if="!classroom" class="bg-white border border-slate-200 p-8 rounded-[12px] flex flex-col items-center justify-center text-center">
            <SchoolIcon class="w-8 h-8 mb-3 text-slate-400" />
            <h2 class="text-[16px] font-medium text-slate-900 mb-1">Belum Ada Kelas Perwalian</h2>
            <p class="text-[13px] text-slate-500">Anda belum ditugaskan sebagai wali kelas untuk tahun ajaran ini. Silakan hubungi bagian kurikulum.</p>
        </div>

        <div v-else>
            <!-- Wali Kelas Banner -->
            <div class="bg-white border border-slate-200 rounded-[12px] p-6 mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center">
                        <UsersIcon class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-[12px] font-medium text-slate-500 uppercase tracking-wide">Wali Kelas</p>
                        <h2 class="text-[20px] font-medium text-slate-900">{{ classroom.name }}</h2>
                    </div>
                </div>
                <Link :href="route('teacher.students.index')" class="flex items-center gap-2 px-4 py-2 bg-indigo-500 text-white text-[13px] font-medium rounded-lg hover:bg-indigo-600 transition-colors">
                    Lihat Data Siswa <ArrowRightIcon class="w-4 h-4" />
                </Link>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-white border border-slate-200 rounded-[12px] p-5 flex items-center gap-4">
                    <div class="p-2 bg-indigo-50 text-indigo-500 rounded-lg">
                        <UsersIcon class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-[12px] font-medium text-slate-500 uppercase tracking-wide">Total Siswa Kelas</p>
                        <h3 class="text-[28px] font-medium text-slate-900 leading-none mt-1">{{ stats.total_students }}</h3>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-[12px] p-5 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="p-2 bg-yellow-50 text-yellow-600 rounded-lg">
                            <FileTextIcon class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="text-[12px] font-medium text-slate-500 uppercase tracking-wide">Surat Izin Menunggu</p>
                            <h3 class="text-[28px] font-medium text-slate-900 leading-none mt-1">{{ stats.pending_leaves }}</h3>
                        </div>
                    </div>
                    <Link :href="route('teacher.leave-requests.index')" class="p-2 bg-slate-50 hover:bg-slate-100 rounded-lg text-slate-400 hover:text-indigo-500 transition-colors">
                        <ArrowRightIcon class="w-5 h-5" />
                    </Link>
                </div>
            </div>

            <!-- Today's Attendance -->
            <div class="bg-white border border-slate-200 rounded-[12px] p-6">
                <div class="flex items-center justify-between mb-5 border-b border-slate-100 pb-3">
                    <h2 class="text-[16px] font-medium text-slate-900">Ringkasan Kehadiran Kelas Hari Ini</h2>
                    <span class="px-2.5 py-0.5 bg-slate-100 text-slate-600 text-[12px] font-medium rounded-full">
                        {{ totalAttendanceToday }} / {{ stats.total_students }} Siswa
                    </span>
                </div>

                <div v-if="totalAttendanceToday === 0" class="py-8 flex flex-col items-center justify-center text-slate-400 text-center">
                    <ClockIcon class="w-8 h-8 mb-2 text-slate-300" />
                    <p class="text-[13px]">Belum ada data kehadiran siswa di kelas Anda hari ini.</p>
                </div>

                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div v-for="key in statusKeys" :key="key" class="p-4 bg-slate-50 rounded-lg border border-slate-100 flex flex-col">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] border" :class="getStatusBadge(key)">
                                <component :is="getStatusIcon(key)" class="w-3 h-3" />
                            </div>
                            <p class="text-[13px] font-medium text-slate-700">{{ getStatusName(key) }}</p>
                        </div>
                        <div class="text-[24px] font-medium text-slate-900">
                            {{ todayAttendance[key] || 0 }} <span class="text-[11px] text-slate-400 font-normal uppercase tracking-wide">Siswa</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
