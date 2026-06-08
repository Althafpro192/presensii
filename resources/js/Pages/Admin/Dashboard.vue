<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    stats: Object,
    todayAttendance: Object,
    monthlyTrend: Array,
});
</script>

<template>
    <AppLayout>
        <Head title="Admin Dashboard" />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Dashboard Sekolah</h1>
            <p class="text-slate-500">Ringkasan aktivitas sekolah.</p>
        </div>

        <!-- Stub for Admin Dashboard -->
        <div class="glass p-6 rounded-2xl">
            <p class="text-slate-600">Konten dashboard Admin Sekolah (Fase 3).</p>
        </div>
    </AppLayout>
</template>
