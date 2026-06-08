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
            color: h.type === 'libur' ? '#f97316' : '#22c55e', // orange / green
        });
    });

    // Academic Events
    props.academicCalendars.forEach(a => {
        events.push({
            title: a.name,
            start: a.start_date,
            end: new Date(new Date(a.end_date).getTime() + 86400000).toISOString().split('T')[0], // Exclusive end date for FC
            color: '#3b82f6', // blue
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
            <h1 class="text-2xl font-bold text-slate-800">Kalender Akademik</h1>
            <p class="text-slate-500">Kelola hari libur dan acara sekolah.</p>
        </div>

        <div class="glass p-6 rounded-2xl">
            <FullCalendar :options="calendarOptions" />
        </div>

        <!-- Simple Modal (v-if for simplicity) -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Atur Status: {{ selectedDate }}</h3>
                <form @submit.prevent="saveHoliday" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Tipe</label>
                        <select v-model="form.type" class="mt-1 block w-full rounded-xl border-slate-200">
                            <option value="libur">Libur Sekolah</option>
                            <option value="masuk">Masuk (Abaikan Libur Nasional)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Keterangan</label>
                        <input v-model="form.name" type="text" class="mt-1 block w-full rounded-xl border-slate-200" placeholder="Opsional">
                    </div>
                    
                    <div class="flex items-center pt-2">
                        <input type="checkbox" v-model="form.override_government" id="override" class="rounded border-slate-300 text-primary focus:ring-primary h-4 w-4">
                        <label for="override" class="ml-2 block text-sm text-slate-900">
                            Override status hari ini
                        </label>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                        <button type="button" @click="isModalOpen = false" class="px-4 py-2 text-slate-600 hover:bg-slate-50 rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-xl hover:bg-primary-dark">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

    </AppLayout>
</template>

<style>
.fc-theme-standard td, .fc-theme-standard th {
    border-color: #e2e8f0;
}
.fc .fc-button-primary {
    background-color: #4361ee;
    border-color: #4361ee;
}
.fc .fc-button-primary:hover {
    background-color: #3751c6;
    border-color: #3751c6;
}
</style>
