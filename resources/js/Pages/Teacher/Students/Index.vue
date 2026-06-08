<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { AlertCircleIcon, CreditCardIcon, FileWarningIcon, UserIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    classroom: Object,
    students: Array,
    error: String,
});

const reportLostCardForm = useForm({
    note: '',
});

const reportLostCard = (student) => {
    if (confirm(`Laporkan kartu RFID ${student.name} hilang? Kartu lama akan diblokir.`)) {
        reportLostCardForm.post(route('teacher.students.report-lost-card', student.id), {
            preserveScroll: true,
            onSuccess: () => {
                reportLostCardForm.reset();
            }
        });
    }
};

const violationForm = useForm({
    violation_type: '',
    points: 10,
    description: '',
    date: new Date().toISOString().split('T')[0],
});

const isViolationModalOpen = ref(false);
const activeStudentForViolation = ref(null);

const openViolationModal = (student) => {
    activeStudentForViolation.value = student;
    violationForm.reset();
    isViolationModalOpen.value = true;
};

const submitViolation = () => {
    if (activeStudentForViolation.value) {
        violationForm.post(route('teacher.students.violations.store', activeStudentForViolation.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                isViolationModalOpen.value = false;
                activeStudentForViolation.value = null;
            }
        });
    }
};

</script>

<template>
    <AppLayout>
        <Head title="Profil Siswa Wali Kelas" />

        <div v-if="error" class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3">
            <AlertCircleIcon class="w-5 h-5" />
            <p>{{ error }}</p>
        </div>

        <div v-else>
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Siswa Perwalian: Kelas {{ classroom.name }}</h1>
                    <p class="text-sm text-slate-500">Kelola pelaporan kartu hilang dan catatan pelanggaran siswa.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <div v-for="student in students" :key="student.id" class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-sm rounded-2xl overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-slate-100 flex items-start gap-4">
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                            <UserIcon class="w-8 h-8" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">{{ student.name }}</h3>
                            <p class="text-sm text-slate-500">NIS: {{ student.nis }}</p>
                            <div class="mt-2">
                                <span v-if="student.has_lost_card" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                    <AlertCircleIcon class="w-3 h-3" /> Kartu Hilang
                                </span>
                                <span v-else class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    Kartu Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-slate-50/50 flex-1">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Pelanggaran Terakhir</h4>
                        <div v-if="student.violations && student.violations.length > 0" class="space-y-2">
                            <div v-for="v in student.violations" :key="v.id" class="text-xs text-slate-600 border-l-2 border-amber-400 pl-2">
                                <div class="font-semibold">{{ v.violation_type }} ({{ v.points }} Poin)</div>
                                <div class="text-slate-400">{{ new Date(v.date).toLocaleDateString('id-ID') }}</div>
                            </div>
                        </div>
                        <div v-else class="text-xs text-slate-400 italic">Belum ada catatan pelanggaran.</div>
                    </div>

                    <div class="p-4 border-t border-slate-100 flex items-center justify-between gap-2 bg-white">
                        <button @click="openViolationModal(student)" class="flex-1 inline-flex justify-center items-center gap-1.5 px-3 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-bold rounded-lg transition-colors">
                            <AlertTriangleIcon class="w-4 h-4" /> Catat Pelanggaran
                        </button>
                        <button @click="reportLostCard(student)" :disabled="student.has_lost_card || reportLostCardForm.processing" 
                            class="flex-1 inline-flex justify-center items-center gap-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition-colors disabled:opacity-50">
                            <CreditCardIcon class="w-4 h-4" /> Hilang
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="students.length === 0" class="p-8 text-center bg-white/70 backdrop-blur-xl border border-white/40 shadow-sm rounded-2xl">
                <UserIcon class="mx-auto h-12 w-12 text-slate-300 mb-3" />
                <h3 class="text-lg font-medium text-slate-800">Tidak ada siswa</h3>
                <p class="text-slate-500">Belum ada siswa yang terdaftar di kelas perwalian Anda.</p>
            </div>
        </div>

        <!-- Violation Modal -->
        <div v-if="isViolationModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800">Catat Pelanggaran</h3>
                </div>
                <div class="p-6">
                    <div class="mb-4 p-3 bg-indigo-50 text-indigo-800 rounded-xl text-sm font-semibold">
                        Siswa: {{ activeStudentForViolation?.name }}
                    </div>
                    <form @submit.prevent="submitViolation" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Tanggal Kejadian</label>
                            <input v-model="violationForm.date" type="date" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-slate-700">Jenis Pelanggaran</label>
                                <input v-model="violationForm.violation_type" type="text" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: Terlambat, Merokok, dll" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Poin</label>
                                <input v-model="violationForm.points" type="number" min="1" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Keterangan / Deskripsi</label>
                            <textarea v-model="violationForm.description" rows="3" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>

                        <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                            <button type="button" @click="isViolationModalOpen = false" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">Batal</button>
                            <button type="submit" :disabled="violationForm.processing" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition-colors disabled:opacity-50">Simpan Catatan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
