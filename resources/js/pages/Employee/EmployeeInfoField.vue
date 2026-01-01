<script setup>
import { ref, watch } from 'vue';
import InputText from 'primevue/inputtext';
import DatePicker from 'primevue/datepicker';

const props = defineProps({
  label: String,
  modelValue: [String, Date],
  type: {
    type: String,
    default: 'text',
  },
  readonly: {
    type: Boolean,
    default: false,
  },
  editing: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['update:modelValue']);
const localValue = ref(props.modelValue);

watch(
  () => props.modelValue,
  (val) => {
    localValue.value = val;
  }
);

const updateValue = (val) => {
  if (props.type === 'date' && val instanceof Date) {
    const formatted = val.toISOString().split('T')[0]; // "YYYY-MM-DD"
    emit('update:modelValue', formatted);
  } else {
    emit('update:modelValue', val);
  }
};

const formatDate = (dateValue) => {
    if (!dateValue) return '—';

    // Split the string assuming dd-mm-yyyy
    const parts = dateValue.split('-'); 
    if (parts.length !== 3) return 'Invalid Date';

    const [day, month, year] = parts.map(Number);

    // Create a JS Date object
    const date = new Date(year, month - 1, day); // month is 0-indexed
    if (isNaN(date)) return 'Invalid Date';

    return date.toLocaleDateString('en-GB', {
      day: '2-digit',
      month: 'long',
      year: 'numeric',
    }).replace(/\s/g, '-'); // e.g., 12-August-2025
};
</script>

<template>
  <div class="flex flex-col">
    <label class="label ms-3.5">{{ label }}</label>

    <div v-if="editing && !readonly">
      <InputText
        v-if="type === 'text'"
        v-model="localValue"
        @update:modelValue="updateValue"
        class="font-medium px-3.5! py-2! w-75"
      />

      <DatePicker
        v-else-if="type === 'date'"
        v-model="localValue"
        @update:modelValue="updateValue"
        showIcon
        dateFormat="dd-mm-yy"
        class="font-medium -ms-3.5 px-3! py-1!"
      />
    </div>

    <div v-else class="text-gray-800 font-medium ms-3.5 my-2 border border-transparent">
      {{ type === 'date' && props.modelValue ? formatDate(modelValue) : props.modelValue || '&#9888; Not set' }}
    </div>
  </div>
</template>
