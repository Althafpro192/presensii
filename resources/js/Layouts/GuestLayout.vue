<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen w-full flex bg-white font-sans">
        <!-- Left Branding Panel (Hidden on Mobile) -->
        <div class="hidden lg:flex lg:w-1/2 bg-slate-900 relative flex-col justify-between p-12 overflow-hidden">
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 right-0 -mr-32 -mt-32 w-96 h-96 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-32 -mb-32 w-96 h-96 rounded-full bg-indigo-500/20 blur-3xl"></div>
            
            <div class="relative z-10 flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-white">
                        <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.227.119-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z" />
                    </svg>
                </div>
                <span class="text-[18px] font-medium text-white">Sistem Presensi RFID</span>
            </div>
            
            <div class="relative z-10 text-white max-w-md">
                <h2 class="text-4xl font-medium leading-tight mb-6 tracking-tight">Presensi cerdas tanpa kerumitan.</h2>
                <p class="text-lg text-slate-400 mb-8">
                    Dari tap kartu hingga laporan otomatis. Pantau kehadiran siswa secara real-time dan terintegrasi langsung dengan orang tua.
                </p>
                <div class="flex items-center gap-4 text-sm text-slate-300">
                    <div class="flex -space-x-3">
                        <div class="w-10 h-10 rounded-full bg-slate-800 border-2 border-slate-900 flex items-center justify-center text-slate-400 font-medium">A</div>
                        <div class="w-10 h-10 rounded-full bg-indigo-900 border-2 border-slate-900 flex items-center justify-center text-indigo-300 font-medium">B</div>
                        <div class="w-10 h-10 rounded-full bg-teal-900 border-2 border-slate-900 flex items-center justify-center text-teal-300 font-medium">C</div>
                    </div>
                    <span>Telah digunakan oleh 50+ sekolah.</span>
                </div>
            </div>
            
            <div class="relative z-10 text-[13px] text-slate-500">
                &copy; {{ new Date().getFullYear() }} Sistem Presensi Multi-Sekolah.
            </div>
        </div>
        
        <!-- Right Form Panel -->
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-slate-50 lg:bg-white p-6 sm:p-12 relative">
            <div class="w-full max-w-md">
                <!-- Mobile Logo Header -->
                <div class="lg:hidden mb-10 flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center mb-4 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7 text-white">
                            <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.227.119-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-medium text-slate-900">Sistem Presensi</h1>
                </div>
                
                <div class="bg-white border border-slate-200 lg:border-0 rounded-2xl lg:rounded-none p-6 sm:p-10 lg:p-0">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
