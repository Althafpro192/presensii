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
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Kelas</h1>
                <p class="text-sm text-slate-500">Kelola data kelas di sekolah Anda.</p>
            </div>
            
            <button @click="openCreateModal"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-all">
                <PlusIcon class="w-4 h-4" />
                Tambah Kelas
            </button>
        </div>

        <div class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-sm rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200/60">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Kelas</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tingkat (Grade)</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Jumlah Siswa</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/60">
                        <tr v-for="classroom in classrooms" :key="classroom.id" class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800">
                                {{ classroom.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                Tingkat {{ classroom.grade_level }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                    <UsersIcon class="w-3.5 h-3.5" /> {{ classroom.students_count || 0 }} Siswa
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="openEditModal(classroom)" class="text-indigo-600 hover:text-indigo-900 mr-4 font-semibold">Edit</button>
                                <button @click="destroy(classroom.id)" class="text-red-600 hover:text-red-900 font-semibold">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="classrooms.length === 0">
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                Belum ada data kelas. Silakan tambah kelas baru.
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
                    <h3 class="text-lg font-bold text-slate-800">{{ editingId ? 'Edit Kelas' : 'Tambah Kelas Baru' }}</h3>
                </div>
                <div class="p-6">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Nama Kelas</label>
                            <input v-model="form.name" type="text" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: X IPA 1" required>
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Tingkat (Grade)</label>
                                <select v-model="form.grade_level" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option :value="10">10 (X)</option>
                                    <option :value="11">11 (XI)</option>
                                    <option :value="12">12 (XII)</option>
                                </select>
                                <p v-if="form.errors.grade_level" class="mt-1 text-sm text-red-600">{{ form.errors.grade_level }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Urutan (Opsional)</label>
                                <input v-model="form.order" type="number" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="text-xs text-slate-500 mt-1">Untuk keperluan sorting.</p>
                            </div>
                        </div>

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
