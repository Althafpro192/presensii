<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { FileTextIcon, CheckCircleIcon, XCircleIcon, ClockIcon, DownloadIcon } from 'lucide-vue-next';

const props = defineProps({
    leaveRequests: Object,
    classroom: Object,
    filters: Object,
});

const filterForm = useForm({
    status: props.filters.status || '',
});

const applyFilter = () => {
    filterForm.get(route('teacher.leave-requests.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const approve = (id) => {
    if (confirm('Anda yakin ingin menyetujui izin ini?')) {
        router.put(route('teacher.leave-requests.approve', id));
    }
};

const reject = (id) => {
    if (confirm('Anda yakin ingin menolak izin ini?')) {
        router.put(route('teacher.leave-requests.reject', id));
    }
};

const getStatusBadge = (status) => {
    const badges = {
        'pending': 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'approved': 'bg-green-100 text-green-800 border-green-200',
        'rejected': 'bg-red-100 text-red-800 border-red-200',
    };
    return badges[status] || 'bg-slate-100 text-slate-700 border-slate-200';
};

const getStatusIcon = (status) => {
    if (status === 'pending') return ClockIcon;
    if (status === 'approved') return CheckCircleIcon;
    if (status === 'rejected') return XCircleIcon;
    return FileTextIcon;
};

const getStatusLabel = (status) => {
    const labels = {
        'pending': 'Menunggu Persetujuan',
        'approved': 'Disetujui',
        'rejected': 'Ditolak',
    };
    return labels[status] || status;
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(date);
};
</script>

<template>
    <AppLayout>
        <Head title="Pengajuan Izin Siswa" />

        <div v-if="!classroom" class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-[13px] font-medium">
            Anda belum ditugaskan sebagai wali kelas untuk tahun ajaran ini.
        </div>

        <div v-else>
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-[22px] font-medium text-slate-900 tracking-tight">Pengajuan Izin Siswa</h1>
                    <p class="text-[13px] text-slate-500 mt-1">Kelas {{ classroom.name }}</p>
                </div>

                <div>
                    <select v-model="filterForm.status" @change="applyFilter"
                        class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="">Semua Status</option>
                        <option value="pending">Menunggu Persetujuan</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
            </div>

            <div class="space-y-4">
                <div v-for="request in leaveRequests.data" :key="request.id"
                    class="bg-white border border-slate-200 rounded-[12px] overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row justify-between gap-6">
                            <div class="flex gap-4">
                                <div class="mt-1">
                                    <component :is="getStatusIcon(request.status)" 
                                        :class="[
                                            'w-7 h-7',
                                            request.status === 'pending' ? 'text-yellow-500' : 
                                            (request.status === 'approved' ? 'text-green-500' : 'text-red-500')
                                        ]" />
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="text-[16px] font-medium text-slate-900">{{ request.student?.name || 'Siswa tidak diketahui' }}</h3>
                                        <span :class="['px-2.5 py-0.5 rounded-full text-[11px] font-medium uppercase tracking-wide border', getStatusBadge(request.status)]">
                                            {{ getStatusLabel(request.status) }}
                                        </span>
                                    </div>
                                    <div class="text-[13px] text-slate-600 mb-4 space-y-1">
                                        <p><span class="font-medium text-slate-700">Jenis:</span> <span class="capitalize">{{ request.type }}</span></p>
                                        <p><span class="font-medium text-slate-700">Tanggal:</span> {{ formatDate(request.date) }}</p>
                                        <p><span class="font-medium text-slate-700">Orang Tua:</span> {{ request.parent?.name || '-' }}</p>
                                    </div>
                                    <div class="bg-slate-50 border border-slate-100 rounded-lg p-4 text-[13px] text-slate-700">
                                        <p class="font-medium mb-1 text-slate-900">Alasan:</p>
                                        <p>{{ request.reason }}</p>
                                    </div>
                                    
                                    <div v-if="request.attachment" class="mt-4">
                                        <a :href="`/storage/${request.attachment}`" target="_blank" class="inline-flex items-center gap-1.5 text-[13px] text-indigo-500 hover:text-indigo-700 font-medium transition-colors">
                                            <DownloadIcon class="w-4 h-4" /> Lihat Lampiran
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-if="request.status === 'pending'" class="flex flex-col gap-2 shrink-0 sm:w-32">
                                <button @click="approve(request.id)" class="w-full inline-flex justify-center items-center gap-1.5 px-4 py-2 bg-green-50 text-green-700 hover:bg-green-100 text-[13px] font-medium rounded-lg transition-colors border border-green-200">
                                    <CheckCircleIcon class="w-4 h-4" /> Setujui
                                </button>
                                <button @click="reject(request.id)" class="w-full inline-flex justify-center items-center gap-1.5 px-4 py-2 bg-red-50 text-red-700 hover:bg-red-100 text-[13px] font-medium rounded-lg transition-colors border border-red-200">
                                    <XCircleIcon class="w-4 h-4" /> Tolak
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="leaveRequests.data.length === 0" class="text-center p-10 bg-white border border-slate-200 rounded-[12px]">
                    <FileTextIcon class="mx-auto h-10 w-10 text-slate-300 mb-3" />
                    <h3 class="text-[14px] font-medium text-slate-900">Tidak ada pengajuan izin</h3>
                    <p class="mt-1 text-[13px] text-slate-500">Saat ini tidak ada pengajuan izin yang sesuai dengan filter.</p>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="leaveRequests.links && leaveRequests.links.length > 3" class="mt-6 flex justify-center">
                <div class="flex gap-1">
                    <template v-for="(link, k) in leaveRequests.links" :key="k">
                        <Link v-if="link.url" :href="link.url" 
                            v-html="link.label"
                            class="px-3 py-1.5 rounded-lg text-[13px] font-medium border transition-colors"
                            :class="link.active ? 'bg-indigo-500 text-white border-indigo-500' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'" />
                        <span v-else class="px-3 py-1.5 rounded-lg text-[13px] font-medium border bg-slate-50 text-slate-400 border-slate-200" v-html="link.label"></span>
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
