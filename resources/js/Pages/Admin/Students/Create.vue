<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLeftIcon } from 'lucide-vue-next';

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
            <Link :href="route('admin.students.index')" class="p-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-slate-500">
                <ArrowLeftIcon class="w-5 h-5" />
            </Link>
            <div>
                <h1 class="text-[22px] font-medium text-slate-900 tracking-tight">Tambah Siswa Baru</h1>
                <p class="text-[13px] text-slate-500 mt-1">Masukkan data siswa ke dalam sistem</p>
            </div>
        </div>

        <div class="bg-white border border-slate-200 p-6 md:p-8 rounded-[12px] max-w-2xl">
            <form @submit.prevent="submit" class="space-y-5">
                
                <div>
                    <label for="nis" class="block text-[12px] font-medium text-slate-700 mb-1">NIS (Nomor Induk Siswa)</label>
                    <input type="text" id="nis" v-model="form.nis" 
                        class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" 
                        required>
                    <p v-if="form.errors.nis" class="mt-1 text-[12px] text-red-600">{{ form.errors.nis }}</p>
                </div>

                <div>
                    <label for="name" class="block text-[12px] font-medium text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" id="name" v-model="form.name" 
                        class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" 
                        required>
                    <p v-if="form.errors.name" class="mt-1 text-[12px] text-red-600">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label for="classroom_id" class="block text-[12px] font-medium text-slate-700 mb-1">Kelas</label>
                    <select id="classroom_id" v-model="form.classroom_id" 
                        class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" 
                        required>
                        <option value="" disabled>Pilih Kelas</option>
                        <option v-for="classroom in classrooms" :key="classroom.id" :value="classroom.id">
                            {{ classroom.name }}
                        </option>
                    </select>
                    <p v-if="form.errors.classroom_id" class="mt-1 text-[12px] text-red-600">{{ form.errors.classroom_id }}</p>
                </div>

                <div>
                    <label for="rfid" class="block text-[12px] font-medium text-slate-700 mb-1">UID Kartu RFID (Opsional)</label>
                    <input type="text" id="rfid" v-model="form.rfid" 
                        class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <p v-if="form.errors.rfid" class="mt-1 text-[12px] text-red-600">{{ form.errors.rfid }}</p>
                </div>

                <div>
                    <label for="parent_phone" class="block text-[12px] font-medium text-slate-700 mb-1">Nomor WhatsApp Orang Tua (Opsional)</label>
                    <input type="text" id="parent_phone" v-model="form.parent_phone" placeholder="Contoh: 08123456789"
                        class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <p v-if="form.errors.parent_phone" class="mt-1 text-[12px] text-red-600">{{ form.errors.parent_phone }}</p>
                    <p class="mt-1 text-[11px] text-slate-500">Nomor akan digunakan untuk verifikasi login orang tua dan bot WhatsApp.</p>
                </div>

                <div class="flex items-center justify-end pt-5 mt-2 border-t border-slate-100">
                    <button type="submit" :disabled="form.processing"
                        class="px-5 py-2 bg-indigo-500 text-white text-[14px] font-medium rounded-lg hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 transition-colors disabled:opacity-50">
                        Simpan Siswa
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
