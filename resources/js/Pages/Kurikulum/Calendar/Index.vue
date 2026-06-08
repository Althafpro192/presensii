<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';

const props = defineProps({
    governmentHolidays: Array,
    schoolHolidays: Array,
    academicCalendars: Array,
    year: [String, Number],
    month: [String, Number],
});

const isModalOpen = ref(false);
const selectedDate = ref('');
const form = useForm({
    event_date: '',
    type: 'libur',
    name: '',
    override_government: false,
});

const mapEvents = () => {
    let events = [];
    
    // Govt Holidays
    props.governmentHolidays.forEach(h => {
        events.push({
            title: h.name,
            start: h.holiday_date,
            color: '#ef4444', // red
            display: 'background'
        });
    });

    // School Holidays
    props.schoolHolidays.forEach(h => {
        events.push({
            title: h.name || (h.type === 'libur' ? 'Libur Sekolah' : 'Masuk (Override)'),
            start: h.event_date,
            color: h.type === 'libur' ? '#f59e0b' : '#10b981', // yellow / green
        });
    });

    // Academic Events
    props.academicCalendars.forEach(a => {
        events.push({
            title: a.name,
            start: a.start_date,
            end: new Date(new Date(a.end_date).getTime() + 86400000).toISOString().split('T')[0], // Exclusive end date for FC
            color: '#6366f1', // indigo
        });
    });

    return events;
};

const handleDateClick = (arg) => {
    selectedDate.value = arg.dateStr;
    form.event_date = arg.dateStr;
    isModalOpen.value = true;
};

const saveHoliday = () => {
    form.post('/kurikulum/calendar/holiday', {
        onSuccess: () => {
            isModalOpen.value = false;
            form.reset();
        }
    });
};

const calendarOptions = ref({
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    events: mapEvents(),
    dateClick: handleDateClick,
    height: 'auto',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth'
    }
});
</script>

<template>
    <AppLayout>
        <Head title="Kalender Akademik" />

        <div class="mb-6">
            <h1 class="text-[22px] font-medium text-slate-900 tracking-tight">Kalender Akademik</h1>
            <p class="text-[13px] text-slate-500 mt-1">Kelola hari libur dan acara sekolah.</p>
        </div>

        <div class="bg-white border border-slate-200 p-6 rounded-[12px]">
            <FullCalendar :options="calendarOptions" />
        </div>

        <!-- Simple Modal (v-if for simplicity) -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40">
            <div class="bg-white rounded-[12px] border border-slate-200 w-full max-w-md overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white">
                    <h3 class="text-[16px] font-medium text-slate-900">Atur Status: {{ selectedDate }}</h3>
                </div>
                <div class="p-6">
                    <form @submit.prevent="saveHoliday" class="space-y-4">
                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">Tipe</label>
                            <select v-model="form.type" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <option value="libur">Libur Sekolah</option>
                                <option value="masuk">Masuk (Abaikan Libur Nasional)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[12px] font-medium text-slate-700 mb-1">Keterangan</label>
                            <input v-model="form.name" type="text" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-[14px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Opsional">
                        </div>
                        
                        <div class="flex items-center pt-2">
                            <input type="checkbox" v-model="form.override_government" id="override" class="rounded border-slate-300 text-indigo-500 focus:ring-indigo-500 h-4 w-4">
                            <label for="override" class="ml-2 block text-[13px] text-slate-700 font-medium">
                                Override status hari ini
                            </label>
                        </div>

                        <div class="pt-5 mt-2 flex justify-end gap-3 border-t border-slate-100">
                            <button type="button" @click="isModalOpen = false" class="px-4 py-2 text-[13px] font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 rounded-lg transition-colors">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-500 text-white text-[13px] font-medium rounded-lg hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 transition-colors">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<style>
.fc-theme-standard td, .fc-theme-standard th {
    border-color: #e2e8f0;
}
.fc .fc-button-primary {
    background-color: #6366f1; /* indigo-500 */
    border-color: #6366f1;
    text-transform: capitalize;
    font-size: 13px;
    font-weight: 500;
    border-radius: 6px;
}
.fc .fc-button-primary:hover {
    background-color: #4f46e5; /* indigo-600 */
    border-color: #4f46e5;
}
.fc .fc-toolbar-title {
    font-size: 1.25rem !important;
    font-weight: 500 !important;
    color: #0f172a; /* slate-900 */
}
.fc-daygrid-day-number {
    font-size: 13px;
    color: #475569; /* slate-600 */
}
</style>
