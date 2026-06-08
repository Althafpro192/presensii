<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLeftIcon, SaveIcon } from 'lucide-vue-next';

defineProps({
    classrooms: Array,
});

const form = useForm({
    nis: '',
    name: '',
    classroom_id: '',
    rfid: '',
    parent_phone: '',
});

const submit = () => {
    form.post(route('admin.students.store'));
};
</script>

<template>
    <AppLayout>
        <Head title="Tambah Siswa" />

        <div class="mb-6 flex items-center gap-4">
            <Link :href="route('admin.students.index')" class="p-2 rounded-xl bg-white/50 backdrop-blur-md shadow-sm border border-white/20 hover:bg-white/80 transition-all text-slate-500">
                <ArrowLeftIcon class="w-5 h-5" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Tambah Siswa Baru</h1>
                <p class="text-sm text-slate-500">Masukkan data siswa ke dalam sistem</p>
            </div>
        </div>

        <div class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-sm p-6 rounded-2xl max-w-2xl">
            <form @submit.prevent="submit" class="space-y-6">
                
                <div>
                    <label for="nis" class="block text-sm font-medium text-slate-700">NIS (Nomor Induk Siswa)</label>
                    <input type="text" id="nis" v-model="form.nis" 
                        class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white/50 backdrop-blur-sm transition-all" 
                        required>
                    <p v-if="form.errors.nis" class="mt-1 text-sm text-red-600">{{ form.errors.nis }}</p>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                    <input type="text" id="name" v-model="form.name" 
                        class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white/50 backdrop-blur-sm transition-all" 
                        required>
                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label for="classroom_id" class="block text-sm font-medium text-slate-700">Kelas</label>
                    <select id="classroom_id" v-model="form.classroom_id" 
                        class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white/50 backdrop-blur-sm transition-all" 
                        required>
                        <option value="" disabled>Pilih Kelas</option>
                        <option v-for="classroom in classrooms" :key="classroom.id" :value="classroom.id">
                            {{ classroom.name }}
                        </option>
                    </select>
                    <p v-if="form.errors.classroom_id" class="mt-1 text-sm text-red-600">{{ form.errors.classroom_id }}</p>
                </div>

                <div>
                    <label for="rfid" class="block text-sm font-medium text-slate-700">UID Kartu RFID (Opsional)</label>
                    <input type="text" id="rfid" v-model="form.rfid" 
                        class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white/50 backdrop-blur-sm transition-all">
                    <p v-if="form.errors.rfid" class="mt-1 text-sm text-red-600">{{ form.errors.rfid }}</p>
                </div>

                <div>
                    <label for="parent_phone" class="block text-sm font-medium text-slate-700">Nomor WhatsApp Orang Tua (Opsional)</label>
                    <input type="text" id="parent_phone" v-model="form.parent_phone" placeholder="Contoh: 08123456789"
                        class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white/50 backdrop-blur-sm transition-all">
                    <p v-if="form.errors.parent_phone" class="mt-1 text-sm text-red-600">{{ form.errors.parent_phone }}</p>
                    <p class="mt-1 text-xs text-slate-500">Nomor akan digunakan untuk verifikasi login orang tua dan bot WhatsApp.</p>
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-slate-200/60">
                    <button type="submit" :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all disabled:opacity-50">
                        <SaveIcon class="w-4 h-4" />
                        Simpan Siswa
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
