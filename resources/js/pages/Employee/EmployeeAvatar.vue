<script setup>

defineProps({
    image: String || null,
    name: String,
    preview: Boolean,
})

</script>

<template>
    <div class="flex items-center justify-center  text-white overflow-hidden border-2 border-primary-900">
        <img v-if="image" :src="preview ? image : `/storage/${image}`" alt="avatar" class="object-fill h-full w-full">
        <div v-else class="flex items-center justify-center select-none font-chakra bg-primary-900 h-full w-full">
            <span class="uppercase font-bold">{{ name[0] }}{{ name[1] === '.' ? name[3] : name[1] }}</span>
        </div>
    </div>
</template>