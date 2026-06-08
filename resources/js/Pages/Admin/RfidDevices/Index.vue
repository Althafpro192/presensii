<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Layout from '@/Layouts/AppLayout.vue';

const props = defineProps({ devices: Array });

const form = ref({ device_name: '', ip_address: '' });

const createDevice = () => {
    router.post('/rfid-devices', form.value);
};

const deleteDevice = (id) => {
    if (confirm('Yakin hapus?')) {
        router.delete(`/rfid-devices/${id}`);
    }
};
</script>

<template>
    <Layout title="Manajemen Perangkat RFID">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-4">Daftar Perangkat ESP32</h2>

                    <div class="mb-6 p-4 bg-gray-100 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Tambah Perangkat Baru</h3>
                        <form @submit.prevent="createDevice">
                            <input v-model="form.device_name" type="text" placeholder="Nama Perangkat" class="mb-2 w-full border rounded p-2" required />
                            <input v-model="form.ip_address" type="text" placeholder="IP Address (opsional)" class="mb-2 w-full border rounded p-2" />
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
                        </form>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">API Key</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="device in devices" :key="device.id">
                                <td class="px-6 py-4">{{ device.device_name }}</td>
                                <td class="px-6 py-4 font-mono text-sm">{{ device.api_key }}</td>
                                <td class="px-6 py-4">{{ device.ip_address || '-' }}</td>
                                <td class="px-6 py-4"><span :class="device.status === 'active' ? 'text-green-600' : 'text-red-600'">{{ device.status }}</span></td>
                                <td class="px-6 py-4"><button @click="deleteDevice(device.id)" class="text-red-600 hover:text-red-900">Hapus</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </Layout>
</template>