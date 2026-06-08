<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { UserCheckIcon, UsersIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    academicYears: Array,
    classrooms: Array,
    teachers: Array,
    assignments: Object,
    filters: Object,
});

const selectedYear = ref(props.filters.academic_year_id || '');

const filterYear = () => {
    router.get('/kurikulum/homeroom-assign', {
        academic_year_id: selectedYear.value,
    }, { preserveState: true });
};

const assignForm = useForm({
    academic_year_id: selectedYear.value,
    classroom_id: '',
    teacher_id: '',
});

const assignTeacher = (classroomId) => {
    if (!selectedYear.value) {
        alert('Pilih tahun ajaran terlebih dahulu.');
        return;
    }
    
    // Check if a teacher is selected
    const selectEl = document.getElementById(`teacher-${classroomId}`);
    if (!selectEl || !selectEl.value) {
        alert('Pilih guru wali kelas terlebih dahulu.');
        return;
    }

    assignForm.academic_year_id = selectedYear.value;
    assignForm.classroom_id = classroomId;
    assignForm.teacher_id = selectEl.value;

    assignForm.post(route('kurikulum.homeroom-assign.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Success
        }
    });
};

const removeAssignment = (assignmentId) => {
    if (confirm('Yakin ingin menghapus penugasan wali kelas ini?')) {
        router.delete(route('kurikulum.homeroom-assign.destroy', assignmentId), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <AppLayout>
        <Head title="Penugasan Wali Kelas" />

        <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Penugasan Wali Kelas</h1>
                <p class="text-sm text-slate-500">Tugaskan guru sebagai wali kelas untuk setiap tahun ajaran.</p>
            </div>
            
            <div>
                <select v-model="selectedYear" @change="filterYear" class="block w-full sm:w-64 pl-3 pr-10 py-2 border-slate-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-xl bg-white shadow-sm font-medium">
                    <option value="">Pilih Tahun Ajaran</option>
                    <option v-for="year in academicYears" :key="year.id" :value="year.id">
                        {{ year.name }} {{ year.is_current ? '(Aktif)' : '' }}
                    </option>
                </select>
            </div>
        </div>

        <div v-if="!selectedYear" class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-sm p-12 rounded-2xl flex flex-col items-center justify-center text-center">
            <UsersIcon class="w-16 h-16 text-slate-300 mb-4" />
            <h3 class="text-xl font-bold text-slate-700">Tahun Ajaran Belum Dipilih</h3>
            <p class="text-slate-500 mt-2">Silakan pilih tahun ajaran pada menu di atas untuk mulai mengatur wali kelas.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="classroom in classrooms" :key="classroom.id" 
                 class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-sm rounded-2xl p-6 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
                
                <!-- Status bar top -->
                <div :class="[assignments[classroom.id] ? 'bg-emerald-500' : 'bg-amber-400', 'absolute top-0 left-0 w-full h-1']"></div>

                <div class="mb-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-xl font-bold text-slate-800">{{ classroom.name }}</h3>
                        <span class="bg-slate-100 text-slate-600 text-xs font-semibold px-2 py-1 rounded-lg border border-slate-200">
                            Tingkat {{ classroom.grade_level }}
                        </span>
                    </div>
                    
                    <div v-if="assignments[classroom.id]" class="mt-4 p-3 bg-emerald-50 border border-emerald-100 rounded-xl">
                        <div class="text-xs text-emerald-600 font-semibold uppercase tracking-wider mb-1">Wali Kelas Saat Ini</div>
                        <div class="flex items-center justify-between">
                            <div class="font-bold text-emerald-900 flex items-center gap-2">
                                <UserCheckIcon class="w-4 h-4" />
                                {{ assignments[classroom.id].teacher.name }}
                            </div>
                            <button @click="removeAssignment(assignments[classroom.id].id)" class="text-xs text-red-600 hover:text-red-800 font-medium px-2 py-1 bg-red-100 hover:bg-red-200 rounded transition-colors">
                                Hapus
                            </button>
                        </div>
                    </div>
                    <div v-else class="mt-4 p-3 bg-amber-50 border border-amber-100 rounded-xl">
                        <div class="text-sm text-amber-800 font-medium flex items-center gap-2">
                            Belum ada wali kelas ditugaskan.
                        </div>
                    </div>
                </div>

                <div class="mt-2 pt-4 border-t border-slate-100/60">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tugaskan Wali Kelas Baru</label>
                    <div class="flex gap-2">
                        <select :id="`teacher-${classroom.id}`" class="block w-full pl-3 pr-10 py-1.5 border-slate-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg bg-white/50 backdrop-blur-sm">
                            <option value="">Pilih Guru</option>
                            <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id" :selected="assignments[classroom.id]?.teacher_id === teacher.id">
                                {{ teacher.name }}
                            </option>
                        </select>
                        <button @click="assignTeacher(classroom.id)" 
                                :disabled="assignForm.processing && assignForm.classroom_id === classroom.id"
                                class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium text-sm transition-colors disabled:opacity-50">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
            
            <div v-if="classrooms.length === 0" class="col-span-full bg-white/70 backdrop-blur-xl border border-white/40 shadow-sm p-8 rounded-2xl text-center">
                <p class="text-slate-500">Belum ada kelas yang terdaftar. Silakan tambahkan kelas terlebih dahulu di menu Admin.</p>
            </div>
        </div>
    </AppLayout>
</template>
