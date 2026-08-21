<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: [Number, String],
    default: 0
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: '0'
  },
  helperText: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue']);

const formattedValue = computed(() => {
  if (props.modelValue === null || props.modelValue === undefined || props.modelValue === '') return '';
  const num = parseInt(props.modelValue, 10);
  if (isNaN(num)) return '';
  return new Intl.NumberFormat('id-ID').format(num);
});

function onInput(event) {
  const raw = event.target.value.replace(/\D/g, '');
  const val = raw ? parseInt(raw, 10) : 0;
  emit('update:modelValue', val);
}
</script>

<template>
  <div class="space-y-1.5">
    <label v-if="label" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
      {{ label }}
    </label>
    <div class="relative flex items-center rounded-lg shadow-sm">
      <span class="absolute left-3 text-xs font-semibold text-slate-400 dark:text-slate-500 select-none">
        Rp
      </span>
      <input
        type="text"
        :value="formattedValue"
        :placeholder="placeholder"
        :disabled="disabled"
        @input="onInput"
        class="w-full pl-9 pr-3 py-2 text-xs font-semibold bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 disabled:opacity-50"
      />
    </div>
    <p v-if="helperText" class="text-[11px] text-slate-500 dark:text-slate-400">
      {{ helperText }}
    </p>
  </div>
</template>
