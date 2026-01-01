<script setup>
import EmployeeAvatar from '../Employee/EmployeeAvatar.vue';
import { ref } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import Textarea from 'primevue/textarea';
import { route } from 'ziggy-js'
import { Button } from 'primevue';

const props = defineProps({
    comment: Object
})

const editing = ref(false);

const auth_user = usePage().props.auth.user;

function formatLatestDate(dateStr1, dateStr2) {
    const date1 = new Date(dateStr1);
    const date2 = new Date(dateStr2);

    // Pick the later date
    const latest = date1 > date2 ? date1 : date2;

    // Format it
    return latest.toLocaleString('en-GB', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
        timeZone: 'Asia/Dhaka',
    }).replace(',', ' -');
}

const editCommentForm = useForm({
    comment: props.comment.comment
})

const saveComment = () => {
    editCommentForm.put(route('project.comment.update', {comment: props.comment.id}), {
        preserveScroll: true,
        onSuccess: () => {
            editing.value = false;
        }
    })
}

const deleteComment = () => {
    router.delete(route('project.comment.delete', {comment: props.comment.id}), {
        preserveScroll: true,
    })
}

</script>

<template>
    <div class="flex gap-x-4">
        <EmployeeAvatar :image="props.comment.user.employee.profile_picture" :name="props.comment.user.name" class="h-8 w-8 rounded-sm text-xs! bg-primary-950"/>
        <div class="flex flex-col text-xs font-medium w-full">
            <div class="flex items-center gap-x-2">
                <span class="font-semibold text-primary-800">{{ props.comment.user.name }}</span>
            </div>
            <div class="flex gap-x-2 items-center">
                <span class="label">{{ formatLatestDate(props.comment.created_at, props.comment.updated_at) }}</span>
                <div class="flex gap-x-2" v-if="auth_user.id == props.comment.user.id && !editing">
                    <span>&#183;</span>
                    <button class="label hover:text-primary-900! cursor-pointer" @click="editing = true">Edit</button>
                    <span>&#183;</span>
                    <button class="label hover:text-primary-900! cursor-pointer" @click="deleteComment">Delete</button>
                </div>
            </div>
            
            <div v-if="editing" class="mt-2">
                <Textarea v-model="editCommentForm.comment" autoResize rows="3" class="w-full text-sm!" placeholder="Add a comment"/>
                <div class="flex mt-2 gap-x-2 justify-end">
                    <Button class="px-4! rounded-sm!" label="Cancel" icon="pi pi-times" size="small" @click="editing = false"/>
                    <Button class="px-4! rounded-sm!" label="Save" icon="pi pi-check" size="small" @click="saveComment"/>
                </div>
            </div>
            <p v-else class="bg-primary-50 min-w-1/3 max-w-2/3 text-sm px-3 py-2 rounded-lg mt-2 text-shadow-primary-950! border border-primary-100">
                {{ props.comment.comment }}
            </p>
        </div>
    </div>
</template>