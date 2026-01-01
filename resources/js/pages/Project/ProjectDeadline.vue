<script setup>
import { Button } from 'primevue';
import Dialog from 'primevue/dialog';
import { ref, computed } from 'vue';
import { usePage, useForm, Link, router } from '@inertiajs/vue3';
import DatePicker from 'primevue/datepicker';
import Textarea from 'primevue/textarea';
import { route } from 'ziggy-js';
import Timeline from 'primevue/timeline';

const page = usePage();
const auth_user = page.props.auth.user;
const project = computed(() => page.props.project);

const isTrainerOrAdmin = computed(() => {
  const role = auth_user.employee?.role
  return ['trainer', 'admin'].includes(role)
})

const isReviewer = computed(() => {
  return project.value.reviewers?.some(r => r.id === auth_user.employee?.id)
})

const extensionRequestForm = useForm({
    new_deadline: null,
    reason: '',
});

const requestDialogVisible = ref(false);

const extensions = computed(() => {
    const base = project.value.deadline_extensions ?? [];

    const newItem = {
        id: 0,
        project_id: 0,
        requested_by: null,
        approved_by: null,
        status: "",
        reason: "",
        new_deadline: project.initial_deadline,
        created_at: project.created_at,
        updated_at: null,
        requester: null,
        approver: null,
    };

    // Return a new array with the new item first
    return [newItem, ...base].reverse();
});

const traineePendingRequest = computed(() =>
    extensions.value.find(
        ext => ext.status === 'pending'
    )
);

const hasPendingRequest = computed(() => !!traineePendingRequest.value);

const submitExtensionRequest = () => {
    extensionRequestForm.post(route('project.extension.store', { project: project.value.id }), {
        preserveScroll: true,
        replace: true, // ✅ ensures fresh data replaces old one
        onSuccess: () => {
            requestDialogVisible.value = false;
            extensionRequestForm.reset();
        },
    });
};

