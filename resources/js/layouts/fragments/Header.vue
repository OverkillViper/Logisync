<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import EmployeeAvatar from '@/pages/Employee/EmployeeAvatar.vue';
import { ref, onMounted } from "vue";
import Popover from 'primevue/popover';
import OverlayBadge from 'primevue/overlaybadge';
import axios from 'axios';
import { echo } from '../../echo.js';

const page = usePage();
const user = page.props.auth.user;

// Popover ref
const notificationOpen = ref(false);
const toggleNotification = (event) => {
    notificationOpen.value.toggle(event);
}

const op = ref(false);
const toggle = (event) => {
    op.value.toggle(event);
}

// Reactive notifications
const notifications = ref([]);
const unreadCount = ref(0);

// Load initial notifications
const loadNotifications = async () => {
    const { data } = await axios.get('/user/notifications');
    notifications.value = data
        .sort((a, b) => (b.read_at ? 1 : -1))
        .slice(0, 4);
    unreadCount.value = data.filter(n => !n.read_at).length;
};

// Live updates
onMounted(() => {
    loadNotifications();

    echo.private(`App.Models.User.${user.id}`)
        .notification((notification) => {
            notifications.value.unshift(notification);
            unreadCount.value++;
            if (notifications.value.length > 4) notifications.value.pop();
        });
});
</script>

<template>
    <nav class="flex p-4 items-center">
        <!-- Logo -->
        <div class="w-1/8 flex flex-col py-1">
            <Link :href="route('dashboard')" class="uppercase font-chakra text-xl tracking-tighter leading-none">
                <span class="text-neutral-400 font-light">Logi</span>
                <span class="text-neutral-900 font-bold">Sync</span>
            </Link>
            <span class="text-[10px] uppercase tracking-widest text-neutral-400 mt-1 font-mono">Synchronize the logic Designers</span>
        </div>
        <div class="grow">
            <form action="" class="flex bg-white rounded-sm px-4 py-3 w-1/3 shadow-xs border border-primary-100 has-[:focus]:border-primary-500 text-sm font-medium justify-between gap-x-4">
                <input type="text" name="search" id="search" placeholder="What are we searching today?"
                class="outline-none grow focus:border-primary-800"/>
                <button><i class="pi pi-search" style="font-size: .8rem"></i></button>
            </form>
        </div>
        <div class="mx-6 pt-2">
            <OverlayBadge v-if="unreadCount > 0"  @click="toggleNotification" :value="unreadCount" size="small" class="text-primary-500 hover:text-primary-900 transition">
                <i class="pi pi-bell" style="font-size: 1.3rem" />
            </OverlayBadge>
            <button v-else class="text-primary-500 hover:text-primary-900 transition" @click="toggleNotification">
                <i class="pi pi-bell" style="font-size: 1.3rem" />
            </button>
            
            <Popover ref="notificationOpen" class="flex flex-col text-xs w-76">
                <div class="flex flex-col p-1 px-3 my-2 font-inter">
                    <div class="flex gap-x-2 items-center">
                        <div class="font-medium text-sm grow">Notifications</div>
                        <Link class="text-primary-500 hover:text-primary-800 transition" :href="route('notifications.index')"><span class="pi pi-external-link" style="font-size: 0.9rem;"></span></Link>
                    </div>
                    <div v-if="notifications.length === 0" class="text-gray-400 text-xs">No notifications</div>

                    <Link
                        v-for="notification in notifications"
                        :key="notification.id"
                        :class="{'font-bold' : !notification.read_at}"
                        class="mt-2 cursor-pointer"
                        :href="route('notifications.show', {notification: notification.id})"
                    >
                        <div class="font-medium">{{ notification.title }}</div>
                        <div class="line-clamp-1 hover:line-clamp-none">{{ notification.message }}</div>
                    </Link>
                </div>
            </Popover>
        </div>
        <div class="flex justify-end items-center">
            <div class="rounded-sm border border-neutral-200 bg-white flex gap-x-2 items-center font-medium text-sm min-w-66">
                <EmployeeAvatar :image="page.props.auth.user.employee.profile_picture" :name="user.name" class="h-12 w-12 object-cover rounded-sm bg-primary-950 border border-primary-500"/>
                <div class="flex flex-col grow">
                    <div class="font-semibold font-chakra">{{ user.name }}</div>
                    <span class="text-xs font-chakra text-primary-600">{{ user.employee.designation }}</span>
                </div>
                <button @click="toggle" class="ms-4 w-12 h-12 flex items-center justify-center hover:bg-primary-200 transition"><i class="pi pi-chevron-down" style="font-size: .8rem"></i></button>

                <Popover ref="op" class="flex flex-col text-xs min-w-40 p-0!">
                    <Link :href="route('employees.show', {employee: user.employee.id})"
                        class="flex items-center gap-x-2 font-medium font-inter px-4 py-2 my-1 hover:bg-primary-100 transition rounded-xs">
                        <span class="material-icons-outlined" style="font-size: medium;">person_2</span>
                        <span>My Profile</span>
                    </Link>
                    <hr class="my-1">
                    <Link :href="route('logout')" method="post" as="button"
                        class="flex items-center gap-x-2 font-medium font-inter px-4 py-2 my-1 hover:bg-primary-100 transition rounded-xs w-full">
                        <span class="material-icons-outlined" style="font-size: medium;">power_settings_new</span>
                        <span>Sign out</span>
                    </Link>
                </Popover>
            </div>
        </div>
    </nav>
</template>

<style>
.p-popover-content {
    --p-popover-content-padding: 0px 0px;
}
</style>