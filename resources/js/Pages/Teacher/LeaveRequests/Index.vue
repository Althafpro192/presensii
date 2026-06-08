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
        'pending': 'bg-amber-100 text-amber-700 border-amber-200',
        'approved': 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'rejected': 'bg-red-100 text-red-700 border-red-200',
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

        <div v-if="!classroom" class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
            Anda belum ditugaskan sebagai wali kelas untuk tahun ajaran ini.
        </div>

        <div v-else>
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Pengajuan Izin Siswa</h1>
                    <p class="text-sm text-slate-500">Kelas {{ classroom.name }}</p>
                </div>

                <div>
                    <select v-model="filterForm.status" @change="applyFilter"
                        class="rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white/70 backdrop-blur-sm text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending">Menunggu Persetujuan</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
            </div>

            <div class="space-y-4">
                <div v-for="request in leaveRequests.data" :key="request.id"
                    class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-sm rounded-2xl overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row justify-between gap-4">
                            <div class="flex gap-4">
                                <div class="mt-1">
                                    <component :is="getStatusIcon(request.status)" 
                                        :class="[
                                            'w-8 h-8',
                                            request.status === 'pending' ? 'text-amber-500' : 
                                            (request.status === 'approved' ? 'text-emerald-500' : 'text-red-500')
                                        ]" />
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="text-lg font-bold text-slate-800">{{ request.student?.name || 'Siswa tidak diketahui' }}</h3>
                                        <span :class="['px-2.5 py-0.5 rounded-full text-xs font-medium border', getStatusBadge(request.status)]">
                                            {{ getStatusLabel(request.status) }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-slate-600 mb-3 space-y-1">
                                        <p><span class="font-medium">Jenis:</span> <span class="capitalize">{{ request.type }}</span></p>
                                        <p><span class="font-medium">Tanggal:</span> {{ formatDate(request.date) }}</p>
                                        <p><span class="font-medium">Orang Tua:</span> {{ request.parent?.name || '-' }}</p>
                                    </div>
                                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm text-slate-700">
                                        <p class="font-medium mb-1">Alasan:</p>
                                        <p>{{ request.reason }}</p>
                                    </div>
                                    
                                    <div v-if="request.attachment" class="mt-4">
                                        <a :href="`/storage/${request.attachment}`" target="_blank" class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                            <DownloadIcon class="w-4 h-4" /> Lihat Lampiran
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-if="request.status === 'pending'" class="flex flex-col gap-2 shrink-0 sm:w-32">
                                <button @click="approve(request.id)" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors">
                                    <CheckCircleIcon class="w-4 h-4" /> Setujui
                                </button>
                                <button @click="reject(request.id)" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl transition-colors">
                                    <XCircleIcon class="w-4 h-4" /> Tolak
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="leaveRequests.data.length === 0" class="text-center p-8 bg-white/70 backdrop-blur-xl border border-white/40 shadow-sm rounded-2xl">
                    <FileTextIcon class="mx-auto h-12 w-12 text-slate-400 mb-3" />
                    <h3 class="text-lg font-medium text-slate-900">Tidak ada pengajuan izin</h3>
                    <p class="mt-1 text-slate-500">Saat ini tidak ada pengajuan izin yang sesuai dengan filter.</p>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="leaveRequests.links && leaveRequests.links.length > 3" class="mt-6 flex justify-center">
                <div class="flex gap-1">
                    <template v-for="(link, k) in leaveRequests.links" :key="k">
                        <Link v-if="link.url" :href="link.url" 
                            v-html="link.label"
                            class="px-3 py-1 rounded-lg text-sm border"
                            :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'" />
                        <span v-else class="px-3 py-1 rounded-lg text-sm border bg-slate-50 text-slate-400 border-slate-200" v-html="link.label"></span>
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
