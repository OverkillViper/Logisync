<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { Button } from 'primevue';

defineProps({
    notifications: Object
});

const markAllAsRead = () => {
    router.post(route('notifications.markAllAsRead'));
};

const deleteAllNotifications = () => {
    router.delete(route('notifications.deleteAll'));
};

</script>
<template>
    <AppLayout title="Notifications" summary="Manage your notifications">
        <template v-slot:titleField>
            <Button @click="deleteAllNotifications" v-if="notifications.data.length" icon="pi pi-trash" label="Delete all" size="small" class="ms-2 px-4! rounded-sm!"/>
            <Button @click="markAllAsRead" v-if="notifications.data.length" icon="pi pi-eye" label="Mark all as read" size="small" class="ms-2 px-4! rounded-sm!"/>
        </template>

        <div class="xl:w-4/5 2xl:w-3/4 mx-auto divide-y divide-primary-100" v-if="notifications.data.length">
            <div v-for="notification in notifications.data" :key="notification.id">
                <div class="p-4 hover:bg-primary-50 transition" :class="{'font-bold' : !notification.read_at}">
                    <div class="flex items-center gap-x-4">
                        <Link
                            :href="notification.data.url ? notification.data.url : '#'"
                            class="font-medium flex items-center gap-x-2"
                        >
                            <span>{{ notification.data.title }}</span>
                            <span class="pi pi-external-link" style="font-size: 0.7rem;"></span>
                        </Link>
                        <div v-if="!notification.read_at">
                            <span class="px-1 py-0.5 bg-primary-100 text-primary-700 text-xs rounded-sm">New</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-primary-700">{{ notification.data.message }}</p>
                        <Link :href="route('notification.destroy', {notification: notification.id})" method="delete" class="ml-2 text-red-500 hover:text-red-700">
                            <span class="pi pi-trash"></span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
        <div v-else >
            <div class="text-sm text-primary-500 mt-6 text-center">You do not have any notifications.</div>
        </div> 
    </AppLayout>
</template>