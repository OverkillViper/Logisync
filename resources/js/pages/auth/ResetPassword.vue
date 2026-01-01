<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { Button } from 'primevue';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';

const props = defineProps({
    token: String,
    email: String,
});

const resetPasswordForm = useForm({
    email: props.email,
    password: '',
    password_confirmation: '',
    token: props.token,
});

const submit = () => {
    resetPasswordForm.post(route('password.store'), {
        onFinish: () => resetPasswordForm.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div class="flex h-screen overflow-hidden font-inter bg-white select-none">
        <div class="w-1/2 bg-neutral-950 p-20 flex flex-col justify-between border-r border-neutral-800">
            <div>
                <div class="text-primary-500 font-mono text-xs uppercase tracking-[0.4em] mb-4">Security Override</div>
                <Link href="/" class="inline-flex text-8xl font-bold font-chakra uppercase tracking-tighter">
                    <span class="text-white">Logi</span>
                    <span class="text-primary-600">sync</span>
                </Link>
                <p class="text-neutral-500 mt-6 max-w-sm font-light leading-relaxed">
                    Update your access credentials. Ensure your new access key meets the system security requirements.
                </p>
            </div>

            <div class="space-y-4">
                <div class="h-[1px] w-12 bg-neutral-700"></div>
                <p class="text-xs text-neutral-600 uppercase tracking-widest font-mono">Restoration Mode // Token Active</p>
            </div>
        </div>

        <div class="w-1/2 flex items-center justify-center bg-white">
            <form @submit.prevent="submit" class="w-full max-w-md p-12">
                <div class="mb-12">
                    <h1 class="text-4xl font-work-sans font-light tracking-tight text-neutral-900">Reset Access</h1>
                    <p class="text-neutral-400 text-sm mt-2">Initialize new security parameters for your account.</p>
                </div>

                <div class="space-y-2 mb-6">
                    <label class="text-xs uppercase tracking-[0.2em] font-bold text-neutral-400">Account Identity</label>
                    <InputText 
                        type="text" 
                        v-model="resetPasswordForm.email" 
                        class="w-full bg-neutral-50 border-neutral-200 rounded-none cursor-not-allowed opacity-60 text-neutral-500" 
                        placeholder="email@example.com" 
                        :disabled="true"
                    />
                </div>

                <div class="space-y-2 mb-6">
                    <label class="text-xs uppercase tracking-[0.2em] font-bold text-neutral-400">New Access Key</label>
                    <Password 
                        v-model="resetPasswordForm.password" 
                        toggleMask 
                        fluid 
                        :feedback="false"
                        class="w-full"
                        :pt="{
                            pcInputText: { class: 'rounded-none border-neutral-200 bg-white focus:border-primary-600 shadow-none' }
                        }"
                        placeholder="Define new key"
                    />
                    <div class="text-red-500 text-sm font-mono flex items-center gap-x-4 mt-1" v-if="resetPasswordForm.errors.password">
                        <span class="pi pi-exclamation-triangle text-xs"></span>
                        <span>{{ resetPasswordForm.errors.password }}</span>
                    </div>
                </div>

                <div class="space-y-2 mb-10">
                    <label class="text-xs uppercase tracking-[0.2em] font-bold text-neutral-400">Confirm Access Key</label>
                    <Password 
                        v-model="resetPasswordForm.password_confirmation" 
                        toggleMask 
                        fluid 
                        :feedback="false"
                        class="w-full"
                        :pt="{
                            pcInputText: { class: 'rounded-none border-neutral-200 bg-white focus:border-primary-600 shadow-none' }
                        }"
                        placeholder="Re-enter key"
                    />
                    <div class="text-red-500 text-sm font-mono flex items-center gap-x-4 mt-1" v-if="resetPasswordForm.errors.password_confirmation">
                        <span class="pi pi-exclamation-triangle text-xs"></span>
                        <span>{{ resetPasswordForm.errors.password_confirmation }}</span>
                    </div>
                </div>
                
                <Button
                    label="Update Password"
                    class="w-full bg-neutral-900 border-none rounded-none py-4 text-sm! uppercase hover:bg-primary-600 transition-all shadow-none"
                    type="submit"
                    :loading="resetPasswordForm.processing"
                />

                <div class="mt-8 text-center">
                    <Link :href="route('login')" class="text-xs uppercase tracking-widest text-neutral-400 hover:text-neutral-900 transition-all">
                        Cancel and return to <span class="font-bold underline decoration-primary-500 underline-offset-4">Sign In</span>
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>