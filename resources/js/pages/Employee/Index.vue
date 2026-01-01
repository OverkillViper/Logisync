<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import EmployeeCard from './EmployeeCard.vue';
import { ref, computed } from 'vue'
import InputText from 'primevue/inputtext';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import { Button } from 'primevue';
import Popover from 'primevue/popover';
import Listbox from 'primevue/listbox';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    employees: Array,
    filters: Object,
})

const searchQuery = ref('')
const filterOpen = ref(false)

const searchedEmployees = computed(() => {
    if (!searchQuery.value) return props.employees
    const q = searchQuery.value.toLowerCase()
    return props.employees.filter(emp =>
        emp.user.name.toLowerCase().includes(q) ||
        emp.user.email?.toLowerCase().includes(q)
    )
})

const toggleFilter = (event) => {
    filterOpen.value.toggle(event);
}

const roles = [
  { name: 'Admin', value: 'admin' },
  { name: 'Trainer', value: 'trainer' },
  { name: 'Trainee', value: 'trainee' },
]

// Initialize filter form from backend filters
const filterEmployeeForm = useForm({
    roles: props.filters.roles || [],
})

const filterEmployees = () => {
    router.get(route('employees.index'), filterEmployeeForm, {
        preserveState: true,
        preserveScroll: true,
    })
}

</script>

<template>
    <AppLayout title="Employees" summary="Manage all employees">
        <template v-slot:titleField>
            <div class="flex gap-x-2">
                <Button variant="outlined" label="Filter" icon="pi pi-filter" size="small" class="px-4! rounded-xs!" @click="toggleFilter"/>
                <Popover ref="filterOpen" class="shadow-xl shadow-neutral-200/50 border border-neutral-100 rounded-2xl overflow-hidden">
                    <div class="w-64 p-2 flex flex-col font-inter">
                        <div class="px-3 py-3">
                            <div class="text-[11px] uppercase tracking-[0.15em] text-neutral-400 font-bold mb-1">
                                Employee Filter
                            </div>
                            <div class="text-xs text-neutral-600 font-medium">
                                Select employee roles to display
                            </div>
                        </div>

                        <Listbox
                            v-model="filterEmployeeForm.roles"
                            multiple
                            :highlightOnSelect="false"
                            :options="roles"
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

                        <div class="pt-2 border-t border-neutral-50">
                            <Button 
                                label="Apply Changes" 
                                icon="pi pi-check" 
                                class="w-full bg-neutral-900 border-none rounded-sm! py-4 text-xs! uppercase hover:bg-primary-600 transition-all shadow-none font-medium tracking-widest" 
                                @click="filterEmployees"
                            />
                        </div>
                    </div>
                </Popover>
                <InputGroup>
                    <InputText placeholder="Filter employee by name" v-model="searchQuery" size="small" class="rounded-xs!"/>
                    <InputGroupAddon class="rounded-xs!">
                        <span class="pi pi-filter" style="font-size: smaller;"></span>
                    </InputGroupAddon>
                </InputGroup>
            </div>
        </template>
        <div class="grid grid-cols-3 gap-4 xl:w-4/5 2xl:w-3/4 mx-auto mb-10">
            <EmployeeCard v-for="employee in searchedEmployees" :key="employee.id" :employee="employee" />
        </div>
    </AppLayout>
</template>

<style scoped>
.p-popover-content {
    --p-popover-content-padding: 14px 10px;
}
</style>