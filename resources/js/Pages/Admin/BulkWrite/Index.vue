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
                <h1 class="text-2xl font-bold text-slate-800">Penulisan Kartu Massal (Bulk Write)</h1>
                <p class="text-slate-500">Tugaskan perangkat ESP32 untuk memprogram banyak kartu secara otomatis.</p>
            </div>
            <button 
                @click="submitJobs"
                class="inline-flex items-center px-4 py-2.5 bg-primary border border-transparent rounded-xl font-medium text-white hover:bg-primary-dark transition-all disabled:opacity-50"
                :disabled="form.processing || selectedStudents.length === 0"
            >
                Kirim Job ke Perangkat
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form & Table -->
            <div class="lg:col-span-2 glass rounded-2xl overflow-hidden shadow-sm flex flex-col">
                <div class="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row gap-4 justify-between items-center">
                    <select v-model="classroomId" @change="filterClass" class="block w-full sm:w-64 pl-3 pr-10 py-2 border-slate-200 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-xl">
                        <option value="">Pilih Kelas</option>
                        <option v-for="c in classrooms" :key="c.id" :value="c.id">{{ c.name }} (Tingkat {{ c.grade_level }})</option>
                    </select>

                    <select v-model="form.device_id" required class="block w-full sm:w-64 pl-3 pr-10 py-2 border-slate-200 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-xl font-medium text-slate-700 bg-white shadow-sm">
                        <option value="" disabled>Pilih Perangkat Target</option>
                        <option v-for="d in devices" :key="d.id" :value="d.id">{{ d.device_name }} ({{ d.ip_address }})</option>
                    </select>
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th class="px-6 py-4 text-left">
                                    <input type="checkbox" @change="selectAll" :checked="selectedStudents.length === students?.length && students?.length > 0" class="rounded border-slate-300 text-primary focus:ring-primary h-4 w-4">
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Siswa</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">NIS</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Kartu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr v-for="student in students" :key="student.id" class="hover:bg-white/50 transition-colors">
                                <td class="px-6 py-4">
                                    <input type="checkbox" :value="student.id" v-model="selectedStudents" class="rounded border-slate-300 text-primary focus:ring-primary h-4 w-4">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                    {{ student.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ student.nis }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span v-if="student.rfid" class="text-green-600 font-medium">Terdaftar</span>
                                    <span v-else class="text-amber-500">Belum Ada</span>
                                </td>
                            </tr>
                            <tr v-if="!students || students.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                    Silakan pilih kelas terlebih dahulu.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Job Queue Sidebar -->
            <div class="glass p-6 rounded-2xl flex flex-col h-full">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center justify-between">
                    Antrean Berjalan
                    <span class="bg-primary/10 text-primary text-xs px-2 py-1 rounded-full font-semibold">{{ pendingJobs.length }} Job</span>
                </h3>
                
                <div class="space-y-4 flex-1 overflow-y-auto pr-2">
                    <div v-for="job in pendingJobs" :key="job.id" class="p-3 border border-slate-100 rounded-xl bg-white shadow-sm relative overflow-hidden">
                        <!-- Progress bar mock -->
                        <div v-if="job.status === 'processing'" class="absolute bottom-0 left-0 h-1 bg-primary animate-pulse w-full"></div>
                        
                        <div class="flex justify-between items-start mb-1">
                            <div class="font-medium text-sm text-slate-800">{{ job.student?.name }}</div>
                            <span class="text-xs px-1.5 py-0.5 rounded" 
                                  :class="{
                                      'bg-slate-100 text-slate-600': job.status === 'pending',
                                      'bg-blue-100 text-blue-700': job.status === 'processing'
                                  }">
                                {{ job.status }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-500 flex justify-between">
                            <span>UID: {{ job.card_uid_to_write }}</span>
                            <span>{{ job.device?.device_name }}</span>
                        </div>
                    </div>
                    
                    <div v-if="pendingJobs.length === 0" class="text-center py-10 text-slate-500 text-sm">
                        Tidak ada antrean berjalan.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
