<script setup>
defineProps({
    project_stats: Object,
});

const formatKey = (key) => key.replace(/_/g, ' ');
</script>

<template>
    <div class="bg-white border border-neutral-300 rounded-sm overflow-hidden shadow-sm shadow-neutral-100/20">
        <div class="flex items-center px-4 py-3 border-b border-neutral-100 gap-x-4">
            <div class="w-10 h-10 bg-neutral-900 rounded-xl flex items-center justify-center text-white shadow-lg">
                <span class="material-icons-outlined" style="font-size: 1.25rem;">bar_chart</span>
            </div>
            
            <div class="flex flex-col grow">
                <h3 class="text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 leading-none mb-1">Project Overview</h3>
                <div class="text-sm font-bold text-neutral-900 uppercase tracking-tight">Project Statistics</div>
            </div>
        </div>

        <div class="p-6 bg-neutral-50 grid grid-cols-2 lg:grid-cols-5 gap-6">
            <div 
                v-for="(value, key) in project_stats" 
                :key="key"
                class="select-none flex flex-col items-start p-5 bg-white border border-neutral-200 rounded-sm hover:border-neutral-900 transition-all duration-300 group"
            >
                <span class="text-[9px] font-bold text-neutral-400 uppercase tracking-[0.2em] mb-3 group-hover:text-primary-600 transition-colors">
                    {{ formatKey(key) }}
                </span>
                
                <div class="flex items-baseline gap-x-2">
                    <span class="text-lg font-inter font-medium text-neutral-900 tracking-tighter">
                        {{ value.toString().padStart(2, '0') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.font-mono {
    font-variant-numeric: tabular-nums;
}
</style>