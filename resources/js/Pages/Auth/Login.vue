<script setup>
import { ref, onMounted } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AppLogo from '@/Components/AppLogo.vue';
import { 
  LogIn, 
  Eye, 
  EyeOff, 
  Check, 
  ShieldCheck,
  Sun,
  Moon
} from 'lucide-vue-next';

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const showPassword = ref(false);
const isDark = ref(false);

onMounted(() => {
  if (typeof window !== 'undefined') {
    isDark.value = document.documentElement.classList.contains('dark') || 
      localStorage.getItem('theme') === 'dark' || 
      (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
    
    if (isDark.value) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  }
});

function toggleTheme() {
  isDark.value = !isDark.value;
  if (isDark.value) {
    document.documentElement.classList.add('dark');
    localStorage.setItem('theme', 'dark');
  } else {
    document.documentElement.classList.remove('dark');
    localStorage.setItem('theme', 'light');
  }
}

function submit() {
  form.post('/login');
}
</script>

<template>
  <Head title="Masuk - DevCalc Quotation System" />

  <div class="min-h-screen relative bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 flex items-center justify-center p-4 sm:p-6 lg:p-8 font-sans antialiased transition-colors duration-200 selection:bg-blue-500 selection:text-white">
    
    <!-- Top-Right Theme Toggle Button -->
    <div class="absolute top-4 right-4 z-20">
      <button 
        type="button" 
        @click="toggleTheme" 
        class="w-10 h-10 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white shadow-sm hover:shadow transition-all flex items-center justify-center cursor-pointer"
        :title="isDark ? 'Ganti ke Mode Terang' : 'Ganti ke Mode Gelap'"
      >
        <Sun v-if="isDark" class="w-4 h-4 text-amber-400" />
        <Moon v-else class="w-4 h-4 text-slate-600" />
      </button>
    </div>

    <!-- Main Centered 2-Column Split Container -->
    <div class="w-full max-w-4xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl shadow-xl dark:shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2 transition-colors duration-200">
      
      <!-- LEFT COLUMN: Blueprint & Waves Tech Banner -->
      <div class="relative p-8 sm:p-10 flex flex-col justify-between bg-gradient-to-b from-blue-600 via-indigo-600 to-indigo-800 dark:from-blue-600 dark:via-indigo-800 dark:to-indigo-950 text-white overflow-hidden min-h-[460px] md:min-h-[520px]">
        
        <!-- Blueprint Grid Pattern Overlay -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.12)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.12)_1px,transparent_1px)] bg-[size:2.5rem_2.5rem] pointer-events-none"></div>

        <!-- Top Wave Vector Shape -->
        <div class="absolute -top-10 -left-10 right-0 h-40 opacity-30 pointer-events-none">
          <svg viewBox="0 0 500 150" preserveAspectRatio="none" class="w-full h-full">
            <path d="M0.00,49.98 C149.99,150.00 349.81,-49.98 500.00,49.98 L500.00,0.00 L0.00,0.00 Z" fill="#ffffff"></path>
          </svg>
        </div>

        <!-- Bottom Wave Vector Shapes -->
        <div class="absolute -bottom-6 left-0 right-0 h-48 opacity-25 pointer-events-none">
          <svg viewBox="0 0 500 150" preserveAspectRatio="none" class="w-full h-full">
            <path d="M0.00,49.98 C180.20,130.40 320.10,10.20 500.00,60.80 L500.00,150.00 L0.00,150.00 Z" fill="#ffffff"></path>
          </svg>
        </div>
        <div class="absolute -bottom-2 -left-6 right-0 h-36 opacity-35 pointer-events-none">
          <svg viewBox="0 0 500 150" preserveAspectRatio="none" class="w-full h-full">
            <path d="M0.00,60.00 C150.00,10.00 350.00,110.00 500.00,40.00 L500.00,150.00 L0.00,150.00 Z" fill="#1e1b4b"></path>
          </svg>
        </div>

        <!-- Tech Graphic Circles & Node Lines -->
        <div class="absolute top-12 right-12 w-14 h-14 rounded-full border border-white/25 pointer-events-none flex items-center justify-center">
          <div class="w-6 h-6 rounded-full bg-white/20"></div>
        </div>
        <div class="absolute top-28 left-8 w-8 h-8 rounded-full border border-white/20 pointer-events-none flex items-center justify-center">
          <div class="w-3 h-3 rounded-full bg-white/30"></div>
        </div>
        <div class="absolute bottom-24 right-16 w-10 h-10 rounded-full border border-white/25 pointer-events-none flex items-center justify-center">
          <div class="w-4 h-4 rounded-full bg-white/20"></div>
        </div>
        
        <!-- Diagonal Node Connectors -->
        <div class="absolute top-16 left-28 w-24 h-px bg-white/20 rotate-45 pointer-events-none"></div>
        <div class="absolute bottom-28 left-12 w-28 h-px bg-white/20 -rotate-45 pointer-events-none"></div>

        <!-- Header: Company Name & Brand -->
        <div class="relative z-10 flex items-center gap-2.5">
          <AppLogo size="sm" />
          <span class="text-sm font-black tracking-widest uppercase text-white/95">DevCalc</span>
        </div>

        <!-- Center: Welcome Back Content Block -->
        <div class="relative z-10 text-center my-auto py-8 space-y-2">
          <p class="text-xs font-medium text-blue-100/90 tracking-wide">
            Internal Quotation System
          </p>
          <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white uppercase drop-shadow-xs">
            Welcome Back
          </h2>
          
          <!-- Center Divider Pill -->
          <div class="w-8 h-1 bg-white/80 rounded-full mx-auto my-3"></div>

          <p class="text-xs text-blue-100/80 leading-relaxed max-w-xs mx-auto pt-1">
            Kalkulasi estimasi biaya software secara presisi, kelola model penagihan, dan terbitkan nota penawaran resmi.
          </p>
        </div>

        <!-- Footer / Version -->
        <div class="relative z-10 flex items-center justify-between text-[11px] text-blue-100/70">
          <span>&copy; 2026 DevCalc</span>
          <span>v2.4 Enterprise</span>
        </div>

      </div>

      <!-- RIGHT COLUMN: Login Account Form -->
      <div class="p-8 sm:p-10 flex flex-col justify-between bg-white dark:bg-slate-900 transition-colors duration-200">
        
        <div class="space-y-6 my-auto">
          
          <!-- Form Header -->
          <div class="text-center space-y-1.5">
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-blue-600 dark:text-blue-400">
              Login Account
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-xs mx-auto leading-relaxed">
              Masukkan email dan kata sandi kredensial Anda untuk mengakses portal estimator.
            </p>
          </div>

          <!-- Form Fields -->
          <form @submit.prevent="submit" class="space-y-4 pt-2">
            
            <!-- Email Input with Signature Left Blue Accent Bar -->
            <div class="space-y-1">
              <div class="relative flex items-center bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg overflow-hidden focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition">
                <div class="w-1.5 self-stretch bg-blue-600 dark:bg-blue-500 shrink-0"></div>
                <input
                  id="email"
                  name="email"
                  v-model="form.email"
                  type="email"
                  autocomplete="username"
                  required
                  placeholder="Email ID"
                  class="w-full px-3.5 py-2.5 bg-transparent text-slate-900 dark:text-white text-xs font-medium outline-none placeholder-slate-400 dark:placeholder-slate-500"
                />
              </div>
              <span v-if="form.errors.email" class="text-[11px] text-rose-500 dark:text-rose-400 font-medium block pl-2">
                {{ form.errors.email }}
              </span>
            </div>

            <!-- Password Input with Signature Left Blue Accent Bar -->
            <div class="space-y-1">
              <div class="relative flex items-center bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg overflow-hidden focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition">
                <div class="w-1.5 self-stretch bg-blue-600 dark:bg-blue-500 shrink-0"></div>
                <input
                  id="password"
                  name="password"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  autocomplete="current-password"
                  required
                  placeholder="Password"
                  class="w-full pl-3.5 pr-10 py-2.5 bg-transparent text-slate-900 dark:text-white text-xs font-medium outline-none placeholder-slate-400 dark:placeholder-slate-500"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-3 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition cursor-pointer p-1"
                  :aria-label="showPassword ? 'Sembunyikan sandi' : 'Tampilkan sandi'"
                >
                  <Eye v-if="!showPassword" class="w-4 h-4" />
                  <EyeOff v-else class="w-4 h-4" />
                </button>
              </div>
              <span v-if="form.errors.password" class="text-[11px] text-rose-500 dark:text-rose-400 font-medium block pl-2">
                {{ form.errors.password }}
              </span>
            </div>

            <!-- Keep me signed in & Info row -->
            <div class="flex items-center justify-between text-xs pt-1">
              <label class="flex items-center gap-2 cursor-pointer select-none" @click.prevent="form.remember = !form.remember">
                <div 
                  class="w-4 h-4 rounded border transition-all duration-150 flex items-center justify-center cursor-pointer"
                  :class="form.remember 
                    ? 'bg-blue-600 border-blue-600 text-white' 
                    : 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950'"
                >
                  <Check v-if="form.remember" class="w-3 h-3 stroke-[3]" />
                </div>
                <span class="text-xs text-slate-600 dark:text-slate-300 font-normal">Keep me signed in</span>
              </label>

              <span class="text-[11px] text-slate-400 dark:text-slate-500">
                Internal Portal
              </span>
            </div>

            <!-- Full-Width Rounded Pill Action Button -->
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full mt-3 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs tracking-wider uppercase rounded-full shadow-lg shadow-blue-600/20 dark:shadow-blue-600/30 transition-all duration-150 flex items-center justify-center gap-2 disabled:opacity-50 cursor-pointer active:scale-[0.99]"
            >
              <LogIn v-if="!form.processing" class="w-3.5 h-3.5" />
              <svg v-else class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ form.processing ? 'Verifying...' : 'Sign In' }}</span>
            </button>
          </form>

        </div>

        <!-- Security Footer -->
        <div class="pt-6 mt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-center gap-1.5 text-slate-400 dark:text-slate-500 text-[11px]">
          <ShieldCheck class="w-3.5 h-3.5 text-slate-400 dark:text-slate-400" />
          <span>Encrypted Session & Protected Gateway</span>
        </div>

      </div>

    </div>

  </div>
</template>
