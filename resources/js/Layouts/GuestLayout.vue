<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen w-full flex flex-col justify-center items-center bg-slate-100 px-4 sm:px-6">
        <!-- Logo Area -->
        <div class="mb-8 flex flex-col items-center">
            <Link href="/" class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center text-white font-medium text-lg">
                    S
                </div>
                <h1 class="text-xl font-medium text-slate-900">Sistem Presensi</h1>
            </Link>
        </div>

        <!-- Form Card Container -->
        <div class="w-full max-w-[400px] bg-white border border-slate-200 rounded-[12px] p-6 sm:p-8">
            <slot />
        </div>
        
        <div class="mt-8 text-center text-[13px] text-slate-500">
            &copy; {{ new Date().getFullYear() }} Sistem Presensi Sekolah.
        </div>
    </div>
</template>
