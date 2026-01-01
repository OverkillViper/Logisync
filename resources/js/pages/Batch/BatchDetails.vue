<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import EmployeeAvatar from '../Employee/EmployeeAvatar.vue';

defineProps({
    batch: Object
})
</script>

<template>
    <AppLayout title="Batch Intelligence" summary="Detailed breakdown of training personnel">
        <div class="xl:w-4/5 2xl:w-3/4 mx-auto mt-6">
            
            <div class="bg-white border border-neutral-200 p-8 rounded-sm shadow-sm shadow-neutral-100/20 mb-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="text-[10px] font-mono text-neutral-400 uppercase tracking-[0.3em] mb-2">Batch Name</div>
                        <h1 class="text-4xl font-chakra font-bold text-neutral-900 tracking-tighter uppercase">
                            {{ batch.name }}
                        </h1>
                    </div>

                    <div class="flex items-center gap-x-6">
                        <div class="flex items-center gap-x-4 px-6 py-3 bg-neutral-100 rounded-lg border border-neutral-200">
                            <div class="text-2xl font-mono font-bold text-neutral-900">{{ batch.trainers.length.toString().padStart(2, '0') }}</div>
                            <div class="flex flex-col leading-none">
                                <span class="text-[10px] uppercase tracking-widest text-neutral-400 font-bold">Assigned</span>
                                <span class="text-xs font-bold text-neutral-700 uppercase">Trainers</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-x-4 px-6 py-3 bg-neutral-900 rounded-lg border border-neutral-800 shadow-lg">
                            <div class="text-2xl font-mono font-bold text-white">{{ batch.trainees.length.toString().padStart(2, '0') }}</div>
                            <div class="flex flex-col leading-none">
                                <span class="text-[10px] uppercase tracking-widest text-neutral-500 font-bold">Assigned</span>
                                <span class="text-xs font-bold text-white uppercase">Trainees</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex items-center gap-x-4">
                    <h2 class="text-xs font-bold uppercase  text-neutral-400 whitespace-nowrap">Trainees in batch</h2>
                    <div class="h-[1px] w-full bg-neutral-100"></div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" v-if="batch.trainees.length">
                    <Link
                        v-for="trainee in batch.trainees"
                        :key="trainee.id"
                        :href="route('employees.show', { employee: trainee.user.id })"
                        class="group flex items-center bg-white border border-neutral-100 rounded-md p-3 hover:border-neutral-900 hover:shadow-xl hover:shadow-neutral-900/5 transition-all duration-300"
                    >
                        <div class="relative shrink-0">
                            <EmployeeAvatar
                                :image="trainee.profile_picture"
                                :name="trainee.user.name"
                                class="h-10 w-10 rounded-lg object-cover grayscale group-hover:grayscale-0 transition-all duration-500 border border-neutral-50"
                            />
                        </div>
                        
                        <div class="flex flex-col ms-4 grow overflow-hidden">
                            <span class="text-xs font-bold text-neutral-900 uppercase tracking-tight truncate transition-colors group-hover:text-primary-600">
                                {{ trainee.user.name }}
                            </span>
                            <span class="text-[10px] text-neutral-700 tracking-tighter mt-0.5">
                                {{ trainee.user.email}}
                            </span>
                        </div>

                        <div class="opacity-0 group-hover:opacity-100 transition-opacity px-2">
                            <i class="pi pi-arrow-up-right text-neutral-300 text-[10px]"></i>
                        </div>
                    </Link>
                </div>

                <div v-else class="flex flex-col items-center justify-center py-20 border-2 border-dashed border-neutral-100 rounded-[2rem]">
                    <span class="material-icons-outlined text-neutral-200 text-5xl mb-4">person_off</span>
                    <p class="text-xs font-bold uppercase tracking-widest text-neutral-300 italic">No trainees synchronized with this batch</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>