<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AppLogo from '@/Components/AppLogo.vue';
import { 
  LogIn, 
  Eye, 
  EyeOff, 
  Check, 
  ShieldCheck,
  Sun,
  Moon,
  ArrowRight,
  AlertCircle,
  XCircle,
  Sparkles,
  Calculator,
  Layers,
  FileCheck2
} from 'lucide-vue-next';

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const emailInputRef = ref(null);
const showPassword = ref(false);
const capsLockOn = ref(false);
const isDark = ref(false);
const activeSlide = ref(0);
const isHoveringBanner = ref(false);

const highlights = [
  {
    icon: Calculator,
    tag: 'Estimasi Presisi',
    title: 'Kalkulasi Dinamis',
    desc: 'Hitung biaya proyek secara presisi dengan pengali bobot kompleksitas modul (1.0x - 3.5x).'
  },
  {
    icon: Layers,
    tag: 'Model Fleksibel',
    title: 'Multi-Skema Billing',
    desc: 'Dukungan penuh kontrak Putus (One-off), Berlangganan bulanan/tahunan, dan skema Hybrid.'
  },
  {
    icon: FileCheck2,
    tag: 'Otomasi Dokumen',
    title: 'Ekspor Nota Resmi PDF',
    desc: 'Penerbitan dokumen penawaran resmi berformat PDF lengkap dengan riwayat addendum siap kirim.'
  }
];

const isEmailValid = computed(() => {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email);
});

let slideInterval = null;

onMounted(() => {
  // Autofocus email input on mount
  if (emailInputRef.value) {
    emailInputRef.value.focus();
  }

  // Detect theme
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

  // Auto-rotate left banner slides
  slideInterval = setInterval(() => {
    if (!isHoveringBanner.value) {
      activeSlide.value = (activeSlide.value + 1) % highlights.length;
    }
  }, 5000);
});

