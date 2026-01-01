<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { Button } from 'primevue';
import InputText from 'primevue/inputtext';

const forgetPasswordForm = useForm({
    email: '',
});
</script>

<template>
    <div class="bg-white flex h-screen overflow-hidden font-inter items-center justify-center select-none">
        <div class="w-full max-w-lg flex flex-col items-center p-8">
            
            <div class="mb-12 flex flex-col items-center">
                <Link :href="route('dashboard')" class="uppercase font-chakra text-4xl tracking-tighter">
                    <span class="text-neutral-400 font-light">Logi</span>
                    <span class="text-neutral-900 font-bold">Sync</span>
                </Link>
                <div class="h-[1px] w-8 bg-primary-600 mt-4"></div>
            </div>

            <form class="w-full border border-neutral-200 p-12 bg-white">
                <div class="mb-10 text-center">
                    <h1 class="text-3xl font-work-sans font-light tracking-tight text-neutral-900">Account Recovery</h1>
                    <p class="text-neutral-500 text-sm mt-3 leading-relaxed">
                        Enter your registered identity to receive an access restoration link.
                    </p>
                </div>

                <div class="space-y-2 mb-8">
                    <label class="text-xs uppercase tracking-[0.2em] font-bold text-neutral-400">Recovery Email</label>
                    <InputText 
                        type="text" 
                        v-model="forgetPasswordForm.email" 
                        class="w-full bg-white border-neutral-200 rounded-none focus:border-primary-600 shadow-none" 
                        placeholder="name@ulkasemi.com"
                    />
                    <div class="text-red-500 text-xs font-mono flex items-center gap-x-2 mt-2" v-if="forgetPasswordForm.errors.email">
                        <span class="pi pi-exclamation-triangle text-xs"></span>
                        <span>{{ forgetPasswordForm.errors.email }}</span>
                    </div>
                </div>
                
                <Button
                    label="Send Recovery Link"
                    class="w-full bg-neutral-900 border-none rounded-none py-4 text-sm! uppercase hover:bg-primary-600 transition-all shadow-none"
                    @click.prevent="forgetPasswordForm.post(route('password.email'))"
                    :loading="forgetPasswordForm.processing"
                />

                <div class="my-8 flex items-center">
                    <div class="flex-grow h-[1px] bg-neutral-100"></div>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-xs text-neutral-400 uppercase tracking-widest">Remembered?</span>
                    <Link :href="route('login')" class="text-xs font-bold uppercase tracking-widest text-neutral-900 hover:text-primary-600 transition-colors">
                        Return to Sign In
                    </Link>
                </div>
            </form>

        </div>
    </div>
</template>

<style>
/* Global override for PrimeVue Focus Ring to keep it monochrome */
.p-inputtext:enabled:focus {
    box-shadow: none !important;
}
</style>