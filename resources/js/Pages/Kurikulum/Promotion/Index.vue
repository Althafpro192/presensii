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
                <h1 class="text-2xl font-bold text-slate-800">Proses Kenaikan Kelas</h1>
                <p class="text-slate-500">Atur status kenaikan kelas untuk tahun ajaran baru.</p>
            </div>
            <button 
                @click="applyPromotions"
                class="inline-flex items-center px-4 py-2.5 bg-green-600 border border-transparent rounded-xl font-medium text-white hover:bg-green-700 transition-all disabled:opacity-50"
                :disabled="applyForm.processing"
            >
                <CheckCircleIcon class="w-5 h-5 mr-2" />
                {{ applyForm.processing ? 'Memproses...' : 'Terapkan Kenaikan' }}
            </button>
        </div>

        <div class="glass rounded-2xl overflow-hidden shadow-sm">
            <!-- Filter -->
            <div class="p-4 border-b border-slate-200 bg-slate-50/50">
                <select v-model="classroomId" @change="filterClass" class="block w-full sm:w-64 pl-3 pr-10 py-2 border-slate-200 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-xl">
                    <option value="">Pilih Kelas</option>
                    <option v-for="c in classrooms" :key="c.id" :value="c.id">{{ c.name }} (Tingkat {{ c.grade_level }})</option>
                </select>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">NIS & Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kelas Saat Ini</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Kenaikan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr v-for="student in students.data" :key="student.id" class="hover:bg-white/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-slate-900">{{ student.name }}</div>
                                <div class="text-xs text-slate-500">{{ student.nis }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ student.classroom?.name || '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-lg" 
                                      :class="{
                                          'bg-amber-100 text-amber-700': student.promotion_status === 'pending',
                                          'bg-green-100 text-green-700': student.promotion_status === 'naik',
                                          'bg-blue-100 text-blue-700': student.promotion_status === 'lulus',
                                          'bg-red-100 text-red-700': student.promotion_status === 'tidak_naik'
                                      }">
                                    {{ student.promotion_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex gap-2">
                                    <button @click="updateStatus(student, 'naik')" class="px-2 py-1 text-xs rounded bg-green-50 text-green-700 border border-green-200 hover:bg-green-100">Naik</button>
                                    <button @click="updateStatus(student, 'tidak_naik')" class="px-2 py-1 text-xs rounded bg-red-50 text-red-700 border border-red-200 hover:bg-red-100">Tinggal</button>
                                    <button @click="updateStatus(student, 'lulus')" class="px-2 py-1 text-xs rounded bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100">Lulus</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="students.data.length === 0">
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                Silakan pilih kelas terlebih dahulu.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-slate-200" v-if="students.links">
                 <!-- Pagination stub -->
                 <p class="text-sm text-slate-500">Gunakan filter kelas untuk menampilkan data.</p>
            </div>
        </div>
    </AppLayout>
</template>
