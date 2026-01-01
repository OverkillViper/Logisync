<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, usePage, router, useForm } from '@inertiajs/vue3';
import ProjectCard from './ProjectCard.vue';
import { Button } from 'primevue';
import InputText from 'primevue/inputtext';
import InputGroup from 'primevue/inputgroup';
import { ref, computed } from 'vue';
import ToggleSwitch from 'primevue/toggleswitch';
import Select from 'primevue/select';
import Popover from 'primevue/popover';
import Listbox from 'primevue/listbox';
import Paginator from 'primevue/paginator';

const role = usePage().props.auth.user.employee.role;

const props = defineProps({
    projects: Object,
    show_all: Boolean,
    sort_by: String,
    filters: Object,
    totalProject: Number,
})

const show_all_projects = ref(props.show_all);
const sort_by = ref(props.sort_by || 'name');

const filterOpen = ref(false)

const toggleFilter = (event) => {
    filterOpen.value.toggle(event);
}

const status = [
    { name: 'In Progress'   , value: 'in_progress'    },
    { name: 'Overdue'       , value: 'overdue'        },
    { name: 'Completed'     , value: 'completed'      },
    { name: 'Completed Late', value: 'completed_late' },
];

const sortByOptions = [
    { name: 'Name'      , value: 'name'       },
    { name: 'Deadline'  , value: 'deadline'   },
    { name: 'Created At', value: 'created at' },
];

const filterProjectForm = useForm({
    status: props.filters.status || [],
})

const performAction = () => {
    router.get(route('project.index'), {
        show_all: show_all_projects.value,
        sort_by: sort_by.value,
        status: filterProjectForm.status,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const onPageChange = (event) => {
    router.get(
        route('project.index'),
        {
            page: event.page + 1,
            sort_by: sort_by.value,
            show_all: show_all_projects.value,
            status: filterProjectForm.status,
        },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        }
    )
}

</script>

<template>

<AppLayout title="Projects" summary="Manage projects">
    <!-- Create Project Button -->
    <template v-slot:titleField>
        <div class="flex items-center gap-x-2">
            <div class="flex items-center gap-x-2" v-if="role != 'trainee'">
                <ToggleSwitch v-model="show_all_projects" @update:modelValue="performAction"/>
                <label for="show-all" class="text-xs text-primary-600">Show all projects</label>
            </div>
            <div>
                <Button variant="outlined" label="Filter" icon="pi pi-filter" size="small" class="px-4! rounded-xs!" @click="toggleFilter"/>
                <Popover ref="filterOpen" class="shadow-xl shadow-neutral-200/40 border border-neutral-100 rounded-2xl overflow-hidden">
                    <div class="w-64 p-2 flex flex-col font-inter bg-white">
                        <div class="px-3 py-3 mb-1">
                            <div class="text-[10px] uppercase tracking-[0.2em] text-neutral-400 font-bold mb-0.5">
                                Lifecycle Stage
                            </div>
                            <div class="text-xs text-neutral-600 font-medium">
                                Filter projects by current status
                            </div>
                        </div>

                        <Listbox
                            v-model="filterProjectForm.status"
                            multiple
                            :highlightOnSelect="false"
                            :options="status"
                            optionLabel="name"
                            checkmark
                            optionValue="value"
                            class="border-none! shadow-none! bg-transparent!"
                        >
                            <template #option="slotProps">
                                <div class="text-sm">
                                    <div class="ms-2">{{ slotProps.option.name }}</div>
                                </div>
                            </template>
                        </Listbox>

                        <div class="mt-3 pt-2">
                            <Button 
                                label="Update View" 
                                icon="pi pi-sync" 
                                class="w-full bg-neutral-900 border-none rounded-sm! py-3.5 text-xs! uppercase tracking-widest hover:bg-primary-600 transition-all shadow-none font-bold" 
                                @click="performAction"
                            />
                        </div>
                    </div>
                </Popover>
            </div>
            <div>
                <Select
                    v-model="sort_by"
                    :options="sortByOptions"
                    optionLabel="name"
                    optionValue="value"
                    size="small"
                    class="w-48 rounded-xs!"
                    placeholder="Sort by"
                    @change="performAction"
                >
                    <template #value="slotProps">
                        <div v-if="slotProps.value" class="flex items-center capitalize">
                            <div>Sort By: {{ slotProps.value }}</div>
                        </div>
                        <span v-else>
                            {{ slotProps.placeholder }}
                        </span>
                    </template>
                    <template #option="slotProps">
                        <div class="flex items-center text-sm px-0! font-medium">
                            <div>{{ slotProps.option.name }}</div>
                        </div>
                    </template>
                </Select>
            </div>
            
            <Link v-if="role != 'trainee'" :href="route('project.create')" class="flex justify-end">
                <Button label="New Project" icon="pi pi-plus" size="small" class="rounded-sm! px-6! uppercase font-inter!"/>
            </Link>
        </div>
    </template>

    <div class="grid grid-cols-3 2xl:grid-cols-4 xl:w-4/5 2xl:w-3/4 mx-auto gap-6 border border-transparent" v-if="props.projects.data.length">
        <ProjectCard :project="project" v-for="project in props.projects.data" :key="project.id" />
    </div>
    <div v-else>
        <div class="text-sm text-primary-500 mt-6 text-center">No project found that you are assigned to. <span v-if="role != 'trainee'">To see all projects, check <b>'Show all projects'</b> above.</span></div>
    </div>

    <div class="text-sm mt-4" v-if="projects.total > projects.per_page">
        <Paginator :rows="12" :totalRecords="props.totalProject" :first="(projects.current_page - 1) * projects.per_page" @page="onPageChange"></Paginator>
    </div>
</AppLayout>

</template>