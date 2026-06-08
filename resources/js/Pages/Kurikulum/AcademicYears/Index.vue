<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { PlusIcon, TrashIcon, CheckCircleIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    academicYears: Array,
});

const isModalOpen = ref(false);
const editingId = ref(null);

const form = useForm({
    name: '',
    start_date: '',
    end_date: '',
    is_current: false,
});

const openCreateModal = () => {
    editingId.value = null;
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (year) => {
    editingId.value = year.id;
    form.name = year.name;
    form.start_date = year.start_date;
    form.end_date = year.end_date;
    form.is_current = year.is_current === 1 || year.is_current === true;
    isModalOpen.value = true;
};

const submit = () => {
    if (editingId.value) {
        form.put(route('kurikulum.academic-years.update', editingId.value), {
            onSuccess: () => { isModalOpen.value = false; }
        });
    } else {
        form.post(route('kurikulum.academic-years.store'), {
            onSuccess: () => { isModalOpen.value = false; }
        });
    }
};

const destroy = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus tahun ajaran ini? Semua data terkait (seperti penetapan wali kelas) mungkin terpengaruh.')) {
        router.delete(route('kurikulum.academic-years.destroy', id));
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
};
</script>

<template>
    <AppLayout>
        <Head title="Tahun Ajaran" />

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Tahun Ajaran</h1>
                <p class="text-sm text-slate-500">Kelola daftar tahun ajaran dan tetapkan tahun akademik yang aktif.</p>
            </div>
            
            <button @click="openCreateModal"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-all">
                <PlusIcon class="w-4 h-4" />
                Tambah Tahun Ajaran
            </button>
        </div>

        <div class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-sm rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200/60">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tahun Ajaran</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Periode</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Aktif</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/60">
                        <tr v-for="year in academicYears" :key="year.id" class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800">
                                {{ year.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ formatDate(year.start_date) }} - {{ formatDate(year.end_date) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span v-if="year.is_current" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <CheckCircleIcon class="w-3.5 h-3.5" /> Aktif
                                </span>
                                <span v-else class="text-slate-400 text-sm">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="openEditModal(year)" class="text-indigo-600 hover:text-indigo-900 mr-4 font-semibold">Edit</button>
                                <button @click="destroy(year.id)" class="text-red-600 hover:text-red-900 font-semibold" :disabled="year.is_current">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="academicYears.length === 0">
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                Belum ada data tahun ajaran.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800">{{ editingId ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran Baru' }}</h3>
                </div>
                <div class="p-6">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Nama Tahun Ajaran</label>
                            <input v-model="form.name" type="text" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: 2024/2025" required>
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Tanggal Mulai</label>
                                <input v-model="form.start_date" type="date" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <p v-if="form.errors.start_date" class="mt-1 text-sm text-red-600">{{ form.errors.start_date }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Tanggal Selesai</label>
                                <input v-model="form.end_date" type="date" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <p v-if="form.errors.end_date" class="mt-1 text-sm text-red-600">{{ form.errors.end_date }}</p>
                            </div>
                        </div>

                        <div class="flex items-center pt-2">
                            <input type="checkbox" v-model="form.is_current" id="is_current" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                            <label for="is_current" class="ml-2 block text-sm font-medium text-slate-700">
                                Jadikan Tahun Ajaran Aktif Saat Ini
                            </label>
                        </div>
                        <p v-if="form.errors.is_current" class="mt-1 text-sm text-red-600">{{ form.errors.is_current }}</p>

                        <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                            <button type="button" @click="isModalOpen = false" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">Batal</button>
                            <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition-colors disabled:opacity-50">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
