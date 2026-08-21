<script setup>
import { onMounted, onUnmounted } from 'vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  maxWidth: {
    type: String,
    default: '2xl'
  }
});

const emit = defineEmits(['close']);

function close() {
  emit('close');
}

function handleKeydown(e) {
  if (e.key === 'Escape' && props.show) {
    close();
  }
}

onMounted(() => document.addEventListener('keydown', handleKeydown));
onUnmounted(() => document.removeEventListener('keydown', handleKeydown));
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="ease-out duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="ease-in duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center">
        <!-- Backdrop with Glassmorphic Blur -->
        <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-md transition-opacity" @click="close" />

        <!-- Modal Window -->
        <div
          class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl overflow-hidden transform transition-all sm:w-full max-h-[90vh] flex flex-col border border-slate-200 dark:border-slate-800"
          :class="[
            maxWidth === 'sm' ? 'sm:max-w-sm' : '',
            maxWidth === 'md' ? 'sm:max-w-md' : '',
            maxWidth === 'lg' ? 'sm:max-w-lg' : '',
            maxWidth === 'xl' ? 'sm:max-w-xl' : '',
            maxWidth === '2xl' ? 'sm:max-w-2xl' : '',
            maxWidth === '4xl' ? 'sm:max-w-4xl' : '',
          ]"
        >
          <slot />
        </div>
      </div>
    </Transition>
  </Teleport>
</template>
