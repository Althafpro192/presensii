<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    auth: Object,
});

const features = [
    {
        icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 0 1 7.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 0 1 1.06 0Z" /></svg>`,
        title: 'Presensi RFID otomatis',
        desc: 'Siswa cukup tempelkan kartu — sistem mencatat masuk & keluar secara real-time tanpa antrian.',
        color: 'indigo',
    },
    {
        icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605" /></svg>`,
        title: 'Laporan & rekap instan',
        desc: 'Dashboard real-time — rekap harian, mingguan, bulanan. Ekspor ke Excel satu klik.',
        color: 'teal',
    },
    {
        icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>`,
        title: 'Notifikasi orang tua',
        desc: 'Orang tua mendapat notifikasi otomatis saat anaknya hadir, terlambat, atau tidak masuk.',
        color: 'violet',
    },
    {
        icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" /></svg>`,
        title: 'Multi-sekolah (SaaS)',
        desc: 'Satu platform, banyak sekolah. Data tiap sekolah terisolasi penuh — aman dan efisien.',
        color: 'amber',
    },
    {
        icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>`,
        title: 'Auto-alpa harian',
        desc: 'Siswa yang tidak tap otomatis ditandai alpa tiap akhir hari, kecuali libur nasional.',
        color: 'rose',
    },
    {
        icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 8.25h3m-3 3.75h3" /></svg>`,
        title: 'Akses mobile siswa',
        desc: 'Siswa & orang tua cek rekap kehadiran kapan saja lewat tampilan mobile yang ringan.',
        color: 'cyan',
    },
];

const colorMap = {
    indigo: { bg: 'bg-indigo-50', text: 'text-indigo-600', hover: 'hover:bg-indigo-100' },
    teal:   { bg: 'bg-teal-50',   text: 'text-teal-600',   hover: 'hover:bg-teal-100' },
    violet: { bg: 'bg-violet-50', text: 'text-violet-600', hover: 'hover:bg-violet-100' },
    amber:  { bg: 'bg-amber-50',  text: 'text-amber-600',  hover: 'hover:bg-amber-100' },
    rose:   { bg: 'bg-rose-50',   text: 'text-rose-600',   hover: 'hover:bg-rose-100' },
    cyan:   { bg: 'bg-cyan-50',   text: 'text-cyan-600',   hover: 'hover:bg-cyan-100' },
};

const stats = [
    { value: '10.000+', label: 'Siswa aktif terdaftar' },
    { value: '50+',     label: 'Sekolah bergabung' },
    { value: '99.9%',   label: 'Uptime sistem' },
    { value: '<100ms',  label: 'Waktu respons RFID' },
];
</script>

