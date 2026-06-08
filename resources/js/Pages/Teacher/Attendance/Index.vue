<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { CalendarIcon, EditIcon, CheckIcon, AlertCircleIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    classroom: Object,
    students: Array,
    date: String,
    statuses: Array,
    error: String,
});

const filterForm = useForm({
    date: props.date,
});

const filterByDate = () => {
    filterForm.get(route('teacher.attendance.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const editingStudent = ref(null);
const editForm = useForm({
    date: props.date,
    status: '',
    check_in_time: '',
});

const startEdit = (student) => {
    editingStudent.value = student.id;
    editForm.status = student.attendance ? student.attendance.status : 'hadir';
    editForm.check_in_time = student.attendance ? student.attendance.check_in_time : '';
};

const saveEdit = (student) => {
    editForm.put(route('teacher.attendance.update', student.id), {
        preserveScroll: true,
        onSuccess: () => {
            editingStudent.value = null;
        }
    });
};

const getStatusColor = (status) => {
    const colors = {
        'hadir': 'bg-emerald-100 text-emerald-700',
        'izin': 'bg-blue-100 text-blue-700',
        'sakit': 'bg-amber-100 text-amber-700',
        'alpa': 'bg-red-100 text-red-700',
        'terlambat': 'bg-orange-100 text-orange-700',
    };
    return colors[status] || 'bg-slate-100 text-slate-700';
};
</script>

<template>
    <AppLayout>
        <Head title="Manajemen Presensi Kelas" />

        <div v-if="error" class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3">
            <AlertCircleIcon class="w-5 h-5" />
            <p>{{ error }}</p>
        </div>

        <div v-else>
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Presensi Kelas {{ classroom.name }}</h1>
                    <p class="text-sm text-slate-500">Kelola dan pantau kehadiran siswa per hari</p>
                </div>

                <div class="flex items-center gap-2">
                    <form @submit.prevent="filterByDate" class="flex items-center gap-2">
                        <div class="relative">
                            <CalendarIcon class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                            <input type="date" v-model="filterForm.date" @change="filterByDate"
                                class="pl-10 pr-4 py-2 rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white/70 backdrop-blur-sm text-sm" />
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-sm rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200/60">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">NIS</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Siswa</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Kartu</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Kehadiran</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Waktu Masuk</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/60">
                            <tr v-for="student in students" :key="student.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 font-medium">
                                    {{ student.nis }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-slate-800">{{ student.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span v-if="student.has_lost_card" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Kartu Hilang
                                    </span>
                                    <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div v-if="editingStudent === student.id">
                                        <select v-model="editForm.status" class="block w-full rounded-lg border-slate-300 py-1 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option v-for="status in statuses" :key="status.value" :value="status.value">
                                                {{ status.label }}
                                            </option>
                                        </select>
                                    </div>
                                    <div v-else>
                                        <span v-if="student.attendance" :class="[getStatusColor(student.attendance.status), 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium']">
                                            {{ student.attendance.status_label }}
                                        </span>
                                        <span v-else class="text-slate-400 text-sm">-</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div v-if="editingStudent === student.id">
                                        <input type="time" step="1" v-model="editForm.check_in_time" class="block w-full rounded-lg border-slate-300 py-1 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div v-else class="text-sm text-slate-600">
                                        {{ student.attendance?.check_in_time || '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div v-if="editingStudent === student.id" class="flex items-center justify-end gap-2">
                                        <button @click="editingStudent = null" class="text-slate-500 hover:text-slate-700 px-2 py-1">Batal</button>
                                        <button @click="saveEdit(student)" :disabled="editForm.processing" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-lg transition-colors">
                                            <CheckIcon class="w-4 h-4 mr-1" /> Simpan
                                        </button>
                                    </div>
                                    <button v-else @click="startEdit(student)" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition-colors">
                                        <EditIcon class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="students.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                    Tidak ada data siswa di kelas ini.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
