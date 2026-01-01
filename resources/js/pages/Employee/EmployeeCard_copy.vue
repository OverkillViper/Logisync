<script setup>
import { Link } from '@inertiajs/vue3';
import EmployeeAvatar from './EmployeeAvatar.vue';

defineProps({
    employee: Object
})

</script>

<template>
    <Link :href="route('employees.show', { employee: employee.id })" class="bg-white shadow-xs border transition-all ease-in cursor-pointer rounded-xl hover:border-primary-300 flex flex-col p-5 items-center justify-center">
        <span class="flex font-chakra items-center w-30 justify-center border tracking-widest border-primary-950 rounded-full uppercase text-xs px-4 py-2 font-medium bg-primary-800 text-white">
            {{ employee.role }}
        </span>

        <!-- Profile Image -->
        <div class="flex items-center mt-6">
            <EmployeeAvatar :image="employee.profile_picture" :name="employee.user.name" class="h-24 w-24 rounded-full object-cover text-3xl border border-primary-300 bg-primary-100! text-primary-800!"/>
        </div>
        
        <span class="mt-4 font-bold text-md">{{ employee.user.name }}</span>
        <span class="text-md text-sm text-primary-400">{{ employee.user.email }}</span>
        <div class="text-primary-400 text-sm flex gap-x-2 items-center mt-4">
            <span>{{ employee.designation ?? 'Not set' }}</span>
            <!-- <span>&#8226;</span>
            <span>{{ employee.experience ?? 'Not set' }}</span> -->
        </div>
        <!-- <Link class="mt-2 border w-30 text-center border-primary-600 text-primary-600 bg-primary-50 font-medium px-2 py-2 rounded-md text-xs hover:text-white hover:bg-primary-800 transition-all ease-in">
            View Profile
        </Link> -->
         
    </Link>
</template>