<template>
    <div>
        <div class="bg-gray-800 text-white p-4">
            <h1 class="text-xl">Sistem Presensi</h1>
        </div>
        <div class="p-4">
            <slot />
        </div>
    </div>
</template>

<script setup>
defineProps({ title: String });
</script>