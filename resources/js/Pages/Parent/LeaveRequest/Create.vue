<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { FileIcon, SendIcon, ArrowLeftIcon, UploadCloudIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    children: Array,
});

const form = useForm({
    student_id: props.children.length === 1 ? props.children[0].id : '',
    date: new Date().toISOString().split('T')[0],
    type: 'izin',
    reason: '',
    attachment: null,
});

const fileInput = ref(null);
const fileName = ref('');

const handleFileUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.attachment = file;
        fileName.value = file.name;
    }
};

const submit = () => {
    form.post(route('parent.leave-requests.store'));
};
</script>

<template>
    <AppLayout>
        <Head title="Pengajuan Izin Online" />

        <div class="mb-6 flex items-center gap-4">
            <Link :href="route('parent.dashboard')" class="p-2 rounded-xl bg-white/50 backdrop-blur-md shadow-sm border border-white/20 hover:bg-white/80 transition-all text-slate-500">
                <ArrowLeftIcon class="w-5 h-5" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Pengajuan Izin Online</h1>
                <p class="text-sm text-slate-500">Ajukan surat izin atau keterangan sakit untuk anak Anda</p>
            </div>
        </div>

        <div class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-sm p-6 rounded-2xl max-w-2xl">
            <form @submit.prevent="submit" class="space-y-6">
                
                <div v-if="children.length > 1">
                    <label for="student_id" class="block text-sm font-medium text-slate-700">Pilih Anak</label>
                    <select id="student_id" v-model="form.student_id" 
                        class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white/50 backdrop-blur-sm transition-all" 
                        required>
                        <option value="" disabled>Pilih Anak</option>
                        <option v-for="child in children" :key="child.id" :value="child.id">
                            {{ child.name }} ({{ child.classroom?.name || 'Belum ada kelas' }})
                        </option>
                    </select>
                    <p v-if="form.errors.student_id" class="mt-1 text-sm text-red-600">{{ form.errors.student_id }}</p>
                </div>
                
                <div v-else-if="children.length === 1">
                    <label class="block text-sm font-medium text-slate-700">Anak</label>
                    <div class="mt-1 p-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700">
                        {{ children[0].name }} ({{ children[0].classroom?.name || 'Belum ada kelas' }})
                    </div>
                </div>

                <div>
                    <label for="date" class="block text-sm font-medium text-slate-700">Tanggal Izin/Sakit</label>
                    <input type="date" id="date" v-model="form.date" 
                        class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white/50 backdrop-blur-sm transition-all" 
                        required>
                    <p v-if="form.errors.date" class="mt-1 text-sm text-red-600">{{ form.errors.date }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Jenis Pengajuan</label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" v-model="form.type" value="izin" class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                            <span class="ml-2 text-slate-700">Izin</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" v-model="form.type" value="sakit" class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                            <span class="ml-2 text-slate-700">Sakit</span>
                        </label>
                    </div>
                    <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">{{ form.errors.type }}</p>
                </div>

                <div>
                    <label for="reason" class="block text-sm font-medium text-slate-700">Alasan Keterangan</label>
                    <textarea id="reason" v-model="form.reason" rows="3"
                        class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white/50 backdrop-blur-sm transition-all" 
                        placeholder="Tuliskan alasan izin atau sakit secara singkat..."
                        required></textarea>
                    <p v-if="form.errors.reason" class="mt-1 text-sm text-red-600">{{ form.errors.reason }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Lampiran (Surat Dokter / Keterangan)</label>
                    
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-xl bg-white/50 hover:bg-slate-50 transition-colors cursor-pointer"
                         @click="fileInput.click()">
                        <div class="space-y-1 text-center">
                            <UploadCloudIcon v-if="!fileName" class="mx-auto h-12 w-12 text-slate-400" />
                            <FileIcon v-else class="mx-auto h-12 w-12 text-indigo-500" />
                            
                            <div class="flex text-sm text-slate-600 justify-center">
                                <span class="relative rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    {{ fileName || 'Upload file' }}
                                </span>
                            </div>
                            <p v-if="!fileName" class="text-xs text-slate-500">JPG, PNG, PDF maksimal 2MB (Sistem akan mengkompresi gambar otomatis)</p>
                        </div>
                        <input ref="fileInput" type="file" class="sr-only" accept=".jpg,.jpeg,.png,.pdf" @change="handleFileUpload">
                    </div>
                    <p v-if="form.errors.attachment" class="mt-1 text-sm text-red-600">{{ form.errors.attachment }}</p>
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-slate-200/60">
                    <button type="submit" :disabled="form.processing || !form.student_id"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all disabled:opacity-50">
                        <SendIcon class="w-4 h-4" />
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
