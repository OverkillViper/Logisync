<script setup>
import { Link } from '@inertiajs/vue3';
import EmployeeAvatar from './EmployeeAvatar.vue';
import { computed } from 'vue';

const props = defineProps({
    employee: Object
});

const role_icon = computed(() => {
    switch (props.employee.role) {
        case 'admin': return 'pi-shield';
        case 'trainer': return 'pi-graduation-cap';
        case 'trainee': return 'pi-user';
        default: return 'pi-user';
    }
});

const role_name = computed(() => {
    switch (props.employee.role) {
        case 'admin': return 'Admin';
        case 'trainer': return 'Trainer';
        case 'trainee': return 'Trainee';
        default: return 'Member';
    }
});
</script>

<template>
    <Link :href="route('employees.show', { employee: employee.id })"
        class="bg-white border border-neutral-100 p-5 rounded-sm flex items-center gap-x-5 hover:border-primary-300 hover:shadow-xl hover:shadow-neutral-50 transition-all duration-300 group cursor-pointer"
    >  
        <div class="relative shrink-0">
            <EmployeeAvatar
                :image="employee.profile_picture" :name="employee.user.name"
                class="h-16 w-16 rounded-sm object-cover border-2 border-neutral-50 transition-colors duration-500"
            />
            <div class="absolute -top-2 -right-2 bg-white shadow-sm border border-neutral-100 w-7 h-7 rounded-full flex items-center justify-center" v-tooltip="role_name">
                <i class="pi text-neutral-400 group-hover:text-primary-500 transition-colors" :class="role_icon" style="font-size: 0.7rem"></i>
            </div>
        </div>

        <div class="flex flex-col grow min-w-0">
            <div class="flex items-center gap-x-2">
                <span class="font-semibold text-neutral-800 tracking-tight line-clamp-1 capitalize">{{ employee.user.name }}</span>
            </div>
            
            <div class="text-xs font-medium text-primary-600/80 mb-2">
                {{ employee.designation }}
            </div>

            <div class="flex items-center gap-x-3 text-[11px] text-neutral-500">
                <div class="flex items-center gap-x-1 truncate">
                    <span class="pi pi-envelope text-[9px]"></span>
                    <span class="truncate">{{ employee.user.email }}</span>
                </div>
            </div>
        </div>
        
        <div class="opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all">
            <i class="pi pi-chevron-right text-neutral-300 text-xs"></i>
        </div>
    </Link>
</template>

<style>
    .p-tooltip {
        font-size: 0.85rem !important;
    } 
</style>