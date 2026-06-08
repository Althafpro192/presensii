<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    school: Object,
});

const form = useForm({
    name: props.school.name || '',
    slug: props.school.slug || '',
    address: props.school.address || '',
    primary_color: props.school.primary_color || '#4361ee',
    secondary_color: props.school.secondary_color || '#3f37c9',
    is_active: props.school.is_active === 1 || props.school.is_active === true,
});

const submit = () => {
    form.put(`/super-admin/schools/${props.school.id}`);
};
</script>

<template>
    <AppLayout>
        <Head :title="`Edit ${school.name}`" />

        <div class="mb-6 flex items-center gap-4">
            <Link href="/super-admin/schools" class="p-2 rounded-lg bg-white shadow-sm hover:bg-slate-50 transition-colors text-slate-500">
                <ArrowLeftIcon class="w-5 h-5" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Edit Sekolah</h1>
                <p class="text-slate-500">{{ school.name }}</p>
            </div>
        </div>

        <div class="glass p-6 md:p-8 rounded-2xl max-w-3xl">
            <form @submit.prevent="submit" class="space-y-6">
                
                <!-- Status -->
                <div class="flex items-center gap-3 bg-slate-50 p-4 rounded-xl border border-slate-100 mb-6">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" v-model="form.is_active" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                    </label>
                    <span class="text-sm font-medium text-slate-700">Status Sekolah Aktif</span>
                </div>

                <!-- Nama Sekolah -->
                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama Sekolah *</label>
                    <input 
                        v-model="form.name" 
                        type="text" 
                        required
                        class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 transition-all"
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

                <div class="pt-4 border-t border-slate-200 flex justify-between">
                    <button 
                        type="button" 
                        @click="$inertia.delete(`/super-admin/schools/${school.id}`)"
                        class="px-4 py-2 text-red-600 font-medium hover:bg-red-50 rounded-xl transition-all"
                    >
                        Hapus Sekolah
                    </button>
                    
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 bg-primary text-white font-medium rounded-xl hover:bg-primary-dark focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        Perbarui Sekolah
                    </button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>
