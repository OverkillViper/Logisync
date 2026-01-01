<script setup>
defineProps({
    leaves: Array,
});

function formatDate(input) {
    const date = new Date(input);

    const day = date.getDate().toString();
    const month = date.toLocaleString("en-GB", { month: "long" });
    const year = date.getFullYear();

    return `${day.length > 1 ? day : '0' + day} ${month}, ${year}`;
}

function getDayName(input) {
    const date = new Date(input);
    return date.toLocaleString("en-GB", { weekday: "long" });
}

</script>

<template>
    <div>
        <div class="label">Employee Leave History</div>

        <div class="mt-4">
            <div class="text-sm">Total days on leave</div>
            <div class="text-lg">{{ leaves.length }} Days</div>
        </div>

        <div class="flex flex-col gap-4 mt-4">
            <div v-for="leave in leaves" class="border border-primary-300 rounded-md p-3 w-1/3">
                <div class="text-sm text-primary-700 font-medium">{{ formatDate(leave.date) }}</div>
                <div class="text-sm">{{ getDayName(leave.date) }}</div>
            </div>
        </div>
    </div>
</template>