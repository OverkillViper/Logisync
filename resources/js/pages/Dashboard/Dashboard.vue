<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import ProjectsWidget from './ProjectsWidget.vue';
import AttendanceWidget from './AttendanceWidget.vue';
import ProjectStatWidget from './ProjectStatWidget.vue';
import LeaveWidget from './LeaveWidget.vue';

defineProps({
    user             : Object ,
    projects         : Array  ,
    isTrainee        : Boolean,
    clock_in_late    : Array  ,
    clock_in_missing : Array  ,
    today_record     : Object ,
    project_stats    : Object ,
    leaves           : Array
});

</script>

<template>
    <AppLayout title="Dashboard" summary="Welcome to your dashboard">
        <div class="flex gap-6 me-4">
            <div class="w-2/3 flex flex-col gap-y-6">
                <!-- Projects -->
                <ProjectsWidget :projects="projects" />

                <!-- Project Statistics -->
                <ProjectStatWidget :project_stats="project_stats" />

                <!-- Leave History -->
                <LeaveWidget :isTrainee="isTrainee" :leaves="leaves"/>
            </div>
            <div class="w-1/3 flex flex-col">
                <!-- Attendance -->
                <AttendanceWidget
                    :isTrainee="isTrainee"
                    :clock_in_late="clock_in_late"
                    :clock_in_missing="clock_in_missing"
                    :today_record="today_record"
                />
            </div>
        </div>
    </AppLayout>
</template>