<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="mb-6 text-center">
            <h2 class="text-xl font-semibold text-slate-800">Selamat Datang Kembali</h2>
            <p class="text-sm text-slate-500 mt-1">Silakan masuk ke akun Anda</p>
        </div>

        <div v-if="form.errors.email" class="mb-4 text-sm text-red-600 bg-red-50 p-3 rounded-lg border border-red-100">
            {{ form.errors.email }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                <input 
                    id="email" 
                    type="email" 
                    class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 transition-all" 
                    v-model="form.email" 
                    required 
                    autofocus 
                    autocomplete="username" 
                />
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <input 
                    id="password" 
                    type="password" 
                    class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 transition-all" 
                    v-model="form.password" 
                    required 
                    autocomplete="current-password" 
                />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        v-model="form.remember" 
                        class="rounded border-slate-300 text-primary shadow-sm focus:ring-primary" 
                    />
                    <span class="ms-2 text-sm text-slate-600">Ingat Saya</span>
                </label>
            </div>

            <div>
                <button 
                    type="submit" 
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all active:scale-[0.98]"
                    :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Memproses...</span>
                    <span v-else>Masuk</span>
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
