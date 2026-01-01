<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';
import Dialog from 'primevue/dialog';
import Select from 'primevue/select';
import { usePage, useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Button } from 'primevue';
import EmployeeAbout from './EmployeeAbout.vue';
import EmployeeAvatar from './EmployeeAvatar.vue';
import EmployeeProjects from './EmployeeProjects.vue';
import EmployeeSubordinates from './EmployeeSubordinates.vue';
import EmployeeEvaluation from './EmployeeEvaluation.vue';
import EmployeeAttendance from './EmployeeAttendance.vue';
import EmployeeLeaves from './EmployeeLeaves.vue';

const props = defineProps({
    employee: Object,
    trainers: Array,
    batches: Array,
    evaluations: Array,
    criterias: Array,
    monthlyAttendance: Array,
    leaveHistory: Array,
})

const page                    = usePage()
const auth_user               = page.props.auth.user;
const roleDialogVisible       = ref(false);
const supervisorDialogVisible = ref(false);
const batchDialogVisible      = ref(false);
const previewUrl              = ref(null);
const editing                 = ref(false);
const authEmployee            = auth_user.employee;
const viewedEmployee          = props.employee;

const userRoleForm = useForm({
    role: props.employee.role,
});

const userSupervisorForm = useForm({
    supervisor_id: props.employee.supervisor_id,
});

const userProfilePictureForm = useForm({
    profile_picture: null,
});

const userBatchForm = useForm({
    batch_id: props.employee.role === 'trainee' ? (props.employee.batch ? props.employee.batch.id : null) : 0,
});

const isAuthTrainee   = computed(() => authEmployee.role   === 'trainee'        ).value;
const isViewedTrainee = computed(() => viewedEmployee.role === 'trainee'        ).value;
const isAuthTrainer   = computed(() => authEmployee.role   === 'trainer'        ).value;
const isViewedTrainer = computed(() => viewedEmployee.role === 'trainer'        ).value;
const isAuthAdmin     = computed(() => authEmployee.role   === 'admin'          ).value;
const isViewedAdmin   = computed(() => viewedEmployee.role === 'admin'          ).value;
const isSelf          = computed(() => authEmployee.id     === viewedEmployee.id).value;

const roles = [
    { label: 'Trainee', value: 'trainee' },
    { label: 'Trainer', value: 'trainer' },
    { label: 'Admin'  , value: 'admin'   },
];

const updateRole = () => {
    if (userRoleForm.role === props.employee.role) {
        roleDialogVisible.value = false;
        return;
    }

    userRoleForm.put(route('employees.updateRole', props.employee.id), {
        preserveScroll: true,
        onSuccess: () => {
            props.employee.role = userRoleForm.role; // update local reactive state
            roleDialogVisible.value = false;
        },
    });
};

const updateSupervisor = () => {
    if (userSupervisorForm.supervisor_id === props.employee.supervisor_id) {
        supervisorDialogVisible.value = false;
        return;
    }

    userSupervisorForm.put(route('employees.updateSupervisor', props.employee.id), {
        preserveScroll: true,
        onSuccess: () => {
            // props.employee.role = userRoleForm.role; // update local reactive state
            supervisorDialogVisible.value = false;
        },
    });
};

const updateBatch = () => {
    if (userBatchForm.batch_id === (props.employee.batch ? props.employee.batch.id : null)) {
        batchDialogVisible.value = false;
        return;
    }

    userBatchForm.put(route('employees.updateBatch', props.employee.id), {
        preserveScroll: true,
        onSuccess: () => {
            // props.employee.role = userRoleForm.role; // update local reactive state
            batchDialogVisible.value = false;
        },
    });
}

const handleFileChange = (event) => {
    const file = event.target.files[0]
    if (!file) return
    userProfilePictureForm.profile_picture = file
    previewUrl.value = URL.createObjectURL(file)
    editing.value = true
}

const confirmUpload = () => {
    userProfilePictureForm.post(route('employees.uploadProfilePicture', props.employee.id), {
        preserveScroll: true,
        onSuccess: (pageReturned) => {
            // 1) Clean up local UI state
            editing.value = false
            userProfilePictureForm.profile_picture = null
            previewUrl.value = null

            // 2) Get the flashed path returned by the server
            const flashedPath = page.props.flash?.profile_picture || pageReturned.props?.flash?.profile_picture

            if (flashedPath) {
                const publicUrl = `/storage/${flashedPath}`

                // 3) Update the local employee object (profile page)
                props.employee.profile_picture = publicUrl

                // 4) Update the shared auth user employee (header)
                if (page.props?.auth?.user?.employee) {
                    page.props.auth.user.employee.profile_picture = publicUrl
                } else {
                // fallback: reload shared auth prop
                // router.reload({ only: ['auth'] })
                }
            }
        },
        onError: () => {
        // optional: handle errors
        }
    })
}

