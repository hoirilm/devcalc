<script setup>
import { 
  Printer, 
  FilePlus, 
  Trash2,
  ChevronRight
} from 'lucide-vue-next';

const props = defineProps({
  project: Object,
});

const emit = defineEmits(['openAddendum', 'deleteProject']);

function handleAddendum(e) {
  e.stopPropagation();
  emit('openAddendum', props.project);
}

function handleDelete(e) {
  e.stopPropagation();
  emit('deleteProject', props.project.id, props.project.code);
}
</script>

<template>
  <div class="inline-flex items-center justify-end gap-1.5" @click.stop>
    <!-- Cetak PDF Button with Custom Tooltip -->
    <div class="relative group/btn">
      <a
        :href="`/projects/${project.id}/pdf`"
        target="_blank"
        @click.stop
        class="w-8 h-8 rounded-xl bg-slate-100/80 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-500 hover:border-emerald-500 transition-all duration-200 flex items-center justify-center border border-slate-200/80 dark:border-slate-700/80 shadow-sm active:scale-95 cursor-pointer"
      >
        <Printer class="w-4 h-4" />
      </a>
      <!-- Custom Tooltip -->
      <div class="opacity-0 group-hover/btn:opacity-100 transition-all duration-150 pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-0.5 text-[10px] font-bold rounded-md bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 shadow-xl whitespace-nowrap z-50 border border-slate-800 dark:border-slate-200">
        Cetak PDF
      </div>
    </div>

    <!-- Buat Adendum Button (If Subscription or Generated) -->
    <div v-if="project.billing_type === 'subscription' || project.status === 'Generated'" class="relative group/btn">
      <button
        @click="handleAddendum"
        class="w-8 h-8 rounded-xl bg-slate-100/80 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 hover:bg-amber-600 hover:text-white dark:hover:bg-amber-500 hover:border-amber-500 transition-all duration-200 flex items-center justify-center border border-slate-200/80 dark:border-slate-700/80 shadow-sm active:scale-95 cursor-pointer"
      >
        <FilePlus class="w-4 h-4" />
      </button>
      <!-- Custom Tooltip -->
      <div class="opacity-0 group-hover/btn:opacity-100 transition-all duration-150 pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-0.5 text-[10px] font-bold rounded-md bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 shadow-xl whitespace-nowrap z-50 border border-slate-800 dark:border-slate-200">
        Adendum
      </div>
    </div>

    <!-- Hapus Penawaran Button -->
    <div class="relative group/btn">
      <button
        @click="handleDelete"
        class="w-8 h-8 rounded-xl bg-slate-100/80 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 hover:bg-rose-600 hover:text-white dark:hover:bg-rose-500 hover:border-rose-500 transition-all duration-200 flex items-center justify-center border border-slate-200/80 dark:border-slate-700/80 shadow-sm active:scale-95 cursor-pointer"
      >
        <Trash2 class="w-4 h-4" />
      </button>
      <!-- Custom Tooltip -->
      <div class="opacity-0 group-hover/btn:opacity-100 transition-all duration-150 pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-0.5 text-[10px] font-bold rounded-md bg-rose-900 text-rose-100 shadow-xl whitespace-nowrap z-50 border border-rose-800">
        Hapus
      </div>
    </div>
  </div>
</template>
