<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { MultiSelect } from 'primevue';
import ToggleSwitch from 'primevue/toggleswitch';
import DatePicker from 'primevue/datepicker';
import Button from 'primevue/button';


defineProps({
    trainees : Array,
    trainers : Array,
})

const createProjectForm = useForm({
    name                : ''   ,
    start_date          : null ,
    deadline            : null ,
    assignee            : []   ,
    reviewer            : []   ,
    assign_individually : false,
})

const formatDateToYMD = (dateObj) => {
    if (!dateObj) return null
    
    const     date    = new Date(dateObj)
    const     year    = date.getFullYear()
    const     month   = String(date.getMonth() + 1).padStart(2, '0')
    const     day     = String(date.getDate()).padStart(2, '0')
    
    return `${year}-${month}-${day}`
}

const createProject = () => {
    const payload = {
        ...createProjectForm.data(),
        start_date: formatDateToYMD(createProjectForm.start_date),
        deadline: formatDateToYMD(createProjectForm.deadline),
    }

    createProjectForm.transform(() => payload).post(route('project.store'), {
            onSuccess: () => {
            createProjectForm.reset()
        },
    })
}
</script>

<template>

<AppLayout title="New Project" summary="Create a new project">
    <div class="w-2/3 mx-auto">
        <form @submit.prevent="createProject" class="flex flex-col">
            <span class="text-primary-400 text-sm font-medium">Project Name</span>
            <input v-model="createProjectForm.name" type="text" class="border-b-2 border-primary-200 text-xl py-2 focus:outline-none focus:border-primary-800 transition-all" placeholder="Enter project name">

            <div class="flex items-center mt-8 gap-x-8">
                <div class="flex flex-col w-1/2">
                    <div class="flex items-center justify-between">
                        <span class="text-primary-400 text-sm font-medium">Project Assignee(s)</span>
                        <div class="flex items-center gap-2">
                            <!-- <Checkbox v-model="createProjectForm.assign_individually" inputId="ingredient1" value="Cheese" size="small"/> -->
                            <span class="text-sm text-primary-500">Assign individually</span>
                            <ToggleSwitch v-model="createProjectForm.assign_individually" />
                        </div>
                    </div>
                    <MultiSelect 
                        v-model="createProjectForm.assignee"
                        display="chip"
                        :options="trainees"
                        optionLabel="label"
                        optionValue="value"
                        filter
                        :maxSelectedLabels="2"
                        placeholder="Select an assignee"
                        class="w-full mt-2"
                        size="small"
                    />
                </div>
                <div class="flex flex-col w-1/2">
                    <span class="text-primary-400 text-sm font-medium">Project Reviewer(s)</span>
                    <MultiSelect 
                        v-model="createProjectForm.reviewer"
                        display="chip"
                        :options="trainers"
                        optionLabel="label"
                        optionValue="value"
                        :maxSelectedLabels="2"
                        filter
                        placeholder="Select an reviewer"
                        class="w-full mt-2"
                        size="small"
                    />
                </div>
            </div>

            <div class="flex items-center mt-8 gap-x-8">
                <div class="flex flex-col w-1/2">
                    <span class="text-primary-400 text-sm font-medium">Project Start Date</span>
                    <DatePicker size="small" v-model="createProjectForm.start_date" showIcon fluid iconDisplay="input" class="mt-2 w-full" showButtonBar dateFormat="dd-M-yy" />
                </div>
                <div class="flex flex-col w-1/2">
                    <span class="text-primary-400 text-sm font-medium">Project Deadline</span>
                    <DatePicker size="small" v-model="createProjectForm.deadline" showIcon fluid iconDisplay="input" class="mt-2 w-full" showButtonBar dateFormat="dd-M-yy" />
                </div>
            </div>

            <div class="mt-8 flex justify-between">
                <Link :href="route('project.index')"><Button label="Cancel" icon="pi pi-times" size="small" class="rounded-sm! px-6! uppercase"/></Link>
                <Button label="Create Project" icon="pi pi-plus" size="small" type="submit" class="rounded-sm! px-6! uppercase"/>
            </div>
        </form>
    </div>
</AppLayout>

</template>