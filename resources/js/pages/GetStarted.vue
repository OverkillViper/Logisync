<script setup>
import AnimatedMouse from './AnimatedMouse.vue';
import { Link } from '@inertiajs/vue3';

const feature_summary = [
    { icon: 'assignment', label: 'Project Management' },
    { icon: 'bar_chart', label: 'Trainee Evaluation' },
    { icon: 'punch_clock', label: 'Trainee Attendance' },
    { icon: 'domain_disabled', label: 'Leave History' },
];

const roles = [
    { title: 'Admin', desc: 'Complete architectural oversight. Manage global settings and evaluation criteria.' },
    { title: 'Trainer', desc: 'Moderate operational access. Lead projects and evaluate trainee performance.' },
    { title: 'Trainee', desc: 'Focused interface. Punch-in, track tasks, and view personal growth logs.' }
];
</script>

<template>
    <div class="bg-white flex min-h-screen overflow-hidden font-inter flex-col items-center select-none border-b border-neutral-100">
        <div class="mt-10 uppercase text-xs tracking-[0.5em] text-neutral-400 font-medium">Introducing</div>

        <div class="font-chakra text-8xl font-bold uppercase my-2 tracking-tighter">
            <span class="text-neutral-900">Logi</span>
            <span class="text-primary-600">Sync</span>
        </div>

        <div class="text-neutral-500 text-sm font-light tracking-widest uppercase">
            Synchronizing Logic Designers
        </div>

        <div class="w-4/5 md:w-3/5 mt-12 border border-neutral-200 p-2 bg-neutral-50">
            <img src="/images/intro.png" alt="App Preview" class="grayscale hover:grayscale-0 transition-all duration-700">
        </div>

        <div class="mt-12 mb-10">
            <AnimatedMouse/>
        </div>
    </div>

    <div class="bg-neutral-950 py-20 px-10 flex flex-col items-center">
        <span class="text-white mx-auto font-work-sans text-3xl font-light tracking-tight mb-16">
            A <span class="italic border-b border-primary-500">training management</span> ecosystem.
        </span>

        <div class="grid grid-cols-2 md:grid-cols-4 w-full max-w-5xl border border-neutral-800">
            <div v-for="item in feature_summary" :key="item.label" 
                 class="flex flex-col border-r border-b md:border-b-0 last:border-r-0 border-neutral-800 text-white items-center justify-center p-10 hover:bg-neutral-900 transition-colors group">
                <span class="material-icons-outlined mb-4 text-neutral-500 group-hover:text-primary-400 transition-colors" style="font-size: 2.5rem;">{{ item.icon }}</span>
                <div class="text-[10px] uppercase tracking-widest text-neutral-400 text-center">{{ item.label }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white py-32 px-10 font-inter">
        <div class="max-w-6xl mx-auto">
            
            <div class="mb-32">
                <h2 class="text-xs uppercase tracking-[0.3em] text-neutral-400 mb-12 text-center">Access Control Hierarchy</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 border border-neutral-200">
                    <div v-for="(role, index) in roles" :key="role.title" 
                         class="p-10 border-b md:border-b-0 md:border-r last:border-0 border-neutral-200">
                        <span class="text-[10px] font-mono text-primary-600 font-bold uppercase tracking-tighter">Level 0{{ index + 1 }}</span>
                        <h3 class="text-2xl font-semibold mt-4 mb-3 tracking-tight text-neutral-900">{{ role.title }}</h3>
                        <p class="text-neutral-500 text-sm leading-relaxed">{{ role.desc }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-20">
                
                <section class="space-y-8">
                    <div class="h-[1px] w-16 bg-primary-600"></div>
                    <h3 class="text-4xl font-light font-work-sans">Project Governance</h3>
                    <div class="space-y-6">
                        <div class="group">
                            <h4 class="text-sm font-bold uppercase tracking-wider text-neutral-900">Multi-Stage Tracking</h4>
                            <p class="text-neutral-500 text-sm mt-1 leading-relaxed">Granular visibility into project stages. Keep stakeholders aligned with real-time status updates.</p>
                        </div>
                        <div class="group">
                            <h4 class="text-sm font-bold uppercase tracking-wider text-neutral-900">Extension Protocols</h4>
                            <p class="text-neutral-500 text-sm mt-1">Formal request system for deadline adjustments with integrated comment threads for justification.</p>
                        </div>
                    </div>
                </section>

                <section class="flex flex-col gap-12">
                    <div class="bg-neutral-50 p-8 border border-neutral-200 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 font-mono text-[10px] text-neutral-300 uppercase tracking-tighter">System Clock</div>
                        <h3 class="text-lg font-semibold mb-4 text-neutral-900">Attendance Sync</h3>
                        <div class="flex items-baseline gap-2 mb-4">
                            <span class="text-3xl font-mono tracking-tighter text-primary-700">08:30</span>
                            <span class="text-xs text-neutral-400 uppercase tracking-widest font-bold">Cut-off</span>
                        </div>
                        <p class="text-xs text-neutral-500 leading-relaxed mb-4">
                            Trainee punch-ins are logged against the threshold. Late marks are automatically generated post-08:30 AM for historical performance auditing.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4 text-neutral-900">Performance Evaluation</h3>
                        <p class="text-sm text-neutral-500 mb-6">Trainers evaluate trainees based on strict admin-defined criteria, ensuring objective growth tracking.</p>
                        <div class="flex gap-4">
                            <div class="px-4 py-2 border border-neutral-200 text-[10px] uppercase tracking-widest font-bold text-neutral-600">Standardized Criteria</div>
                            <div class="px-4 py-2 border border-neutral-200 text-[10px] uppercase tracking-widest font-bold text-neutral-600">Qualitative Feedback</div>
                        </div>
                    </div>
                </section>

            </div>

            <Link href="/" class="group flex flex-col items-center gap-4 no-underline mt-10">
                <span class="text-[10px] uppercase tracking-[0.4em] text-neutral-400 group-hover:text-primary-600 transition-colors">Return to Dashboard</span>
                <div class="w-12 h-12 rounded-full border border-neutral-200 flex items-center justify-center group-hover:border-primary-600 transition-all group-hover:scale-110">
                    <span class="material-icons-outlined text-neutral-400 group-hover:text-primary-600">arrow_forward</span>
                </div>
            </Link>
        </div>
    </div>
</template>

<style scoped>
/* Ensure fonts are imported in your main CSS or index.html
.font-chakra { font-family: 'Chakra Petch', sans-serif; }
.font-work-sans { font-family: 'Work Sans', sans-serif; }
.font-inter { font-family: 'Inter', sans-serif; } */
</style>