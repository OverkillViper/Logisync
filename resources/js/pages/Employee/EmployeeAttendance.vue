<script setup>
import { Link } from '@inertiajs/vue3';
import MonthlyAttendanceChart from '../Attendance/MonthlyAttendanceChart.vue';

defineProps({
    employee: Object,
    attendance: Array,
});
</script>

<template>
    <div class="flex text-sm text-primary-600 items-center gap-x-2">
        <span class="pi pi-info-circle" style="font-size: small;"></span>
        <div>Monthly average  attendance data for this year is shown here. For detailed attendance, view attendance from
            <Link class="underline text-primary-700 hover:text-primary-900" :href="route('attendance.index', {trainee_id: employee.id})">Attendance</Link> page.
        </div>
    </div>

    <div class="mt-4 w-4/5 mx-auto">
        <MonthlyAttendanceChart
            :labels="attendance.map(m => m.month)"
            :punch-in="attendance.map(m => m.avg_punch_in)"
            :punch-out="attendance.map(m => m.avg_punch_out)"
        />
    </div>
</template>