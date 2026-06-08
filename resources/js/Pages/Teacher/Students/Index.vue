<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { AlertCircleIcon, CreditCardIcon, AlertTriangleIcon, UserIcon } from 'lucide-vue-next';
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
    description: '',
    violation_date: new Date().toISOString().split('T')[0],
    sanction: '',
});

const isViolationModalOpen = ref(false);
const activeStudentForViolation = ref(null);

const openViolationModal = (student) => {
    activeStudentForViolation.value = student;
    violationForm.reset();
    violationForm.violation_date = new Date().toISOString().split('T')[0];
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

        <div v-if="error" class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 flex items-center gap-3">
            <AlertCircleIcon class="w-5 h-5" />
            <p class="text-[13px] font-medium">{{ error }}</p>
        </div>

        <div v-else>
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-[22px] font-medium text-slate-900 tracking-tight">Siswa Perwalian: Kelas {{ classroom.name }}</h1>
                    <p class="text-[13px] text-slate-500 mt-1">Kelola pelaporan kartu hilang dan catatan pelanggaran siswa.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <div v-for="student in students" :key="student.id" class="bg-white border border-slate-200 rounded-[12px] overflow-hidden flex flex-col">
                    <div class="p-5 border-b border-slate-100 flex items-start gap-4">
                        <div class="p-2 bg-slate-100 text-slate-400 rounded-full border border-slate-200">
                            <UserIcon class="w-8 h-8" />
                        </div>
                        <div>
                            <h3 class="text-[16px] font-medium text-slate-900">{{ student.name }}</h3>
                            <p class="text-[13px] text-slate-500">NIS: {{ student.nis }}</p>
                            <div class="mt-2">
                                <span v-if="student.has_lost_card" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium uppercase tracking-wide bg-red-50 text-red-700 border border-red-200">
                                    <AlertCircleIcon class="w-3 h-3" /> Kartu Hilang
                                </span>
                                <span v-else class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium uppercase tracking-wide bg-green-50 text-green-700 border border-green-200">
                                    Kartu Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-slate-50 flex-1">
                        <h4 class="text-[11px] font-medium uppercase tracking-wider text-slate-500 mb-2">Pelanggaran Terakhir</h4>
                        <div v-if="student.violations && student.violations.length > 0" class="space-y-2">
                            <div v-for="v in student.violations" :key="v.id" class="text-[12px] text-slate-600 border-l-2 border-yellow-400 pl-2">
                                <div class="font-medium text-slate-900">{{ v.violation_type }} <span v-if="v.sanction" class="text-red-600 font-normal">({{ v.sanction }})</span></div>
                                <div class="text-slate-400 mt-0.5">{{ new Date(v.violation_date).toLocaleDateString('id-ID') }}</div>
                            </div>
                        </div>
                        <div v-else class="text-[12px] text-slate-400 italic">Belum ada catatan pelanggaran.</div>
                    </div>

                    <div class="p-4 border-t border-slate-100 flex items-center justify-between gap-3 bg-white">
                        <button @click="openViolationModal(student)" class="flex-1 inline-flex justify-center items-center gap-1.5 px-3 py-2 bg-white border border-yellow-200 hover:bg-yellow-50 text-yellow-700 text-[12px] font-medium rounded-lg transition-colors">
                            <AlertTriangleIcon class="w-4 h-4" /> Catat Pelanggaran
                        </button>
                        <button @click="reportLostCard(student)" :disabled="student.has_lost_card || reportLostCardForm.processing" 
                            class="flex-1 inline-flex justify-center items-center gap-1.5 px-3 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-[12px] font-medium rounded-lg transition-colors disabled:opacity-50 disabled:bg-slate-50 disabled:text-slate-400 disabled:border-slate-100">
                            <CreditCardIcon class="w-4 h-4" /> Hilang
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="students.length === 0" class="p-10 text-center bg-white border border-slate-200 rounded-[12px]">
                <UserIcon class="mx-auto h-10 w-10 text-slate-300 mb-3" />
                <h3 class="text-[14px] font-medium text-slate-900">Tidak ada siswa</h3>
                <p class="text-[13px] text-slate-500 mt-1">Belum ada siswa yang terdaftar di kelas perwalian Anda.</p>
            </div>
        </div>

        <!-- Violation Modal -->
        <div v-if="isViolationModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40">
            <div class="bg-white rounded-[12px] w-full max-w-md overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white">
                    <h3 class="text-[16px] font-medium text-slate-900">Catat Pelanggaran</h3>
                </div>
                <div class="p-6">
                    <div class="mb-5 p-3 bg-indigo-50 border border-indigo-100 text-indigo-700 rounded-lg text-[13px] font-medium">
                        Siswa: {{ activeStudentForViolation?.name }}
                    </div>
                    <form @submit.prevent="submitViolation" class="space-y-4">
                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">Tanggal Kejadian</label>
                            <input v-model="violationForm.violation_date" type="date" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                        </div>
                        
                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">Jenis Pelanggaran</label>
                            <input v-model="violationForm.violation_type" type="text" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Contoh: Terlambat, Merokok, dll" required>
                        </div>

                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">Sanksi</label>
                            <select v-model="violationForm.sanction" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <option value="">Tidak ada sanksi</option>
                                <option value="peringatan">Peringatan</option>
                                <option value="skors">Skorsing</option>
                                <option value="dikembalikan_ke_ortu">Dikembalikan ke Orang Tua</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">Keterangan / Deskripsi</label>
                            <textarea v-model="violationForm.description" rows="3" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"></textarea>
                        </div>

                        <div class="pt-5 mt-2 flex justify-end gap-3 border-t border-slate-100">
                            <button type="button" @click="isViolationModalOpen = false" class="px-4 py-2 text-[13px] font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 rounded-lg transition-colors">Batal</button>
                            <button type="submit" :disabled="violationForm.processing" class="px-4 py-2 bg-indigo-500 text-white text-[13px] font-medium rounded-lg hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 transition-colors disabled:opacity-50">Simpan Catatan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
