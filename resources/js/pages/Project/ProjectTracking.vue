<script setup>
import { ref, computed, watch } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { Button } from 'primevue';
import NumberInput from '@/components/NumberInput.vue';
import Dialog from 'primevue/dialog'
import { route } from 'ziggy-js'
import InputText from 'primevue/inputtext';
import ScrollPanel from 'primevue/scrollpanel';

const props = defineProps({
    project: Object,
    trackingStages: Array,
    type: { type: String, required: true }
})

const authUser = usePage().props.auth.user

const isReviewer = computed(() =>
  props.project.reviewers.some(r => r.user_id === authUser.employee.id)
)

const isAssignee = computed(() =>
  props.project.assignees.some(r => r.user_id === authUser.employee.id)
)

const enabled = props.type === 'expected' ? isReviewer : (props.type === 'actual' ? isAssignee : false);

const startDate = new Date(props.project.start_date)
const endDate = new Date(props.project.deadline)

const dateRange = computed(() => {
    const arr = []
    const current = new Date(startDate)
    while (current <= endDate) {
        arr.push(new Date(current))
        current.setDate(current.getDate() + 1)
    }
    return arr
})

const stages = ref(
    props.trackingStages.map(stage => ({
        ...stage,
        entriesMap: Object.fromEntries(
        (props.type === 'expected' ? stage.expected_entries : stage.actual_entries)
            .map(e => [e.date, parseFloat(e.value)])
        ),
    }))
)

const columnTotals = computed(() => {
  const totals = {}
  for (const date of dateRange.value) {
    const dateStr = date.toLocaleDateString('en-CA')
    totals[dateStr] = stages.value.reduce(
      (sum, s) => sum + (parseFloat(s.entriesMap[dateStr]) || 0),
      0
    )
  }
  return totals
})

const addStageDialog = ref(false)
const dayExceedDialog = ref(false)
const newStageName = ref('')
const exceedErrorMessage = ref('')
// Add stage handler
const addStage = () => {
    router.post(route('project.tracking.stage.store', { project: props.project.id }), {
        name: newStageName.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
        newStageName.value = ''
        addStageDialog.value = false
        },
    })
}

// ✅ Delete stage
const removeStage = (stageId) => {
    router.delete(route('project.tracking.stage.destroy', { stage: stageId }), {
        preserveScroll: true,
    })
}

const moveStage = (stageId, direction) => {
    const routeName = direction === 'up'
        ? 'project.tracking.stage.moveUp'
        : 'project.tracking.stage.moveDown'

    router.put(route(routeName, { stage: stageId }), {}, {
        preserveScroll: true,
        onSuccess: () => router.reload({ only: ['trackingStages'] }),
    })
}

// ✅ Helpers
const formatDateHeader = (d) =>
    d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short' })

watch(
    () => props.trackingStages,
    (newVal) => {
        stages.value = newVal.map(stage => ({
        ...stage,
        entriesMap: Object.fromEntries(
            (
            props.type === 'expected'
                ? stage.expected_entries
                : stage.actual_entries
            ).map(e => [e.date, parseFloat(e.value)])
        ),
        }))
    },
    { deep: true }
)

const saveAllStages = () => {
    // 1️⃣ Recalculate totals per column (date)
    const totals = {};
    for (const date of dateRange.value) {
        const dateStr = date.toLocaleDateString('en-CA');
        totals[dateStr] = stages.value.reduce(
        (sum, s) => sum + (parseFloat(s.entriesMap[dateStr]) || 0),
        0
        );
    }

    // 2️⃣ Find any dates exceeding 1.0
    const invalidDates = Object.entries(totals)
        .filter(([_, total]) => total > 1)
        .map(([date]) => date);

    if (invalidDates.length > 0) {
        const formatted = invalidDates
            .map(d => {
            const [year, month, day] = d.split('-');
            const localDate = new Date(year, month - 1, day); // Local timezone-safe
            return localDate.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
            });
            })
            .join(', ');

        exceedErrorMessage.value = `The total workday for these dates exceeds 1.0: ${formatted}`;
        dayExceedDialog.value = true;
        return;
    }

    // 3️⃣ Prepare payload
    const payload = stages.value.map(stage => ({
        stage_id: stage.id,
        entries: Object.entries(stage.entriesMap)
            .filter(([_, value]) => parseFloat(value) > 0) // ❌ exclude zero values
            .map(([date, value]) => ({
            date,
            value: parseFloat(value),
            })),
    }));

    // 4️⃣ Submit to backend
    router.put(
        route('project.tracking.entry.update'),
        { stages: payload, type: props.type },
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
};


</script>

