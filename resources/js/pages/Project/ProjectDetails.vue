<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import EmployeeAvatar from '../Employee/EmployeeAvatar.vue';
import { Link, usePage, useForm, router } from '@inertiajs/vue3';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';
import ProjectAbout from './ProjectAbout.vue';
import ProjectTracking from './ProjectTracking.vue';
import ProjectDeadline from './ProjectDeadline.vue';
import { Button } from 'primevue';
import ProjectComments from './ProjectComments.vue';
import Badge from 'primevue/badge';
import Dialog from 'primevue/dialog';
import { ref,computed } from 'vue';
import DatePicker from 'primevue/datepicker';
import { useConfirm } from "primevue/useconfirm";
import ConfirmPopup from 'primevue/confirmpopup';

const requestDialogVisible = ref(false);

const props = defineProps({
    project: Object,
    trackingStages: Array,
})

const auth_user = usePage().props.auth.user;
const auth_employee = auth_user.employee;
const confirm = useConfirm();

const isReviewer = computed(() => {
  return props.project.reviewers?.some(r => r.id === auth_employee?.id)
})

const isAssignee = computed(() => {
  return props.project.assignees?.some(r => r.id === auth_employee?.id)
})

const getDay = (dateObj) => {
    if (!dateObj) return null;
    const date = new Date(dateObj);
    if (isNaN(date)) return null;
    return String(date.getDate()).padStart(2, '0');
};

// Returns month as short name, e.g., "May"
const getMonth = (dateObj) => {
    if (!dateObj) return null;
    const date = new Date(dateObj);
    if (isNaN(date)) return null;
    return date.toLocaleString('en-GB', { month: 'long' });
};

// Returns year as string, e.g., "2025"
const getYear = (dateObj) => {
    if (!dateObj) return null;
    const date = new Date(dateObj);
    if (isNaN(date)) return null;
    return String(date.getFullYear());
};

const statusColor = (status) => ({
    'Not Started'   : 'border-2 border-gray-200 text-gray-500'  ,
    'In Progress'   : 'border-2 border-blue-200 text-blue-500'  ,
    'Overdue'       : 'border-2 border-red-200 text-red-500'   ,
    'Completed'     : 'border-2 border-green-200 text-green-500' ,
    'Completed Late': 'border-2 border-orange-200 text-orange-500',
}[status] || '');

const requestCompletionForm = useForm({
    completion_date: null
})

const minRequestDate = computed(() => {
    const start_date = new Date(props.project.star_date);

    return start_date;
});

const requestCompletion = () => {
    requestCompletionForm.post(route('project.completion.request', {project: props.project.id}), {
        preserveScroll: true,
        onSuccess: () => {
            requestCompletionForm.reset();
            requestDialogVisible.value = false;
        }
    })
}

const cancelCompletionRequest = (completionRequestID) => {
    router.delete(route('project.completion.request.cancel', {completionRequest: completionRequestID}), {
        preserveScroll: true
    })
}

const cancelProjectCompletion = () => {
    router.delete(route('project.completion.cancel', {project: props.project.id}), {
        preserveScroll: true
    })
}

const approveCompletionRequest = (completionRequestID) => {
    router.delete(route('project.completion.approve', {completionRequest: completionRequestID}), {
        preserveScroll: true
    })
}

const declineCompletionRequest = (completionRequestID) => {
    router.delete(route('project.completion.decline', {completionRequest: completionRequestID}), {
        preserveScroll: true
    })
}

const confirmProjectDelete = (event) => {
    confirm.require({
        target: event.currentTarget,
        message: 'Are you sure you want to delete this project?',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true,
            class: 'px-4! rounded-sm!'
        },
        acceptProps: {
            label: 'Delete',
            class: 'px-4! rounded-sm!'
        },
        accept: () => {
            router.delete(route('project.destroy', {project: props.project.id}));
        },
        reject: () => {
            
        }
    });
};

</script>

