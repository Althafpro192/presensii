<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold">Sistem Presensi</h1>
                    </div>
                    <div class="flex items-center">
                        <form method="POST" action="/logout">
                            <input type="hidden" name="_token" :value="csrfToken" />
                            <button type="submit" class="text-gray-700 hover:text-gray-900">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
        <main>
            <slot />
        </main>
    </div>
</template>

<script setup>
import { ref } from 'vue';
// CSRF token dari meta tag (opsional, bisa diambil dari window.Laravel)
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
</script>