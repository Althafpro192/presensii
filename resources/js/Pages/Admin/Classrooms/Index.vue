<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { PlusIcon, UsersIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    classrooms: Array,
});

const isModalOpen = ref(false);
const editingId = ref(null);

const form = useForm({
    name: '',
    grade_level: 10,
    order: 0,
});

const openCreateModal = () => {
    editingId.value = null;
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (classroom) => {
    editingId.value = classroom.id;
    form.name = classroom.name;
    form.grade_level = classroom.grade_level;
    form.order = classroom.order;
    isModalOpen.value = true;
};

const submit = () => {
    if (editingId.value) {
        form.put(route('admin.classrooms.update', editingId.value), {
            onSuccess: () => { isModalOpen.value = false; }
        });
    } else {
        form.post(route('admin.classrooms.store'), {
            onSuccess: () => { isModalOpen.value = false; }
        });
    }
};

const destroy = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus kelas ini?')) {
        router.delete(route('admin.classrooms.destroy', id));
    }
};
</script>

<template>
    <AppLayout>
        <Head title="Manajemen Kelas" />

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-[22px] font-medium text-slate-900 tracking-tight">Manajemen Kelas</h1>
                <p class="text-[13px] text-slate-500 mt-1">Kelola data kelas di sekolah Anda.</p>
            </div>
            
            <button @click="openCreateModal"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 border border-transparent rounded-lg font-medium text-[13px] text-white hover:bg-indigo-600 focus:outline-none transition-colors">
                <PlusIcon class="w-4 h-4" />
                Tambah Kelas
            </button>
        </div>

        <div class="bg-white border border-slate-200 rounded-[12px] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Nama Kelas</th>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Tingkat (Grade)</th>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200 text-center">Jumlah Siswa</th>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="classroom in classrooms" :key="classroom.id" class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3 whitespace-nowrap text-[14px] font-medium text-slate-900">
                                {{ classroom.name }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-[14px] text-slate-600">
                                Tingkat {{ classroom.grade_level }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[12px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                    <UsersIcon class="w-3.5 h-3.5" /> {{ classroom.students_count || 0 }} Siswa
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-right text-[13px] font-medium">
                                <button @click="openEditModal(classroom)" class="text-indigo-500 hover:text-indigo-700 mr-4 transition-colors">Edit</button>
                                <button @click="destroy(classroom.id)" class="text-red-500 hover:text-red-700 transition-colors">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="classrooms.length === 0">
                            <td colspan="4" class="px-5 py-10 text-center text-[13px] text-slate-400">
                                Belum ada data kelas. Silakan tambah kelas baru.
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
                    <h3 class="text-[16px] font-medium text-slate-900">{{ editingId ? 'Edit Kelas' : 'Tambah Kelas Baru' }}</h3>
                </div>
                <div class="p-6">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">Nama Kelas</label>
                            <input v-model="form.name" type="text" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Contoh: X IPA 1" required>
                            <p v-if="form.errors.name" class="mt-1 text-[12px] text-red-600">{{ form.errors.name }}</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[12px] font-medium text-slate-700 mb-1">Tingkat (Grade)</label>
                                <select v-model="form.grade_level" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                                    <option :value="10">10 (X)</option>
                                    <option :value="11">11 (XI)</option>
                                    <option :value="12">12 (XII)</option>
                                </select>
                                <p v-if="form.errors.grade_level" class="mt-1 text-[12px] text-red-600">{{ form.errors.grade_level }}</p>
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-slate-700 mb-1">Urutan (Opsional)</label>
                                <input v-model="form.order" type="number" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <p class="text-[11px] text-slate-400 mt-1">Untuk keperluan sorting.</p>
                            </div>
                        </div>

                        <div class="pt-5 mt-2 flex justify-end gap-3 border-t border-slate-100">
                            <button type="button" @click="isModalOpen = false" class="px-4 py-2 text-[13px] font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 rounded-lg transition-colors">Batal</button>
                            <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-indigo-500 text-white text-[13px] font-medium rounded-lg hover:bg-indigo-600 transition-colors disabled:opacity-50">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
