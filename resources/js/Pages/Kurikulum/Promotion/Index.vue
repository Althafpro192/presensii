<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { CheckCircleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    classrooms: Array,
    students: Object,
    filters: Object,
    statuses: Array,
});

const classroomId = ref(props.filters.classroom_id || '');

const filterClass = () => {
    router.get('/kurikulum/promotion', {
        classroom_id: classroomId.value,
    }, { preserveState: true });
};

const applyForm = useForm({});

const applyPromotions = () => {
    if(confirm('Apakah Anda yakin ingin memproses semua siswa dengan status kenaikan yang telah diatur? Proses ini akan memperbarui kelas mereka.')){
        applyForm.post('/kurikulum/promotion/apply');
    }
};

const updateStatus = (student, status) => {
    router.put(`/kurikulum/promotion/student/${student.id}`, {
        promotion_status: status
    }, { preserveScroll: true });
};
</script>

<template>
    <AppLayout>
        <Head title="Kenaikan Kelas" />

        <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-[22px] font-medium text-slate-900 tracking-tight">Proses Kenaikan Kelas</h1>
                <p class="text-[13px] text-slate-500 mt-1">Atur status kenaikan kelas untuk tahun ajaran baru.</p>
            </div>
            <button 
                @click="applyPromotions"
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 border border-transparent rounded-lg font-medium text-[13px] text-white hover:bg-green-600 transition-colors disabled:opacity-50"
                :disabled="applyForm.processing"
            >
                <CheckCircleIcon class="w-4 h-4" />
                {{ applyForm.processing ? 'Memproses...' : 'Terapkan Kenaikan' }}
            </button>
        </div>

        <div class="bg-white border border-slate-200 rounded-[12px] overflow-hidden">
            <!-- Filter -->
            <div class="p-4 border-b border-slate-200 bg-slate-50">
                <select v-model="classroomId" @change="filterClass" class="block w-full sm:w-64 px-3 py-2 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-[13px] rounded-lg bg-white transition-colors">
                    <option value="">Pilih Kelas</option>
                    <option v-for="c in classrooms" :key="c.id" :value="c.id">{{ c.name }} (Tingkat {{ c.grade_level }})</option>
                </select>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide">NIS & Nama</th>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide">Kelas Saat Ini</th>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide">Status Kenaikan</th>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="student in students.data" :key="student.id" class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3 whitespace-nowrap">
                                <div class="text-[14px] font-medium text-slate-900">{{ student.name }}</div>
                                <div class="text-[13px] text-slate-500">{{ student.nis }}</div>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-[14px] text-slate-600">
                                {{ student.classroom?.name || '-' }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 text-[11px] font-medium uppercase tracking-wide rounded-full border" 
                                      :class="{
                                          'bg-yellow-50 text-yellow-700 border-yellow-200': student.promotion_status === 'pending',
                                          'bg-green-50 text-green-700 border-green-200': student.promotion_status === 'naik',
                                          'bg-indigo-50 text-indigo-700 border-indigo-200': student.promotion_status === 'lulus',
                                          'bg-red-50 text-red-700 border-red-200': student.promotion_status === 'tidak_naik'
                                      }">
                                    {{ student.promotion_status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-sm">
                                <div class="flex gap-2">
                                    <button @click="updateStatus(student, 'naik')" class="px-2.5 py-1 text-[11px] font-medium uppercase tracking-wide rounded bg-white text-green-700 border border-green-200 hover:bg-green-50 transition-colors">Naik</button>
                                    <button @click="updateStatus(student, 'tidak_naik')" class="px-2.5 py-1 text-[11px] font-medium uppercase tracking-wide rounded bg-white text-red-700 border border-red-200 hover:bg-red-50 transition-colors">Tinggal</button>
                                    <button @click="updateStatus(student, 'lulus')" class="px-2.5 py-1 text-[11px] font-medium uppercase tracking-wide rounded bg-white text-indigo-700 border border-indigo-200 hover:bg-indigo-50 transition-colors">Lulus</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="students.data.length === 0">
                            <td colspan="4" class="px-5 py-12 text-center text-[13px] text-slate-500">
                                Silakan pilih kelas terlebih dahulu.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50" v-if="students.links">
                 <!-- Pagination stub -->
                 <p class="text-[13px] text-slate-500">Gunakan filter kelas untuk menampilkan data.</p>
            </div>
        </div>
    </AppLayout>
</template>
