<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    classrooms: Array,
    devices: Array,
    students: Array, // It's a Collection, passed as array
    pendingJobs: Array,
    filters: Object,
});

const classroomId = ref(props.filters.classroom_id || '');
const selectedStudents = ref([]);

const filterClass = () => {
    router.get('/admin/bulk-write', {
        classroom_id: classroomId.value,
    }, { preserveState: true });
    selectedStudents.value = [];
};

const form = useForm({
    device_id: '',
    student_ids: [],
});

const selectAll = (event) => {
    if (event.target.checked) {
        selectedStudents.value = props.students.map(s => s.id);
    } else {
        selectedStudents.value = [];
    }
};

const submitJobs = () => {
    if (!form.device_id) {
        alert('Silakan pilih perangkat RFID tujuan terlebih dahulu.');
        return;
    }
    if (selectedStudents.value.length === 0) {
        alert('Pilih setidaknya satu siswa untuk ditulis kartunya.');
        return;
    }

    form.student_ids = selectedStudents.value;
    form.post('/admin/bulk-write', {
        onSuccess: () => {
            selectedStudents.value = [];
            form.reset('student_ids');
        }
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Bulk Write RFID" />

        <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-[22px] font-medium text-slate-900">Penulisan Kartu Massal (Bulk Write)</h1>
                <p class="text-[13px] text-slate-500 mt-1">Tugaskan perangkat ESP32 untuk memprogram banyak kartu secara otomatis.</p>
            </div>
            <button 
                @click="submitJobs"
                class="inline-flex items-center px-4 py-2 bg-indigo-500 border border-transparent rounded-lg font-medium text-[13px] text-white hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 transition-colors disabled:opacity-50"
                :disabled="form.processing || selectedStudents.length === 0"
            >
                Kirim Job ke Perangkat
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form & Table -->
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-[12px] overflow-hidden flex flex-col">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row gap-4 justify-between items-center">
                    <select v-model="classroomId" @change="filterClass" class="block w-full sm:w-64 pl-3 pr-8 py-2 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="">Pilih Kelas</option>
                        <option v-for="c in classrooms" :key="c.id" :value="c.id">{{ c.name }} (Tingkat {{ c.grade_level }})</option>
                    </select>

                    <select v-model="form.device_id" required class="block w-full sm:w-64 pl-3 pr-8 py-2 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="" disabled>Pilih Perangkat Target</option>
                        <option v-for="d in devices" :key="d.id" :value="d.id">{{ d.device_name }} ({{ d.ip_address }})</option>
                    </select>
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-3 border-b border-slate-200">
                                    <input type="checkbox" @change="selectAll" :checked="selectedStudents.length === students?.length && students?.length > 0" class="rounded border-slate-300 text-indigo-500 focus:ring-indigo-500 h-4 w-4">
                                </th>
                                <th class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Nama Siswa</th>
                                <th class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">NIS</th>
                                <th class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Status Kartu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="student in students" :key="student.id" class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3">
                                    <input type="checkbox" :value="student.id" v-model="selectedStudents" class="rounded border-slate-300 text-indigo-500 focus:ring-indigo-500 h-4 w-4">
                                </td>
                                <td class="px-5 py-3 text-[14px] font-medium text-slate-900">
                                    {{ student.name }}
                                </td>
                                <td class="px-5 py-3 text-[14px] text-slate-500">
                                    {{ student.nis }}
                                </td>
                                <td class="px-5 py-3 text-[13px]">
                                    <span v-if="student.rfid" class="text-green-600 font-medium">Terdaftar</span>
                                    <span v-else class="text-yellow-600 font-medium">Belum Ada</span>
                                </td>
                            </tr>
                            <tr v-if="!students || students.length === 0">
                                <td colspan="4" class="px-5 py-10 text-center text-[13px] text-slate-400">
                                    Silakan pilih kelas terlebih dahulu.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Job Queue Sidebar -->
            <div class="bg-white border border-slate-200 p-5 rounded-[12px] flex flex-col h-full">
                <h3 class="text-[16px] font-medium text-slate-900 mb-4 flex items-center justify-between">
                    Antrean Berjalan
                    <span class="bg-indigo-50 text-indigo-600 text-[11px] px-2 py-0.5 rounded-full font-medium">{{ pendingJobs.length }} Job</span>
                </h3>
                
                <div class="space-y-3 flex-1 overflow-y-auto pr-2">
                    <div v-for="job in pendingJobs" :key="job.id" class="p-3 border border-slate-100 rounded-lg bg-slate-50 relative overflow-hidden">
                        <!-- Progress bar mock -->
                        <div v-if="job.status === 'processing'" class="absolute bottom-0 left-0 h-0.5 bg-indigo-500 animate-pulse w-full"></div>
                        
                        <div class="flex justify-between items-start mb-1">
                            <div class="font-medium text-[13px] text-slate-900">{{ job.student?.name }}</div>
                            <span class="text-[11px] px-2 py-0.5 rounded-full font-medium" 
                                  :class="{
                                      'bg-slate-200 text-slate-600': job.status === 'pending',
                                      'bg-blue-100 text-blue-700': job.status === 'processing'
                                  }">
                                {{ job.status }}
                            </span>
                        </div>
                        <div class="text-[12px] text-slate-500 flex justify-between">
                            <span class="font-mono">UID: {{ job.card_uid_to_write }}</span>
                            <span>{{ job.device?.device_name }}</span>
                        </div>
                    </div>
                    
                    <div v-if="pendingJobs.length === 0" class="text-center py-8 text-slate-400 text-[13px]">
                        Tidak ada antrean berjalan.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
