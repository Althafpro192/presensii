<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import Layout from '@/Layouts/AppLayout.vue';
import { PlusIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ devices: Array });

const form = ref({ device_name: '', ip_address: '' });

const createDevice = () => {
    router.post('/rfid-devices', form.value, {
        onSuccess: () => {
            form.value.device_name = '';
            form.value.ip_address = '';
        }
    });
};

const deleteDevice = (id) => {
    if (confirm('Yakin hapus?')) {
        router.delete(`/rfid-devices/${id}`);
    }
};
</script>

<template>
    <Layout>
        <Head title="Manajemen Perangkat RFID" />

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-[22px] font-medium text-slate-900 tracking-tight">Manajemen Perangkat RFID</h1>
                <p class="text-[13px] text-slate-500 mt-1">Daftar Perangkat ESP32 yang terhubung.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white border border-slate-200 rounded-[12px] p-5">
                    <h3 class="text-[16px] font-medium text-slate-900 mb-4">Tambah Perangkat Baru</h3>
                    <form @submit.prevent="createDevice" class="space-y-4">
                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">Nama Perangkat</label>
                            <input v-model="form.device_name" type="text" placeholder="Contoh: Gerbang Depan" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required />
                        </div>
                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">IP Address (opsional)</label>
                            <input v-model="form.ip_address" type="text" placeholder="192.168.1.100" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                        </div>
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-500 text-white text-[13px] font-medium rounded-lg hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 transition-colors">
                            <PlusIcon class="w-4 h-4 mr-2" />
                            Simpan
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-[12px] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Nama</th>
                                <th class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">API Key</th>
                                <th class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">IP</th>
                                <th class="px-5 py-3 text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Status</th>
                                <th class="px-5 py-3 text-right text-[12px] font-medium text-slate-700 uppercase tracking-wide border-b border-slate-200">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="device in devices" :key="device.id" class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3 text-[14px] font-medium text-slate-900">{{ device.device_name }}</td>
                                <td class="px-5 py-3 font-mono text-[13px] text-slate-500">{{ device.api_key }}</td>
                                <td class="px-5 py-3 text-[14px] text-slate-500">{{ device.ip_address || '-' }}</td>
                                <td class="px-5 py-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[12px] font-medium" :class="device.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                        {{ device.status }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <button @click="deleteDevice(device.id)" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                        <TrashIcon class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="devices.length === 0">
                                <td colspan="5" class="px-5 py-10 text-center text-[13px] text-slate-500">
                                    Belum ada perangkat terdaftar.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </Layout>
</template>