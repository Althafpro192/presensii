<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { CreditCardIcon, AlertTriangleIcon, CheckCircleIcon } from 'lucide-vue-next';

const props = defineProps({
    assignments: Object,
});

const destroy = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus kartu ini dari siswa? Ini akan menonaktifkan kartu tersebut.')) {
        router.delete(route('admin.rfid-cards.destroy', id), {
            preserveScroll: true
        });
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const getStatusBadge = (status) => {
    if (status === 'active') return 'bg-green-100 text-green-800 border border-green-200';
    if (status === 'lost') return 'bg-red-100 text-red-800 border border-red-200';
    if (status === 'blocked') return 'bg-slate-100 text-slate-800 border border-slate-200';
    return 'bg-slate-100 text-slate-700';
};
</script>

<template>
    <AppLayout>
        <Head title="Manajemen Kartu RFID" />

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-[22px] font-medium text-slate-900 tracking-tight">Riwayat & Status Kartu RFID</h1>
                <p class="text-[13px] text-slate-500 mt-1">Pantau kartu yang ditugaskan kepada siswa dan kelola kartu hilang.</p>
            </div>
            
            <Link :href="route('admin.bulk-write.index')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 border border-transparent rounded-lg font-medium text-[13px] text-white hover:bg-indigo-600 transition-colors">
                <CreditCardIcon class="w-4 h-4" />
                Tulis Kartu Massal (Bulk Write)
            </Link>
        </div>

        <div class="bg-white border border-slate-200 rounded-[12px] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Siswa</th>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">UID Kartu</th>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200 text-center">Status</th>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Waktu Penugasan</th>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="assignment in assignments.data" :key="assignment.id" class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3 whitespace-nowrap">
                                <div class="text-[14px] font-medium text-slate-900">{{ assignment.student?.name }}</div>
                                <div class="text-[13px] text-slate-500">{{ assignment.student?.classroom?.name || '-' }}</div>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap font-mono text-[13px] text-slate-600">
                                {{ assignment.card_uid }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-center">
                                <span :class="['inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium uppercase tracking-wide', getStatusBadge(assignment.status)]">
                                    <CheckCircleIcon v-if="assignment.status === 'active'" class="w-3 h-3" />
                                    <AlertTriangleIcon v-if="assignment.status === 'lost'" class="w-3 h-3" />
                                    {{ assignment.status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-[13px] text-slate-600">
                                {{ formatDate(assignment.assigned_at) }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-right text-[13px] font-medium">
                                <button @click="destroy(assignment.id)" class="text-red-600 hover:text-red-900 font-medium bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors border border-transparent">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="assignments.data.length === 0">
                            <td colspan="5" class="px-5 py-10 text-center text-[13px] text-slate-500">
                                Belum ada riwayat kartu RFID.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div v-if="assignments.links && assignments.links.length > 3" class="px-5 py-3 border-t border-slate-100 flex justify-center">
                <div class="flex gap-1">
                    <template v-for="(link, k) in assignments.links" :key="k">
                        <Link v-if="link.url" :href="link.url" 
                            v-html="link.label"
                            class="px-3 py-1.5 rounded-lg text-[13px] font-medium border"
                            :class="link.active ? 'bg-indigo-500 text-white border-indigo-500' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'" />
                        <span v-else class="px-3 py-1.5 rounded-lg text-[13px] font-medium border bg-slate-50 text-slate-400 border-slate-200" v-html="link.label"></span>
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
