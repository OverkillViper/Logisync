<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import Select from 'primevue/select';
import { Button, InputText } from 'primevue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import EmployeeAvatar from '../Employee/EmployeeAvatar.vue';
import DatePicker from 'primevue/datepicker';
import Dialog from 'primevue/dialog';
import axios from 'axios';
import { reactive, computed, ref } from 'vue';
import Textarea from 'primevue/textarea';
import { route } from 'ziggy-js';

const page = usePage();
const role = page.props.auth.user.employee.role;

const props = defineProps({
    batches: Array,
    criterias: Array,
});

const batchSelectForm = useForm({
    batch: null,
    evaluation: null,
});

const batch = ref(null);
const evaluations = ref([]);
const evaluationLoaded = ref(false);
const evaluationForm = useForm({
    evaluation_id: null,
    scores: reactive({}),
});
const focusedTraineeId = ref(null);
const loadingEvaluation = ref(false);

const currentEvaluation = ref(null);

const technicalCriterias = computed(() =>
    props.criterias.filter(c => c.type === 'technical')
);
const nonTechnicalCriterias = computed(() =>
    props.criterias.filter(c => c.type === 'non-technical')
);

const createEvaluationDialogVisible = ref(false);
const editEvaluationDialogVisible   = ref(false);

const createEvaluationForm = useForm({
    batch_id  : null,
    title     : ''  ,
    start_date: null,
    end_date  : null,
});

const editEvaluationForm = useForm({
    title           : ''  ,
    start_date      : null,
    end_date        : null,
});

const remarksForm = useForm({
    evaluation_id: null,
    remarks: {}
});

