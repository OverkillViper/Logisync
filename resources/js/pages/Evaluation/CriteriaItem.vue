<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import RadioButton from 'primevue/radiobutton';

const editing = ref(false);

const props = defineProps({
    criteria: Object,
    index: Number,
});

const editCriteriaForm = useForm({
    name: props.criteria.name,
    type: props.criteria.type,
})

const editCriteria = () => {
    editing.value = true;
}

const deleteCriteria = () => {
    router.delete(route('evaluation.criteria.destroy', {criteria: props.criteria.id}), {
        preserveScroll: true,
    });
}

const cancelEditing = () => {
    editing.value = false;
}

const saveCriteria = () => {
    editCriteriaForm.put(route('evaluation.criteria.update', {criteria: props.criteria.id}), {
        preserveScroll: true,
        onSuccess: () => {
            editing.value = false;
        }
    });
}
</script>

<template>
    <tr :class="index % 2 ? 'bg-white' : 'bg-primary-50'">
        <td class="py-4 min-w-20 flex gap-x-2 px-4">
            <button v-if="!editing" @click="editCriteria" class="flex justify-center items-center w-8 h-8 rounded-md border border-primary-200 hover:border-primary-500 transition">
                <span class="material-icons-round" style="font-size: medium;">edit</span>
            </button>
            <button v-if="!editing" @click="deleteCriteria" class="flex justify-center items-center w-8 h-8 rounded-md border border-primary-200 hover:border-primary-500 transition">
                <span class="material-icons-outlined" style="font-size: medium;">delete</span>
            </button>
        </td>
        <td v-if="editing" class="py-2 text-primary-800">
            <InputText type="text" v-model="editCriteriaForm.name"size="small" placeholder="Criteria name"/>
        </td>
        <td v-else class="py-4 text-primary-800">{{ criteria.name }}</td>
        <td v-if="editing" class="py-2 text-primary-800 capitalize">
            <div class="flex items-center gap-x-2">
                <RadioButton v-model="editCriteriaForm.type" inputId="technical" value="technical" size="small"/>
                <label for="technical">Technical</label>
                <RadioButton v-model="editCriteriaForm.type" inputId="non-technical" value="non-technical" size="small"/>
                <label for="non-technical">Non-technical</label>
            </div>
        </td>
        <td v-else class="py-4 text-primary-800 capitalize">{{ criteria.type }}</td>
        <td class="py-4 flex gap-x-2 items-center px-4">
            <button v-if="editing" @click="cancelEditing" class="cursor-pointer flex justify-center items-center px-3 h-9 gap-x-2 rounded-md border border-primary-200 hover:border-primary-500 transition">
                <span class="material-icons-round" style="font-size: medium;">close</span>
                <span>Cancel</span>
            </button>
            <button v-if="editing" @click="saveCriteria" class="cursor-pointer flex justify-center items-center px-3 h-9 gap-x-2 rounded-md border border-primary-200 hover:border-primary-500 transition">
                <span class="material-icons-outlined" style="font-size: medium;">check</span>
                <span>Save</span>
            </button>
        </td>
    </tr>
</template>