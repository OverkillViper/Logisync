<script setup>
import Checkbox from 'primevue/checkbox'
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Button } from 'primevue'

const props = defineProps({
    employee: Object,
    evaluations: Array,
})

// Selected evaluation IDs
const selectedEvaluations = ref([])

// Build a list of unique criterias from all evaluations
const allCriterias = computed(() => {
    const map = new Map()
    props.evaluations.forEach(evaluation => {
        evaluation.scores.forEach(score => {
        const c = score.criteria
        if (!map.has(c.id)) map.set(c.id, c)
        })
    })
    return Array.from(map.values())
})

// Group criterias by type
const groupedCriterias = computed(() => ({
    technical: allCriterias.value.filter(c => c.type === 'technical'),
    nonTechnical: allCriterias.value.filter(c => c.type === 'non-technical')
}))

// Create score lookup table: scoreMatrix[criteria_id][evaluation_id] = score
const scoreMatrix = computed(() => {
    const map = {}
    props.evaluations.forEach(evaluation => {
        evaluation.scores.forEach(score => {
        if (!map[score.criteria_id]) map[score.criteria_id] = {}
        map[score.criteria_id][evaluation.id] = score.score
        })
    })
    return map
})

// Only show evaluations that are selected
const visibleEvaluations = computed(() =>
    props.evaluations.filter(e => selectedEvaluations.value.includes(e.id))
)
</script>

<template>
    <div class="flex flex-col space-y-8">

        <!-- Evaluation Selection -->
        <div class="flex items-center justify-between" v-if="evaluations.length">
            <div>
                <div class="label">Show evaluations</div>
                <div class="flex flex-wrap items-center mt-2 gap-x-6 gap-y-2">
                    <div
                    v-for="evaluation in evaluations"
                    :key="evaluation.id"
                    class="flex items-center gap-x-2 text-sm"
                    >
                    <Checkbox
                        :inputId="'eval-' + evaluation.id"
                        :value="evaluation.id"
                        v-model="selectedEvaluations"
                    />
                    <label :for="'eval-' + evaluation.id" class="cursor-pointer">
                        {{ evaluation.title }}
                    </label>
                    </div>
                </div>
            </div>
        </div>
        

        <!-- Technical Evaluation Table -->
        <div v-if="visibleEvaluations.length">
            <div class="font-medium text-primary-600 mb-2">Technical Evaluation</div>
            <table class="text-sm w-full" v-if="groupedCriterias.technical.length">
                <thead>
                    <tr class="bg-primary-900 text-white border border-primary-900">
                        <th class="text-left px-3 py-2">Evaluation Criteria</th>
                        <th
                        v-for="evaluation in visibleEvaluations"
                        :key="evaluation.id"
                        class="text-center px-3 py-2"
                        >
                        {{ evaluation.title }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                <tr
                    v-for="criteria in groupedCriterias.technical"
                    :key="criteria.id"
                    class="border-y border-primary-100 hover:bg-primary-50"
                >
                    <td class="px-3 py-2 font-medium text-primary-700">
                    {{ criteria.name }}
                    </td>
                    <td
                    v-for="evaluation in visibleEvaluations"
                    :key="evaluation.id"
                    class="text-center px-3 py-2"
                    >
                    {{ scoreMatrix[criteria.id]?.[evaluation.id] ?? '-' }}
                    </td>
                </tr>
                </tbody>
            </table>
            <div v-else class="flex items-center gap-x-2 mt-2 text-sm  text-primary-400">
                <span class="material-icons-outlined" style="font-size: medium;">info</span>
                <span>No technical scores found. Please perform technical evaluations.</span>
            </div>
        </div>

        <!-- Non-Technical Evaluation Table -->
        <div v-if="visibleEvaluations.length">
            <div class="font-medium text-primary-600 mt-2 mb-2">Non-Technical Evaluation</div>
            <table class="text-sm w-full" v-if="groupedCriterias.nonTechnical.length">
                <thead>
                <tr class="bg-primary-900 text-white border border-primary-900">
                    <th class="text-left px-3 py-2">Evaluation Criteria</th>
                    <th
                    v-for="evaluation in visibleEvaluations"
                    :key="evaluation.id"
                    class="text-center px-3 py-2"
                    >
                    {{ evaluation.title }}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="criteria in groupedCriterias.nonTechnical"
                    :key="criteria.id"
                    class="border-y border-primary-100 hover:bg-primary-50"
                >
                    <td class="px-3 py-2 font-medium text-primary-700">
                    {{ criteria.name }}
                    </td>
                    <td
                    v-for="evaluation in visibleEvaluations"
                    :key="evaluation.id"
                    class="text-center px-3 py-2"
                    >
                    {{ scoreMatrix[criteria.id]?.[evaluation.id] ?? '-' }}
                    </td>
                </tr>
                </tbody>
            </table>
            <div v-else class="flex items-center gap-x-2 mt-2 text-sm  text-primary-400">
                <span class="material-icons-outlined" style="font-size: medium;">info</span>
                <span>No non-technical scores found. Please perform non-technical evaluations.</span>
            </div>
        </div>

        <div v-if="visibleEvaluations.length">
            <div class="font-medium text-primary-600 mt-2 mb-2">Remarks</div>
            <div v-for="evaluation in visibleEvaluations" :key="evaluation.id" class="mb-4">
                <span class="text-sm font-semibold text-primary-900">{{ evaluation.title }}</span>
                <p style="white-space: pre-wrap;" class="text-sm" v-if="evaluation.remarks.length">
                    {{ evaluation.remarks[0].remarks }}
                </p>
                <div v-else class="flex items-center gap-x-2 mt-2 text-sm  text-primary-400">
                    <span class="material-icons-outlined" style="font-size: medium;">info</span>
                    <span>No remarks. Please put evaluation remarks.</span>
                </div>
            </div>
        </div>

        <!-- Message when nothing is selected -->
        <div v-if="evaluations.length">
            <div v-if="!visibleEvaluations.length" class="text-sm text-gray-500">
                Select one or more evaluations to view their scores.
            </div>
        </div>
        <div v-else class="text-sm text-gray-500">
            No evaluation records found for this employee. Please perform <Link :href="route('evaluation.index')" class="underline text-primary-700 hover:text-primary-900">evaluations</Link>.
        </div>
        
    </div>
</template>