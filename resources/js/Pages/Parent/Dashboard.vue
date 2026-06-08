<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
    UserCircleIcon,
    CalendarCheckIcon,
    FileTextIcon,
    CheckCircleIcon,
    XCircleIcon,
    AlertCircleIcon,
    ClockIcon,
    ActivityIcon,
    ArrowRightIcon
} from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    children: Array,
});

// For multiple children, keep track of which tab is active
const activeTab = ref(0);

const getStatusColor = (status) => {
    switch (status) {
        case 'hadir': return 'text-green-600 bg-green-50';
        case 'sakit': return 'text-indigo-600 bg-indigo-50';
        case 'izin': return 'text-yellow-600 bg-yellow-50';
        case 'alpha': return 'text-red-600 bg-red-50';
        case 'terlambat': return 'text-orange-600 bg-orange-50';
        default: return 'text-slate-600 bg-slate-50';
    }
};

const getStatusBorder = (status) => {
    switch (status) {
        case 'hadir': return 'border-green-200';
        case 'sakit': return 'border-indigo-200';
        case 'izin': return 'border-yellow-200';
        case 'alpha': return 'border-red-200';
        case 'terlambat': return 'border-orange-200';
        default: return 'border-slate-200';
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

const formatDate = (dateString) => {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
};

const formatTime = (timeString) => {
    if (!timeString) return '-';
    // If it's a full datetime, extract time
    if (timeString.includes('T') || timeString.includes(' ')) {
        const d = new Date(timeString);
        return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }
    // If it's just time 'HH:mm:ss'
    return timeString.substring(0, 5);
};
</script>

<template>
    <AppLayout>
        <Head title="Dashboard Orang Tua" />

        <div class="mb-6">
            <h1 class="text-[22px] font-medium text-slate-900 tracking-tight">Dashboard Orang Tua</h1>
            <p class="text-[13px] text-slate-500 mt-1">Pantau kehadiran dan aktivitas anak Anda di sekolah.</p>
        </div>

        <div v-if="!children || children.length === 0" class="bg-white border border-slate-200 p-8 rounded-[12px] flex flex-col items-center justify-center text-center">
            <UserCircleIcon class="w-10 h-10 mb-3 text-slate-400" />
            <h2 class="text-[16px] font-medium text-slate-900 mb-1">Belum Ada Data Anak</h2>
            <p class="text-[13px] text-slate-500">Akun Anda belum dihubungkan dengan data siswa manapun. Silakan hubungi tata usaha sekolah.</p>
        </div>

        <div v-else>
            <!-- Child Selector Tabs -->
            <div v-if="children.length > 1" class="flex overflow-x-auto gap-2 mb-6 pb-1 no-scrollbar">
                <button 
                    v-for="(childData, index) in children" :key="childData.student.id"
                    @click="activeTab = index"
                    class="px-4 py-2 rounded-lg font-medium text-[13px] whitespace-nowrap transition-colors border"
                    :class="activeTab === index 
                        ? 'bg-indigo-50 text-indigo-700 border-indigo-200' 
                        : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                >
                    {{ childData.student.name }}
                </button>
            </div>

            <div v-for="(childData, index) in children" :key="childData.student.id" v-show="activeTab === index">
                
                <!-- Child Profile Banner -->
                <div class="bg-white border border-slate-200 rounded-[12px] p-6 mb-6 flex flex-col sm:flex-row items-center sm:justify-between gap-6">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 w-full sm:w-auto">
                        <div class="w-16 h-16 bg-slate-100 rounded-full border border-slate-200 flex items-center justify-center shrink-0">
                            <UserCircleIcon class="w-8 h-8 text-slate-400" />
                        </div>
                        <div class="text-center sm:text-left">
                            <h2 class="text-[20px] font-medium text-slate-900">{{ childData.student.name }}</h2>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 text-[13px] text-slate-500 mt-1">
                                <span>NIS: <span class="font-medium text-slate-700">{{ childData.student.nis }}</span></span>
                                <span class="hidden sm:inline">•</span>
                                <span>Kelas: <span class="font-medium text-slate-700">{{ childData.student.classroom?.name || '-' }}</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="w-full sm:w-auto">
                        <Link :href="route('parent.leave-requests.create')" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-5 py-2 bg-indigo-500 text-white hover:bg-indigo-600 rounded-lg text-[13px] font-medium transition-colors border border-transparent">
                            <FileTextIcon class="w-4 h-4" /> Ajukan Surat Izin
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Last 30 Days Timeline -->
                    <div class="lg:col-span-2">
                        <div class="bg-white border border-slate-200 rounded-[12px] p-6 h-full">
                            <div class="flex items-center justify-between mb-5 border-b border-slate-100 pb-3">
                                <h2 class="text-[16px] font-medium text-slate-900">Riwayat Kehadiran (30 Hari)</h2>
                                <CalendarCheckIcon class="w-5 h-5 text-slate-400" />
                            </div>

                            <div v-if="childData.attendance.length === 0" class="py-12 text-center text-[13px] text-slate-400">
                                <p>Belum ada riwayat kehadiran tercatat.</p>
                            </div>

                            <div v-else class="space-y-3">
                                <div v-for="record in childData.attendance.slice(0, 7)" :key="record.id" 
                                     class="flex gap-4 p-4 rounded-lg border bg-white"
                                     :class="getStatusBorder(record.status)">
                                     
                                    <div class="shrink-0 w-12 h-12 rounded-lg flex items-center justify-center font-medium text-[11px] uppercase tracking-wide border border-transparent" :class="getStatusColor(record.status)">
                                        {{ record.status === 'hadir' ? 'HDR' : 
                                           record.status === 'sakit' ? 'SKT' : 
                                           record.status === 'izin' ? 'IZN' : 
                                           record.status === 'terlambat' ? 'TLB' : 'ALP' }}
                                    </div>
                                    
                                    <div class="flex-1 min-w-0 flex flex-col justify-center">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-1 gap-1">
                                            <p class="font-medium text-[14px] text-slate-900 truncate">{{ formatDate(record.date) }}</p>
                                            <span class="inline-flex shrink-0 px-2 py-0.5 rounded-full text-[11px] font-medium uppercase tracking-wide" :class="getStatusColor(record.status)">
                                                {{ getStatusName(record.status) }}
                                            </span>
                                        </div>
                                        
                                        <div v-if="record.status === 'hadir' || record.status === 'terlambat'" class="flex items-center gap-4 text-[12px] text-slate-500">
                                            <span class="flex items-center gap-1.5"><ArrowRightIcon class="w-3.5 h-3.5 text-green-500" /> Masuk: <span class="font-medium text-slate-700">{{ formatTime(record.time_in) }}</span></span>
                                            <span class="flex items-center gap-1.5" v-if="record.time_out"><ArrowRightIcon class="w-3.5 h-3.5 text-orange-500" /> Pulang: <span class="font-medium text-slate-700">{{ formatTime(record.time_out) }}</span></span>
                                        </div>
                                        <div v-else-if="record.notes" class="text-[12px] text-slate-500 line-clamp-1 italic">
                                            "{{ record.notes }}"
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-if="childData.attendance.length > 7" class="pt-3 text-center">
                                    <button class="text-[13px] font-medium text-indigo-500 hover:text-indigo-700 transition-colors">
                                        Lihat Semua Riwayat ({{ childData.attendance.length }})
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white border border-slate-200 rounded-[12px] p-6 h-full">
                            <h2 class="text-[16px] font-medium text-slate-900 mb-5 border-b border-slate-100 pb-3">Rekap Bulan Ini</h2>
                            
                            <div v-if="childData.monthlySummary && childData.monthlySummary.length > 0">
                                <!-- Take current month (index 0 because it was pushed in loop going down, but wait, loop was 5 to 0. So index 5 is the current month.) -->
                                <div class="space-y-3 mb-8" v-if="childData.monthlySummary[5]">
                                    <div class="flex justify-between items-center p-3 bg-green-50 text-green-700 rounded-lg border border-green-100">
                                        <span class="font-medium text-[13px]">Hadir</span>
                                        <span class="text-[18px] font-medium">{{ childData.monthlySummary[5].data['hadir'] || 0 }}</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-indigo-50 text-indigo-700 rounded-lg border border-indigo-100">
                                        <span class="font-medium text-[13px]">Sakit / Izin</span>
                                        <span class="text-[18px] font-medium">{{ (childData.monthlySummary[5].data['sakit'] || 0) + (childData.monthlySummary[5].data['izin'] || 0) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-red-50 text-red-700 rounded-lg border border-red-100">
                                        <span class="font-medium text-[13px]">Tanpa Keterangan</span>
                                        <span class="text-[18px] font-medium">{{ childData.monthlySummary[5].data['alpha'] || 0 }}</span>
                                    </div>
                                </div>
                                
                                <h3 class="text-[12px] font-medium text-slate-500 uppercase tracking-wide mb-3">Bulan Sebelumnya</h3>
                                <div class="space-y-2">
                                    <!-- Show months 4 to 0 (previous months) -->
                                    <div v-for="i in 5" :key="i" class="flex justify-between items-center p-2 border-b border-slate-100 last:border-0">
                                        <span class="font-medium text-[13px] text-slate-700">{{ childData.monthlySummary[5-i].month }}</span>
                                        <div class="flex items-center gap-2 text-[12px]">
                                            <span class="w-6 text-center font-medium text-green-600">{{ childData.monthlySummary[5-i].data['hadir'] || 0 }}</span>
                                            <span class="w-6 text-center font-medium text-yellow-600">{{ (childData.monthlySummary[5-i].data['sakit'] || 0) + (childData.monthlySummary[5-i].data['izin'] || 0) }}</span>
                                            <span class="w-6 text-center font-medium text-red-500">{{ childData.monthlySummary[5-i].data['alpha'] || 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-[13px] text-slate-400 text-center py-4">Belum ada data bulanan.</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
