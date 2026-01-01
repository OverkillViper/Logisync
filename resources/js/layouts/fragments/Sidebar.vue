<script setup>
import SidebarItems from './SidebarItems.vue';
import { usePage } from '@inertiajs/vue3';

const role = usePage().props.auth.user.employee.role;

const sidebarItems = [
    { name: 'Dashboard'  , icon: 'dashboard'  , href: 'dashboard'        , roles: ['admin', 'trainer', 'trainee']},
    { name: 'Employees'  , icon: 'badge'      , href: 'employees.index'  , roles: ['admin', 'trainer', 'trainee']},
    { name: 'Projects'   , icon: 'assignment' , href: 'project.index'    , roles: ['admin', 'trainer', 'trainee']},
    { name: 'Evaluation' , icon: 'bar_chart'  , href: 'evaluation.index' , roles: ['admin', 'trainer'           ]},
    { name: 'Batches'    , icon: 'group_work' , href: 'batch.index'      , roles: ['admin', 'trainer'           ]},
    { name: 'Attendance' , icon: 'punch_clock', href: 'attendance.index' , roles: ['admin', 'trainer', 'trainee']},
    // { name: 'Settings'   , icon: 'settings'    , href: ''              },
];

const emit = defineEmits(['toggleSidebar'])
const props = defineProps({
  isCollapsed: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
    <div class="flex flex-col h-full">
        <div class="flex flex-col grow justify-center">
            <div v-for="item in sidebarItems" :key="item.name">
                <SidebarItems v-if="item.roles.includes(role)" :name="item.name" :icon="item.icon" :href="item.href" :collapsed="isCollapsed"/>
            </div>
        </div>
        
        <button @click="emit('toggleSidebar')" class="flex items-center gap-x-3 p-3 rounded-lg cursor-pointer transition">
            <span class="material-icons-round" style="font-size: 1.2rem;">{{isCollapsed ? 'keyboard_double_arrow_right' : 'keyboard_double_arrow_left' }}</span>
            <span class="text-sm font-medium font-work-sans ms-4">Collapse</span>
        </button>
    </div>
</template>