const cancelUpload = () => {
    userProfilePictureForm.profile_picture = null;
    previewUrl.value = null;
    editing.value = false;
};

//                   xx Permission Matrix xx
// +----------------+-------------+---------------------------+
// |                |             | Profile Owner ->          |
// +----------------+-------------+-------+---------+---------+
// | Page           + Viewer ↓    | Admin | Trainer | Trainee |
// +----------------+-------------+-------+---------+---------+
// | projects       | Admin       |   x   |    ✓    |    ✓    |
// |                | Trainer     |   x   |    ✓    |    ✓    |
// |                | Trainee     |   x   |    x    |  isSelf |
// +----------------+-------------+-------+---------+---------+
// | subordinates   | Admin       |   x   |    ✓    |    x    |
// |                | Trainer     |   x   |    ✓    |    x    |
// |                | Trainee     |   x   |    x    |    x    |
// +----------------+-------------+-------+---------+---------+
// | about          | Admin       |   ✓   |    ✓    |    ✓    |
// |                | Trainer     |   ✓   |    ✓    |    ✓    |
// |                | Trainee     |   ✓   |    ✓    |    ✓    |
// +----------------+-------------+-------+---------+---------+
// | evaluation     | Admin       |   x   |    x    |    ✓    |
// |                | Trainer     |   x   |    x    |    ✓    |
// |                | Trainee     |   x   |    x    |    x    |
// +----------------+-------------+-------+---------+---------+
// | attendance     | Admin       |   x   |    x    |    ✓    |
// |                | Trainer     |   x   |    x    |    ✓    |
// |                | Trainee     |   x   |    x    |  isSelf |
// +----------------+-------------+-------+---------+---------+
// | leave          | Admin       |   x   |    x    |    ✓    |
// |                | Trainer     |   x   |    x    |    ✓    |
// |                | Trainee     |   x   |    x    |  isSelf |
// +----------------+-------------+-------+---------+---------+

const permissions = computed(() => ({
    // Owner Role ->                  Admin                     Trainer          Trainee
    projects        : isViewedAdmin ? false : isViewedTrainer ? !isAuthTrainee : !isAuthTrainee ? true : isSelf,
    subordinates    : isViewedAdmin ? false : isViewedTrainer ? !isAuthTrainee : false,
    about           : true,
    evaluation      : isViewedAdmin ? false : isViewedTrainer ? false          : !isAuthTrainee,
    attendance      : isViewedAdmin ? false : isViewedTrainer ? false          : !isAuthTrainee ? true : isSelf,
    leave           : isViewedAdmin ? false : isViewedTrainer ? false          : !isAuthTrainee ? true : isSelf,
}));

const role_icon = computed(() => {
    switch (props.employee.role) {
        case 'admin': return 'pi-shield';
        case 'trainer': return 'pi-graduation-cap';
        case 'trainee': return 'pi-user';
        default: return 'pi-user';
    }
});

</script>

