<script setup>
import { Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useConfirm } from "primevue/useconfirm"; // [NEW]
import ConfirmPopup from 'primevue/confirmpopup'; // [NEW]

const confirm = useConfirm(); // [NEW]
const editingBatch = ref(false);
const props = defineProps({
    batch: Object
})

const toggleEdit = () => {
    editingBatch.value = !editingBatch.value
}

const editBatchForm = useForm({
    name: props.batch.name,
})

const editBatch = () => {
    editBatchForm.put(route('batch.update', { batch: props.batch.id }), {
        onSuccess: () => editingBatch.value = false
    })
}

// [NEW] PrimeVue Confirmation Logic
const deleteBatch = (event) => {
    confirm.require({
        target: event.currentTarget,
        message: 'Are you sure you want to delete this batch?',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
        },
        acceptProps: {
            label: 'Delete',

        },
        accept: () => {
            router.delete(route('batch.destroy', { batch: props.batch.id }), {
                preserveScroll: true,
                onSuccess: () => {
                    // List updates automatically via Inertia
                }
            });
        }
    });
}
</script>

<template>
    <ConfirmPopup />

    <div class="group bg-white border border-neutral-200 p-4 rounded-sm flex flex-col hover:border-neutral-900 transition-all duration-300 cursor-pointer relative shadow-sm hover:shadow-xl hover:shadow-neutral-900/5">
        
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-x-4 grow">
                <div class="w-12 h-12 rounded-xl bg-neutral-900 flex items-center justify-center text-white group-hover:bg-primary-600 transition-colors duration-300 shadow-lg">
                    <span class="material-icons-round" style="font-size: 1.5rem;">workspaces</span>
                </div>
                
                <div class="grow">
                    <div class="text-[10px] font-mono text-neutral-400 uppercase tracking-[0.2em] mb-1" v-if="!editingBatch">Batch - {{ props.batch.id }}</div>
                    <form @submit.prevent="editBatch" v-if="editingBatch" class="flex items-center">
                        <input 
                            class="w-full text-base font-bold uppercase tracking-widest bg-neutral-50 border border-neutral-200 rounded-none px-3 py-1 focus:border-neutral-900 outline-none" 
                            v-model="editBatchForm.name"
                            autoFocus
                        />
                    </form>
                    <Link 
                        :href="route('batch.show', { batch: props.batch.id })" 
                        v-else 
                        class="text-lg font-bold font-work-sans text-neutral-900 uppercase tracking-widest leading-none block"
                    >
                        {{ props.batch.name }}
                    </Link>
                </div>
            </div>

            <div class="flex items-center gap-x-1 ms-4">
                <div v-if="!editingBatch" class="flex items-center gap-x-2">
                    <button @click="toggleEdit" class="text-neutral-400 hover:text-neutral-900 transition-colors">
                        <span class="material-icons-outlined" style="font-size: 1.2rem;">edit</span>
                    </button>
                    <button @click="deleteBatch($event)" class="text-neutral-400 hover:text-red-600 transition-colors">
                        <span class="material-icons-outlined" style="font-size: 1.2rem;">delete_outline</span>
                    </button>
                </div>
                <div v-else class="flex items-center gap-x-2">
                    <button @click="editBatch" class="text-neutral-900 hover:text-primary-600">
                        <span class="material-icons-outlined" style="font-size: 1.2rem;">check</span>
                    </button>
                    <button @click="toggleEdit" class="text-neutral-400 hover:text-neutral-900">
                        <span class="material-icons-outlined" style="font-size: 1.2rem;">close</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-x-4 border-t border-neutral-100 pt-5">
            <div class="flex flex-col border-r border-neutral-100">
                <span class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 mb-1">Trainees</span>
                <div class="flex items-center text-neutral-900">
                    <span class="material-icons-round text-neutral-400" style="font-size: 1rem;">group</span>
                    <span class="text-sm text-primary-700 font-bold ms-2 tracking-tighter">{{ props.batch.trainees.length.toString().padStart(2, '0') }}</span>
                </div>
            </div>
            <div class="flex flex-col pl-4">
                <span class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 mb-1">Trainers</span>
                <div class="flex items-center text-neutral-900">
                    <span class="material-icons-round text-neutral-400" style="font-size: 1rem;">school</span>
                    <span class="text-sm text-primary-700 font-bold ms-2 tracking-tighter">{{ props.batch.trainers.length.toString().padStart(2, '0') }}</span>
                </div>
            </div>
            <div class="flex flex-col pl-4">
                <span class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 mb-1">Employees</span>
                <div class="flex items-center text-neutral-900">
                    <span class="material-icons-round text-neutral-400" style="font-size: 1rem;">school</span>
                    <span class="text-sm text-primary-700 font-bold ms-2 tracking-tighter">{{ props.batch.employees.length.toString().padStart(2, '0') }}</span>
                </div>
            </div>
        </div>

        <div class="absolute bottom-0 left-0 h-1 bg-neutral-900 transition-all duration-500 ease-in-out w-0 group-hover:w-full group-hover:bg-primary-600"></div>
    </div>
</template>

<style>
/* Styling the popup to match the vibe */
/* .p-confirm-popup {
    @apply bg-white border border-neutral-200 rounded-2xl p-4;
}
.p-confirm-popup-message {
    @apply text-xs font-bold uppercase tracking-tight text-neutral-600 ml-3;
}
.p-confirm-popup-icon {
    @apply text-red-500;
} */
</style>