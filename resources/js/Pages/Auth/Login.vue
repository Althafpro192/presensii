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

        <div class="mb-6">
            <h2 class="text-[22px] font-medium text-slate-900">Masuk ke Akun</h2>
            <p class="text-[14px] text-slate-500 mt-1">Masukkan email dan password untuk melanjutkan.</p>
        </div>

        <div v-if="form.errors.email" class="mb-5 p-3 bg-red-50 text-red-800 rounded-lg border border-red-100 text-[13px]">
            {{ form.errors.email }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label for="email" class="block text-[12px] font-medium text-slate-700 mb-1">Email</label>
                <input 
                    id="email" 
                    type="email" 
                    class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" 
                    v-model="form.email" 
                    placeholder="nama@sekolah.com"
                    required 
                    autofocus 
                    autocomplete="username" 
                />
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-[12px] font-medium text-slate-700">Password</label>
                    <a href="#" class="text-[12px] text-indigo-500 hover:text-indigo-600">Lupa password?</a>
                </div>
                <input 
                    id="password" 
                    type="password" 
                    class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" 
                    v-model="form.password" 
                    placeholder="••••••••"
                    required 
                    autocomplete="current-password" 
                />
            </div>

            <div class="flex items-center pt-1">
                <label class="flex items-center cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        v-model="form.remember" 
                        class="w-4 h-4 text-indigo-500 border-slate-200 rounded focus:ring-indigo-500" 
                    />
                    <span class="ml-2 text-[13px] text-slate-700">Ingat sesi ini</span>
                </label>
            </div>

            <div class="pt-4">
                <button 
                    type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg text-[14px] font-medium text-white bg-indigo-500 hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 transition-colors"
                    :class="{ 'opacity-70 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Memproses...</span>
                    <span v-else>Masuk</span>
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
