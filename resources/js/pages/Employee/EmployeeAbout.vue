<script setup>
import { Button } from 'primevue';
import { ref } from 'vue';
import EmployeeInfoField from './EmployeeInfoField.vue';
import { useForm, router, usePage } from '@inertiajs/vue3';

const editing = ref(false);
const auth_user = usePage().props.auth.user;

const props = defineProps({
    employee: Object
})

const employeeEditForm = useForm({
    name                       : props.employee.user.name                      ,
    employee_id                : props.employee.employee_id                    ,
    designation                : props.employee.designation                    ,
    emergency_contact_number   : props.employee.emergency_contact_number       ,
    emergency_contact_relation : props.employee.emergency_contact_relation     ,
    joining_date               : props.employee.joining_date                   ,
    supervisor_id              : props.employee.supervisor_id                  ,
    contact_number             : props.employee.contact_number                 ,
});

const saveEmployeeProfile = () => {
    employeeEditForm.put(route('employees.update', props.employee.id), {
        preserveScroll: true,
        onSuccess: () => {
            editing.value = false;
        },
    });
};

</script>

<template>
    <form @submit.prevent>
        <div class="flex items-center">
            <div class="label grow">Basic Information</div>
            <div class="flex items-center gap-x-2" v-if="auth_user.employee.role === 'admin' || props.employee.user_id === auth_user.id">
                <Button v-if="editing" @click="editing = false" label="Cancel" size="small" icon="pi pi-times" class="px-4! rounded-sm! uppercase" outlined></Button>
                <Button v-else @click="editing = !editing" label="Edit profile" size="small" icon="pi pi-pencil" class="px-4! rounded-sm! uppercase"></Button>
                <Button v-if="editing" @click="saveEmployeeProfile" label="Save" size="small" icon="pi pi-save" class="px-4! rounded-sm! uppercase"></Button>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-y-6 mt-6">
            <EmployeeInfoField label="Name"                       v-model="employeeEditForm.name"                       :editing="editing"             />
            <EmployeeInfoField label="Email"                      v-model="employee.user.email"                         :editing="editing" readonly    />
            <EmployeeInfoField label="Employee ID"                v-model="employeeEditForm.employee_id"                :editing="editing"             />
            <EmployeeInfoField label="Designation"                v-model="employeeEditForm.designation"                :editing="editing"             />
            <EmployeeInfoField label="Emergency Contact Number"   v-model="employeeEditForm.emergency_contact_number"   :editing="editing"             v-if="auth_user.employee.role != 'trainee'"/>
            <EmployeeInfoField label="Emergency Contact Relation" v-model="employeeEditForm.emergency_contact_relation" :editing="editing"             v-if="auth_user.employee.role != 'trainee'"/>
            <EmployeeInfoField label="Contact Number"             v-model="employeeEditForm.contact_number"             :editing="editing"             />
            <EmployeeInfoField label="Joining Date"               v-model="employeeEditForm.joining_date"               :editing="editing" type="date" />
        </div>
    </form>
</template>