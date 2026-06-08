<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { CalendarIcon, EditIcon, CheckIcon, AlertCircleIcon, XIcon } from 'lucide-vue-next';
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
        'hadir': 'bg-green-100 text-green-800 border-green-200',
        'izin': 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'sakit': 'bg-indigo-100 text-indigo-800 border-indigo-200',
        'alpa': 'bg-red-100 text-red-800 border-red-200',
        'terlambat': 'bg-orange-100 text-orange-800 border-orange-200',
    };
    return colors[status] || 'bg-slate-100 text-slate-700 border-slate-200';
};
</script>

<template>
    <AppLayout>
        <Head title="Manajemen Presensi Kelas" />

        <div v-if="error" class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 flex items-center gap-3">
            <AlertCircleIcon class="w-5 h-5" />
            <p class="text-[13px] font-medium">{{ error }}</p>
        </div>

        <div v-else>
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-[22px] font-medium text-slate-900 tracking-tight">Presensi Kelas {{ classroom.name }}</h1>
                    <p class="text-[13px] text-slate-500 mt-1">Kelola dan pantau kehadiran siswa per hari</p>
                </div>

                <div class="flex items-center gap-2">
                    <form @submit.prevent="filterByDate" class="flex items-center gap-2">
                        <div class="relative">
                            <CalendarIcon class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                            <input type="date" v-model="filterForm.date" @change="filterByDate"
                                class="block w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-[12px] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">NIS</th>
                                <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Nama Siswa</th>
                                <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200 text-center">Status Kartu</th>
                                <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200 text-center">Kehadiran</th>
                                <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200 text-center">Waktu Masuk</th>
                                <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="student in students" :key="student.id" class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3 whitespace-nowrap text-[13px] text-slate-500 font-medium">
                                    {{ student.nis }}
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <div class="text-[14px] font-medium text-slate-900">{{ student.name }}</div>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-center">
                                    <span v-if="student.has_lost_card" class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium uppercase tracking-wide bg-red-50 text-red-700 border border-red-200">
                                        Kartu Hilang
                                    </span>
                                    <span v-else class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium uppercase tracking-wide bg-green-50 text-green-700 border border-green-200">
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-center">
                                    <div v-if="editingStudent === student.id">
                                        <select v-model="editForm.status" class="block w-full px-2 py-1 bg-white border border-slate-200 rounded text-[13px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option v-for="status in statuses" :key="status.value" :value="status.value">
                                                {{ status.label }}
                                            </option>
                                        </select>
                                    </div>
                                    <div v-else>
                                        <span v-if="student.attendance" :class="[getStatusColor(student.attendance.status), 'inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium uppercase tracking-wide border']">
                                            {{ student.attendance.status_label }}
                                        </span>
                                        <span v-else class="text-slate-400 text-[13px]">-</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-center">
                                    <div v-if="editingStudent === student.id">
                                        <input type="time" step="1" v-model="editForm.check_in_time" class="block w-full px-2 py-1 bg-white border border-slate-200 rounded text-[13px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div v-else class="text-[13px] text-slate-600 font-mono">
                                        {{ student.attendance?.check_in_time || '-' }}
                                    </div>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-right text-[13px] font-medium">
                                    <div v-if="editingStudent === student.id" class="flex items-center justify-end gap-1">
                                        <button @click="editingStudent = null" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded transition-colors">
                                            <XIcon class="w-4 h-4" />
                                        </button>
                                        <button @click="saveEdit(student)" :disabled="editForm.processing" class="p-1.5 text-indigo-500 hover:text-indigo-700 hover:bg-indigo-50 rounded transition-colors disabled:opacity-50">
                                            <CheckIcon class="w-4 h-4" />
                                        </button>
                                    </div>
                                    <button v-else @click="startEdit(student)" class="p-1.5 text-slate-400 hover:text-indigo-500 hover:bg-indigo-50 rounded transition-colors">
                                        <EditIcon class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="students.length === 0">
                                <td colspan="6" class="px-5 py-10 text-center text-[13px] text-slate-400">
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
