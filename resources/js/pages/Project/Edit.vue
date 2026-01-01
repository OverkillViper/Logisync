<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { MultiSelect } from 'primevue';
import Button from 'primevue/button';
import EmployeeAvatar from '../Employee/EmployeeAvatar.vue';
import { Link } from '@inertiajs/vue3';
import DatePicker from 'primevue/datepicker';

const props = defineProps({
    project: Object,
    trainees: Array,
    trainers: Array,
})


const formatYMDToDate = (dateObj) => {
    if (!dateObj) return null;
    
    const date = new Date(dateObj);
    const year = date.getFullYear();
    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", 
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    const month = months[date.getMonth()];
    const day = String(date.getDate()).padStart(2, '0');
    
    return `${day}-${month}-${year}`;
};

const editProjectForm = useForm({
    name: props.project.name,
    start_date: formatYMDToDate(props.project.start_date),
    deadline: formatYMDToDate(props.project.deadline)
})

const createAssigneesForm = useForm({
    assignees: null,
})

const createReviewersForm = useForm({
    reviewers: null,
})

const createAssignees = () => {
    createAssigneesForm.post(route('project.assignee.store', {project: props.project}), {
        preserveScroll: true,
        onSuccess: () => {
            createAssigneesForm.reset()
        }
    })
}

const createReviewers = () => {
    createReviewersForm.post(route('project.reviewer.store', {project: props.project}), {
        preserveScroll: true,
        onSuccess: () => {
            createAssigneesForm.reset()
        }
    })
}

const saveProject = () => {
    editProjectForm.put(route('project.update', {project: props.project.id}))
}

</script>

<template>
    <AppLayout title="Edit Project" summary="Modify the project">
        <form @submit.prevent class="xl:w-4/5 2xl:w-3/4 mx-auto grid grid-cols-2 mt-6 gap-x-12">
            <span class="text-primary-400 text-sm font-medium">Project Name</span>
            <input v-model="editProjectForm.name" autofocus type="text" class="col-span-2 border-b-2 border-primary-200 text-xl py-2 focus:outline-none focus:border-primary-800 transition-all" placeholder="Enter project name">

            <span class="text-primary-400 text-sm font-medium mt-6">Project Start Date</span>
            <span class="text-primary-400 text-sm font-medium mt-6">Project Deadline</span>

            <DatePicker size="small" v-model="editProjectForm.start_date" showIcon fluid iconDisplay="input" class="mt-2 w-full" showButtonBar dateFormat="dd-M-yy" />
            <DatePicker size="small" v-model="editProjectForm.deadline" showIcon fluid iconDisplay="input" class="mt-2 w-full" showButtonBar dateFormat="dd-M-yy" />

            <span class="text-primary-400 text-sm font-medium mt-6">Project Assignees</span>
            <span class="text-primary-400 text-sm font-medium mt-6">Project Reviewers</span>
            <div class="flex justify-between items-center gap-x-3 mt-2">
                <MultiSelect 
                    v-model="createAssigneesForm.assignees"
                    display="chip"
                    :options="trainees"
                    optionLabel="label"
                    optionValue="value"
                    filter
                    :maxSelectedLabels="1"
                    placeholder="Select assignee"
                    class="grow"
                    size="small"
                >
                    <template #option="slotProps">
                        <div class="flex items-center text-sm font-inter">
                            <div>{{ slotProps.option.label }}</div>
                        </div>
                    </template>
                </MultiSelect>
                <Button @click="createAssignees()" label="Add" icon="pi pi-plus" size="small" class="px-4! rounded-sm! uppercase"/>
            </div>

            <div class="flex justify-between items-center gap-x-3 mt-2">
                <MultiSelect 
                    v-model="createReviewersForm.reviewers"
                    display="chip"
                    :options="trainers"
                    optionLabel="label"
                    optionValue="value"
                    filter
                    :maxSelectedLabels="1"
                    placeholder="Select reviewer"
                    class="grow"
                    size="small"
                />
                <Button @click="createReviewers()" label="Add" icon="pi pi-plus" size="small" class="px-4! rounded-sm! uppercase"/>
            </div>
        
            <div class="flex flex-col mt-4 gap-y-2">
                <div v-for="assignee in project.assignees"  :key="assignee.id" class="flex items-center gap-x-4">
                    <Link
                        :href="route('employees.show', {employee: assignee.user.id})"
                        class="group grow flex items-center bg-white border border-neutral-100 rounded-md p-3 hover:border-neutral-900 hover:shadow-lg hover:shadow-neutral-900/5 transition-all duration-300"
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
                    <Link as="button" method="delete" :href="route('project.assignee.remove', {project: project, employee: assignee.id})">
                        <Button label="Remove" icon="pi pi-times" size="small" variant="outlined" class="px-4! rounded-sm! uppercase"/>
                    </Link>
                </div>
            </div>

            <div class="flex flex-col mt-4 gap-y-2">
                <div v-for="reviewer in project.reviewers" :key="reviewer.id" class="flex items-center gap-x-4">
                    <Link
                        :href="route('employees.show', {employee: reviewer.user_id})"
                        class="grow group flex items-center bg-white border border-neutral-100 rounded-md p-3 hover:border-neutral-900 hover:shadow-lg hover:shadow-neutral-900/5 transition-all duration-300"
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
                    <Link as="button" method="delete" :href="route('project.reviewer.remove', {project: project, employee: reviewer.id})">
                        <Button label="Remove" icon="pi pi-times" size="small" variant="outlined" class="px-4! rounded-sm! uppercase"/>
                    </Link>
                </div>
            </div>

            <div class="flex justify-end gap-x-4 col-span-2 mt-6">
                <Link :href="route('project.show', {project: props.project.id})">
                    <Button label="Cancel" icon="pi pi-times" size="small" class="px-4! rounded-sm! uppercase" outlined/>
                </Link>
                <Button label="Save" icon="pi pi-save" size="small" @click="saveProject()" class="px-4! rounded-sm! uppercase"/>
            </div>
        </form>
    </AppLayout>
</template>