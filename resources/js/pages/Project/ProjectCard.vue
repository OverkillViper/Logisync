<script setup>
import Avatar from 'primevue/avatar';
import AvatarGroup from 'primevue/avatargroup';
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    project: Object
});

// First 3 assignees
const firstThreeAssignees = computed(() => props.project.assignees.slice(0, 3));

// Remaining count
const remainingAssigneeCount = computed(() => {
    return props.project.assignees.length > 3 ? props.project.assignees.length - 3 : 0;
});

const firstThreeReviewers = computed(() => props.project.reviewers.slice(0, 3));

// Remaining count
const remainingReviewersCount = computed(() => {
    return props.project.assignees.length > 3 ? props.project.reviewers.length - 3 : 0;
});

const statusColor = (status) => ({
    'Not Started'   : 'border border-gray-200 text-gray-500'  ,
    'In Progress'   : 'border border-blue-200 text-blue-500'  ,
    'Overdue'       : 'border border-red-200 text-red-500'   ,
    'Completed'     : 'border border-green-200 text-green-500' ,
    'Completed Late': 'border border-orange-200 text-orange-500',
}[status] || '');

</script>

<template>
    <Link :href="route('project.show', {project: project.id})" class="border rounded-md border-primary-100 p-3 flex flex-col hover:border-primary-600 transition ease-in">
        <!-- Project Status -->
        <div class="flex select-none">
            <span class="uppercase text-xs font-medium tracking-wider px-2 py-0.5 rounded-sm" :class="statusColor(project.status)">{{ project.status }}</span>
        </div>

        <!-- Project Title -->
        <div class="mt-4 font-semibold font-work-sans text-primary-800">{{ project.name }}</div>

        <!-- Project Deadline -->
        <div class="text-xs text-primary-500" v-if="!project.completion_date">{{ project.remaining_days < 0 ? `${Math.abs(project.remaining_days)} days overdue` : `${project.remaining_days} days left` }}</div>
        <div class="text-xs text-primary-500" v-else>Project complete</div>
        
        <hr class="my-3">
        <div class="grid grid-cols-2">
            <span class="text-xs text-primary-500 mb-2">Assignee(s)</span>
            <span class="text-xs text-primary-500 mb-2 place-self-end">Reviewer(s)</span>

            <AvatarGroup v-if="firstThreeAssignees.length">
                <Avatar
                    v-for="assignee in firstThreeAssignees"
                    :key="assignee.id"
                    :label="!assignee.user.profile_picture ? assignee.user.name[0] : undefined"
                    :image="assignee.user.profile_picture || undefined"
                    shape="circle"
                    class="border-2 border-white select-none"
                    v-tooltip.bottom="assignee.user.name"
                />
                <!-- Show +n if more than 3 -->
                <Avatar
                    v-if="remainingAssigneeCount > 0"
                    :label="`+${remainingAssigneeCount}`"
                    shape="circle"
                    class="border-2 border-white bg-gray-300 text-gray-800"
                />
            </AvatarGroup>
            <div v-else>
                N/A
            </div>

            <AvatarGroup v-if="firstThreeReviewers.length" class="place-self-end">
                <Avatar
                    v-for="reviewer in firstThreeReviewers"
                    :key="reviewer.id"
                    :label="!reviewer.user.profile_picture ? reviewer.user.name[0] : undefined"
                    :image="reviewer.user.profile_picture || undefined"
                    v-tooltip.bottom="reviewer.user.name"
                    shape="circle"
                    class="border-2 border-white select-none"
                />
                <!-- Show +n if more than 3 -->
                <Avatar
                    v-if="remainingReviewersCount > 0"
                    :label="`+${remainingReviewersCount}`"
                    shape="circle"
                    class="border-2 border-white bg-gray-300 text-gray-800"
                />
            </AvatarGroup>
            <div v-else class="place-self-end">
                N/A
            </div>
        </div>
        <!-- Assignees -->
        
    </Link>
</template>

<style>
.p-tooltip-text {
    font-size: smaller;
    font-family: 'Inter', sans-serif;
}
</style>