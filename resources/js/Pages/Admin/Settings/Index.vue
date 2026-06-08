<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    school: Object,
});

const form = useForm({
    name: props.school.name || '',
    address: props.school.address || '',
    primary_color: props.school.primary_color || '#4361ee',
    secondary_color: props.school.secondary_color || '#3f37c9',
    late_threshold: props.school.settings?.late_threshold || '07:00:00',
    absent_threshold: props.school.settings?.absent_threshold || '15:00:00',
    late_tolerance_minutes: props.school.settings?.late_tolerance_minutes || 15,
});

const submit = () => {
    form.put('/admin/settings');
};
</script>

<template>
    <AppLayout>
        <Head title="Pengaturan Sekolah" />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Pengaturan Sekolah</h1>
            <p class="text-slate-500">Sesuaikan profil dan batasan presensi sekolah Anda.</p>
        </div>

        <div class="glass p-6 md:p-8 rounded-2xl max-w-4xl">
            <form @submit.prevent="submit" class="space-y-8">
                
                <!-- Identitas Sekolah -->
                <div>
                    <h3 class="text-lg font-semibold text-slate-800 mb-4 border-b border-slate-200 pb-2">Identitas Dasar</h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Nama Sekolah</label>
                            <input 
                                v-model="form.name" 
                                type="text" 
                                required
                                class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring focus:ring-primary/20"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Alamat</label>
                            <textarea 
                                v-model="form.address" 
                                rows="2"
                                class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring focus:ring-primary/20"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Tampilan -->
                <div>
                    <h3 class="text-lg font-semibold text-slate-800 mb-4 border-b border-slate-200 pb-2">Tema & Tampilan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Warna Utama (HEX)</label>
                            <div class="mt-1 flex gap-3 items-center">
                                <input v-model="form.primary_color" type="color" class="h-10 w-10 p-1 block bg-white border border-slate-200 rounded-lg cursor-pointer"/>
                                <input v-model="form.primary_color" type="text" class="flex-1 rounded-xl border-slate-200 shadow-sm focus:border-primary" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Warna Sekunder (HEX)</label>
                            <div class="mt-1 flex gap-3 items-center">
                                <input v-model="form.secondary_color" type="color" class="h-10 w-10 p-1 block bg-white border border-slate-200 rounded-lg cursor-pointer"/>
                                <input v-model="form.secondary_color" type="text" class="flex-1 rounded-xl border-slate-200 shadow-sm focus:border-primary" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parameter Presensi -->
                <div>
                    <h3 class="text-lg font-semibold text-slate-800 mb-4 border-b border-slate-200 pb-2">Parameter Presensi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Jam Terlambat (HH:MM:SS)</label>
                            <input 
                                v-model="form.late_threshold" 
                                type="time" step="1"
                                required
                                class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring focus:ring-primary/20"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Toleransi Terlambat (Menit)</label>
                            <input 
                                v-model="form.late_tolerance_minutes" 
                                type="number" min="0"
                                required
                                class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring focus:ring-primary/20"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Jam Alpa (HH:MM:SS)</label>
                            <input 
                                v-model="form.absent_threshold" 
                                type="time" step="1"
                                required
                                class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring focus:ring-primary/20"
                            />
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 bg-primary text-white font-medium rounded-xl hover:bg-primary-dark transition-all disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
