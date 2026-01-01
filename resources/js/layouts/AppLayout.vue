<script setup>
import Header from './fragments/Header.vue';
import Sidebar from './fragments/Sidebar.vue';
import { ref, watchEffect } from 'vue';
import Toast from 'primevue/toast';
import { usePage, Link } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import ScrollPanel from 'primevue/scrollpanel';
import Breadcrumb from 'primevue/breadcrumb';

const toast = useToast();
const page = usePage();

const sidebarOpen = ref(page.props.sidebarOpen ?? true);
const breadcrumbs = page.props.breadcrumbs || [];

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value;
  document.cookie = `sidebar_state=${sidebarOpen.value}; path=/`;
};

const props = defineProps({
    title         : String,
    summary       : String,
    previousRoute : String,
});

watchEffect(() => {
    const flash = page.props.flash;

    if (flash.success) {
        toast.add({ severity: 'success', summary: 'Success', detail: flash.success, life: 4000 });
    }
    if (flash.error) {
        toast.add({ severity: 'error', summary: 'Error', detail: flash.error, life: 5000 });
    }
    if (flash.info) {
        toast.add({ severity: 'info', summary: 'Info', detail: flash.info, life: 4000 });
    }
});
</script>

<template>
    <div class="flex flex-col h-screen overflow-hidden">
        <!-- Header -->
        <Header />

        <!-- Body -->
        <div class="flex flex-1 px-4 pb-4 overflow-hidden">
        <!-- Sidebar -->
        <div
            class="pe-3 transition-all duration-500 ease-in-out"
            :class="sidebarOpen ? 'w-1/8 min-w-1/8' : 'w-[55px] min-w-[55px]'"
        >
            <Sidebar @toggleSidebar="toggleSidebar" :is-collapsed="!sidebarOpen" />
        </div>

        <!-- Main content area -->
        <div class="flex flex-col flex-1 bg-white py-4 px-5 rounded-xl border border-neutral-100 overflow-hidden">
            <Breadcrumb :model="breadcrumbs" class="px-0! py-0! bg-transparent border-none" v-if="breadcrumbs.length > 1">
                <template #item="{ item }">
                    <Link v-if="item.label === 'Dashboard'" :href="item.url" class="text-neutral-400 hover:text-neutral-950 transition-colors">
                        <span class="pi pi-home text-xs!"></span>
                    </Link>
                    <Link v-else-if="item.url" :href="item.url" class="text-xs! uppercase tracking-widest font-bold text-neutral-400 hover:text-primary-600 transition-colors">
                        {{ item.label }}
                    </Link>
                    <span v-else class="text-xs! uppercase tracking-widest font-bold text-neutral-900">
                        {{ item.label }}
                    </span>
                </template>
                <template #separator>
                    <span class="text-neutral-200 mx-2">/</span>
                </template>
            </Breadcrumb>
            <!-- Page Header (Fixed) -->
            <div class="flex items-center shrink-0 justify-between">
                <div class="flex flex-col my-1">
                    <span class="font-primary text-lg font-bold">{{ props.title }}</span>
                    <span class="text-xs text-neutral-500">{{ props.summary }}</span>
                </div>
            <div>
                <slot name="titleField"></slot>
            </div>
            </div>

            <!-- Scrollable Content -->
            <ScrollPanel class="mt-4 flex-1 overflow-y-auto font-inter" :dt="{ bar: { background: '{primary.color}' } }">
            <slot />
            </ScrollPanel>

            <!-- Message Toast -->
            <Toast position="bottom-right" />
        </div>
        </div>
    </div>
</template>