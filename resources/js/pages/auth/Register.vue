<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { Button } from 'primevue';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';

const registerForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

// Helper for form submission
const submit = () => {
    registerForm.post(route('register.store'), {
        onFinish: () => registerForm.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div class="flex h-screen overflow-hidden font-inter bg-white select-none">
        <div class="w-1/2 bg-neutral-950 p-20 flex flex-col justify-between border-r border-neutral-800">
            <div>
                <div class="text-primary-500 font-mono text-xs uppercase tracking-[0.4em] mb-4">Request Access</div>
                <Link href="/" class="inline-flex text-8xl font-bold font-chakra uppercase tracking-tighter">
                    <span class="text-white">Logi</span>
                    <span class="text-primary-600">sync</span>
                </Link>
                <p class="text-neutral-500 mt-6 max-w-sm font-light leading-relaxed">
                    Join the ecosystem. Standardizing evaluation, attendance, and project lifecycles for the next generation of logic designers.
                </p>
            </div>

            <div class="space-y-4">
                <div class="h-px w-12 bg-neutral-700"></div>
                <p class="text-[10px] text-neutral-600 uppercase tracking-widest font-mono">Build 2025.0.1 // Registration</p>
            </div>
        </div>

        <div class="w-1/2 flex items-center justify-center bg-white overflow-y-auto">
            <form @submit.prevent="submit" class="w-full max-w-md p-12">
                <div class="mb-10">
                    <h1 class="text-4xl font-work-sans font-light tracking-tight text-neutral-900">Sign Up</h1>
                    <p class="text-neutral-400 text-sm mt-2">Enter your details to begin.</p>
                </div>

                <div class="space-y-2 mb-5">
                    <label class="text-[10px] uppercase tracking-[0.2em] font-bold text-neutral-400">Full Name</label>
                    <InputText 
                        type="text"
                        v-model="registerForm.name" 
                        class="w-full bg-white! border-neutral-200! rounded-none! focus:border-primary-600! shadow-none!" 
                        placeholder="Enter your full name"
                    />
                    <div class="text-red-500 text-xs font-mono flex items-center gap-x-2 mt-1" v-if="registerForm.errors.name">
                        <span class="pi pi-exclamation-triangle text-[10px]!"></span>
                        <span>{{ registerForm.errors.name }}</span>
                    </div>
                </div>

                <div class="space-y-2 mb-5">
                    <label class="text-[10px] uppercase tracking-[0.2em] font-bold text-neutral-400">Corporate Email</label>
                    <InputText 
                        type="text" 
                        v-model="registerForm.email" 
                        class="w-full bg-white! border-neutral-200! rounded-none! focus:border-primary-600! shadow-none!" 
                        placeholder="name@logisync.com"
                    />
                    <div class="text-red-500 text-xs font-mono flex items-center gap-x-2 mt-1" v-if="registerForm.errors.email">
                        <span class="pi pi-exclamation-triangle text-[10px]!"></span>
                        <span>{{ registerForm.errors.email }}</span>
                    </div>
                </div>

                <div class="space-y-2 mb-5">
                    <label class="text-[10px] uppercase tracking-[0.2em] font-bold text-neutral-400">Define Access Key</label>
                    <Password 
                        v-model="registerForm.password" 
                        toggleMask 
                        fluid 
                        :feedback="false"
                        class="w-full"
                        :pt="{
                            pcInputText: { class: '!rounded-none !border-neutral-200 !bg-white focus:!border-primary-600 !shadow-none' }
                        }"
                        placeholder="Minimum 8 characters"
                    />
                    <div class="text-red-500 text-xs font-mono flex items-center gap-x-2 mt-1" v-if="registerForm.errors.password">
                        <span class="pi pi-exclamation-triangle text-[10px]!"></span>
                        <span>{{ registerForm.errors.password }}</span>
                    </div>
                </div>

                <div class="space-y-2 mb-10">
                    <label class="text-[10px] uppercase tracking-[0.2em] font-bold text-neutral-400">Verify Access Key</label>
                    <Password 
                        v-model="registerForm.password_confirmation" 
                        toggleMask 
                        fluid 
                        :feedback="false"
                        class="w-full"
                        :pt="{
                            pcInputText: { class: '!rounded-none !border-neutral-200 !bg-white focus:!border-primary-600 !shadow-none' }
                        }"
                        placeholder="Repeat access key"
                    />
                    <div class="text-red-500 text-xs font-mono flex items-center gap-x-2 mt-1" v-if="registerForm.errors.password_confirmation">
                        <span class="pi pi-exclamation-triangle text-[10px]!"></span>
                        <span>{{ registerForm.errors.password_confirmation }}</span>
                    </div>
                </div>
                
                <Button
                    label="Create Account"
                    class="w-full bg-neutral-900! border-none! rounded-none! py-4! text-xs! uppercase! tracking-[0.3em] hover:bg-primary-600 transition-all"
                    type="submit"
                    :loading="registerForm.processing"
                />

                <div class="mt-8 text-center pt-6 border-t border-neutral-100">
                    <Link :href="route('login')" class="text-xs uppercase tracking-widest text-neutral-400 hover:text-neutral-900 transition-all">
                        Already Synchronized? <span class="text-neutral-900 font-bold ml-1">Sign In</span>
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>