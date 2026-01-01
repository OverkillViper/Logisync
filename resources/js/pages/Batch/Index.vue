<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';
import { Button, Dialog, InputText } from 'primevue';
import { useForm } from '@inertiajs/vue3';
import BatchCard from './BatchCard.vue';

const createBatchForm = useForm({
    name: '',
});

const visible = ref(false);

const createBatch = () => {
  createBatchForm.post(route('batch.store'), {
    onSuccess: () => {
      visible.value = false
      createBatchForm.reset()
    }
  })
}

const props = defineProps({
    batches: Array
});

</script>

<template>
    <AppLayout title="Batches" summary="Manage training batches">
        <!-- Create batch button -->
        <template v-slot:titleField>
            
            <Button label="New Batch" @click="visible = true" class="rounded-sm! px-6! uppercase font-inter!" size="small" icon="pi pi-plus"/>
        </template>

        <!-- Create batch form dialog -->
        <Dialog v-model:visible="visible" modal header="Create a new batch" :style="{ width: '30rem' }">
            <form @submit.prevent="createBatch">
                <div class="flex flex-col mb-4">
                    <label for="batchName" class="font-medium w-24 text-sm mb-1">Batch name</label>
                    <InputText v-model="createBatchForm.name" autofocus id="batchName" class="flex-auto rounded-sm!" size="small" autocomplete="off" placeholder="Enter a name for the new batch"/>
                </div>
                <div class="flex justify-end gap-2">
                    <Button type="button" class="rounded-sm! text-sm! px-4!" label="Cancel" severity="secondary" @click="visible = false"></Button>
                    <Button type="submit" class="rounded-sm! text-sm! px-4!" label="Save" @click="visible = false"></Button>
                </div>
            </form>
        </Dialog>

        <!-- Batches list -->
        <div class="grid grid-cols-2 gap-6 xl:w-4/5 2xl:w-3/4 mx-auto">
            <BatchCard v-for="batch in batches" :key="batch.id" :batch="batch"/>
        </div>
    </AppLayout>
</template>