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
            <h1 class="text-[22px] font-medium text-slate-900">Dashboard Utama</h1>
            <p class="text-[13px] text-slate-500 mt-1">Selamat datang di Sistem Presensi Multi-Sekolah.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white border border-slate-200 p-5 rounded-[12px]">
                <h3 class="text-[16px] font-medium text-slate-900 mb-2">Status Akun</h3>
                <p class="text-[13px] text-slate-600">Anda login sebagai: <span class="font-medium text-indigo-600">{{ user?.role }}</span></p>
            </div>
        </div>
    </AppLayout>
</template>
