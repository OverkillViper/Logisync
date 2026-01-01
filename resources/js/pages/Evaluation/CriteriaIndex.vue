<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import InputText from 'primevue/inputtext';
import { useForm } from '@inertiajs/vue3';
import RadioButton from 'primevue/radiobutton';
import { Button } from 'primevue';
import CriteriaItem from './CriteriaItem.vue';

const createCriteriaForm = useForm({
    name: '',
    type: null
});

defineProps({
    criterias: Array
})

const createCriteria = () => {
    createCriteriaForm.post(route('evaluation.criteria.store'), {
        preserveScroll: true,
        onSuccess: () => {
            createCriteriaForm.reset();
        }
    });
}

</script>

<template>
    <AppLayout title="Evaluation Criterias" summary="Manage evaluation criterias">
        <div class="xl:w-4/5 2xl:w-3/4 mx-auto">
            <form @submit.prevent class="grid grid-cols-3 gap-x-4 gap-y-2">
                <div class="text-sm text-primary-500 font-medium">Criteria Name</div>
                <div class="text-sm text-primary-500 font-medium">Criteria Type</div>
                <div></div>
                <InputText type="text" v-model="createCriteriaForm.name"size="small" placeholder="Enter the name of the criteria"/>
                <div class="flex items-center gap-x-2">
                    <RadioButton v-model="createCriteriaForm.type" inputId="technical" value="technical" size="small"/>
                    <label for="technical">Technical</label>
                    <RadioButton v-model="createCriteriaForm.type" inputId="non-technical" value="non-technical" size="small"/>
                    <label for="non-technical">Non-technical</label>
                </div>
                <div>
                    <Button type="submit" @click="createCriteria" label="Create Criteria" size="small" class="w-40" icon="pi pi-plus"/>
                </div>
            </form>

            <div class="mt-10">
                <table v-if="criterias.length" class="w-1/2 text-sm">
                    <thead>
                        <tr>
                            <td class="min-w-26"></td>
                            <td class="min-w-60 text-primary-600 font-medium py-4">Criteria Name</td>
                            <td class="min-w-60 text-primary-600 font-medium py-4">Criteria Type</td>
                            <td class="min-w-60"></td>
                        </tr>
                    </thead>
                    <tbody>
                        <CriteriaItem v-for="(criteria, index) in criterias" :key="criteria.id" :criteria="criteria" :index="index"/>
                    </tbody>
                </table>
                <div v-else class="text-sm text-primary-500 font-medium">No evaluation criteria found</div>
            </div>
        </div>
    </AppLayout>
</template>