<template>
    <AppLayout title="Projects Details" summary="View project detail">
        <div class="xl:w-4/5 2xl:w-3/4 mx-auto">
            <div class="text-[10px] uppercase text-primary-500">
                Project Name
            </div>
            <div class="flex items-center gap-x-2">
                <!-- Project Title -->
                <div class="text-2xl font-bold font-chakra grow">{{ project.name }}</div>
                <Button @click="confirmProjectDelete($event)" label="Delete Project" class="px-4! rounded-sm! uppercase text-sm!" size="small" icon="pi pi-trash" outlined v-if="auth_employee.role === 'admin' || auth_user.id === project.creator.id"/>
                <ConfirmPopup class="text-sm!"></ConfirmPopup>
                <Link :href="route('project.edit', {project: project.id})" v-if="auth_employee.role != 'trainee'">
                    <Button label="Edit Project" size="small" icon="pi pi-pencil" class="px-4! rounded-sm! uppercase text-sm!"/>
                </Link>
            </div>
            
            <div class="grid gap-x-6 mt-6 grid-cols-5">
                <span class="text-[10px] uppercase text-primary-500">Created By</span>
                <span class="text-[10px] uppercase text-primary-500">Started on</span>
                <span class="text-[10px] uppercase text-primary-500">Deadline</span>
                <span class="text-[10px] uppercase text-primary-500">{{ project.completion_date ? 'Completed On' : ''}}</span>
                <span class="text-[10px] uppercase text-primary-500">Status</span>
            </div>
            
            <div class="grid gap-x-6 pt-1 items-center grid-cols-5">
                <!-- Project Created By -->
                <Link :href="route('employees.show', {employee: project.creator.id})" class="flex gap-x-2 items-center border border-primary-200 p-1 rounded-sm hover:border-primary-500 transition">
                    <EmployeeAvatar :image="project.creator.employee.profile_picture" :name="project.creator.name" class="h-10 w-10 rounded-sm text-sm! object-cover bg-primary-950"/>
                    <div class="flex flex-col">
                        <div class="text-primary-700 text-xs font-medium">{{ project.creator.name }}</div>
                        <div class="text-[10px] text-neutral-700 tracking-tighter mt-0.5">{{ project.creator.employee.designation }}</div>
                    </div>
                </Link>

                <!-- Project Start Date -->
                <div class="text-primary-900 font-semibold flex gap-x-2 items-center">
                    <div class="text-lg bg-primary-950 w-10 h-10 flex items-center justify-center text-white rounded-sm">{{ getDay(project.start_date) }}</div>
                    <div class="flex flex-col font-medium text-xs">
                        <span>{{ getMonth(project.start_date) }}</span>
                        <span>{{ getYear(project.start_date) }}</span>
                    </div>
                </div>

                <!-- Project Deadline -->
                <div class="text-primary-900 font-semibold flex gap-x-2 items-center">
                    <div class="text-lg bg-primary-950 w-10 h-10 flex items-center justify-center text-white rounded-sm">{{ getDay(project.deadline) }}</div>
                    <div class="flex flex-col font-medium text-xs">
                        <span>{{ getMonth(project.deadline) }}</span>
                        <span>{{ getYear(project.deadline) }} ({{ project.remaining_days < 0 ? `${Math.abs(project.remaining_days)} days overdue` : `${project.remaining_days} days left` }})</span>
                    </div>
                </div>

                <!-- Project Completion Date -->
                <div class="text-primary-900 font-semibold flex gap-x-2 items-center">
                    <div v-if="project.completion_date" class="text-lg bg-primary-950 w-10 h-10 flex items-center justify-center text-white rounded-sm">{{ getDay(project.completion_date) }}</div>
                    <div v-if="project.completion_date" class="flex flex-col font-medium text-xs">
                        <span>{{ getMonth(project.completion_date) }}</span>
                        <span>{{ getYear(project.completion_date) }}</span>
                    </div>
                </div>

                <div class="h-10 flex select-none items-center justify-center rounded-sm font-semibold" :class="statusColor(project.status)">
                    {{ project.status }}
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-y-2" v-if="isAssignee">
                <div class="flex items-center gap-x-2">
                    <div class="text-[10px] uppercase text-primary-500">Project Completion</div>
                    <div class="grow">
                        <hr class="border"/>
                    </div>
                </div>
                <div v-if="project.completion_date">
                    <Button @click="cancelProjectCompletion" label="Cancel Project Completion" size="small" icon="pi pi-times" class="px-4! rounded-sm!"/>
                </div>
                <div v-else-if="project.completion_request.length" class="flex justify-between items-center ps-4 pe-2 border py-2 rounded-md border-primary-200">
                    <span class="text-sm text-primary-500">
                        Project completion requested with completion date
                        <span class="font-semibold">{{ getDay(project.completion_request[0].completion_date) }} {{ getMonth(project.completion_request[0].completion_date) }},
                        {{ getYear(project.completion_request[0].completion_date) }}</span>
                        
                    </span>

                    <div class="flex items-center gap-x-2">
                        <Button class="px-4! rounded-sm!" @click="cancelCompletionRequest(project.completion_request[0].id)" label="Cancel Completion Request" size="small" icon="pi pi-times" />
                    </div>
                </div>
                <div v-else>
                    <Button @click="requestDialogVisible = true" label="Request Project Completion" size="small" class="px-4! rounded-sm!"/>
                </div>
            </div>
            <div class="mt-4 flex flex-col gap-y-2" v-if="isReviewer">
                <div class="flex items-center gap-x-2">
                    <div class="text-[10px] uppercase text-primary-500">Project Completion</div>
                    <div class="grow">
                        <hr class="border"/>
                    </div>
                </div>
                <div v-if="project.completion_request.length" class="flex justify-between items-center ps-4 pe-2 border py-2 rounded-md border-primary-200">
                    <span  class="text-sm text-primary-500">Project completion requested with completion date
                        <span class="font-semibold">
                            {{ getDay(project.completion_request[0].completion_date) }} {{ getMonth(project.completion_request[0].completion_date) }},
                        {{ getYear(project.completion_request[0].completion_date) }}
                        </span>
                        
                    </span>
                    <div class="flex items-center gap-x-2">
                        <Button class="rounded-sm! px-4!" @click="approveCompletionRequest(project.completion_request[0].id)" label="Approve Completion Request" size="small" icon="pi pi-check"/>
                        <Button class="rounded-sm! px-4!" @click="declineCompletionRequest(project.completion_request[0].id)" outlined label="Decline Completion Request" size="small" icon="pi pi-times"/>
                    </div>
                </div>
                <div v-else-if="!project.completion_date" class="flex items-center gap-x-2 text-sm  text-primary-400">
                    <span class="material-icons-outlined" style="font-size: medium;">info</span>
                    <span>No project completion request</span>
                </div>
                <div v-else>
                    <Button @click="cancelProjectCompletion" label="Cancel Project Completion" size="small" icon="pi pi-times" class="px-4! rounded-sm!"/>
                </div>
            </div>
            <Dialog
                v-model:visible="requestDialogVisible"
                modal
                header="Request Project Completion"
                :style="{ width: '25rem' }"
            >
                <form @submit.prevent>
                    <div class="text-sm font-medium mb-2">Completion Date</div>
                    <DatePicker
                        v-model="requestCompletionForm.completion_date"
                        showIcon
                        fluid
                        iconDisplay="input"
                        dateFormat="dd-M-yy"
                        :minDate="minRequestDate"
                    />
                    <div class="flex justify-end mt-4">
                        <Button @click="requestCompletion" label="Request" size="small"/>
                    </div>
                </form>
            </Dialog>

            <Tabs value="resources" class="mt-6"> 
                <TabList>
                    <Tab value="resources" class="font-inter text-sm">Resources</Tab>
                    <Tab value="tracking" class="font-inter text-sm">Project Tracking</Tab>
                    <Tab value="deadline" class="font-inter text-sm">Deadline Extenstions</Tab>
                    <Tab value="comments" class="font-inter text-sm flex items-center gap-x-2">Comments <Badge :value="project.comments.length" size="small"/></Tab>
                </TabList>
                <TabPanels>
                    <TabPanel value="resources">
                        <ProjectAbout :project="project"/>
                    </TabPanel>
                    <TabPanel value="tracking">
                        <ProjectTracking :project="project" :trackingStages="trackingStages" type="expected"/>
                        <ProjectTracking :project="project" :trackingStages="trackingStages" type="actual"/>
                        <div class="min-h-20"></div>
                    </TabPanel>
                    <TabPanel value="deadline">
                        <ProjectDeadline/>
                    </TabPanel>
                    <TabPanel value="comments">
                        <ProjectComments :project="project"/>
                    </TabPanel>
                </TabPanels>
            </Tabs>
        </div>
    </AppLayout>
</template>