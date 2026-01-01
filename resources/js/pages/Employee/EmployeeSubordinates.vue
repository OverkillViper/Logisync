<script setup>
import { Link } from '@inertiajs/vue3';
import EmployeeAvatar from './EmployeeAvatar.vue';

defineProps({
    subordinates: Array
})
</script>

<template>
    <div class="label">Trainees supervised by trainer</div>

    <div class="flex flex-col mt-4 gap-3" v-if="subordinates.length">
        <Link
            v-for="subordinate in subordinates" :key="subordinate.id"
            :href="route('employees.show', {employee: subordinate.id})"
            class="group w-1/3 flex items-center bg-white border border-neutral-100 rounded-md p-3 hover:border-neutral-900 hover:shadow-lg hover:shadow-neutral-900/5 transition-all duration-300"
        >
            <div class="relative shrink-0">
                <EmployeeAvatar
                    :image="subordinate.profile_picture"
                    :name="subordinate.user.name"
                    class="h-10 w-10 rounded-lg object-cover grayscale group-hover:grayscale-0 transition-all duration-500 border border-neutral-50"
                />
            </div>
            
            <div class="flex flex-col ms-4 grow overflow-hidden">
                <span class="text-xs font-bold text-neutral-900 uppercase truncate transition-colors group-hover:text-primary-600">
                    {{ subordinate.user.name }}
                </span>
                <span class="text-[10px] text-neutral-700 tracking-tighter mt-0.5">
                    {{ subordinate.designation}}
                </span>
            </div>

            <div class="opacity-0 group-hover:opacity-100 transition-opacity px-2">
                <i class="pi pi-arrow-up-right text-neutral-300 text-[10px]"></i>
            </div>
        </Link>
    </div>
    <div v-else class="flex items-center gap-x-2 mt-2 text-sm  text-primary-400">
        <span class="material-icons-outlined" style="font-size: medium;">info</span>
        <span>Not currently supervising any trainees</span>
    </div>
    
</template>