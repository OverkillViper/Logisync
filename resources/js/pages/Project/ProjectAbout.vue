<script setup>
import { Link } from '@inertiajs/vue3';
import EmployeeAvatar from '../Employee/EmployeeAvatar.vue';

defineProps({
    project: Object
})

</script>

<template>
    <div class="grid grid-cols-2 gap-y-4 gap-x-8">
        <div class="text-[11px] uppercase font-medium">Assignee{{ project.assignees.length > 1 ? 's' : '' }}</div>
        <div class="text-[11px] uppercase font-medium">Reviewer{{ project.reviewers.length > 1 ? 's' : '' }}</div>

        <div class="flex flex-col gap-y-4">
            <Link
                v-for="assignee in project.assignees"
                :key="assignee.user.id"
                :href="route('employees.show', {employee: assignee.user.id})"
                class="group flex items-center bg-white border border-neutral-100 rounded-md p-3 hover:border-neutral-900 hover:shadow-xl hover:shadow-neutral-900/5 transition-all duration-300"
            >
                <div class="relative shrink-0">
                    <EmployeeAvatar
                        :image="assignee.profile_picture"
                        :name="assignee.user.name"
                        class="h-10 w-10 rounded-lg object-cover grayscale group-hover:grayscale-0 transition-all duration-500 border border-neutral-50"
                    />
                </div>
                
                <div class="flex flex-col ms-4 grow overflow-hidden">
                    <span class="text-xs font-bold text-neutral-900 uppercase tracking-tight truncate transition-colors group-hover:text-primary-600">
                        {{ assignee.user.name }}
                    </span>
                    <span class="text-[10px] text-neutral-700 tracking-tighter mt-0.5">
                        {{ assignee.designation}}
                    </span>
                </div>

                <div class="opacity-0 group-hover:opacity-100 transition-opacity px-2">
                    <i class="pi pi-arrow-up-right text-neutral-300 text-[10px]"></i>
                </div>
            </Link>
        </div>

        <div class="flex flex-col gap-y-4">
            <Link
                v-for="reviewer in project.reviewers"
                :key="reviewer.user.id"
                :href="route('employees.show', {employee: reviewer.user.id})"
                class="group flex items-center bg-white border border-neutral-100 rounded-md p-3 hover:border-neutral-900 hover:shadow-xl hover:shadow-neutral-900/5 transition-all duration-300"
            >
                <div class="relative shrink-0">
                    <EmployeeAvatar
                        :image="reviewer.profile_picture"
                        :name="reviewer.user.name"
                        class="h-10 w-10 rounded-lg object-cover grayscale group-hover:grayscale-0 transition-all duration-500 border border-neutral-50"
                    />
                </div>
                
                <div class="flex flex-col ms-4 grow overflow-hidden">
                    <span class="text-xs font-bold text-neutral-900 uppercase tracking-tight truncate transition-colors group-hover:text-primary-600">
                        {{ reviewer.user.name }}
                    </span>
                    <span class="text-[10px] text-neutral-700 tracking-tighter mt-0.5">
                        {{ reviewer.designation}}
                    </span>
                </div>

                <div class="opacity-0 group-hover:opacity-100 transition-opacity px-2">
                    <i class="pi pi-arrow-up-right text-neutral-300 text-[10px]"></i>
                </div>
            </Link>
        </div>
    </div>
</template>