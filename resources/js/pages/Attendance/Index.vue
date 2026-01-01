<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage, router, useForm, Link } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Popover from 'primevue/popover';
import { ref, watch } from 'vue';
import DatePicker from 'primevue/datepicker';
import Select from 'primevue/select';
import AttendanceChart from './DailyAttendanceChart.vue';

const info_visible = ref();
const option_visible = ref([]);
const role = usePage().props.auth.user.employee.role;
const loadingAttendance = ref(false);

const props = defineProps({
    attendance_records : Array  ,
    today_record       : Object ,
    attendance_stats   : Object ,
    trainees           : Array  ,
    isTrainee          : Boolean,
    selectedMonth      : String || Number ,
    selectedYear       : String || Number,
    selectedEmployee   : Object ,
});

const toggle_info = (event) => {
    info_visible.value.toggle(event);
}

const toggle_option = (event, index) => {
    option_visible.value[index]?.toggle(event);
};

const punch = () => {
    router.put(route('attendance.punch'));
}

const formatTime = (datetime) => {
    if (!datetime) return null;
    const date = new Date(datetime);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

const selectMonthForm = useForm({
    month: new Date(),
    year: new Date().getFullYear(),
});

const selectAttendanceForm = useForm({
    trainee_id : props.selectedEmployee.id ?? null,
    month      : new Date(props.selectedYear, props.selectedMonth - 1),
    year       : props.selectedYear,
});

function formatDate(input) {
    const date = new Date(input);

    const day = date.getDate().toString();
    const month = date.toLocaleString("en-GB", { month: "long" });
    const year = date.getFullYear();

    return `${day.length > 1 ? day : '0' + day} ${month}, ${year}`;
}

function getDayName(input) {
    const date = new Date(input);
    return date.toLocaleString("en-GB", { weekday: "long" });
}

watch(() => selectMonthForm.month, async (val) => {
    if (!val) return;

    await loadAttendanceForMonth();
});

const loadAttendanceForMonth = async () => {
    const d = new Date(selectMonthForm.month);

    selectMonthForm.year = d.getFullYear();

    await router.get('/attendance', {
        month: d.getMonth() + 1,
        year: d.getFullYear(),
    }, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

const loadAttendanceForTrainee = async () => {
    loadingAttendance.value = true;
    const d = new Date(selectAttendanceForm.month);
    selectAttendanceForm.year = d.getFullYear();

    await router.get(route('attendance.index'), {
        trainee_id: selectAttendanceForm.trainee_id,
        month: d.getMonth() + 1,
        year: d.getFullYear(),
    }, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });

    loadingAttendance.value = false;
};

const isWeekend = (date) => {
    const day = date.getDay();
    return day === 0 || day === 6;
};

const isLate = (time) => {
    if (!time) return false;
    
    const date = new Date(time);
    const hour = date.getHours();
    const minute = date.getMinutes();
    
    if (hour > 8 || (hour === 8 && minute > 30)) {
        return true;
    }
    return false;
};

function isoToHours(datetime) {
    if (!datetime) return null;
    const date = new Date(datetime);
    return date.getHours() + date.getMinutes() / 60;
}

// Optional: format decimal hours to 12-hour string
function decimalTo12Hour(hoursDecimal) {
    if (hoursDecimal === null) return '';
    const hours = Math.floor(hoursDecimal);
    const minutes = Math.round((hoursDecimal - hours) * 60);
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const h12 = hours % 12 === 0 ? 12 : hours % 12;
    const mStr = minutes.toString().padStart(2, '0');
    return `${h12}:${mStr} ${ampm}`;
}

// General average function for ISO datetimes
function averageIsoTime(timesArray) {
    const validTimes = timesArray
        .map(isoToHours)
        .filter(v => v !== null);

    if (validTimes.length === 0) return null;

    const sum = validTimes.reduce((acc, val) => acc + val, 0);
    const avg = sum / validTimes.length;
    return decimalTo12Hour(avg);
}

function markOnLeave(record, index) {
    router.post(route('attendance.onleave.mark'), {record, employee_id: props.selectedEmployee.id});
}

function cancelOnLeave(record, index) {
    router.post(route('attendance.onleave.clear'), {record});
}

function markOnHoliday(record, index) {
    router.post(route('attendance.holiday.mark'), {record, employee_id: props.selectedEmployee.id});
}

function cancelOnHoliday(record, index) {
    router.post(route('attendance.holiday.clear'), {record});
}

</script>

<template>
    <AppLayout title="Attendance" summary="Manage attendance">
        <template v-slot:titleField>
            <div class="flex items-center gap-x-1" v-if="role === 'trainee'">
                <Button label="Punch Attendance" size="small" @click="punch()" icon="pi pi-clock" class="px-4! rounded-sm!"/>
                <button class="border flex items-center justify-center border-primary-400 rounded-sm h-8.5 aspect-square text-primary-400 hover:border-primary-700 hover:text-primary-700 transition" @click="toggle_info">
                    <span class="pi pi-info-circle"></span>
                </button>
                <Popover ref="info_visible">
                    <div class="text-sm p-4">
                        <div class="label text-primary-800!">Clock In</div>
                        <p class="text-primary-500 text-medium text-xs">First punch in will be registered as Clock-In</p>
                        <div class="label text-primary-800! mt-4">Clock Out</div>
                        <p class="text-primary-500 text-medium text-xs">Subsequent punch ins will be registered as Clock-Out</p>
                    </div>
                </Popover>
            </div>
        </template>

        <div class="w-2/3 mx-auto">
            <div class="label" v-if="isTrainee">Today's Attendance</div>
            <div class="flex mt-4 gap-x-12" v-if="isTrainee">
                <!-- Today's Clock In -->
                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-primary-500 mb-1">Clock In</span>
                    <div v-if="today_record && today_record.punch_in" class="text-lg font-semibold font-inter">
                        {{ formatTime(today_record.punch_in) }}
                    </div>
                    <div v-else class="flex gap-x-1 items-center text-xs font-medium text-primary-400 py-1 px-2 border rounded-md mt-0.5">
                        <span class="pi pi-info-circle" style="font-size: small;"></span>
                        <span>Not clocked in yet</span>
                    </div>
                </div>

                <!-- Today's Clock Out -->
                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-primary-500 mb-1">Clock Out</span>
                    <div v-if="today_record && today_record.punch_out" class="text-lg font-semibold font-inter">
                        {{ formatTime(today_record.punch_out) }}
                    </div>
                    <div v-else class="flex gap-x-1 items-center text-xs font-medium text-primary-400 py-1 px-2 border rounded-md mt-0.5">
                        <span class="pi pi-info-circle" style="font-size: small;"></span>
                        <span>Not clocked out yet</span>
                    </div>
                </div>
            </div>

            <hr class="border my-4" v-if="isTrainee"/>

            <div class="justify-between" :class="{'flex items-center': isTrainee}">
                <div v-if="isTrainee">
                    <span class="label me-2">Showing record for</span>
                    <DatePicker v-model="selectMonthForm.month" view="month" dateFormat="MM yy" size="small"/>
                </div>
                <div v-else class="mt-2 flex w-full gap-x-4">
                    <div class="flex flex-col">
                        <span class="label">Select Trainee</span>
                        <Select
                            v-model="selectAttendanceForm.trainee_id"
                            :options="trainees.map(t => ({
                                label: t.user.name,
                                value: t.id
                            }))"
                            option-label="label"
                            option-value="value"
                            placeholder="Select Trainee"
                            class="w-96 mt-2"
                            filter
                            size="small">
                            <template #option="slotProps">
                                <span class="text-sm">{{ slotProps.option.label }}</span>
                            </template>
                        </Select>
                    </div>
                    <div class="flex flex-col">
                        <span class="label">Select Month</span>
                        <DatePicker v-model="selectAttendanceForm.month" view="month" dateFormat="MM yy" size="small" class="mt-2"/>
                    </div>
                    <div class="flex flex-col justify-end">
                        <Button label="Load Records" @click="loadAttendanceForTrainee" size="small" icon="pi pi-refresh" :loading="loadingAttendance" class="px-4! rounded-sm!"/>
                    </div>

                    <div class="flex flex-col items-end justify-end grow">
                        <Link :href="route('attendance.today')">
                            <Button label="Today's Attendance" size="small" icon="pi pi-calendar" class="px-4! rounded-sm!"/>
                        </Link>
                    </div>
                </div>
            </div>

            <div v-if="attendance_records.length">
                <div  v-if="!isTrainee" class="label mt-4">Showing attendance record for</div>
                <Link v-if="!isTrainee" :href="route('employees.show', {employee: selectedEmployee.id})" class="text-xl">{{ selectedEmployee.user.name }}</Link>

                <div class="flex w-full my-4">
                    <div class="w-4/5">
                        <AttendanceChart
                            :labels="attendance_records.map(r => formatDate(r.date))"
                            :punch-in="attendance_records.map(r => formatTime(r.punch_in))"
                            :punch-out="attendance_records.map(r => formatTime(r.punch_out))"
                        />
                    </div>
                    
                    
                    <div class="flex flex-col justify-center items-end w-1/5">
                        <span class="text-sm font-medium">Average Punch-In</span>
                        <span class="text-xl">{{ averageIsoTime(attendance_records.map(r => r.punch_in)) }}</span>
                        <span class="mt-4 text-sm font-medium">Average Punch-Out</span>
                        <span class="text-xl">{{ averageIsoTime(attendance_records.map(r => r.punch_out)) }}</span>
                        <span class="mt-4 text-sm font-medium">Late</span>
                        <span class="text-xl">{{ attendance_stats?.late }} Days</span>
                        <span class="mt-4 text-sm font-medium">Missing Punch-In</span>
                        <span class="text-xl">{{ attendance_stats?.missing_clock_in }} Days</span>
                    </div>
                    
                </div>

                <table class="w-full mt-6 text-sm text-primary-500">
                    <thead>
                        <tr class="font-semibold border-b border-primary-200">
                            <td class="ps-2 pb-2 w-1/6">Date</td>
                            <td class="pb-2 w-1/6">Day</td>
                            <td class="pb-2 text-center w-1/6">Clock In</td>
                            <td class="pb-2 text-center w-1/6">Clock Out</td>
                            <td class="pe-2 pb-2">Remarks</td>
                            <td v-if="!isTrainee"></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(record, index) in attendance_records"
                            :key="record.date"
                            class="border-b border-primary-100"
                            :class="isWeekend(new Date(record.date)) ? 'bg-primary-50' : ''"
                        >
                            <!-- Date -->
                            <td class="ps-2 py-2 w-1/6">{{ formatDate(record.date) }}</td>

                            <!-- Day -->
                            <td class="w-1/6">{{ getDayName(record.date) }}</td>

                            <!-- Clock In -->
                            <td class="text-center w-1/6">
                                <template v-if="record.is_future">
                                    —
                                </template>

                                <template v-else-if="record.punch_in">
                                    {{ formatTime(record.punch_in) }}
                                </template>

                                <template v-else>
                                    <span v-if="!isWeekend(new Date(record.date)) && !record.on_leave && !record.holiday" class="bg-red-100 text-red-400 font-medium px-2 py-1 rounded-md text-xs">Missing clock in</span>
                                    <span v-else>—</span>
                                </template>
                            </td>

                            <!-- Clock Out -->
                            <td class="text-center w-1/6">
                                <template v-if="record.is_future">
                                    —
                                </template>

                                <template v-else-if="record.punch_out">
                                    {{ formatTime(record.punch_out) }}
                                </template>

                                <template v-else-if="record.punch_in && !isWeekend(new Date(record.date)) && !record.on_leave && !record.holiday">
                                    <span class="bg-red-100 text-red-400 font-medium px-2 py-1 rounded-md text-xs">Missing clock out</span>
                                </template>

                                <template v-else>
                                    —
                                </template>
                            </td>

                            <!-- Remarks -->
                            <td class="pe-2">
                                <span v-if="isWeekend(new Date(record.date))" class="bg-primary-200 status">Weekend</span>

                                <span v-if="record.punch_in && isLate(record.punch_in)" class="bg-red-100 text-red-400 status">
                                    Late
                                </span>

                                <span v-if="record.on_leave" class="bg-blue-100 text-blue-400 status">
                                    On Leave
                                </span>

                                <span v-if="record.holiday" class="bg-blue-100 text-blue-400 status">
                                    Holiday
                                </span>
                            </td>

                            <td v-if="!isTrainee" class="text-end pe-2" >
                                <button v-if="!record.punch_in && !isWeekend(new Date(record.date))" class="w-6 h-6 hover:bg-primary-50 rounded-md transition pi pi-ellipsis-v" style="font-size: smaller;" @click="toggle_option($event, index)"></button>
                                <Popover :ref="el => option_visible[index] = el">
                                    <div class="flex flex-col py-2 items-start gap-y-2 text-sm text-primary-500 hover:text-primary-700 transition">
                                        <button class="hover:bg-primary-100 transition px-2 py-1 w-full" @click="markOnLeave(record, index)" v-if="!record.on_leave">Mark as <span class="bg-primary-50 px-2 py-0.5 rounded-sm ms-1">on leave</span></button>
                                        <button class="hover:bg-primary-100 transition px-2 py-1 w-full" @click="cancelOnLeave(record, index)" v-else>Cancel <span class="bg-primary-50 px-2 py-0.5 rounded-sm ms-1">on leave</span></button>

                                        <button class="hover:bg-primary-100 transition px-2 py-1 w-full" @click="markOnHoliday(record, index)" v-if="!record.holiday">Mark as <span class="bg-primary-50 px-2 py-0.5 rounded-sm ms-1">holiday</span></button>
                                        <button class="hover:bg-primary-100 transition px-2 py-1 w-full" @click="cancelOnHoliday(record, index)" v-else>Cancel <span class="bg-primary-50 px-2 py-0.5 rounded-sm ms-1">holiday</span></button>
                                    </div>
                                </Popover>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="text-primary-500 text-sm mt-2">
                No attendance records found for the selected month.
            </div>
        </div>
    </AppLayout>
</template>

<style>
@reference "tailwindcss";
.status {
    @apply font-medium px-2 py-1 rounded-md text-xs me-2;
}
</style>