<template>
    <div class="flex justify-between mb-4">
        <div class="label">{{ type === 'expected' ? 'Expected' : 'Actual' }} project tracking</div>
        <Button
            v-if="type === 'expected' && isReviewer"
            label="Add Stage"
            icon="pi pi-plus"
            size="small"
            @click="addStageDialog = true"
            class="rounded-sm! px-4! text-xs! uppercase  py-2!"
        />
    </div>
   
    <div class="mb-6">
        <ScrollPanel class="overflow-x-auto">
        
            <table class="w-full text-sm border-separate border-spacing-y-0 mt-4">
                <tbody>
                    <tr class="my-2 text-xs">
                        <td class="w-10"></td>
                        <td class="w-40 font-semibold pe-6">Stages</td>
                        <td
                            v-for="date in dateRange"
                            :key="date"
                            class="bg-primary-900 min-w-10 text-white px-1 py-1.5 text-center border-2 border-primary-900"
                            :class="[date === dateRange[0] ? 'rounded-tl-sm' : '', date === dateRange[dateRange.length-1] ? 'rounded-tr-sm' : '']"
                        >{{ formatDateHeader(date) }}</td>
                        <td class="text-end min-w-10 font-semibold">Total</td>
                    </tr>
                    <tr v-for="stage in stages" :key="stage.id">
                        <td v-if="enabled && isReviewer" class="flex justify-between items-center gap-x-2 pe-2 w-14">
                            <button @click="removeStage(stage.id)" class="cursor-pointer border border-primary-500 hover:border-primary-900 text-primary-500 hover:text-primary-900 transition w-6 h-6 pe-0.5 flex items-center justify-center  rounded-sm">
                                <span class="material-icons-round" style="font-size: small;">clear</span>
                            </button>
                            <span class="flex flex-col">
                                <button class="cursor-pointer text-primary-500 hover:text-primary-900 transition" @click="moveStage(stage.id, 'up')"><span class="material-icons-round" style="font-size: small;">keyboard_arrow_up</span></button>
                                <button class="cursor-pointer text-primary-500 hover:text-primary-900 transition" @click="moveStage(stage.id, 'down')"><span class="material-icons-round" style="font-size: small;">keyboard_arrow_down</span></button>
                            </span>
                        </td>
                        <td v-else class="w-14"></td>
                        <td class="text-xs py-2 pe-6">{{ stage.name }}</td>
                        <td v-for="(date, index) in dateRange" :key="date" :class="[date === dateRange[0] ? 'border-s border-s-primary-900' : '', date === dateRange[dateRange.length-1] ? 'border-e border-e-primary-900' : '', index % 2 === 0 ? 'bg-primary-50' : '']">
                            <NumberInput v-model="stage.entriesMap[date.toLocaleDateString('en-CA')]" :max="1" convertFraction :disabled="!enabled"/>
                        </td>
                        <td class="text-end font-semibold">
                            {{
                                Object.values(stage.entriesMap).reduce((a, b) => a + (parseFloat(b) || 0), 0).toFixed(2)
                            }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td v-for="date in dateRange" :key="date"
                            class="bg-primary-300 min-w-10 text-primary-900 px-1 py-1 text-center border-2 border-primary-300 font-semibold"
                            :class="[date === dateRange[0] ? 'rounded-bl-sm' : '', date === dateRange[dateRange.length-1] ? 'rounded-br-sm' : '']"
                        >
                            {{ columnTotals[date.toLocaleDateString('en-CA')]?.toFixed(2) || '0.00' }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </ScrollPanel>
        <div class="flex justify-end">
            <Button
                v-if="enabled"
                label="Save Tracking"
                icon="pi pi-save"
                class="rounded-sm! px-4! text-xs! uppercase my-4 py-2!"
                @click="saveAllStages"
                size="small"
            />
        </div>

        <!-- Add Stage Dialog -->
        <Dialog v-model:visible="addStageDialog" modal header="Add New Stage" :style="{ width: '25rem' }">
            <div class="flex items-center justify-between font-inter!">
                <label class="text-sm font-medium text-gray-700">Stage Name</label>
                <InputText v-model="newStageName" type="text" placeholder="Enter stage name" size="small" class="font-medium"/>
            </div>
            <template #footer>
                <Button label="Add" size="small" @click="addStage" class="px-4! rounded-sm!" />
            </template>
        </Dialog>

        <!-- Error day exceed dialog -->
        <Dialog v-model:visible="dayExceedDialog" modal header="Could not save entry" :style="{ width: '25rem' }">
            <div class="flex items-center justify-between font-medium text-sm">
                {{ exceedErrorMessage }}
            </div>
            <template #footer>
                <Button label="Close" size="small" />
            </template>
        </Dialog>
    </div>
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