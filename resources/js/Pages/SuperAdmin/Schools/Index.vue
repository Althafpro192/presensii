<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { PlusIcon, PencilSquareIcon } from '@heroicons/vue/24/outline';

defineProps({
    schools: Object,
});
</script>

<template>
    <AppLayout>
        <Head title="Manajemen Sekolah" />

        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-[22px] font-medium text-slate-900">Manajemen Sekolah</h1>
                <p class="text-[13px] text-slate-500 mt-1">Kelola data sekolah yang terdaftar di sistem.</p>
            </div>
            <Link 
                href="/super-admin/schools/create" 
                class="inline-flex items-center px-4 py-2 bg-indigo-500 border border-transparent rounded-lg font-medium text-[13px] text-white hover:bg-indigo-600 transition-colors"
            >
                <PlusIcon class="w-4 h-4 mr-2" />
                Tambah Sekolah
            </Link>
        </div>

        <div class="bg-white border border-slate-200 rounded-[12px] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Nama Sekolah</th>
                            <th class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Subdomain (Slug)</th>
                            <th class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Siswa</th>
                            <th class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Status</th>
                            <th class="px-5 py-3 text-right text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="school in schools.data" :key="school.id" class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3 text-[14px] text-slate-900 font-medium">
                                {{ school.name }}
                            </td>
                            <td class="px-5 py-3 text-[14px] text-slate-500">
                                {{ school.slug }}
                            </td>
                            <td class="px-5 py-3 text-[14px] text-slate-500">
                                {{ school.students_count }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[12px] font-medium" 
                                      :class="school.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                    {{ school.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <Link :href="`/super-admin/schools/${school.id}/edit`" class="inline-flex p-1.5 text-slate-400 hover:text-indigo-500 hover:bg-indigo-50 rounded-lg transition-colors">
                                    <PencilSquareIcon class="w-4 h-4" />
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="schools.data.length === 0">
                            <td colspan="5" class="px-5 py-10 text-center text-[13px] text-slate-400">
                                Tidak ada data sekolah ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between" v-if="schools.links && schools.data.length > 0">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-[13px] text-slate-500">
                            Menampilkan <span class="font-medium text-slate-900">{{ schools.from }}</span> hingga <span class="font-medium text-slate-900">{{ schools.to }}</span> dari <span class="font-medium text-slate-900">{{ schools.total }}</span> hasil
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-lg -space-x-px" aria-label="Pagination">
                            <Link v-for="(link, i) in schools.links" :key="i" :href="link.url || '#'" 
                                  class="relative inline-flex items-center px-3 py-1.5 border text-[13px] font-medium"
                                  :class="[
                                    link.active ? 'z-10 bg-indigo-500 border-indigo-500 text-white' : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50',
                                    i === 0 ? 'rounded-l-lg' : '',
                                    i === schools.links.length - 1 ? 'rounded-r-lg' : '',
                                    !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                  ]"
                                  v-html="link.label"
                                  :preserve-scroll="true"
                            />
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
