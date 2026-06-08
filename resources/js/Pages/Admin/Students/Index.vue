<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { PlusIcon, PencilSquareIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    students: Object,
    classrooms: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const classroomId = ref(props.filters.classroom_id || '');

watch([search, classroomId], () => {
    router.get('/admin/students', {
        search: search.value,
        classroom_id: classroomId.value,
    }, { preserveState: true, replace: true });
});
</script>

<template>
    <AppLayout>
        <Head title="Manajemen Siswa" />

        <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-[22px] font-medium text-slate-900">Manajemen Siswa</h1>
                <p class="text-[13px] text-slate-500 mt-1">Kelola data siswa, baris RFID, dan wali murid.</p>
            </div>
            <Link 
                href="/admin/students/create" 
                class="inline-flex items-center justify-center px-4 py-2 bg-indigo-500 border border-transparent rounded-lg font-medium text-[13px] text-white hover:bg-indigo-600 transition-colors"
            >
                <PlusIcon class="w-4 h-4 mr-2" />
                Tambah Siswa
            </Link>
        </div>

        <div class="bg-white border border-slate-200 rounded-[12px] overflow-hidden">
            <!-- Toolbar -->
            <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row gap-4 justify-between items-center">
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <MagnifyingGlassIcon class="h-4 w-4 text-slate-400" />
                    </div>
                    <input 
                        v-model="search"
                        type="text" 
                        class="block w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-lg text-[13px] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" 
                        placeholder="Cari NIS atau Nama..."
                    >
                </div>
                
                <div class="w-full sm:w-auto">
                    <select v-model="classroomId" class="block w-full pl-3 pr-8 py-2 bg-white border border-slate-200 rounded-lg text-[13px] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="">Semua Kelas</option>
                        <option v-for="c in classrooms" :key="c.id" :value="c.id">{{ c.name }} (Tingkat {{ c.grade_level }})</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">NIS</th>
                            <th class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Nama Siswa</th>
                            <th class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Kelas</th>
                            <th class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">UID RFID</th>
                            <th class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Status</th>
                            <th class="px-5 py-3 text-right text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="student in students.data" :key="student.id" class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3 text-[14px] text-slate-900 font-medium">
                                {{ student.nis }}
                            </td>
                            <td class="px-5 py-3 text-[14px] text-slate-900">
                                {{ student.name }}
                            </td>
                            <td class="px-5 py-3 text-[14px] text-slate-500">
                                {{ student.classroom?.name || '-' }}
                            </td>
                            <td class="px-5 py-3 text-[13px] text-slate-500 font-mono">
                                {{ student.rfid || 'Belum Ada' }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2.5 py-0.5 text-[12px] font-medium rounded-full" 
                                      :class="{
                                          'bg-yellow-100 text-yellow-800': student.promotion_status === 'pending',
                                          'bg-green-100 text-green-800': student.promotion_status === 'naik' || student.promotion_status === 'lulus',
                                          'bg-red-100 text-red-800': student.promotion_status === 'tidak_naik'
                                      }">
                                    {{ student.promotion_status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <Link :href="`/admin/students/${student.id}/edit`" class="inline-flex p-1.5 text-slate-400 hover:text-indigo-500 hover:bg-indigo-50 rounded-lg transition-colors">
                                    <PencilSquareIcon class="w-4 h-4" />
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="students.data.length === 0">
                            <td colspan="6" class="px-5 py-10 text-center text-[13px] text-slate-400">
                                Tidak ada data siswa ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between" v-if="students.links && students.data.length > 0">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-[13px] text-slate-500">
                            Menampilkan <span class="font-medium text-slate-900">{{ students.from }}</span> - <span class="font-medium text-slate-900">{{ students.to }}</span> dari <span class="font-medium text-slate-900">{{ students.total }}</span> hasil
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-lg -space-x-px">
                            <Link v-for="(link, i) in students.links" :key="i" :href="link.url || '#'" 
                                  class="relative inline-flex items-center px-3 py-1.5 border text-[13px] font-medium"
                                  :class="[
                                    link.active ? 'z-10 bg-indigo-500 border-indigo-500 text-white' : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50',
                                    i === 0 ? 'rounded-l-lg' : '',
                                    i === students.links.length - 1 ? 'rounded-r-lg' : '',
                                    !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                  ]"
                                  v-html="link.label"
                            />
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