const formatDate = (dateString) => {
    if (!dateString) return '—';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const reasonExpanded = ref(false);

const today = new Date();

const minExtensionDate = computed(() => {
    if (!project.value?.deadline) return today;

    const deadline = new Date(project.value.deadline);

    // Return whichever is later
    return deadline > today ? deadline : today;
});

const reasonDialogVisible = ref(false);
const selectedReason = ref('');

const showReason = (reason) => {
    selectedReason.value = reason;
    reasonDialogVisible.value = true;
};



</script>


<template>
    <div class="label">Deadline Extension Request</div>
    
    <div v-if="traineePendingRequest">
        <div class="mt-2 p-2 rounded-sm border border-primary-300">
            <div class="flex items-center gap-x-2">
                <span class="material-icons-round text-primary-700">hourglass_empty</span>
                <div class="flex flex-col">
                    <span class="text-[10px] uppercase text-primary-500">Pending Deadline Request</span>
                    <span class="text-sm font-semibold">
                        New Deadline:
                        {{ formatDate(traineePendingRequest?.new_deadline) }}
                    </span>
                </div>

                <div class="grow flex flex-col ms-4">
                    <span class="text-[10px] uppercase text-primary-500">Requested By</span>
                    <span class="text-sm font-semibold">
                        {{ traineePendingRequest.requester.name }}
                    </span>
                </div>

                <Link
                    v-if="traineePendingRequest.requester.id === auth_user.id"
                    :href="route('project.extension.delete', { extension: traineePendingRequest.id })"
                    as="button"
                    method="delete"
                    class="bg-red-200 border border-transparent hover:border-red-600 text-red-600 px-4 py-2 text-xs rounded-sm transition"
                >
                    Cancel Request
                </Link>

                <Link
                    v-if="isReviewer"
                    :href="route('project.extension.approve', { extension: traineePendingRequest.id })"
                    as="button"
                    method="put"
                    class="bg-green-200 border border-transparent hover:border-green-600 text-green-600 px-4 py-2 text-xs rounded-sm transition font-semibold"
                >
                    Approve Request
                </Link>

                <Link
                    v-if="isReviewer"
                    :href="route('project.extension.decline', { extension: traineePendingRequest.id })"
                    as="button"
                    method="put"
                    class="bg-red-200 border border-transparent hover:border-red-600 text-red-600 px-4 py-2 text-xs rounded-sm transition font-semibold"
                >
                    Decline Request
                </Link>
            </div>
            <div class="ms-8">
                <button class="text-xs text-primary-500 hover:text-primary-900 transition flex items-center" @click="reasonExpanded = !reasonExpanded">
                    <span>{{ reasonExpanded ? 'Hide reason' : 'View reason'}}</span>
                    <span class="material-icons-round">{{ reasonExpanded ? 'expand_less' : 'expand_more'}}</span>
                </button>

                <p class="text-sm overflow-hidden transition-all" :class="reasonExpanded ? '' : 'h-0'">
                    {{ traineePendingRequest.reason }}
                </p>
            </div>
        </div>
    </div>
    <div v-else>
        <!-- For Trainer or Admin -->
        <div v-if="isTrainerOrAdmin" class="text-primary-500 text-sm mt-2 px-2 py-1 border rounded-sm flex items-center gap-x-2">
            <span class="material-icons-outlined" style="font-size: medium;">info</span>
            <span>No prending deadline extension requests</span>
        </div>

        <!-- For Assignee -->
        <div v-else class="mt-2">
            <div v-if="project.completion_date" class="text-primary-500 text-sm mt-2 px-2 py-1 border rounded-sm flex items-center gap-x-2">
                <span class="material-icons-outlined" style="font-size: medium;">info</span>
                <span>Project already completed</span>
            </div>
            <Button v-else label="Request Extension" size="small" @click="requestDialogVisible = true" class="px-4! rounded-sm! text-sm! uppercase"/>

            <Dialog v-model:visible="requestDialogVisible" modal header="Request Deadline Extension" :style="{ width: '35rem' }">
                <form @submit.prevent="submitExtensionRequest" class="font-inter!">
                    <div class="flex text-sm items-center justify-between mb-4">
                        <div class="font-medium">Current Deadline</div>
                        <div class="">{{ formatDate(project.deadline) }}</div>
                    </div>
                    <div class="flex items-center gap-x-6">
                        <div class="text-sm font-medium grow">New deadline</div>
                        <DatePicker
                            v-model="extensionRequestForm.new_deadline"
                            showIcon
                            fluid
                            iconDisplay="input"
                            dateFormat="dd-M-yy"
                            :minDate="minExtensionDate"
                            size="small"
                        />
                    </div>
                    <div class="mt-2">
                        <div class="text-sm font-medium grow">Reason for extension</div>
                        <Textarea
                            v-model="extensionRequestForm.reason"
                            rows="5"
                            fluid
                            class="mt-2 text-sm font-medium"
                            placeholder="Keep it brief"
                        />
                    </div>
                    <div class="flex justify-end mt-4">
                        <Button type="submit" label="Request" size="small" class="px-4! uppercase text-sm! rounded-sm! font-inter"/>
                    </div>
                </form>
            </Dialog>
        </div>
    </div>

    <div class="label mt-4">Deadline Request History</div>
    <div class="m-6">
        <Timeline :value="extensions">
            <template #content="slotProps">
                <div v-if="slotProps.item.requester" class="mb-4">
                    <div class="text-xs t ext-primary-4500">{{ slotProps.item.updated_at == null || slotProps.item.updated_at > slotProps.item.created_at ? formatDate(slotProps.item.updated_at) : formatDate(slotProps.item.created_at) }}</div>
                    <span  :class="slotProps.item.status === 'pending' ? 'text-primary-600 bg-primary-200' : slotProps.item.status === 'approved' ? 'text-green-600 bg-green-200' : 'text-red-600 bg-red-200'" class="text-xs px-1.5 py-0.5 rounded-sm border capitalize">
                        {{ slotProps.item.status }}
                    </span>
                    <div class="text-sm font-semibold mt-1">{{ slotProps.item.approver ? 'Extended' : 'Requested' }} Deadline: {{ formatDate(slotProps.item.new_deadline) }}</div>
                    <div class="text-sm" >Requested By: {{ slotProps.item.requester.name }}</div>
                    <div class="text-sm" v-if="slotProps.item.approver">{{ slotProps.item.status === 'approved' ? 'Approved' : 'Declined'}} By: {{ slotProps.item.approver.name }}</div>
                    <button  class="text-sm text-primary-500 hover:text-primary-900 transition font-medium" @click="showReason(slotProps.item.reason)">See request reason</button>
                </div>
                <div v-else class="text-sm font-semibold -mt-0.5">
                    Initial Deadline: {{ formatDate(project.initial_deadline) }}
                </div>
            </template>
        </Timeline>
    </div>

    <Dialog
        v-model:visible="reasonDialogVisible"
        modal
        header="Extension Request Reason"
        :style="{ width: '30rem' }"
    >
        <div class="text-sm text-gray-700 whitespace-pre-line">
            {{ selectedReason }}
        </div>

        <template #footer>
            <Button label="Close" size="small" @click="reasonDialogVisible = false" />
        </template>
    </Dialog>
</template>
