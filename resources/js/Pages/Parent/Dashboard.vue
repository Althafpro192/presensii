<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    children: Array,
});
</script>

<template>
    <AppLayout>
        <Head title="Parent Dashboard" />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Dashboard Orang Tua</h1>
            <p class="text-slate-500">Pantau presensi anak Anda.</p>
        </div>

        <div class="glass p-6 rounded-2xl">
            <p class="text-slate-600">Konten dashboard Orang Tua (Fase 5).</p>
        </div>
    </AppLayout>
</template>