onUnmounted(() => {
  if (slideInterval) clearInterval(slideInterval);
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

function checkCapsLock(e) {
  if (e.getModifierState) {
    capsLockOn.value = e.getModifierState('CapsLock');
  }
}

function clearEmail() {
  form.email = '';
  if (emailInputRef.value) {
    emailInputRef.value.focus();
  }
}

function submit() {
  form.post('/login');
}
</script>

<template>
  <Head title="Masuk - DevCalc Quotation System" />

  <div class="min-h-screen relative bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 flex items-center justify-center p-4 sm:p-6 lg:p-8 font-sans antialiased transition-colors duration-300 selection:bg-blue-500 selection:text-white">
    
    <!-- Top-Right Interactive Theme Toggle -->
    <div class="absolute top-4 right-4 z-20">
      <button 
        type="button" 
        @click="toggleTheme" 
        class="w-10 h-10 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white shadow-xs hover:shadow-md active:scale-95 transition-all duration-200 flex items-center justify-center cursor-pointer group"
        :title="isDark ? 'Ganti ke Mode Terang' : 'Ganti ke Mode Gelap'"
      >
        <Sun v-if="isDark" class="w-4.5 h-4.5 text-amber-400 group-hover:rotate-45 transition-transform duration-300" />
        <Moon v-else class="w-4.5 h-4.5 text-slate-600 group-hover:-rotate-12 transition-transform duration-300" />
      </button>
    </div>

    <!-- Main Centered 2-Column Split Container -->
    <div class="w-full max-w-4xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-3xl shadow-xl dark:shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2 transition-all duration-300">
      
      <!-- LEFT COLUMN: Interactive Blueprint & Value Showcase Banner -->
      <div 
        class="relative p-8 sm:p-10 flex flex-col justify-between bg-gradient-to-b from-blue-600 via-indigo-600 to-indigo-800 dark:from-blue-600 dark:via-indigo-800 dark:to-indigo-950 text-white overflow-hidden min-h-[480px] md:min-h-[540px] select-none"
        @mouseenter="isHoveringBanner = true"
        @mouseleave="isHoveringBanner = false"
      >
        
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

        <!-- Interactive Animated Node Points -->
        <div class="absolute top-12 right-12 w-14 h-14 rounded-full border border-white/20 pointer-events-none flex items-center justify-center animate-pulse" style="animation-duration: 4s;">
          <div class="w-5 h-5 rounded-full bg-white/20"></div>
        </div>
        <div class="absolute top-28 left-8 w-8 h-8 rounded-full border border-white/20 pointer-events-none flex items-center justify-center">
          <div class="w-3 h-3 rounded-full bg-white/30"></div>
        </div>
        <div class="absolute bottom-24 right-16 w-10 h-10 rounded-full border border-white/25 pointer-events-none flex items-center justify-center">
          <div class="w-4 h-4 rounded-full bg-white/25"></div>
        </div>
        
        <!-- Diagonal Node Connectors -->
        <div class="absolute top-16 left-28 w-24 h-px bg-white/20 rotate-45 pointer-events-none"></div>
        <div class="absolute bottom-28 left-12 w-28 h-px bg-white/20 -rotate-45 pointer-events-none"></div>

        <!-- Header: Company Brand -->
        <div class="relative z-10 flex items-center gap-2.5">
          <AppLogo size="sm" />
          <span class="text-xs font-black tracking-[0.25em] uppercase text-white">DevCalc</span>
        </div>

        <!-- Center: Interactive Narrative & Dynamic Slides -->
        <div class="relative z-10 text-center my-auto py-6 space-y-3">
          
          <!-- Category Tag -->
          <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-[11px] font-bold uppercase tracking-[0.18em] text-blue-100/95 backdrop-blur-xs">
            <component :is="highlights[activeSlide].icon" class="w-3 h-3 text-blue-200" />
            <span>{{ highlights[activeSlide].tag }}</span>
          </div>

          <!-- Dynamic Feature Headline -->
          <h2 class="text-lg sm:text-xl lg:text-2xl font-extrabold tracking-wide text-white uppercase leading-snug drop-shadow-xs min-h-[32px] flex items-center justify-center">
            {{ highlights[activeSlide].title }}
          </h2>
          
          <!-- Divider Accent Pill -->
          <div class="w-10 h-0.5 bg-white/80 rounded-full mx-auto my-2"></div>

          <!-- Transitioning Slide Content -->
          <div class="min-h-[55px] flex items-center justify-center transition-all duration-300">
            <p class="text-[13px] text-blue-100/90 font-normal leading-relaxed max-w-xs mx-auto">
              {{ highlights[activeSlide].desc }}
            </p>
          </div>

          <!-- Interactive Slide Indicators (Clickable Pills) -->
          <div class="flex items-center justify-center gap-1.5 pt-2">
            <button 
              v-for="(_, idx) in highlights" 
              :key="idx"
              @click="activeSlide = idx"
              type="button"
              class="h-1.5 rounded-full transition-all duration-300 cursor-pointer"
              :class="activeSlide === idx ? 'w-6 bg-white' : 'w-2 bg-white/35 hover:bg-white/60'"
              :aria-label="`Pilih slide ${idx + 1}`"
            ></button>
          </div>

        </div>

        <!-- Footer / Version & Live Pulse -->
        <div class="relative z-10 flex items-center justify-between text-[11px] font-semibold tracking-wider text-blue-200/75 uppercase">
          <span>&copy; 2026 DevCalc</span>
          <span class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
            Sistem Internal v2.4
          </span>
        </div>

      </div>

      <!-- RIGHT COLUMN: Interactive Login Account Form -->
      <div class="p-8 sm:p-10 flex flex-col justify-between bg-white dark:bg-slate-900 transition-colors duration-300">
        
        <div class="space-y-6 my-auto">
          
          <!-- Form Header -->
          <div class="text-center space-y-1.5">
            <h1 class="text-2xl sm:text-[26px] font-black tracking-tight text-blue-600 dark:text-blue-500">
              Selamat Datang
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-normal max-w-xs mx-auto leading-relaxed">
              Masukkan email dan kata sandi kredensial Anda untuk mengakses portal estimator.
            </p>
          </div>

          <!-- Form Fields -->
          <form @submit.prevent="submit" class="space-y-4 pt-1">
            
            <!-- Email Input Field -->
            <div class="space-y-1">
              <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                Alamat Email
              </label>
              <div 
                class="group relative flex items-center bg-slate-50/80 dark:bg-slate-950 border rounded-xl overflow-hidden transition-all duration-200"
                :class="form.errors.email 
                  ? 'border-rose-500 ring-1 ring-rose-500/30' 
                  : 'border-slate-200 dark:border-slate-800 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20'"
              >
                <!-- Dynamic Left Accent Bar -->
                <div 
                  class="w-1 self-stretch transition-colors duration-200 shrink-0"
                  :class="form.errors.email ? 'bg-rose-500' : 'bg-blue-600 dark:bg-blue-500'"
                ></div>

                <input
                  ref="emailInputRef"
                  id="email"
                  name="email"
                  v-model="form.email"
                  type="email"
                  autocomplete="username"
                  required
                  placeholder="nama@perusahaan.id"
                  class="w-full px-3.5 py-3 bg-transparent text-slate-900 dark:text-white text-xs sm:text-sm font-medium outline-none placeholder:text-slate-400 dark:placeholder:text-slate-500"
                />

                <!-- Interactive Clear Email Icon Button -->
                <button
                  v-if="form.email"
                  type="button"
                  @click="clearEmail"
                  class="pr-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition cursor-pointer p-1"
                  title="Hapus teks"
                >
                  <XCircle class="w-4 h-4" />
                </button>

                <!-- Valid Email Green Indicator Badge -->
                <span v-else-if="isEmailValid" class="pr-3 text-emerald-500">
                  <Check class="w-4 h-4 stroke-[2.5]" />
                </span>
              </div>
              
              <!-- Validation Error -->
              <span v-if="form.errors.email" class="text-[11px] text-rose-500 dark:text-rose-400 font-medium block pl-1 flex items-center gap-1">
                <AlertCircle class="w-3 h-3 shrink-0" />
                {{ form.errors.email }}
              </span>
            </div>

            <!-- Password Input Field -->
            <div class="space-y-1">
              <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                Kata Sandi
              </label>
              <div 
                class="group relative flex items-center bg-slate-50/80 dark:bg-slate-950 border rounded-xl overflow-hidden transition-all duration-200"
                :class="form.errors.password 
                  ? 'border-rose-500 ring-1 ring-rose-500/30' 
                  : 'border-slate-200 dark:border-slate-800 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20'"
              >
                <!-- Dynamic Left Accent Bar -->
                <div 
                  class="w-1 self-stretch transition-colors duration-200 shrink-0"
                  :class="form.errors.password ? 'bg-rose-500' : 'bg-blue-600 dark:bg-blue-500'"
                ></div>

                <input
                  id="password"
                  name="password"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  autocomplete="current-password"
                  @keyup="checkCapsLock"
                  @keydown="checkCapsLock"
                  required
                  placeholder="••••••••"
                  class="w-full pl-3.5 pr-10 py-3 bg-transparent text-slate-900 dark:text-white text-xs sm:text-sm font-medium outline-none placeholder:text-slate-400 dark:placeholder:text-slate-500"
                />

                <!-- Interactive Password Toggle -->
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-3.5 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition cursor-pointer p-1 active:scale-90"
                  :aria-label="showPassword ? 'Sembunyikan sandi' : 'Tampilkan sandi'"
                  :title="showPassword ? 'Sembunyikan sandi' : 'Tampilkan sandi'"
                >
                  <Eye v-if="!showPassword" class="w-4 h-4" />
                  <EyeOff v-else class="w-4 h-4" />
                </button>
              </div>

              <!-- Caps Lock Warning Helper -->
              <div v-if="capsLockOn" class="text-[11px] text-amber-600 dark:text-amber-400 font-medium pl-1 flex items-center gap-1">
                <AlertCircle class="w-3 h-3 shrink-0" />
                <span>Peringatan: Caps Lock sedang aktif</span>
              </div>

              <!-- Validation Error -->
              <span v-if="form.errors.password" class="text-[11px] text-rose-500 dark:text-rose-400 font-medium block pl-1 flex items-center gap-1">
                <AlertCircle class="w-3 h-3 shrink-0" />
                {{ form.errors.password }}
              </span>
            </div>

            <!-- Keep me signed in & Info row -->
            <div class="flex items-center justify-between text-xs pt-1">
              <label class="flex items-center gap-2 cursor-pointer select-none group" @click.prevent="form.remember = !form.remember">
                <div 
                  class="w-4.5 h-4.5 rounded-md border transition-all duration-150 flex items-center justify-center cursor-pointer group-hover:border-blue-500"
                  :class="form.remember 
                    ? 'bg-blue-600 border-blue-600 text-white shadow-xs' 
                    : 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950'"
                >
                  <Check v-if="form.remember" class="w-3 h-3 stroke-[3]" />
                </div>
                <span class="text-xs text-slate-600 dark:text-slate-300 font-medium group-hover:text-slate-900 dark:group-hover:text-white transition-colors">
                  Ingat sesi saya
                </span>
              </label>

              <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">
                Portal Internal
              </span>
            </div>

            <!-- Full-Width Rounded Pill Action Button with Interactive Hover & Scale -->
            <button
              type="submit"
              :disabled="form.processing"
              class="group w-full mt-3 py-3.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs sm:text-sm tracking-wider uppercase rounded-full shadow-lg shadow-blue-600/25 dark:shadow-blue-600/30 transition-all duration-150 flex items-center justify-center gap-2 disabled:opacity-50 cursor-pointer active:scale-[0.98]"
            >
              <LogIn v-if="!form.processing" class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
              <svg v-else class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ form.processing ? 'Memverifikasi...' : 'Masuk ke Sistem' }}</span>
            </button>
          </form>

        </div>

        <!-- Security Footer -->
        <div class="pt-6 mt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-center gap-1.5 text-slate-400 dark:text-slate-500 text-xs font-medium">
          <ShieldCheck class="w-4 h-4 text-slate-400 dark:text-slate-400 shrink-0" />
          <span>Sesi Terenkripsi & Akses Terlindungi</span>
        </div>

      </div>

    </div>

  </div>
</template>
