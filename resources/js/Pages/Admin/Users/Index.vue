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
        'admin_sekolah': 'bg-purple-100 text-purple-700',
        'teacher': 'bg-blue-100 text-blue-700',
        'kurikulum': 'bg-emerald-100 text-emerald-700',
        'orang_tua': 'bg-amber-100 text-amber-700',
    };
    return badges[role] || 'bg-slate-100 text-slate-700';
};
</script>

<template>
    <AppLayout>
        <Head title="Manajemen Pengguna" />

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-[22px] font-medium text-slate-900 tracking-tight">Manajemen Pengguna</h1>
                <p class="text-[13px] text-slate-500 mt-1">Kelola staf, guru, dan akun orang tua.</p>
            </div>
            
            <button @click="openCreateModal"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 border border-transparent rounded-lg font-medium text-[13px] text-white hover:bg-indigo-600 focus:outline-none transition-colors">
                <PlusIcon class="w-4 h-4" />
                Tambah Pengguna
            </button>
        </div>

        <div class="bg-white border border-slate-200 rounded-[12px] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Nama & Email</th>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Peran (Role)</th>
                            <th scope="col" class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 h-9 w-9 bg-slate-100 rounded-full flex items-center justify-center text-slate-500">
                                        <UserCircleIcon class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <div class="text-[14px] font-medium text-slate-900">{{ user.name }}</div>
                                        <div class="text-[13px] text-slate-500">{{ user.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span :class="['px-2.5 py-0.5 rounded-full text-[12px] font-medium uppercase tracking-wide', getRoleBadge(user.role)]">
                                    {{ user.role.replace('_', ' ') }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-right text-[13px] font-medium">
                                <button @click="openEditModal(user)" class="text-indigo-500 hover:text-indigo-700 mr-4 transition-colors">Edit</button>
                                <button @click="destroy(user.id)" class="text-red-500 hover:text-red-700 transition-colors" :disabled="user.id === $page.props.auth.user.id">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="users.length === 0">
                            <td colspan="3" class="px-5 py-10 text-center text-[13px] text-slate-500">
                                Belum ada data pengguna.
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
                    <h3 class="text-[16px] font-medium text-slate-900">{{ editingId ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h3>
                </div>
                <div class="p-6">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">Nama Lengkap</label>
                            <input v-model="form.name" type="text" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                            <p v-if="form.errors.name" class="mt-1 text-[12px] text-red-600">{{ form.errors.name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">Email Login</label>
                            <input v-model="form.email" type="email" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                            <p v-if="form.errors.email" class="mt-1 text-[12px] text-red-600">{{ form.errors.email }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">Peran (Role)</label>
                            <select v-model="form.role" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                                <option value="admin_sekolah">Admin (Tata Usaha)</option>
                                <option value="teacher">Guru / Wali Kelas</option>
                                <option value="kurikulum">Kurikulum</option>
                                <option value="orang_tua">Orang Tua</option>
                            </select>
                            <p v-if="form.errors.role" class="mt-1 text-[12px] text-red-600">{{ form.errors.role }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">
                                {{ editingId ? 'Password Baru (Opsional)' : 'Password' }}
                            </label>
                            <input v-model="form.password" type="password" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" :required="!editingId">
                            <p class="text-[11px] text-slate-500 mt-1" v-if="editingId">Biarkan kosong jika tidak ingin mengubah password.</p>
                            <p v-if="form.errors.password" class="mt-1 text-[12px] text-red-600">{{ form.errors.password }}</p>
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
