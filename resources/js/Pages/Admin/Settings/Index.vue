<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    school: Object,
});

const form = useForm({
    name: props.school.name || '',
    address: props.school.address || '',
    primary_color: props.school.primary_color || '#6366F1',
    secondary_color: props.school.secondary_color || '#4F46E5',
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
            <h1 class="text-[22px] font-medium text-slate-900 tracking-tight">Pengaturan Sekolah</h1>
            <p class="text-[13px] text-slate-500 mt-1">Sesuaikan profil dan batasan presensi sekolah Anda.</p>
        </div>

        <div class="bg-white border border-slate-200 p-6 md:p-8 rounded-[12px] max-w-4xl">
            <form @submit.prevent="submit" class="space-y-8">
                
                <!-- Identitas Sekolah -->
                <div>
                    <h3 class="text-[16px] font-medium text-slate-900 mb-4 border-b border-slate-100 pb-2">Identitas Dasar</h3>
                    <div class="grid grid-cols-1 gap-5">
                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">Nama Sekolah</label>
                            <input 
                                v-model="form.name" 
                                type="text" 
                                required
                                class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            />
                        </div>
                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">Alamat</label>
                            <textarea 
                                v-model="form.address" 
                                rows="2"
                                class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Tampilan -->
                <div>
                    <h3 class="text-[16px] font-medium text-slate-900 mb-4 border-b border-slate-100 pb-2">Tema & Tampilan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">Warna Utama (HEX)</label>
                            <div class="flex gap-2 items-center">
                                <input v-model="form.primary_color" type="color" class="h-[38px] w-[38px] p-0.5 block bg-white border border-slate-200 rounded-lg cursor-pointer"/>
                                <input v-model="form.primary_color" type="text" class="flex-1 px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">Warna Sekunder (HEX)</label>
                            <div class="flex gap-2 items-center">
                                <input v-model="form.secondary_color" type="color" class="h-[38px] w-[38px] p-0.5 block bg-white border border-slate-200 rounded-lg cursor-pointer"/>
                                <input v-model="form.secondary_color" type="text" class="flex-1 px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parameter Presensi -->
                <div>
                    <h3 class="text-[16px] font-medium text-slate-900 mb-4 border-b border-slate-100 pb-2">Parameter Presensi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">Jam Terlambat (HH:MM:SS)</label>
                            <input 
                                v-model="form.late_threshold" 
                                type="time" step="1"
                                required
                                class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            />
                        </div>
                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">Toleransi Terlambat (Menit)</label>
                            <input 
                                v-model="form.late_tolerance_minutes" 
                                type="number" min="0"
                                required
                                class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            />
                        </div>
                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">Jam Alpa (HH:MM:SS)</label>
                            <input 
                                v-model="form.absent_threshold" 
                                type="time" step="1"
                                required
                                class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            />
                        </div>
                    </div>
                </div>

                <div class="pt-5 border-t border-slate-100 flex justify-end">
                    <button 
                        type="submit" 
                        class="px-5 py-2 bg-indigo-500 text-white text-[14px] font-medium rounded-lg hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 transition-colors disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
