<template>
  <input
    type="text"
    :value="displayValue"
    @input="onInput"
    class="text-center w-full focus:outline-0"
    :class="displayValue > 0 ? 'bg-primary-900 text-white border-e-2 border-primary-900' : ''"
    :disabled="disabled"
  />
</template>

<script setup>
import { ref, computed, watch } from 'vue'

// ✅ Props
const props = defineProps({
    modelValue: {
      type: [Number, String],
      default: ''
    },
    max: {
      type: Number,
      default: Infinity
    },
    convertFraction: {
      type: Boolean,
      default: true
    },
    disabled: {
      type: Boolean,
      default: false
    }
})

// ✅ Emits
const emit = defineEmits(['update:modelValue'])

const internalValue = ref(props.modelValue ?? '')

watch(() => props.modelValue, (val) => {
  if (val !== internalValue.value) internalValue.value = val
})

const displayValue = computed(() => internalValue.value > 0 ? internalValue.value : '')

// ✅ Handle Input
const onInput = (e) => {
  let val = e.target.value.trim()

  // Allow only digits, one dot, one slash
  val = val.replace(/[^0-9./]/g, '')

  // Prevent multiple dots or slashes
  if ((val.match(/\./g) || []).length > 1) val = val.replace(/\.+$/, '')
  if ((val.match(/\//g) || []).length > 1) val = val.replace(/\/+$/, '')

  // ✅ If valid fraction (like 3/4)
  if (/^\d+\/\d+$/.test(val) && props.convertFraction) {
    const [num, den] = val.split('/').map(Number)
    if (den && den !== 0) {
      val = (num / den).toFixed(3).replace(/\.?0+$/, '') // convert to decimal
    } else {
      val = ''
    }
  }

  // ✅ Convert to number
  let numVal = parseFloat(val)
  if (isNaN(numVal)) numVal = 0

  // ✅ Enforce max immediately
  if (numVal > props.max) {
    numVal = props.max
    val = String(props.max)
  }

  // ✅ Update model and UI
  internalValue.value = val
  emit('update:modelValue', numVal)
}
</script>

<style scoped>
input {
  text-align: center;
}
</style>