<script setup>
import Button from 'primevue/button';
import ProjectCard from '../Project/ProjectCard.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    projects : Array ,
});
</script>

<template>
    <div class="bg-white border border-neutral-300 rounded-sm overflow-hidden shadow-sm shadow-neutral-100/20">
        <div class="flex items-center px-4 py-3 border-b border-neutral-100 gap-x-4">
            <div class="w-10 h-10 bg-neutral-900 rounded-xl flex items-center justify-center text-white shadow-lg">
                <span class="material-icons-outlined" style="font-size: 1.25rem;">assignment</span>
            </div>
            
            <div class="flex flex-col grow">
                <h3 class="text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 leading-none mb-1">Project Overview</h3>
                <div class="text-sm font-bold text-neutral-900 uppercase tracking-tight">Upcoming Project Deadlines</div>
            </div>

            <Link :href="route('project.index')">
                <Button 
                    label="View Projects" 
                    icon="pi pi-external-link"
                    iconPos="right"
                    class="text-[10px]! font-bold! uppercase! tracking-widest! text-neutral-400! hover:text-neutral-900! bg-transparent! border-none! transition-all! p-0!"
                />
            </Link>
        </div>

        <div class="p-6 bg-neutral-50">
            <div v-if="projects.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <ProjectCard 
                    v-for="project in projects" 
                    :key="project.id" 
                    :project="project" 
                    class="hover:shadow-lg transition-shadow duration-500 bg-white"
                />
            </div>

            <div v-else class="flex flex-col items-center justify-center py-12 border-2 border-dashed border-neutral-50 rounded-3xl">
                <i class="pi pi-check-circle text-neutral-200 text-3xl mb-3"></i>
                <p class="text-[11px] font-mono uppercase tracking-widest text-neutral-400 italic">
                    All projects are complete.
                </p>
            </div>
        </div>
    </div>
</template>