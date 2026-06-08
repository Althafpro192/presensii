<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    auth: Object,
    stats: Object
});

// Auto redirect berdasarkan role
const userRole = props.auth?.user?.role;

if (userRole === 'admin') {
    // Admin tetap di dashboard ini
} else if (userRole === 'teacher') {
    router.visit('/teacher');
} else if (userRole === 'student') {
    router.visit('/student');
} else if (userRole === 'parent') {
    router.visit('/parent');
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-lg font-medium text-slate-900">Dashboard</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-lg border border-slate-200 bg-white p-6">
                        <div class="text-sm font-medium text-slate-500">Total Siswa</div>
                        <div class="mt-2 text-3xl font-medium text-indigo-600">{{ stats?.total_students || 0 }}</div>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-white p-6">
                        <div class="text-sm font-medium text-slate-500">Total Guru</div>
                        <div class="mt-2 text-3xl font-medium text-indigo-600">{{ stats?.total_teachers || 0 }}</div>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-white p-6">
                        <div class="text-sm font-medium text-slate-500">Hadir Hari Ini</div>
                        <div class="mt-2 text-3xl font-medium text-green-600">{{ stats?.today_attendance || 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>