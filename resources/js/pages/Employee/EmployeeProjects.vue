<script setup>
import ProjectCard from '../Project/ProjectCard.vue';
import InputText from 'primevue/inputtext';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import { ref, computed } from 'vue';

const props = defineProps({
    projects: Array
})

const searchQuery = ref('')

const searchedProjects = computed(() => {
    if (!searchQuery.value) return props.projects
    const q = searchQuery.value.toLowerCase()
    return props.projects.filter(project =>
        project.name.toLowerCase().includes(q)
    )
})

const currentProjects = computed(() => {
    return searchedProjects.value.filter(project =>
        project.status === "In Progress"
    )
})

const previousProjects = computed(() => {
    return searchedProjects.value.filter(project =>
        project.status != "In Progress"
    )
})

</script>
<template>
    <div class="flex justify-end">
        <InputGroup class="max-w-80">
            <InputText placeholder="Search projects" v-model="searchQuery" size="small"/>
            <InputGroupAddon>
                <span class="pi pi-search" style="font-size: small;"></span>
            </InputGroupAddon>
        </InputGroup>
    </div>
    <!-- Current Projects -->
    <div class="label">Current projects</div>
    <div class="grid grid-cols-3 2xl:grid-cols-4 gap-5 mt-2" v-if="currentProjects.length">
        <ProjectCard v-for="project in currentProjects" :project="project" :key="project.id"/>
    </div>
    <div v-else class="flex items-center gap-x-2 mt-2 text-sm  text-primary-400">
        <span class="material-icons-outlined" style="font-size: medium;">info</span>
        <span>No currently ongoing projects</span>
    </div>

    <!-- Previous Projects -->
    <div class="label mt-6">Previous projects</div>
    <div class="grid grid-cols-3 2xl:grid-cols-4 gap-5 mt-2" v-if="previousProjects.length">
        <ProjectCard v-for="project in previousProjects" :project="project" :key="project.id"/>
    </div>
    <div v-else class="flex items-center gap-x-2 mt-2 text-sm  text-primary-400">
        <span class="material-icons-outlined" style="font-size: medium;">info</span>
        <span>No projects found</span>
    </div>

</template>