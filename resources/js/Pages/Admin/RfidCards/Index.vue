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
    if (status === 'active') return 'bg-emerald-100 text-emerald-800 border-emerald-200';
    if (status === 'lost') return 'bg-red-100 text-red-800 border-red-200';
    if (status === 'blocked') return 'bg-slate-200 text-slate-800 border-slate-300';
    return 'bg-slate-100 text-slate-700';
};
</script>

<template>
    <AppLayout>
        <Head title="Manajemen Kartu RFID" />

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Riwayat & Status Kartu RFID</h1>
                <p class="text-sm text-slate-500">Pantau kartu yang ditugaskan kepada siswa dan kelola kartu hilang.</p>
            </div>
            
            <Link :href="route('admin.bulk-write.index')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-semibold text-white hover:bg-indigo-700 transition-all">
                <CreditCardIcon class="w-4 h-4" />
                Tulis Kartu Massal (Bulk Write)
            </Link>
        </div>

        <div class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-sm rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200/60">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Siswa</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">UID Kartu</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Waktu Penugasan</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/60">
                        <tr v-for="assignment in assignments.data" :key="assignment.id" class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-slate-800">{{ assignment.student?.name }}</div>
                                <div class="text-xs text-slate-500">{{ assignment.student?.classroom?.name || '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-slate-600">
                                {{ assignment.card_uid }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span :class="['inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider border', getStatusBadge(assignment.status)]">
                                    <CheckCircleIcon v-if="assignment.status === 'active'" class="w-3.5 h-3.5" />
                                    <AlertTriangleIcon v-if="assignment.status === 'lost'" class="w-3.5 h-3.5" />
                                    {{ assignment.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ formatDate(assignment.assigned_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="destroy(assignment.id)" class="text-red-600 hover:text-red-900 font-semibold bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="assignments.data.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                Belum ada riwayat kartu RFID.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div v-if="assignments.links && assignments.links.length > 3" class="px-6 py-4 border-t border-slate-200/60 flex justify-center">
                <div class="flex gap-1">
                    <template v-for="(link, k) in assignments.links" :key="k">
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
