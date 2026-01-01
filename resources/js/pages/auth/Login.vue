<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { Button } from 'primevue';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import ToggleSwitch from 'primevue/toggleswitch';

const loginForm = useForm({
    email: '',
    password: '',
    remember: false,
});

defineProps({
    status: String,
    canResetPassword: Boolean,
});
</script>

<template>
    <div class="flex h-screen overflow-hidden font-inter bg-white select-none">
        <div class="w-1/2 bg-neutral-950 p-20 flex flex-col justify-between border-r border-neutral-800">
            <div>
                <div class="text-primary-500 font-mono text-xs uppercase tracking-[0.4em] mb-4">System Access</div>
                <Link href="/" class="inline-flex text-8xl font-bold font-chakra uppercase tracking-tighter">
                    <span class="text-white">Logi</span>
                    <span class="text-primary-600">sync</span>
                </Link>
                <p class="text-neutral-500 mt-6 max-w-sm font-light leading-relaxed">
                    Synchronizing logic designers through a standardized training management ecosystem.
                </p>
            </div>

            <div class="space-y-4">
                <div>
                    <Link :href="route('get-started')" class="text-primary-500 hover:text-white transition">Get started</Link>
                </div>
                <div class="h-px w-12 bg-neutral-700"></div>
                <p class="text-[10px] text-neutral-600 uppercase tracking-widest font-mono">Build 2025.0.1</p>
            </div>
        </div>

        <div class="w-1/2 flex items-center justify-center bg-white">
            <form class="w-full max-w-md p-12">
                <div class="mb-12">
                    <h1 class="text-4xl font-work-sans font-light tracking-tight text-neutral-900">Sign In</h1>
                    <p class="text-neutral-400 text-sm mt-2">Enter credentials to synchronize session.</p>
                </div>

                <div class="mb-8 text-xs font-mono border border-primary-900 bg-primary-950/5 p-4 flex items-center gap-x-3 text-primary-800" v-if="status">
                    <span class="pi pi-info-circle text-primary-600"></span>
                    <span>{{ status }}</span>
                </div>

                <div class="space-y-2 mb-6">
                    <label class="text-[10px] uppercase tracking-[0.2em] font-bold text-neutral-400">Email</label>
                    <InputText 
                        type="text" 
                        v-model="loginForm.email" 
                        class="w-full bg-white! border-neutral-200! rounded-none! focus:border-primary-600! shadow-none!" 
                        placeholder="name@ulkasemi.com"
                    />
                    <div class="text-red-500 text-xs font-mono flex items-center gap-x-2 mt-1" v-if="loginForm.errors.email">
                        <span class="pi pi-exclamation-triangle"></span>
                        <span>{{ loginForm.errors.email }}</span>
                    </div>
                </div>

                <div class="space-y-2 mb-6">
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] uppercase tracking-[0.2em] font-bold text-neutral-400">Password</label>
                        <Link v-if="canResetPassword" :href="route('password.request')" class="text-[10px] uppercase tracking-widest text-neutral-400 hover:text-primary-600 transition-colors">
                            Lost Key?
                        </Link>
                    </div>
                    <Password 
                        v-model="loginForm.password" 
                        toggleMask 
                        fluid 
                        :feedback="false"
                        class="w-full"
                        :pt="{
                            pcInputText: { class: 'rounded-none! border-neutral-200! bg-white! focus:border-primary-600! shadow-none!' }
                        }"
                        placeholder="••••••••"
                    />
                    <div class="text-red-500 text-xs font-mono flex items-center gap-x-2 mt-1" v-if="loginForm.errors.password">
                        <span class="pi pi-exclamation-triangle"></span>
                        <span>{{ loginForm.errors.password }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-10 pb-6 border-b border-neutral-100">
                    <span class="text-xs uppercase tracking-widest text-neutral-500 font-medium">Keep session active</span>
                    <ToggleSwitch v-model="loginForm.remember" class="scale-75!" />
                </div>
                
                <Button
                    label="Sign In"
                    class="w-full bg-neutral-900! border-none! rounded-none! py-4! text-xs! uppercase! tracking-[0.3em]! hover:bg-primary-600! transition-all"
                    @click.prevent="loginForm.post(route('login.store'))"
                    :loading="loginForm.processing"
                />

                <div class="mt-8 text-center">
                    <Link :href="route('register')" class="text-xs uppercase tracking-widest text-neutral-400 hover:text-neutral-900 transition-all">
                        Register into system <span class="ml-2">→</span>
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>

<style>
/* Override PrimeVue globals to match the vibe if not using Pass-Through (pt) */
.p-inputtext:enabled:focus {
    box-shadow: none !important;
}
</style>