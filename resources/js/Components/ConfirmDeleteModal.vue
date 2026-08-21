<script setup>
import { 
  AlertTriangle, 
  Trash2, 
  X 
} from 'lucide-vue-next';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Konfirmasi Hapus'
  },
  message: {
    type: String,
    default: 'Apakah Anda yakin ingin menghapus data ini?'
  },
  itemName: {
    type: String,
    default: ''
  },
  confirmButtonText: {
    type: String,
    default: 'Ya, Hapus Sekarang'
  },
  processing: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['close', 'confirm']);

function close() {
  if (!props.processing) {
    emit('close');
  }
}

function confirm() {
  emit('confirm');
}
</script>

<template>
  <Modal :show="show" @close="close" maxWidth="md">
    <div class="p-6 space-y-6 text-center relative">
      <!-- Close X Button -->
      <button
        @click="close"
        :disabled="processing"
        class="absolute right-4 top-4 p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
      >
        <X class="w-4 h-4" />
      </button>

      <!-- Glowing Rose Icon Container -->
      <div class="w-16 h-16 rounded-3xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/80 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto shadow-xl shadow-rose-500/10">
        <AlertTriangle class="w-8 h-8 stroke-[2.2]" />
      </div>

      <!-- Title & Details -->
      <div class="space-y-2">
        <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">
          {{ title }}
        </h3>
        
        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
          {{ message }}
        </p>

        <div v-if="itemName" class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 text-slate-900 dark:text-white text-xs font-bold break-words">
          "{{ itemName }}"
        </div>
      </div>

      <!-- Warning Note -->
      <div class="p-3 rounded-2xl bg-rose-50/70 dark:bg-rose-950/30 border border-rose-200/80 dark:border-rose-900/40 text-[11px] font-semibold text-rose-700 dark:text-rose-300 flex items-center justify-center gap-2">
        <Trash2 class="w-3.5 h-3.5 shrink-0" />
        <span>Tindakan ini bersifat permanen dan tidak dapat dibatalkan.</span>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-end gap-3 pt-2">
        <button
          type="button"
          @click="close"
          :disabled="processing"
          class="flex-1 py-2.5 px-4 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition cursor-pointer"
        >
          Batal
        </button>

        <button
          type="button"
          @click="confirm"
          :disabled="processing"
          class="flex-1 py-2.5 px-4 rounded-xl text-xs font-extrabold bg-rose-600 hover:bg-rose-500 text-white shadow-lg shadow-rose-600/30 transition cursor-pointer active:scale-98 disabled:opacity-50 flex items-center justify-center gap-2"
        >
          <Trash2 class="w-4 h-4" />
          <span>{{ processing ? 'Menghapus...' : confirmButtonText }}</span>
        </button>
      </div>
    </div>
  </Modal>
</template>
