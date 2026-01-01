<script setup>
import EmployeeAvatar from '../Employee/EmployeeAvatar.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    isTrainee: Boolean,
    leaves: Array,
});

function formatDate(input) {
    const date = new Date(input);
    const day = date.getDate().toString().padStart(2, '0');
    const month = date.toLocaleString("en-GB", { month: "short" });
    const year = date.getFullYear();
    return `${day} ${month}, ${year}`;
}

function getDayName(input) {
    const date = new Date(input);
    return date.toLocaleString("en-GB", { weekday: "long" });
}
</script>

<template>
    <div class="bg-white border border-neutral-300 rounded-sm overflow-hidden shadow-sm shadow-neutral-100/20">
        <div class="flex items-center px-4 py-3 border-b border-neutral-100 gap-x-4">
            <div class="w-10 h-10 bg-neutral-900 rounded-xl flex items-center justify-center text-white shadow-lg shrink-0">
                <span class="material-icons-outlined" style="font-size: 1.25rem;">domain_disabled</span>
            </div>
            
            <div class="flex flex-col grow">
                <h3 class="text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 leading-none mb-1">Current Month's</h3>
                <div class="text-sm font-bold text-neutral-900 uppercase tracking-tight">Leave History</div>
            </div>

            <div class="text-xs font-bold bg-neutral-100 text-neutral-500 px-2 py-1 rounded">
                {{ leaves.length.toString().padStart(2, '0') }}
            </div>
        </div>

        <div class="p-4 bg-neutral-50">
            <div v-if="leaves.length" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div 
                    v-for="leave in leaves" 
                    :key="leave.id" 
                    class="bg-white border border-neutral-200 p-2 rounded-sm hover:border-neutral-900 transition-all group"
                >
                    <Link 
                        v-if="!isTrainee"
                        :href="route('employees.show', {employee: leave.employee.user.id})"
                        class="flex items-center gap-x-3 mb-2"
                    >
                        <EmployeeAvatar 
                            :image="leave.employee.profile_picture" 
                            :name="leave.employee.user.name" 
                            class="h-8 w-8 rounded-sm grayscale group-hover:grayscale-0 transition-all"
                        />
                        <span class="font-bold text-sm text-neutral-800 group-hover:text-primary-600 truncate">
                            {{ leave.employee.user.name }}
                        </span>
                    </Link>

                    <div class="flex">
                        <div class="flex flex-col" :class="{'ms-10': !isTrainee}">
                            <span class="text-xs text-neutral-900">{{ formatDate(leave.date) }}</span>
                            <span class="text-xs text-neutral-600">
                                {{ getDayName(leave.date) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="flex flex-col items-center justify-center py-10">
                <span class="text-sm text-neutral-500">No absence records found.</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Tabular numbers for clean date vertical alignment */
.font-mono {
    font-variant-numeric: tabular-nums;
}
</style>