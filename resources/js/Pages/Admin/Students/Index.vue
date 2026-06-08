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
                <h1 class="text-2xl font-bold text-slate-800">Manajemen Siswa</h1>
                <p class="text-slate-500">Kelola data siswa, RFID, dan wali murid.</p>
            </div>
            <Link 
                href="/admin/students/create" 
                class="inline-flex items-center justify-center px-4 py-2.5 bg-primary border border-transparent rounded-xl font-medium text-white hover:bg-primary-dark transition-all"
            >
                <PlusIcon class="w-5 h-5 mr-2" />
                Tambah Siswa
            </Link>
        </div>

        <div class="glass rounded-2xl overflow-hidden shadow-sm">
            <!-- Toolbar -->
            <div class="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row gap-4 justify-between items-center">
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <MagnifyingGlassIcon class="h-5 w-5 text-slate-400" />
                    </div>
                    <input 
                        v-model="search"
                        type="text" 
                        class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm transition-colors" 
                        placeholder="Cari NIS atau Nama..."
                    >
                </div>
                
                <div class="w-full sm:w-auto">
                    <select v-model="classroomId" class="block w-full pl-3 pr-10 py-2 text-base border-slate-200 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-xl">
                        <option value="">Semua Kelas</option>
                        <option v-for="c in classrooms" :key="c.id" :value="c.id">{{ c.name }} (Tingkat {{ c.grade_level }})</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50/80 backdrop-blur">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">NIS</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">UID RFID</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Kenaikan</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr v-for="student in students.data" :key="student.id" class="hover:bg-white/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                {{ student.nis }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                {{ student.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ student.classroom?.name || '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 font-mono">
                                {{ student.rfid || 'Belum Ada' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-lg" 
                                      :class="{
                                          'bg-amber-100 text-amber-700': student.promotion_status === 'pending',
                                          'bg-green-100 text-green-700': student.promotion_status === 'naik' || student.promotion_status === 'lulus',
                                          'bg-red-100 text-red-700': student.promotion_status === 'tidak_naik'
                                      }">
                                    {{ student.promotion_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link :href="`/admin/students/${student.id}/edit`" class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors inline-block">
                                    <PencilSquareIcon class="w-5 h-5" />
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="students.data.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                Tidak ada data siswa ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between" v-if="students.links && students.data.length > 0">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-slate-700">
                            Menampilkan <span class="font-medium">{{ students.from }}</span> - <span class="font-medium">{{ students.to }}</span> dari <span class="font-medium">{{ students.total }}</span> hasil
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            <Link v-for="(link, i) in students.links" :key="i" :href="link.url || '#'" 
                                  class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                  :class="[
                                    link.active ? 'z-10 bg-primary border-primary text-white' : 'bg-white border-slate-300 text-slate-500 hover:bg-slate-50',
                                    i === 0 ? 'rounded-l-md' : '',
                                    i === students.links.length - 1 ? 'rounded-r-md' : '',
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