<template>
    <AppLayout title="Profile" summary="Manage employee profile">
        <div class="flex xl:w-4/5 2xl:w-3/4 mx-auto">
            <div class="grow flex flex-col">
                <span class="text-primary-800 font-chakra font-bold text-2xl">{{ props.employee.user.name }}</span>
                <span class="text-sm text-primary-600">{{ props.employee.user.email }}</span>
                <div class="mt-4 flex items-center gap-x-4">
                    <div class="uppercase tracking-widest font-chakra bg-primary-950 text-white rounded-sm px-4 py-2 text-xs font-bold flex items-center gap-x-2">
                        <span class="pi" :class="role_icon" style="font-size: small;"></span>
                        <span>{{ props.employee.role }}</span>
                    </div>
                    <button @click="roleDialogVisible = true" v-if="auth_user.employee.role == 'admin'" class="border border-primary-200 rounded-md px-4 py-2 flex gap-x-2 items-center text-xs text-primary-500 hover:text-primary-900 hover:border-primary-600 transition-all"><span class="material-icons-outlined " style="font-size: medium;">cached</span>Change role</button>
                </div>

                <div class="flex gap-x-10">
                    <!-- Supervisor Information -->
                    <div class="mt-4 w-1/3" v-if="employee.role === 'trainee'">
                        <div class="flex items-center justify-between">
                            <div class="label">Supervisor</div>
                            <button @click="supervisorDialogVisible = true" v-if="auth_user.employee.role == 'admin' || auth_user.employee.role == 'trainer'" class="text-xs text-primary-600 hover:text-primary-900 transition flex items-center gap-x-1">
                                <span class="material-icons-outlined " style="font-size: medium;">cached</span>Change supervisor
                            </button>
                        </div>
                        <div class="flex items-center mt-2">
                            <Link
                                v-if="props.employee.supervisor"
                                :href="route('employees.show', {employee: props.employee.supervisor_id})"
                                class="group grow flex items-center bg-white border border-neutral-200 rounded-md p-2 hover:border-neutral-900 hover:shadow-lg hover:shadow-neutral-900/5 transition-all duration-300"
                            >
                                <div class="relative shrink-0">
                                    <EmployeeAvatar
                                        :image="props.employee.supervisor.profile_picture"
                                        :name="props.employee.supervisor.name"
                                        class="h-10 w-10 rounded-lg object-cover grayscale group-hover:grayscale-0 transition-all duration-500 border border-neutral-50"
                                    />
                                </div>
                                
                                <div class="flex flex-col ms-4 grow overflow-hidden">
                                    <span class="text-xs font-bold text-neutral-900 uppercase truncate transition-colors group-hover:text-primary-600">
                                        {{ props.employee.supervisor.name }}
                                    </span>
                                    <span class="text-[10px] text-neutral-700 tracking-tighter mt-0.5">
                                        {{ props.employee.supervisor.employee.designation}}
                                    </span>
                                </div>

                                <div class="opacity-0 group-hover:opacity-100 transition-opacity px-2">
                                    <i class="pi pi-arrow-up-right text-neutral-300 text-[10px]"></i>
                                </div>
                            </Link>
                            <div v-else class="flex gap-x-2 text-sm text-primary-600 items-center mt-2">
                                <span class="material-icons-round">warning</span>
                                <span>Not assigned</span>
                            </div>
                            <div>
                                
                            </div>
                        </div>
                    </div>

                    <!-- Batch Information -->
                    <div class="mt-4 w-1/4" v-if="employee.role === 'trainee'">
                        <div class="flex items-center justify-between">
                            <div class="label">Batch</div>
                            <button @click="batchDialogVisible = true" v-if="auth_user.employee.role == 'admin' || auth_user.employee.role == 'trainer'" class="text-xs text-primary-600 hover:text-primary-900 transition flex items-center gap-x-1">
                                <span class="material-icons-outlined " style="font-size: medium;">cached</span>Change batch
                            </button>
                        </div>
                        <div class="flex items-center gap-x-4 mt-2">
                            <Link :href="route('batch.show', {batch: props.employee.batch.id })" class="flex items-center gap-x-4 rounded-sm border border-primary-200 hover:border-primary-700 transition px-2 h-14.5 w-full" v-if="props.employee.batch">
                                <div class="w-10 h-10 bg-primary-800 flex items-center justify-center rounded-md text-white">
                                    <span class="material-icons-round" style="font-size: 1.5rem;">workspaces</span>
                                </div>
                                <span class="text-primary-700 text-sm font-medium">{{ props.employee.batch.name }}</span>
                            </Link>
                            <div v-else class="flex gap-x-2 text-sm text-primary-600 items-center h-10 w-full">
                                <span class="material-icons-round">warning</span>
                                <span>Not assigned</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Update Role Dialog -->
                <Dialog v-model:visible="roleDialogVisible" modal header="Change Employee Role" :style="{ width: '25rem' }">
                    <form @submit.prevent="updateRole">
                        <div class="flex items-center gap-x-2 justify-between">
                            <div class="font-medium">Employee Role</div>
                            <Select v-model="userRoleForm.role" :options="roles" optionLabel="label" optionValue="value" placeholder="Select role" class="w-40 font-medium text-sm" />
                        </div>
                        
                        <div class="flex justify-end mt-4">
                            <Button label="Save" type="submit" icon="pi pi-save" size="small"/>
                        </div>
                    </form>
                </Dialog>

                <!-- Update Supervisor Dialog -->
                <Dialog v-if="props.employee.role === 'trainee'" v-model:visible="supervisorDialogVisible" modal header="Change Employee Supervisor" :style="{ width: '34rem' }">
                    <form @submit.prevent="updateSupervisor">
                        <div class="flex flex-col gap-y-2">
                            <div class="label">Employee Supervisor</div>
                            <Select filter v-model="userSupervisorForm.supervisor_id" :options="trainers" optionLabel="label" optionValue="value" placeholder="Select trainer" class="w-full font-medium text-sm">
                                <template #option="slotProps">
                                    <div class="flex items-center text-sm font-medium">
                                        <div>{{ slotProps.option.label }}</div>
                                    </div>
                                </template>
                            </Select>
                        </div>
                        
                        <div class="flex justify-end mt-4">
                            <Button label="Save" type="submit" icon="pi pi-save" size="small" class="px-4! rounded-sm!"/>
                        </div>
                    </form>
                </Dialog>

                <!-- Update Batch Dialog -->
                <Dialog v-if="props.employee.role === 'trainee'" v-model:visible="batchDialogVisible" modal header="Change Employee Batch" :style="{ width: '30rem' }">
                    <form @submit.prevent="updateBatch" class="font-inter!">
                        <div class="flex flex-col gap-y-2">
                            <div class="label">Employee Batch</div>
                            <Select filter v-model="userBatchForm.batch_id" :options="batches" optionLabel="label" optionValue="value" size="small" placeholder="Select batch" class="w-full font-medium text-sm">
                                <template #option="slotProps">
                                    <div class="flex items-center text-sm font-medium">
                                        <div>{{ slotProps.option.label }}</div>
                                    </div>
                                </template>
                            </Select>
                        </div>
                        
                        <div class="flex justify-end mt-4">
                            <Button label="Save" type="submit" icon="pi pi-save" size="small" class="px-4! rounded-sm!"/>
                        </div>
                    </form>
                </Dialog>
            </div>
            <form @submit.prevent class="w-40 h-40 flex items-center justify-center relative group">
                <input type="file" accept="image/*" ref="fileInput" class="hidden" @change="handleFileChange" />
                <EmployeeAvatar :preview="previewUrl ? true : false" :image="previewUrl || props.employee.profile_picture" :name="props.employee.user.name" class="h-36 w-36 rounded-xl object-cover text-3xl border border-primary-500 bg-primary-800!"/>
                <div v-if="props.employee.user_id === auth_user.id" class="absolute top-5 right-5 flex gap-2">
                    <!-- Show Add Photo if not editing -->
                    <button
                        v-if="!editing"
                        type="button"
                        class="cursor-pointer bg-white rounded-lg hover:bg-primary-200 w-8 h-8 flex items-center justify-center opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all"
                        @click="$refs.fileInput.click()"
                    >
                        <span class="material-icons-round" style="font-size: medium;">add_a_photo</span>
                    </button>

                    <!-- Show Confirm + Cancel if editing -->
                    <template v-else>
                        <button
                        type="button"
                        class="cursor-pointer bg-primary-500 text-white rounded-lg w-8 h-8 flex items-center justify-center hover:bg-primary-600 transition"
                        @click="confirmUpload"
                        >
                        <span class="material-icons-round" style="font-size: medium;">check</span>
                        </button>
                        <button
                        type="button"
                        class="cursor-pointer bg-primary-500 text-white rounded-lg w-8 h-8 flex items-center justify-center hover:bg-primary-600 transition"
                        @click="cancelUpload"
                        >
                        <span class="material-icons-round" style="font-size: medium;">close</span>
                        </button>
                    </template>
                </div>
            </form>
        </div>

        <div class="xl:w-4/5 2xl:w-3/4 mx-auto mt-4">
            <Tabs :value="isViewedTrainee ? 'projects' : 'about'"> 
                <TabList>
                    <Tab value="about"        v-if="permissions.about"        class="font-inter text-sm">About</Tab>
                    <Tab value="projects"     v-if="permissions.projects"     class="font-inter text-sm">Projects</Tab>
                    <Tab value="subordinates" v-if="permissions.subordinates" class="font-inter text-sm">Subordinates</Tab>
                    <Tab value="evaluation"   v-if="permissions.evaluation"   class="font-inter text-sm">Evaluation</Tab>
                    <Tab value="attendance"   v-if="permissions.attendance"   class="font-inter text-sm">Attendance</Tab>
                    <Tab value="leave"        v-if="permissions.leave"        class="font-inter text-sm">Leave History</Tab>
                </TabList>
                <TabPanels>
                    <TabPanel value="projects"      v-if="permissions.projects">
                        <EmployeeProjects           :projects="props.employee.role === 'trainee' ? props.employee.assigned_projects : props.employee.reviewing_projects"/>
                    </TabPanel>
                    <TabPanel value="subordinates"  v-if="permissions.subordinates">
                        <EmployeeSubordinates       :subordinates="props.employee.subordinates"/>
                    </TabPanel>
                    <TabPanel value="about"         v-if="permissions.about">
                        <EmployeeAbout              :employee="props.employee"/>
                    </TabPanel>
                    <TabPanel value="evaluation"    v-if="permissions.evaluation">
                        <EmployeeEvaluation         :evaluations="props.evaluations" :employee="props.employee"/>
                    </TabPanel>
                    <TabPanel value="attendance"    v-if="permissions.attendance">
                        <EmployeeAttendance         :employee="props.employee" :attendance="props.monthlyAttendance"/>
                    </TabPanel>
                    <TabPanel value="leave"         v-if="permissions.leave">
                        <EmployeeLeaves             :employee="props.employee" :leaves="props.leaveHistory"/>
                    </TabPanel>
                </TabPanels>
            </Tabs>
        </div>
        
    </AppLayout>
</template>