<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();
const user = page.props.auth?.user;
</script>

<template>
    <AppLayout>
        <Head title="Dashboard" />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Dashboard Utama</h1>
            <p class="text-slate-500">Selamat datang di Sistem Presensi Multi-Sekolah.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="glass p-6 rounded-2xl">
                <h3 class="text-lg font-semibold text-slate-800 mb-2">Status Akun</h3>
                <p class="text-slate-600">Anda login sebagai: <span class="font-medium text-primary">{{ user?.role }}</span></p>
            </div>
        </div>
    </AppLayout>
</template>
