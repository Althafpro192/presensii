<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-background bg-gradient-to-br from-indigo-50 via-white to-blue-50">
        <!-- Brand / Logo Area -->
        <div 
            v-motion
            :initial="{ opacity: 0, y: -20 }"
            :enter="{ opacity: 1, y: 0, transition: { duration: 500 } }"
        >
            <Link href="/" class="flex items-center gap-2">
                <div class="w-12 h-12 rounded-xl bg-primary flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-primary/30">
                    S
                </div>
                <h1 class="text-2xl font-bold text-slate-800">Sistem Presensi</h1>
            </Link>
        </div>

        <!-- Card Container -->
        <div 
            v-motion
            :initial="{ opacity: 0, scale: 0.95 }"
            :enter="{ opacity: 1, scale: 1, transition: { delay: 100, duration: 400 } }"
            class="w-full sm:max-w-md mt-8 px-6 py-8 glass rounded-2xl overflow-hidden"
        >
            <slot />
        </div>
    </div>
</template>
