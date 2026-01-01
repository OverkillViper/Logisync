<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue'

const props = defineProps({
    name      : String ,
    icon      : String ,
    href      : String ,
    collapsed : Boolean
});

const page = usePage()
const isActive = computed(() => {
  const current = page.props.ziggy?.location
    ? route().current()
    : page.url.replace('/', '') // fallback if ziggy not configured

  return route().current(props.href)
})

</script>

<template>
    <Link class="flex items-center" :href="route(href)">
        <!-- <span class="h-8 w-1 select-none rounded-lg text-transparent" :class="isActive ? 'bg-primary-900' : 'bg-transparent'">|</span> -->
        <div class="flex-grow flex items-center gap-x-3 p-3 rounded-lg border border-transparent cursor-pointer transition" :class="isActive ? ( collapsed ? 'bg-primary-900 text-white w-[45px]' : 'bg-primary-900 text-white') : 'bg-transparent text-neutral-400 hover:text-neutral-700'">
            <span class="material-icons-round" style="font-size: 1.2rem;">{{ icon }}</span>
            <span class="text-sm font-work-sans ms-4">{{ name }}</span>
        </div>
    </Link>
</template>