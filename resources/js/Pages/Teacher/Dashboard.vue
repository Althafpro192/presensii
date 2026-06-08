<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    classroom: Object,
    stats: Object,
    todayAttendance: Object,
});
</script>

<template>
    <AppLayout>
        <Head title="Teacher Dashboard" />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Dashboard Guru</h1>
            <p class="text-slate-500">Ringkasan aktivitas kelas.</p>
        </div>

        <div class="glass p-6 rounded-2xl">
            <p class="text-slate-600">Konten dashboard Guru (Fase 3).</p>
        </div>
    </AppLayout>
</template>
