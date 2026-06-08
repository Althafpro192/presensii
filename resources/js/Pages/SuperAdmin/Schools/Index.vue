<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { PlusIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';

defineProps({
    schools: Object,
});
</script>

<template>
    <AppLayout>
        <Head title="Manajemen Sekolah" />

        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Manajemen Sekolah</h1>
                <p class="text-slate-500">Kelola data sekolah yang terdaftar di sistem.</p>
            </div>
            <Link 
                href="/super-admin/schools/create" 
                class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-xl font-medium text-white hover:bg-primary-dark transition-all"
            >
                <PlusIcon class="w-5 h-5 mr-2" />
                Tambah Sekolah
            </Link>
        </div>

        <div class="glass rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50/80 backdrop-blur">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Sekolah</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Subdomain (Slug)</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Siswa</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr v-for="school in schools.data" :key="school.id" class="hover:bg-white/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-slate-900">{{ school.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ school.slug }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ school.students_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-lg" 
                                      :class="school.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                                    {{ school.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <Link :href="`/super-admin/schools/${school.id}/edit`" class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors">
                                        <PencilSquareIcon class="w-5 h-5" />
                                    </Link>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="schools.data.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                Tidak ada data sekolah ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between" v-if="schools.links && schools.data.length > 0">
                <div class="flex-1 flex justify-between sm:hidden">
                    <!-- Mobile Pagination -->
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-slate-700">
                            Menampilkan <span class="font-medium">{{ schools.from }}</span> hingga <span class="font-medium">{{ schools.to }}</span> dari <span class="font-medium">{{ schools.total }}</span> hasil
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <Link v-for="(link, i) in schools.links" :key="i" :href="link.url || '#'" 
                                  class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                  :class="[
                                    link.active ? 'z-10 bg-primary border-primary text-white' : 'bg-white border-slate-300 text-slate-500 hover:bg-slate-50',
                                    i === 0 ? 'rounded-l-md' : '',
                                    i === schools.links.length - 1 ? 'rounded-r-md' : '',
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