<template>
    <div class="min-h-screen bg-white font-sans">

        <!-- ── Navbar ──────────────────────────────────── -->
        <nav class="sticky top-0 z-20 bg-white border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-indigo-500 rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-white">
                            <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.227.119-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z" />
                            <path d="M13.06 15.473a48.45 48.45 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.46.71 47.87 47.87 0 0 1-8.105 2.91.75.75 0 0 1-.832-.227 47.877 47.877 0 0 0-3.65-4.065 48.17 48.17 0 0 1 5.126-1.543Z" />
                        </svg>
                    </div>
                    <span class="text-base font-medium text-slate-900">Presensi RFID</span>
                </div>

                <div class="hidden md:flex items-center gap-6">
                    <a href="#fitur" class="text-sm text-slate-500 hover:text-slate-900 transition-colors">Fitur</a>
                    <a href="#cara-kerja" class="text-sm text-slate-500 hover:text-slate-900 transition-colors">Cara kerja</a>
                </div>

                <div class="flex items-center gap-3">
                    <template v-if="auth?.user">
                        <Link :href="route('dashboard')" class="btn-primary py-2 px-5 text-sm">
                            Dashboard →
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="btn-secondary py-2 px-5 text-sm">
                            Masuk
                        </Link>
                        <Link :href="route('login')" class="btn-primary py-2 px-5 text-sm">
                            Mulai gratis →
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- ── Hero ───────────────────────────────────── -->
        <section class="bg-slate-50 border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-8 pt-20 pb-16">
                <!-- Badge -->
                <div class="flex justify-center mb-8">
                    <div class="inline-flex items-center gap-2 bg-indigo-50 border border-indigo-100 text-indigo-700 text-sm font-medium px-4 py-2 rounded-full">
                        <span class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></span>
                        Platform presensi RFID #1 untuk sekolah Indonesia
                    </div>
                </div>

                <!-- Headline -->
                <div class="text-center mb-8">
                    <h1 class="text-6xl font-medium text-slate-900 leading-tight tracking-tight mb-6" style="line-height:1.1">
                        Presensi sekolah<br/>
                        <span class="text-indigo-500">lebih cerdas</span>,<br/>
                        tanpa kerumitan
                    </h1>
                    <p class="text-xl text-slate-500 max-w-2xl mx-auto leading-relaxed">
                        Dari tap kartu RFID hingga notifikasi orang tua — semua berjalan otomatis.
                        Tidak ada input manual, tidak ada antrian, tidak ada kesalahan.
                    </p>
                </div>

                <!-- CTA Buttons -->
                <div class="flex items-center justify-center gap-4 mb-16 flex-wrap">
                    <Link :href="route('login')" class="inline-flex items-center gap-2 bg-indigo-500 hover:bg-indigo-600 text-white text-base font-medium px-8 py-4 rounded-xl transition-colors duration-150">
                        Mulai sekarang — gratis
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </Link>
                    <a href="#fitur" class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-700 text-base font-medium px-8 py-4 rounded-xl hover:bg-slate-50 transition-colors duration-150">
                        Lihat semua fitur
                    </a>
                </div>

                <!-- Dashboard preview mockup -->
                <div class="max-w-5xl mx-auto bg-white border border-slate-200 rounded-xl overflow-hidden">
                    <!-- Fake browser bar -->
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50">
                        <div class="flex gap-2">
                            <div class="w-3 h-3 rounded-full bg-slate-200"></div>
                            <div class="w-3 h-3 rounded-full bg-slate-200"></div>
                            <div class="w-3 h-3 rounded-full bg-slate-200"></div>
                        </div>
                        <div class="flex-1 h-6 bg-slate-100 rounded-lg max-w-sm mx-auto flex items-center justify-center">
                            <span class="text-xs text-slate-400">presensi.sekolah.sch.id/dashboard</span>
                        </div>
                        <span class="text-xs text-green-600 font-medium flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                            Live
                        </span>
                    </div>

                    <!-- Fake dashboard content -->
                    <div class="p-6">
                        <!-- Stat row -->
                        <div class="grid grid-cols-4 gap-4 mb-6">
                            <div v-for="(s, i) in [
                                { label:'Total siswa', val:'412', sub:'terdaftar', color:'text-slate-900' },
                                { label:'Hadir hari ini', val:'389', sub:'siswa', color:'text-green-600' },
                                { label:'Alfa', val:'14', sub:'tidak hadir', color:'text-red-500' },
                                { label:'Izin / sakit', val:'9', sub:'mengajukan', color:'text-amber-500' },
                            ]" :key="i" class="bg-slate-50 border border-slate-100 rounded-xl p-4">
                                <p class="text-xs text-slate-400 font-medium mb-2">{{ s.label }}</p>
                                <p :class="['text-3xl font-medium', s.color]">{{ s.val }}</p>
                                <p class="text-xs text-slate-400 mt-1">{{ s.sub }}</p>
                            </div>
                        </div>

                        <!-- Table preview -->
                        <div class="bg-white border border-slate-100 rounded-xl overflow-hidden">
                            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 bg-slate-50">
                                <span class="text-sm font-medium text-slate-700">Presensi hari ini</span>
                                <span class="text-xs bg-green-50 text-green-700 border border-green-100 px-2.5 py-1 rounded-full font-medium">Real-time</span>
                            </div>
                            <div v-for="(row, i) in [
                                { name: 'Ahmad Fauzi', kelas: 'XI RPL 1', waktu: '06:55', status: 'hadir', sc: 'bg-green-100 text-green-800' },
                                { name: 'Budi Santoso', kelas: 'X AKL 2', waktu: '07:12', status: 'terlambat', sc: 'bg-orange-100 text-orange-800' },
                                { name: 'Citra Dewi', kelas: 'XII TKJ 1', waktu: '06:48', status: 'hadir', sc: 'bg-green-100 text-green-800' },
                                { name: 'Dian Pratiwi', kelas: 'XI RPL 2', waktu: '—', status: 'alfa', sc: 'bg-red-100 text-red-800' },
                            ]" :key="i" class="flex items-center gap-4 px-5 py-3.5 border-b border-slate-50 last:border-0 hover:bg-slate-50 transition-colors duration-100">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 text-sm font-medium flex items-center justify-center shrink-0">
                                    {{ row.name[0] }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-800">{{ row.name }}</p>
                                    <p class="text-xs text-slate-400">{{ row.kelas }}</p>
                                </div>
                                <span class="text-sm text-slate-400 font-mono shrink-0">{{ row.waktu }}</span>
                                <span :class="['text-xs font-medium px-2.5 py-1 rounded-full shrink-0', row.sc]">{{ row.status }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Stats bar ──────────────────────────────── -->
        <section class="bg-white border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-8 py-10">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div v-for="stat in stats" :key="stat.label" class="text-center">
                        <p class="text-4xl font-medium text-slate-900 mb-1">{{ stat.value }}</p>
                        <p class="text-sm text-slate-500">{{ stat.label }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Features ───────────────────────────────── -->
        <section id="fitur" class="bg-slate-50 border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-8 py-20">
                <div class="text-center mb-14">
                    <p class="text-sm font-medium text-indigo-600 mb-3 uppercase tracking-widest">Fitur Lengkap</p>
                    <h2 class="text-4xl font-medium text-slate-900 mb-4">Semua yang dibutuhkan sekolah Anda</h2>
                    <p class="text-lg text-slate-500 max-w-xl mx-auto">Tidak perlu konfigurasi rumit. Pasang, daftarkan siswa, dan sistem langsung berjalan hari itu juga.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="f in features"
                        :key="f.title"
                        class="group bg-white border border-slate-200 rounded-xl p-7 hover:border-slate-300 transition-all duration-200 hover:-translate-y-0.5 cursor-default"
                    >
                        <div :class="['w-12 h-12 rounded-xl flex items-center justify-center mb-5 transition-colors duration-200', colorMap[f.color].bg, colorMap[f.color].text]">
                            <span v-html="f.icon" class="w-6 h-6 block [&>svg]:w-6 [&>svg]:h-6" />
                        </div>
                        <h3 class="text-base font-medium text-slate-900 mb-2">{{ f.title }}</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">{{ f.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── How it works ───────────────────────────── -->
        <section id="cara-kerja" class="bg-white border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-8 py-20">
                <div class="text-center mb-14">
                    <p class="text-sm font-medium text-indigo-600 mb-3 uppercase tracking-widest">Cara Kerja</p>
                    <h2 class="text-4xl font-medium text-slate-900 mb-4">Tiga langkah, berjalan otomatis</h2>
                    <p class="text-lg text-slate-500">Tidak ada proses manual yang perlu diingat setiap hari.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div v-for="(step, i) in [
                        { n:'01', title:'Tap kartu RFID', desc:'Siswa menempelkan kartu ke reader di pintu masuk. Sistem merespons kurang dari 100ms.' },
                        { n:'02', title:'Sistem memproses', desc:'UID dicocokkan dengan database siswa. Status ditentukan otomatis berdasarkan jam masuk.' },
                        { n:'03', title:'Notifikasi & rekap', desc:'Orang tua ternotifikasi. Admin melihat rekap real-time di dashboard. Semua otomatis.' },
                    ]" :key="step.n">
                        <div>
                            <div class="text-5xl font-medium text-slate-100 mb-5 font-mono">{{ step.n }}</div>
                            <div class="w-10 h-1 bg-indigo-500 rounded-full mb-5"></div>
                            <h3 class="text-lg font-medium text-slate-900 mb-3">{{ step.title }}</h3>
                            <p class="text-sm text-slate-500 leading-relaxed">{{ step.desc }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── CTA ────────────────────────────────────── -->
        <section class="bg-slate-900">
            <div class="max-w-7xl mx-auto px-8 py-20 text-center">
                <div class="inline-flex items-center gap-2 bg-slate-800 text-slate-400 text-sm px-4 py-2 rounded-full mb-8">
                    <span class="w-2 h-2 bg-indigo-400 rounded-full animate-pulse"></span>
                    Siap digunakan mulai hari ini
                </div>
                <h2 class="text-4xl font-medium text-white mb-4 tracking-tight">Mulai modernisasi presensi sekolah Anda</h2>
                <p class="text-lg text-slate-400 mb-10 max-w-xl mx-auto">Daftarkan sekolah Anda dan kelola presensi siswa dengan lebih efisien, akurat, dan otomatis.</p>
                <Link :href="route('login')" class="inline-flex items-center gap-2 bg-indigo-500 hover:bg-indigo-600 text-white text-base font-medium px-10 py-4 rounded-xl transition-colors duration-150">
                    Mulai sekarang
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </Link>
            </div>
        </section>

        <!-- ── Footer ─────────────────────────────────── -->
        <footer class="bg-white border-t border-slate-100">
            <div class="max-w-7xl mx-auto px-8 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 bg-indigo-500 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-white">
                            <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.227.119-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z" />
                        </svg>
                    </div>
                    <span class="text-sm text-slate-600 font-medium">Presensi RFID</span>
                </div>
                <p class="text-sm text-slate-400">&copy; {{ new Date().getFullYear() }} Presensi RFID. All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>
