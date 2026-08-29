<script setup>
defineProps({
  size: {
    type: String,
    default: 'md' // 'xs', 'sm', 'md', 'lg', 'xl'
  }
});
</script>

<template>
  <div class="relative flex items-center justify-center shrink-0">
    <!-- Outer squircle with purple-indigo gradient matching user reference -->
    <div 
      class="bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/25 border border-indigo-400/30 shrink-0"
      :class="{
        'w-7 h-7 rounded-lg p-1.25': size === 'xs',
        'w-8 h-8 rounded-xl p-1.5': size === 'sm',
        'w-10 h-10 rounded-2xl p-2': size === 'md',
        'w-12 h-12 rounded-2xl p-2.5': size === 'lg',
        'w-14 h-14 rounded-2xl p-3': size === 'xl',
      }"
    >
      <!-- 3 Concentric Rings Target/Iris Brand Icon -->
      <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
        <!-- Outer Ring -->
        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" />
        <!-- Middle Ring -->
        <circle cx="12" cy="12" r="5.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" />
        <!-- Center Ring / Dot -->
        <circle cx="12" cy="12" r="2" fill="currentColor" stroke="none" />
      </svg>
    </div>
  </div>
</template>
