<script setup>
import Textarea from 'primevue/textarea';
import EmployeeAvatar from '../Employee/EmployeeAvatar.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { Button } from 'primevue';
import ProjectCommentCard from './ProjectCommentCard.vue';
import { route } from 'ziggy-js'

const props = defineProps({
    project: Object,
})

const authUser = usePage().props.auth.user

const createCommentForm = useForm({
    comment: '',
})

const createComment = () => {
    createCommentForm.post(route('project.comment.store', {project: props.project.id}), {
        preserveScroll: true,
        onSuccess: () => {
            createCommentForm.reset()
        }
    })
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center gap-x-4">
            <h3 class="text-[10px] font-bold uppercase text-neutral-600 whitespace-nowrap grow">Project Discussions</h3>
            <div class="flex items-center gap-x-2 bg-neutral-50 px-3 py-1 rounded-full border border-neutral-200">
                <span class="text-[10px] font-mono font-bold text-neutral-900">{{ props.project.comments.length.toString().padStart(2, '0') }}</span>
                <span class="text-[9px] font-bold uppercase tracking-widest text-neutral-400">Entries</span>
            </div>
        </div>

        <div class="border border-neutral-200 rounded-sm p-2 focus-within:border-neutral-900 transition-all duration-300">
            <div class="flex gap-x-2">
                <div class="shrink-0">
                    <EmployeeAvatar 
                        :image="authUser.employee.profile_picture" 
                        :name="authUser.name" 
                        class="h-10 w-10 rounded-sm object-cover grayscale border border-neutral-100"
                    />
                </div>
                <div class="grow flex flex-col">
                    <Textarea 
                        v-model="createCommentForm.comment" 
                        autoResize 
                        rows="3" 
                        class="w-full border-none! shadow-none! bg-transparent! text-sm! placeholder:text-neutral-300 focus:ring-0! p-0! pt-2!" 
                        placeholder="Type your comment"
                    />
                    
                    <div class="flex justify-between items-center mt-4 pt-4 border-t border-neutral-50">
                        <span class="text-xs text-neutral-500 font-mono">Shift + Enter for new line</span>
                        <Button 
                            label="Comment" 
                            icon="pi pi-send"
                            size="small"
                            @click="createComment"
                            class="rounded-sm! px-4!"
                            :loading="createCommentForm.processing"
                            :disabled="!createCommentForm.comment.trim()"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-y-8 mt-6">
            <ProjectCommentCard 
                v-for="comment in props.project.comments" 
                :key="comment.id" 
                :comment="comment"
                class="hover:translate-x-1 transition-transform duration-300"
            />
        </div>

        <div v-if="props.project.comments.length === 0" class="flex flex-col items-center justify-center py-16 opacity-40">
            <i class="pi pi-comments text-3xl mb-3"></i>
            <p class="text-[10px] uppercase tracking-[0.3em] font-bold">Terminal Silent: No active discussion</p>
        </div>
    </div>
</template>

<style scoped>
/* Ensure the PrimeVue Textarea doesn't fight the minimalist look */
:deep(.p-textarea) {
    font-family: 'Inter', sans-serif;
    line-height: 1.6;
}
</style>