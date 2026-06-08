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
                <h1 class="text-[22px] font-medium text-slate-900 tracking-tight">Tahun Ajaran</h1>
                <p class="text-[13px] text-slate-500 mt-1">Kelola daftar tahun ajaran dan tetapkan tahun akademik yang aktif.</p>
            </div>
            
            <button @click="openCreateModal"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 border border-transparent rounded-lg font-medium text-[13px] text-white hover:bg-indigo-600 focus:outline-none transition-colors">
                <PlusIcon class="w-4 h-4" />
                Tambah Tahun Ajaran
            </button>
        </div>

        <div class="bg-white border border-slate-200 rounded-[12px] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Tahun Ajaran</th>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Periode</th>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200 text-center">Status Aktif</th>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="year in academicYears" :key="year.id" class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3 whitespace-nowrap text-[14px] font-medium text-slate-900">
                                {{ year.name }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-[14px] text-slate-600">
                                {{ formatDate(year.start_date) }} - {{ formatDate(year.end_date) }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-center">
                                <span v-if="year.is_current" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[12px] font-medium bg-green-50 text-green-700 border border-green-200">
                                    <CheckCircleIcon class="w-3.5 h-3.5" /> Aktif
                                </span>
                                <span v-else class="text-slate-400 text-[13px]">-</span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-right text-[13px] font-medium">
                                <button @click="openEditModal(year)" class="text-indigo-500 hover:text-indigo-700 mr-4 transition-colors">Edit</button>
                                <button @click="destroy(year.id)" class="text-red-500 hover:text-red-700 transition-colors" :disabled="year.is_current">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="academicYears.length === 0">
                            <td colspan="4" class="px-5 py-10 text-center text-[13px] text-slate-400">
                                Belum ada data tahun ajaran.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40">
            <div class="bg-white rounded-[12px] w-full max-w-md overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white">
                    <h3 class="text-[16px] font-medium text-slate-900">{{ editingId ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran Baru' }}</h3>
                </div>
                <div class="p-6">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">Nama Tahun Ajaran</label>
                            <input v-model="form.name" type="text" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Contoh: 2024/2025" required>
                            <p v-if="form.errors.name" class="mt-1 text-[12px] text-red-600">{{ form.errors.name }}</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[12px] font-medium text-slate-700 mb-1">Tanggal Mulai</label>
                                <input v-model="form.start_date" type="date" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                                <p v-if="form.errors.start_date" class="mt-1 text-[12px] text-red-600">{{ form.errors.start_date }}</p>
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-slate-700 mb-1">Tanggal Selesai</label>
                                <input v-model="form.end_date" type="date" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                                <p v-if="form.errors.end_date" class="mt-1 text-[12px] text-red-600">{{ form.errors.end_date }}</p>
                            </div>
                        </div>

                        <div class="flex items-center pt-2">
                            <input type="checkbox" v-model="form.is_current" id="is_current" class="rounded border-slate-300 text-indigo-500 focus:ring-indigo-500 h-4 w-4">
                            <label for="is_current" class="ml-2 block text-[13px] font-medium text-slate-700">
                                Jadikan Tahun Ajaran Aktif Saat Ini
                            </label>
                        </div>
                        <p v-if="form.errors.is_current" class="mt-1 text-[12px] text-red-600">{{ form.errors.is_current }}</p>

                        <div class="pt-5 mt-2 flex justify-end gap-3 border-t border-slate-100">
                            <button type="button" @click="isModalOpen = false" class="px-4 py-2 text-[13px] font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 rounded-lg transition-colors">Batal</button>
                            <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-indigo-500 text-white text-[13px] font-medium rounded-lg hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 transition-colors disabled:opacity-50">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
