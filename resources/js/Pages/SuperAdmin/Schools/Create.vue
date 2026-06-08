<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const form = useForm({
    name: '',
    slug: '',
    address: '',
    primary_color: '#4361ee',
    secondary_color: '#3f37c9',
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
            <Link href="/super-admin/schools" class="p-2 rounded-lg bg-white shadow-sm hover:bg-slate-50 transition-colors text-slate-500">
                <ArrowLeftIcon class="w-5 h-5" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Tambah Sekolah Baru</h1>
                <p class="text-slate-500">Daftarkan institusi/sekolah baru ke dalam sistem.</p>
            </div>
        </div>

        <div class="glass p-6 md:p-8 rounded-2xl max-w-3xl">
            <form @submit.prevent="submit" class="space-y-6">
                
                <!-- Nama Sekolah -->
                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama Sekolah *</label>
                    <input 
                        v-model="form.name" 
                        type="text" 
                        required
                        class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 transition-all"
                        placeholder="Contoh: SMA Negeri 1 Jakarta"
                    />
                    <p class="mt-1 text-sm text-red-600" v-if="form.errors.name">{{ form.errors.name }}</p>
                </div>

                <!-- Slug / Subdomain -->
                <div>
                    <label class="block text-sm font-medium text-slate-700">Subdomain (Slug) *</label>
                    <div class="mt-1 flex rounded-xl shadow-sm">
                        <input 
                            v-model="form.slug" 
                            type="text" 
                            required
                            class="flex-1 min-w-0 block w-full rounded-none rounded-l-xl border-slate-200 focus:border-primary focus:ring focus:ring-primary/20 transition-all"
                            placeholder="sman1jkt"
                        />
                        <span class="inline-flex items-center px-4 rounded-r-xl border border-l-0 border-slate-200 bg-slate-50 text-slate-500 sm:text-sm">
                            .domain.com
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-red-600" v-if="form.errors.slug">{{ form.errors.slug }}</p>
                </div>

                <!-- Alamat -->
                <div>
                    <label class="block text-sm font-medium text-slate-700">Alamat Lengkap</label>
                    <textarea 
                        v-model="form.address" 
                        rows="3"
                        class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 transition-all"
                    ></textarea>
                    <p class="mt-1 text-sm text-red-600" v-if="form.errors.address">{{ form.errors.address }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Warna Primary -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Warna Utama (HEX)</label>
                        <div class="mt-1 flex gap-3 items-center">
                            <input 
                                v-model="form.primary_color" 
                                type="color" 
                                class="h-10 w-10 p-1 block bg-white border border-slate-200 rounded-lg cursor-pointer"
                            />
                            <input 
                                v-model="form.primary_color" 
                                type="text" 
                                class="flex-1 rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 transition-all"
                            />
                        </div>
                    </div>
                    
                    <!-- Warna Secondary -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Warna Sekunder (HEX)</label>
                        <div class="mt-1 flex gap-3 items-center">
                            <input 
                                v-model="form.secondary_color" 
                                type="color" 
                                class="h-10 w-10 p-1 block bg-white border border-slate-200 rounded-lg cursor-pointer"
                            />
                            <input 
                                v-model="form.secondary_color" 
                                type="text" 
                                class="flex-1 rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 transition-all"
                            />
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-200 flex justify-end">
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 bg-primary text-white font-medium rounded-xl hover:bg-primary-dark focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        Simpan Sekolah
                    </button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>
