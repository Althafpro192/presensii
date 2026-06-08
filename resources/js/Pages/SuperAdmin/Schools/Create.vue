<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const form = useForm({
    name: '',
    slug: '',
    address: '',
    primary_color: '#6366F1',
    secondary_color: '#4F46E5',
    subscription_ends_at: '',
});

const submit = () => {
    form.post('/super-admin/schools');
};
</script>

<template>
    <AppLayout>
        <Head title="Tambah Sekolah" />

        <div class="mb-6 flex items-center gap-4">
            <Link href="/super-admin/schools" class="p-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-slate-500">
                <ArrowLeftIcon class="w-5 h-5" />
            </Link>
            <div>
                <h1 class="text-[22px] font-medium text-slate-900">Tambah Sekolah Baru</h1>
                <p class="text-[13px] text-slate-500">Daftarkan institusi/sekolah baru ke dalam sistem.</p>
            </div>
        </div>

        <div class="bg-white border border-slate-200 p-6 md:p-8 rounded-[12px] max-w-3xl">
            <form @submit.prevent="submit" class="space-y-5">
                
                <!-- Nama Sekolah -->
                <div>
                    <label class="block text-[12px] font-medium text-slate-700 mb-1">Nama Sekolah *</label>
                    <input 
                        v-model="form.name" 
                        type="text" 
                        required
                        class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                        placeholder="Contoh: SMA Negeri 1 Jakarta"
                    />
                    <p class="mt-1 text-[12px] text-red-600" v-if="form.errors.name">{{ form.errors.name }}</p>
                </div>

                <!-- Slug / Subdomain -->
                <div>
                    <label class="block text-[12px] font-medium text-slate-700 mb-1">Subdomain (Slug) *</label>
                    <div class="flex rounded-lg border border-slate-200 overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 transition-colors">
                        <input 
                            v-model="form.slug" 
                            type="text" 
                            required
                            class="flex-1 px-3 py-2 border-none text-[14px] text-slate-900 placeholder-slate-400 focus:ring-0"
                            placeholder="sman1jkt"
                        />
                        <span class="inline-flex items-center px-4 bg-slate-50 text-[13px] text-slate-500 border-l border-slate-200">
                            .domain.com
                        </span>
                    </div>
                    <p class="mt-1 text-[12px] text-red-600" v-if="form.errors.slug">{{ form.errors.slug }}</p>
                </div>

                <!-- Alamat -->
                <div>
                    <label class="block text-[12px] font-medium text-slate-700 mb-1">Alamat Lengkap</label>
                    <textarea 
                        v-model="form.address" 
                        rows="3"
                        class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    ></textarea>
                    <p class="mt-1 text-[12px] text-red-600" v-if="form.errors.address">{{ form.errors.address }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Warna Primary -->
                    <div>
                        <label class="block text-[12px] font-medium text-slate-700 mb-1">Warna Utama (HEX)</label>
                        <div class="flex gap-2 items-center">
                            <input 
                                v-model="form.primary_color" 
                                type="color" 
                                class="h-[38px] w-[38px] p-0.5 block bg-white border border-slate-200 rounded-lg cursor-pointer"
                            />
                            <input 
                                v-model="form.primary_color" 
                                type="text" 
                                class="flex-1 px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            />
                        </div>
                    </div>
                    
                    <!-- Warna Secondary -->
                    <div>
                        <label class="block text-[12px] font-medium text-slate-700 mb-1">Warna Sekunder (HEX)</label>
                        <div class="flex gap-2 items-center">
                            <input 
                                v-model="form.secondary_color" 
                                type="color" 
                                class="h-[38px] w-[38px] p-0.5 block bg-white border border-slate-200 rounded-lg cursor-pointer"
                            />
                            <input 
                                v-model="form.secondary_color" 
                                type="text" 
                                class="flex-1 px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            />
                        </div>
                    </div>
                </div>

                <div class="pt-5 flex justify-end">
                    <button 
                        type="submit" 
                        class="px-5 py-2 bg-indigo-500 text-white text-[14px] font-medium rounded-lg hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 transition-colors disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        Simpan Sekolah
                    </button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>
