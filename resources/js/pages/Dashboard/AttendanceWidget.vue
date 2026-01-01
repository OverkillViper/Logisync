<script setup>
import EmployeeAvatar from '../Employee/EmployeeAvatar.vue';
import Button from 'primevue/button';
import Popover from 'primevue/popover';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    isTrainee        : Boolean,
    clock_in_late    : Array  ,
    clock_in_missing : Array  ,
    today_record     : Object ,
});

const formatTime = (datetime) => {
    if (!datetime) return null;
    const date = new Date(datetime);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

const punch = () => {
    router.put(route('attendance.punch'));
}

const info_visible = ref();
const toggle_info = (event) => {
    info_visible.value.toggle(event);
}
</script>

<template>
    <div class="bg-white border border-neutral-300 rounded-sm overflow-hidden shadow-sm shadow-neutral-100/20">
        <div class="flex items-center px-4 py-3 border-b border-neutral-100 gap-x-4">
            <div class="w-10 h-10 bg-neutral-900 rounded-xl flex items-center justify-center text-white shadow-lg">
                <span class="material-icons-outlined" style="font-size: 1.25rem;">punch_clock</span>
            </div>
            
            <div class="flex flex-col grow">
                <h3 class="text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 leading-none mb-1">Attendance</h3>
                <div class="text-sm font-bold text-neutral-900 uppercase tracking-tight">Today's Attendance</div>
            </div>

            <div class="flex items-center gap-x-2">
                <Link :href="route('attendance.index')" v-if="!isTrainee">
                    <Button 
                        label="View Attendance" 
                        icon="pi pi-external-link"
                        iconPos="right"
                        class="text-[10px]! font-bold! uppercase! tracking-widest! text-neutral-400! hover:text-neutral-900! bg-transparent! border-none! transition-all! p-0!"
                    />
                </Link>

                <template v-else>
                    <Button 
                        label="PUNCH_TIME" 
                        size="small" 
                        @click="punch()" 
                        icon="pi pi-clock"
                        class="px-4! rounded-sm!"
                    />
                    <button @click="toggle_info" class="text-neutral-300 hover:text-neutral-900 transition-colors">
                        <span class="pi pi-info-circle"></span>
                    </button>
                </template>
            </div>

            <Popover ref="info_visible" class="shadow-xl! border-neutral-200!">
                <div class="w-80 p-2 font-inter">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 mb-2 px-1">Punch In Rule</div>
                    <div class="text-sm p-4">
                        <div class="label text-primary-800!">Clock In</div>
                        <p class="text-primary-500 text-medium text-xs">First punch in will be registered as Clock-In</p>
                        <div class="label text-primary-800! mt-4">Clock Out</div>
                        <p class="text-primary-500 text-medium text-xs">Subsequent punch ins will be registered as Clock-Out</p>
                    </div>
                </div>
            </Popover>
        </div>

        <div class="p-4 bg-neutral-50">
            <div v-if="!isTrainee" class="gap-4">
                <div class="flex flex-col">
                    <div class="flex items-center justify-between mb-2 font-inter">
                        <span class="text-xs font-bold uppercase text-neutral-600">Late Attendance</span>
                        <span class="text-xs font-bold bg-neutral-900 text-white px-2 py-0.5 rounded w-8 h-6 flex items-center justify-center">{{ clock_in_late.length < 9 ? '0' : '' }}{{ clock_in_late.length }}</span>
                    </div>
                    <div class="space-y-2">
                        <Link v-for="record in clock_in_late" :key="record.id" 
                            :href="route('employees.show', {employee: record.employee.user.id})"
                            class="flex items-center bg-white border border-neutral-200 p-1 pe-3 rounded-sm hover:border-neutral-900 transition-all group"
                        >
                            <EmployeeAvatar :image="record.employee.profile_picture" :name="record.employee.user.name"
                            class="h-9 w-9 rounded-sm transition-all"/>
                            <div class="grow ms-3 text-sm font-medium text-neutral-700 tracking-tight truncate">{{ record.employee.user.name }}</div>
                            <div class="text-sm font-medium text-primary-600">{{ formatTime(record.punch_in) }}</div>
                        </Link>
                        <div v-if="!clock_in_late.length" class="text-xs text-neutral-600 py-1">No delayed entries recorded.</div>
                    </div>
                </div>

                <div class="flex flex-col mt-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold uppercase text-neutral-600">Attendance Missing</span>
                        <span class="text-xs font-bold bg-neutral-300 text-primary-800 px-2 py-0.5 rounded w-8 h-6 flex items-center justify-center">{{ clock_in_missing.length < 9 ? '0' : '' }}{{ clock_in_missing.length }}</span>
                    </div>
                    <div class="space-y-2">
                        <Link v-for="record in clock_in_missing" :key="record.id" 
                            :href="route('employees.show', {employee: record.employee.user.id})"
                            class="flex items-center bg-white border border-neutral-200 p-1 pe-3 rounded-sm hover:border-neutral-900 transition-all group"
                        >
                            <EmployeeAvatar :image="record.employee.profile_picture" :name="record.employee.user.name"
                            class="h-9 w-9 rounded-sm transition-all"/>
                            <div class="grow ms-3 text-sm font-medium text-neutral-700 tracking-tight truncate">{{ record.employee.user.name }}</div>
                            <div class="text-sm font-medium text-primary-600">{{ formatTime(record.punch_in) }}</div>
                        </Link>
                        <div v-if="!clock_in_missing.length" class="text-xs text-neutral-600 py-1">All trainees have punch in</div>
                    </div>
                </div>
            </div>

            <div v-else class="flex flex-col h-full justify-center">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white border border-neutral-200 p-4 rounded-sm">
                        <div class="text-[9px] font-bold uppercase tracking-[0.2em] text-neutral-800 mb-2">Clock In</div>
                        <div v-if="today_record && today_record.punch_in" class="text-2xl font-work-sans font-bold text-neutral-900">
                            {{ formatTime(today_record.punch_in) }}
                        </div>
                        <div v-else class="text-sm text-neutral-800 font-bold">Not Clocked In Yet</div>
                    </div>
                    <div class="bg-white border border-neutral-200 p-4 rounded-sm">
                        <div class="text-[9px] font-bold uppercase tracking-[0.2em] text-neutral-800 mb-2">Clock In</div>
                        <div v-if="today_record && today_record.punch_out" class="text-2xl font-work-sans font-bold text-neutral-900">
                            {{ formatTime(today_record.punch_out) }}
                        </div>
                        <div v-else class="text-sm text-neutral-800 font-bold">Not Clocked Out Yet</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.font-mono {
    font-variant-numeric: tabular-nums;
}
</style>