const formatDateToYMD = (dateObj) => {
    if (!dateObj) return null;
    const date  = new Date(dateObj);
    const year  = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day   = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const formatDateToDMY = (dateObj) => {
    if (!dateObj) return null;
    const date  = new Date(dateObj);
    const year  = date.getFullYear();
    const month = date.toLocaleString('en-GB', { month: 'short' });
    const day   = String(date.getDate()).padStart(2, '0');
    return `${day} ${month}, ${year}`;
};

// Load evaluations after selecting a batch
const loadEvaluationsForBatch = async () => {
    if (!batchSelectForm.batch) return;
    try {
        const { data } = await axios.get(route('evaluation.byBatch', batchSelectForm.batch));
        evaluations.value = data.evaluations;
        batch.value = data.batch;
        batchSelectForm.evaluation = null;
        evaluationLoaded.value = false;
    } catch (err) {
        console.error('Failed to load evaluations:', err);
    }
};

// Load selected evaluation details (criteria + trainee scores)
const loadEvaluationDetails = async () => {
    if (!batchSelectForm.batch || !batchSelectForm.evaluation) return;
    try {
        loadingEvaluation.value = true;
        const { data } = await axios.get(
            route('evaluation.details', {
                batch: batchSelectForm.batch,
                evaluation: batchSelectForm.evaluation,
            })
        );
        loadingEvaluation.value = false;

        batch.value = data.batch;
        evaluationForm.evaluation_id = batchSelectForm.evaluation;
        evaluationForm.scores = reactive(data.scores || {});

        remarksForm.remarks = reactive(data.remarks || {});
        remarksForm.evaluation_id = batchSelectForm.evaluation;

        currentEvaluation.value = data.evaluation;
        editEvaluationForm.title = data.evaluation.title;
        editEvaluationForm.start_date = data.evaluation.start_date
            ? new Date(data.evaluation.start_date)
            : null;
        editEvaluationForm.end_date = data.evaluation.end_date
            ? new Date(data.evaluation.end_date)
            : null;
        
        evaluationLoaded.value = true;
    } catch (err) {
        console.error('Failed to load evaluation details:', err);
    }
};

const handleScoreChange = (criteriaId, traineeId, value) => {
    if (!evaluationForm.scores[criteriaId]) {
        evaluationForm.scores[criteriaId] = {};
    }
    evaluationForm.scores[criteriaId][traineeId] = value;
};

const saveEvaluations = () => {
    evaluationForm.post(route('evaluation.score.store'), {
        preserveScroll: true
    });
};


// Create new evaluation inside a batch
const createEvaluation = () => {
    const payload = {
        ...createEvaluationForm.data(),
        batch_id: batchSelectForm.batch,
        start_date: formatDateToYMD(createEvaluationForm.start_date),
        end_date: formatDateToYMD(createEvaluationForm.end_date),
    };

    createEvaluationForm.transform(() => payload).post(route('evaluation.store'), {
        onSuccess: () => {
            createEvaluationForm.reset();
            createEvaluationDialogVisible.value = false;
            loadEvaluationsForBatch(); // refresh the dropdown
        },
    });
};

const updateEvaluation = () => {
    const payload = {
        ...editEvaluationForm.data(),
        start_date: formatDateToYMD(editEvaluationForm.start_date),
        end_date: formatDateToYMD(editEvaluationForm.end_date),
    };

    editEvaluationForm.transform(() => payload).put(route('evaluation.update', {evaluation: currentEvaluation.value.id}), {
        preserveScroll: true,
        onSuccess: () => {
            editEvaluationDialogVisible.value = false;
            loadEvaluationsForBatch();
        }
    })
}

const deleteEvaluation = (evaluationId) => {
    router.delete(route('evaluation.destroy', {evaluation: evaluationId}), {
        onSuccess: () => {
            loadEvaluationsForBatch();
        }
    });
}

const saveRemarks = () => {
    remarksForm.evaluation_id = batchSelectForm.evaluation;
    remarksForm.post(route('evaluation.remarks.store'), {
        preserveScroll: true,
        onSuccess: () => {
            remarksForm.defaults(remarksForm.data());
        },
    });
};

</script>

<template>
    <AppLayout title="Evaluation" summary="Manage trainee evaluation">
        <!-- Header -->
        <template #titleField>
            <div v-if="role === 'admin'">
                <Link :href="route('evaluation.criteria.index')">
                    <Button
                        label="Manage Evaluation Criterias"
                        size="small"
                        icon="pi pi-chart-bar"
                        outlined
                    />
                </Link>
            </div>
        </template>

        <!-- Select Batch & Evaluation -->
        <div class="xl:w-4/5 2xl:w-3/4 mx-auto mt-4">
            <div class="flex items-end gap-x-4">
                <!-- Batch Selector -->
                <div class="flex flex-col w-1/3 gap-y-1">
                    <div class="text-sm text-primary-500 font-medium">Select batch</div>
                    <Select
                        v-model="batchSelectForm.batch"
                        :options="props.batches"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Select a batch"
                        class="text-sm!"
                        size="small"
                        @change="loadEvaluationsForBatch"
                    >
                        <template #option="slotProps">
                            <div class="flex items-center text-sm">
                                <div>{{ slotProps.option.label }}</div>
                            </div>
                        </template>
                    </Select>
                </div>

                <!-- Evaluation Selector -->
                <div class="flex flex-col w-1/3 gap-y-1">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-primary-500 font-medium">Select evaluation</div>
                        <button
                            v-if="batchSelectForm.batch"
                            @click="createEvaluationDialogVisible = true"
                            class="flex items-center gap-x-1 text-primary-500 text-sm! hover:text-primary-800 transition"
                        >
                            <span class="material-icons-outlined text-sm!">add_circle_outline</span>
                            <span>Create new evaluation</span>
                        </button>
                    </div>
                    <Select
                        v-model="batchSelectForm.evaluation"
                        :options="evaluations"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Select evaluation"
                        :disabled="!batchSelectForm.batch"
                        class="text-sm!"
                        size="small"
                        @change="evaluationLoaded = false"
                    >
                    <template #option="slotProps">
                        <div class="text-sm">
                            {{ slotProps.option.label }}
                        </div>
                    </template>
                    </Select>
                </div>

                <div>
                    <Button
                        label="Load Evaluation"
                        icon="pi pi-sync"
                        size="small"
                        class="rounded-sm! w-50"
                        @click="loadEvaluationDetails"
                        :loading="loadingEvaluation"
                    />
                </div>
            </div>
        </div>

        <!-- Create Evaluation Dialog -->
        <Dialog
            v-model:visible="createEvaluationDialogVisible"
            modal
            header="Create Evaluation"
            :style="{ width: '30rem' }"
        >
            <form @submit.prevent class="flex flex-col font-inter!">
                <label for="title" class="label">Evaluation Title</label>
                <InputText
                    id="title"
                    v-model="createEvaluationForm.title"
                    class="grow mt-1"
                    autocomplete="off"
                    size="small"
                />

                <span class="label mt-4">Evaluation Period</span>
                <div class="grid grid-cols-2 mt-1 gap-x-2 gap-y-1">
                    <span class="label">From</span>
                    <span class="label">To</span>

                    <DatePicker
                        size="small"
                        v-model="createEvaluationForm.start_date"
                        showIcon
                        fluid
                        iconDisplay="input"
                        showButtonBar
                        dateFormat="dd-M-yy"
                    />
                    <DatePicker
                        size="small"
                        v-model="createEvaluationForm.end_date"
                        showIcon
                        fluid
                        iconDisplay="input"
                        showButtonBar
                        dateFormat="dd-M-yy"
                    />
                </div>

                <div class="flex justify-end mt-4">
                    <Button
                        type="submit"
                        size="small"
                        label="Create Evaluation"
                        @click="createEvaluation"
                        class="px-4 rounded-sm!"
                    />
                </div>
            </form>
        </Dialog>

        <!-- Evaluation Table -->
        <div v-if="evaluationLoaded" class="mt-6 flex flex-col xl:w-4/5 2xl:w-3/4 mx-auto">
            <hr class="mb-6 border-primary-200">
            <div class="flex items-end gap-x-10">
                <div class="flex flex-col gap-y-1">
                    <span class="text-sm text-primary-500 font-medium">Now evaluating</span>
                    <span class="text-xl font-chakra font-bold grow">{{ batch.name }}</span>
                </div>
                <div class="flex flex-col gap-y-1">
                    <span class="text-sm text-primary-500 font-medium">Evaluation Title</span>
                    <span class="text-xl font-chakra font-bold grow">{{ currentEvaluation.title }}</span>
                </div>
                <div class="flex flex-col gap-y-1 grow">
                    <span class="text-sm text-primary-500 font-medium">Evaluation Period</span>
                    <span class="text-xl font-chakra font-bold grow">{{ formatDateToDMY(currentEvaluation.start_date) }} - {{ formatDateToDMY(currentEvaluation.end_date) }}</span>
                </div>
                <div class="flex gap-x-4">
                    <Button @click="editEvaluationDialogVisible = true" label="Edit Evaluation" icon="pi pi-pencil" size="small" class="px-4! rounded-sm!"/>
                    <Button @click="deleteEvaluation" label="Delete Evaluation" icon="pi pi-trash" size="small" class="px-4! rounded-sm!"/>

                    <Dialog
                        v-model:visible="editEvaluationDialogVisible"
                        modal
                        header="Edit Evaluation"
                        :style="{ width: '30rem' }"
                    >
                        <form @submit.prevent class="flex flex-col">
                            <label for="title" class="text-sm font-medium text-primary-500">Evaluation Title</label>
                            <InputText
                                id="title"
                                v-model="editEvaluationForm.title"
                                class="grow mt-1"
                                autocomplete="off"
                                size="small"
                            />

                            <span class="text-sm font-medium text-primary-500 mt-4">Evaluation Period</span>
                            <div class="grid grid-cols-2 mt-1 gap-x-2 gap-y-1">
                                <span class="text-sm font-medium text-primary-500">From</span>
                                <span class="text-sm font-medium text-primary-500">To</span>

                                <DatePicker
                                    size="small"
                                    v-model="editEvaluationForm.start_date"
                                    showIcon
                                    fluid
                                    iconDisplay="input"
                                    showButtonBar
                                    dateFormat="dd-M-yy"
                                />
                                <DatePicker
                                    size="small"
                                    v-model="editEvaluationForm.end_date"
                                    showIcon
                                    fluid
                                    iconDisplay="input"
                                    showButtonBar
                                    dateFormat="dd-M-yy"
                                />
                            </div>

                            <div class="flex justify-end mt-4">
                                <Button
                                    type="submit"
                                    size="small"
                                    label="Update Evaluation"
                                    @click="updateEvaluation"
                                />
                            </div>
                        </form>
                    </Dialog>
                </div>
            </div>

            <!-- Trainees -->
            <div class="text-sm text-primary-500 font-medium mt-4">Trainees</div>
            <div class="flex flex-wrap gap-2 mt-2">
                <Link
                    v-for="trainee in batch.trainees"
                    :key="trainee.id"
                    :href="route('employees.show', { employee: trainee.user.id })"
                    class="flex items-center border border-primary-100 rounded-xl p-1 hover:border-primary-500 transition"
                >
                    <EmployeeAvatar
                        :image="trainee.profile_picture"
                        :name="trainee.user.name"
                        class="h-8 w-8 rounded-lg text-xs! object-cover bg-primary-950"
                    />
                    <span class="text-primary-700 text-sm font-medium mx-3">
                        {{ trainee.user.name }}
                    </span>
                </Link>
            </div>

            <hr class="my-6 border-primary-200" />

            <!-- Criteria Tables -->
            <template v-for="(criteriaGroup, title) in {
                'Technical Evaluation': technicalCriterias,
                'Non-Technical Evaluation': nonTechnicalCriterias
            }" :key="title">
                <div class="text text-primary-900 font-semibold mb-2">{{ title }}</div>
                <table class="text-sm w-full border-collapse">
                    <thead>
                        <tr class="bg-primary-50">
                            <th class="py-2 px-2 text-left text-primary-500 w-1/4">
                                Evaluation Criteria
                            </th>
                            <th
                                v-for="trainee in batch.trainees"
                                :key="trainee.id"
                                class="text-center font-medium text-primary-700 border border-transparent"
                                :class="focusedTraineeId === trainee.id
                                    ? 'bg-primary-800 text-white border border-primary-800'
                                    : 'bg-primary-50 text-primary-700'"
                            >
                                {{ trainee.user.name }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(criteria, index) in criteriaGroup"
                            :key="criteria.id"
                            class="border-b hover:bg-primary-50/30 transition"
                        >
                            <td class="py-2 px-2">{{ criteria.name }}</td>
                            <td
                                v-for="trainee in batch.trainees"
                                :key="trainee.id"
                                class="text-center w-40 transition-all"
                                :class="focusedTraineeId === trainee.id ? index == criteriaGroup.length-1 ? 'border border-x-primary-900 border-b-primary-900' : 'border border-x-primary-900' : ''"
                            >
                                <input
                                    type="number"
                                    :min="0"
                                    :max="5"
                                    :value="evaluationForm.scores[criteria.id]?.[trainee.id] ?? ''"
                                    @input="handleScoreChange(criteria.id, trainee.id, Number($event.target.value))"
                                    @focus="focusedTraineeId = trainee.id"
                                    @blur="focusedTraineeId = null"
                                    class="w-full text-center py-1.5 focus:outline-none"
                                    placeholder="-"
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="flex justify-end mt-3">
                    <Button
                        label="Save Evaluations"
                        icon="pi pi-save"
                        size="small"
                        @click="saveEvaluations"
                        class="px-4! rounded-sm!"
                    />
                </div>
                <hr class="my-6 border-primary-200" />
            </template>

            <!-- Comments Table -->
            <div class="flex flex-col">
                <div class="text text-primary-900 font-semibold mb-2">Evaluation Remarks</div>

                <div
                    v-for="trainee in batch.trainees"
                    :key="trainee.id"
                    class="mb-4"
                >
                    <div class="text-sm font-medium text-primary-700 mb-2 flex items-center gap-x-2">
                        <span class="material-icons-round">person</span>
                        <span>{{ trainee.user.name }}</span>
                    </div>
                    
                    <Textarea v-model="remarksForm.remarks[trainee.id]" rows="5" class="w-full text-sm!" autoResize placeholder="Type your remarks"/>
                </div>

                <div class="flex justify-end mt-4">
                    <Button
                        label="Save Remarks"
                        icon="pi pi-save"
                        size="small"
                        @click="saveRemarks"
                        class="px-4! rounded-sm!"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
</style>