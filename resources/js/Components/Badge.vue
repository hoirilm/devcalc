<script setup>
import { computed } from 'vue';

const props = defineProps({
  variant: {
    type: String,
    default: 'gray'
  }
});

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'primary':
    case 'indigo':
      return 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800';
    case 'success':
    case 'emerald':
      return 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800';
    case 'warning':
    case 'amber':
      return 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800';
    case 'danger':
    case 'rose':
      return 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800';
    case 'info':
    case 'sky':
      return 'bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border-sky-200 dark:border-sky-800';
    default:
      return 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700';
  }
});
</script>

<template>
  <span
    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold border transition duration-150"
    :class="variantClasses"
  >
    <slot />
  </span>
</template>
