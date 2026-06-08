<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { PlusIcon, UserCircleIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    users: Array,
});

const isModalOpen = ref(false);
const editingId = ref(null);

const form = useForm({
    name: '',
    email: '',
    role: 'teacher',
    password: '',
});

const openCreateModal = () => {
    editingId.value = null;
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (user) => {
    editingId.value = user.id;
    form.name = user.name;
    form.email = user.email;
    form.role = user.role;
    form.password = ''; // Reset password field for security
    isModalOpen.value = true;
};

const submit = () => {
    if (editingId.value) {
        form.put(route('admin.users.update', editingId.value), {
            onSuccess: () => { isModalOpen.value = false; }
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => { isModalOpen.value = false; }
        });
    }
};

const destroy = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
        router.delete(route('admin.users.destroy', id));
    }
};

const getRoleBadge = (role) => {
    const badges = {
        'admin': 'bg-purple-100 text-purple-700',
        'teacher': 'bg-blue-100 text-blue-700',
        'kurikulum': 'bg-emerald-100 text-emerald-700',
        'parent': 'bg-amber-100 text-amber-700',
    };
    return badges[role] || 'bg-slate-100 text-slate-700';
};
</script>

<template>
    <AppLayout>
        <Head title="Manajemen Pengguna" />

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Pengguna</h1>
                <p class="text-sm text-slate-500">Kelola staf, guru, dan akun orang tua.</p>
            </div>
            
            <button @click="openCreateModal"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-all">
                <PlusIcon class="w-4 h-4" />
                Tambah Pengguna
            </button>
        </div>

        <div class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-sm rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200/60">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama & Email</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Peran (Role)</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/60">
                        <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-500">
                                        <UserCircleIcon class="w-6 h-6" />
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-slate-800">{{ user.name }}</div>
                                        <div class="text-sm text-slate-500">{{ user.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span :class="['px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider', getRoleBadge(user.role)]">
                                    {{ user.role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="openEditModal(user)" class="text-indigo-600 hover:text-indigo-900 mr-4 font-semibold">Edit</button>
                                <button @click="destroy(user.id)" class="text-red-600 hover:text-red-900 font-semibold" :disabled="user.id === $page.props.auth.user.id">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="users.length === 0">
                            <td colspan="3" class="px-6 py-8 text-center text-slate-500">
                                Belum ada data pengguna.
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
                    <h3 class="text-lg font-bold text-slate-800">{{ editingId ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h3>
                </div>
                <div class="p-6">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                            <input v-model="form.name" type="text" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Email Login</label>
                            <input v-model="form.email" type="email" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Peran (Role)</label>
                            <select v-model="form.role" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="admin">Admin (Tata Usaha)</option>
                                <option value="teacher">Guru / Wali Kelas</option>
                                <option value="kurikulum">Kurikulum</option>
                                <option value="parent">Orang Tua</option>
                            </select>
                            <p v-if="form.errors.role" class="mt-1 text-sm text-red-600">{{ form.errors.role }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700">
                                {{ editingId ? 'Password Baru (Opsional)' : 'Password' }}
                            </label>
                            <input v-model="form.password" type="password" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :required="!editingId">
                            <p class="text-xs text-slate-500 mt-1" v-if="editingId">Biarkan kosong jika tidak ingin mengubah password.</p>
                            <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
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
