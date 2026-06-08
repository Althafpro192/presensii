<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { SaveIcon, Settings2Icon } from 'lucide-vue-next';

const props = defineProps({
    features: Array,
});

const form = useForm({
    features: props.features.map(f => ({
        feature_name: f.feature_name,
        is_enabled: f.is_enabled == 1 || f.is_enabled === true,
    }))
});

const getFeatureLabel = (name) => {
    const labels = {
        'pelanggaran_siswa': 'Fitur Pelanggaran Siswa',
        'n8n_whatsapp': 'Notifikasi n8n WhatsApp',
        'dashboard_sakit': 'Dashboard Pemantauan Sakit',
        'ekspor_excel': 'Ekspor Laporan Excel',
    };
    return labels[name] || name;
};

const getFeatureDesc = (name) => {
    const descs = {
        'pelanggaran_siswa': 'Mengaktifkan modul untuk mencatat dan mengelola pelanggaran indisipliner siswa.',
        'n8n_whatsapp': 'Mengaktifkan webhook n8n untuk bot WhatsApp (presensi, info kelas, dan izin online).',
        'dashboard_sakit': 'Menampilkan widget pemantauan khusus bagi siswa yang sering sakit.',
        'ekspor_excel': 'Mengizinkan admin sekolah dan kurikulum mengekspor data absensi ke format Excel/PDF.',
    };
    return descs[name] || '';
};

const submit = () => {
    form.put(route('admin.features.update'));
};
</script>

<template>
    <AppLayout>
        <Head title="Pengaturan Fitur" />

        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-100 text-indigo-600 rounded-xl">
                    <Settings2Icon class="w-6 h-6" />
                </div>
                <div>
                    <h1 class="text-[22px] font-medium text-slate-900 tracking-tight">Pengaturan Fitur Tambahan</h1>
                    <p class="text-sm text-slate-500">Aktifkan atau nonaktifkan modul spesifik untuk sekolah Anda</p>
                </div>
            </div>
            
            <button @click="submit" :disabled="form.processing"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 border border-transparent rounded-[8px] font-medium text-white hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all disabled:opacity-50">
                <SaveIcon class="w-4 h-4" />
                Simpan Konfigurasi
            </button>
        </div>

        <div class="bg-white border border-slate-200 rounded-[12px] overflow-hidden">
            <ul role="list" class="divide-y divide-slate-200/60">
                <li v-for="(feature, index) in form.features" :key="feature.feature_name" class="p-6 flex items-center justify-between hover:bg-slate-50/50 transition-colors">
                    <div class="flex-1 pr-8">
                        <label :for="`feature-${index}`" class="text-[16px] font-medium text-slate-900 cursor-pointer">
                            {{ getFeatureLabel(feature.feature_name) }}
                        </label>
                        <p class="mt-1 text-sm text-slate-500">{{ getFeatureDesc(feature.feature_name) }}</p>
                    </div>
                    <div class="flex items-center">
                        <button type="button" 
                            :class="[feature.is_enabled ? 'bg-indigo-600' : 'bg-slate-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2']"
                            role="switch" 
                            :aria-checked="feature.is_enabled"
                            @click="feature.is_enabled = !feature.is_enabled">
                            <span aria-hidden="true" 
                                :class="[feature.is_enabled ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                        </button>
                    </div>
                </li>
            </ul>
            
            <div v-if="form.features.length === 0" class="p-8 text-center text-slate-500">
                Tidak ada fitur tambahan yang tersedia untuk dikonfigurasi saat ini.
            </div>
        </div>
    </AppLayout>
</template>
