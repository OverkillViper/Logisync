<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import EmployeeAvatar from '../Employee/EmployeeAvatar.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    attendance_record: Array,
    clock_in_time    : Array,
    clock_in_late    : Array,
    clock_in_missing : Array,
});

const formatTime = (datetime) => {
    if (!datetime) return null;
    const date = new Date(datetime);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

</script>

<template>
    <AppLayout title="Today's Attendance" summary="View Today's Attendance Summary">
        <div class="w-2/3 mx-auto">
            <div class="flex items-center gap-x-2">
                <div class="mx-2">
                    <span class="pi pi-calendar" style="font-size: x-large;"></span>
                </div>
                <div class="flex flex-col">
                    <div class="font-inter font-medium">12 December, 2025</div>
                    <div class="text-sm">Friday</div>
                </div>
            </div>
            <div class="bg-primary-950 rounded-sm my-4 p-4 gap-4 grid grid-cols-3">
                <div class="flex gap-x-2 items-center">
                    <div class="bg-primary-900 w-10 h-10 flex items-center justify-center rounded-sm text-white font-medium">
                        {{ clock_in_time.length }}
                    </div>
                    <div class="flex flex-col text-sm">
                        <span class="label">Clocked In</span>
                        <span class="font-medium text-white">In Time</span>
                    </div>
                </div>
                <div class="flex gap-x-2 items-center">
                    <div class="bg-primary-900 w-10 h-10 flex items-center justify-center rounded-sm text-white font-medium">
                        {{ clock_in_late.length }}
                    </div>
                    <div class="flex flex-col text-sm">
                        <span class="label">Clocked In</span>
                        <span class="font-medium text-white">Late</span>
                    </div>
                </div>
                <div class="flex gap-x-2 items-center">
                    <div class="bg-primary-900 w-10 h-10 flex items-center justify-center rounded-sm text-white font-medium">
                        {{ clock_in_missing.length }}
                    </div>
                    <div class="flex flex-col text-sm">
                        <span class="label">Clock In</span>
                        <span class="font-medium text-white">Missing</span>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4 mt-4 w-full px-4">
                <div class="flex flex-col gap-y-2">
                    <Link :href="route('employees.show', {employee: record.employee.user.id})" v-for="record in clock_in_time" :key="record.id"
                        class="flex items-center gap-x-2 text-sm border rounded-sm p-2 border-primary-200 hover:border-primary-600 transition"
                    >
                        <EmployeeAvatar :image="record.employee.profile_picture" :name="record.employee.user.name" class="h-8 w-8 rounded-md text-xs! object-cover bg-primary-950"/>
                        <div>{{ record.employee.user.name }}</div>
                    </Link>
                </div>
                <div class="flex flex-col gap-y-2">
                    <Link :href="route('employees.show', {employee: record.employee.user.id})" v-for="record in clock_in_late" :key="record.id"
                        class="flex items-center gap-x-2 text-sm border rounded-sm p-2 border-primary-200 hover:border-primary-600 transition"
                    >
                        <EmployeeAvatar :image="record.employee.profile_picture" :name="record.employee.user.name" class="h-8 w-8 rounded-md text-xs! object-cover bg-primary-950"/>
                        <div class="grow">{{ record.employee.user.name }}</div>

                        <div class="text-primary-600 font-medium">{{ formatTime(record.punch_in) }}</div>
                    </Link>
                </div>
                <div class="flex flex-col gap-y-2">
                    <Link :href="route('employees.show', {employee: record.employee.user.id})" v-for="record in clock_in_missing" :key="record.id"
                        class="flex items-center gap-x-2 text-sm border rounded-sm p-2 border-primary-200 hover:border-primary-600 transition"
                    >
                        <EmployeeAvatar :image="record.employee.profile_picture" :name="record.employee.user.name" class="h-8 w-8 rounded-md text-xs! object-cover bg-primary-950"/>
                        <div>{{ record.employee.user.name }}</div>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>