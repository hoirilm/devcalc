<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { 
  LayoutDashboard, 
  FileText, 
  Layers, 
  HelpCircle, 
  Sun, 
  Moon, 
  LogOut, 
  Menu, 
  X,
  Search,
  Plus,
  TrendingUp,
  ChevronRight,
  Calculator,
  Briefcase
} from 'lucide-vue-next';

const props = defineProps({
  title: {
    type: String,
    default: ''
  }
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const navbarStats = computed(() => page.props.navbarStats || { total_projects: 0, total_value_formatted: 'Rp 0' });
const searchIndex = computed(() => page.props.searchIndex || { projects: [], modules: [] });

const isDark = ref(false);
const mobileMenuOpen = ref(false);
const isSearchOpen = ref(false);
const searchQuery = ref('');

function toggleDarkMode() {
  isDark.value = !isDark.value;
  if (isDark.value) {
    document.documentElement.classList.add('dark');
    localStorage.setItem('theme', 'dark');
  } else {
    document.documentElement.classList.remove('dark');
    localStorage.setItem('theme', 'light');
  }
}

// Keydown listener for Command+K or Ctrl+K
function handleKeyDown(e) {
  if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
    e.preventDefault();
    isSearchOpen.value = !isSearchOpen.value;
  }
  if (e.key === 'Escape' && isSearchOpen.value) {
    isSearchOpen.value = false;
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown);
  const saved = localStorage.getItem('theme');
  if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    isDark.value = true;
    document.documentElement.classList.add('dark');
  } else {
    isDark.value = false;
    document.documentElement.classList.remove('dark');
  }
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyDown);
});

function logout() {
  router.post('/logout');
}

// Filtered Search Results
const filteredProjects = computed(() => {
  if (!searchQuery.value.trim()) return searchIndex.value.projects.slice(0, 5);
  const q = searchQuery.value.toLowerCase();
  return searchIndex.value.projects.filter(p => 
    p.client_name.toLowerCase().includes(q) || 
    p.code.toLowerCase().includes(q)
  ).slice(0, 6);
});

const filteredModules = computed(() => {
  if (!searchQuery.value.trim()) return searchIndex.value.modules.slice(0, 5);
  const q = searchQuery.value.toLowerCase();
  return searchIndex.value.modules.filter(m => 
    m.name.toLowerCase().includes(q) || 
    m.category.toLowerCase().includes(q)
  ).slice(0, 6);
});

function navigateToProject(id) {
  isSearchOpen.value = false;
  searchQuery.value = '';
  router.get(`/projects/${id}/edit`);
}

function navigateToModules() {
  isSearchOpen.value = false;
  searchQuery.value = '';
  router.get('/modules');
}

function getUserInitial(name) {
  if (!name) return 'U';
  return name.charAt(0).toUpperCase();
}
</script>

<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 flex flex-col md:flex-row transition-colors duration-200">

    <!-- Sidebar Desktop -->
    <aside class="hidden md:flex md:w-64 flex-col fixed inset-y-0 z-40 bg-white dark:bg-slate-900 border-r border-slate-200/80 dark:border-slate-800/80 shadow-sm select-none">
      
      <!-- Artistic Logo Header -->
      <div class="h-20 flex items-center px-6 border-b border-slate-200/80 dark:border-slate-800/80 gap-3.5">
        <div class="relative flex items-center justify-center">
          <div class="absolute inset-0 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 blur-md opacity-50 animate-pulse"></div>
          <div class="relative w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 border border-indigo-400/30">
            <FileText class="w-5 h-5 stroke-[2.5]" />
          </div>
        </div>
        <div>
          <div class="flex items-center gap-1.5">
            <h1 class="text-base font-black text-slate-900 dark:text-white tracking-tight">DevCalc</h1>
            <span class="px-1.5 py-0.2 text-[9px] font-mono font-extrabold bg-indigo-50 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 rounded-md">v2.4</span>
          </div>
          <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Quotation System</p>
        </div>
      </div>

      <!-- Categorized Navigation Menu -->
      <nav class="flex-1 px-4 py-5 space-y-6 overflow-y-auto custom-scrollbar">
        
        <!-- Group 1: Menu Utama -->
        <div class="space-y-1.5">
          <div class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">
            Menu Utama
          </div>

          <Link
            href="/dashboard"
            class="group relative flex items-center gap-3 px-3 py-2 rounded-2xl text-xs font-bold transition-all duration-200"
            :class="$page.url.startsWith('/dashboard') 
              ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-lg shadow-indigo-600/30 border border-indigo-400/30' 
              : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100 hover:translate-x-1'"
          >
            <div 
              class="w-7 h-7 rounded-xl flex items-center justify-center transition-all duration-200"
              :class="$page.url.startsWith('/dashboard') ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-950 group-hover:text-indigo-600 dark:group-hover:text-indigo-400'"
            >
              <LayoutDashboard class="w-4 h-4 stroke-[2.2]" />
            </div>
            <span class="flex-1">Dasbor Utama</span>
            <div v-if="$page.url.startsWith('/dashboard')" class="w-1.5 h-1.5 rounded-full bg-white shadow-xs"></div>
          </Link>

          <Link
            href="/projects"
            class="group relative flex items-center gap-3 px-3 py-2 rounded-2xl text-xs font-bold transition-all duration-200"
            :class="$page.url.startsWith('/projects') 
              ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-lg shadow-indigo-600/30 border border-indigo-400/30' 
              : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100 hover:translate-x-1'"
          >
            <div 
              class="w-7 h-7 rounded-xl flex items-center justify-center transition-all duration-200"
              :class="$page.url.startsWith('/projects') ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-950 group-hover:text-indigo-600 dark:group-hover:text-indigo-400'"
            >
              <FileText class="w-4 h-4 stroke-[2.2]" />
            </div>
            <span class="flex-1">Penawaran Harga</span>
            <div v-if="$page.url.startsWith('/projects')" class="w-1.5 h-1.5 rounded-full bg-white shadow-xs"></div>
          </Link>
        </div>

        <!-- Group 2: Master Data & Katalogs -->
        <div class="space-y-1.5">
          <div class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">
            Master Data
          </div>

          <Link
            href="/modules"
            class="group relative flex items-center gap-3 px-3 py-2 rounded-2xl text-xs font-bold transition-all duration-200"
            :class="$page.url.startsWith('/modules') 
              ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-lg shadow-indigo-600/30 border border-indigo-400/30' 
              : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100 hover:translate-x-1'"
          >
            <div 
              class="w-7 h-7 rounded-xl flex items-center justify-center transition-all duration-200"
              :class="$page.url.startsWith('/modules') ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-950 group-hover:text-indigo-600 dark:group-hover:text-indigo-400'"
            >
              <Layers class="w-4 h-4 stroke-[2.2]" />
            </div>
            <span class="flex-1">Katalog Modul</span>
            <div v-if="$page.url.startsWith('/modules')" class="w-1.5 h-1.5 rounded-full bg-white shadow-xs"></div>
          </Link>
        </div>

        <!-- Group 3: Dokumentasi & Help -->
        <div class="space-y-1.5">
          <div class="px-3 text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">
            Dokumentasi
          </div>

          <Link
            href="/help"
            class="group relative flex items-center gap-3 px-3 py-2 rounded-2xl text-xs font-bold transition-all duration-200"
            :class="$page.url.startsWith('/help') 
              ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-lg shadow-indigo-600/30 border border-indigo-400/30' 
              : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-100 hover:translate-x-1'"
          >
            <div 
              class="w-7 h-7 rounded-xl flex items-center justify-center transition-all duration-200"
              :class="$page.url.startsWith('/help') ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-950 group-hover:text-indigo-600 dark:group-hover:text-indigo-400'"
            >
              <HelpCircle class="w-4 h-4 stroke-[2.2]" />
            </div>
            <span class="flex-1">Panduan & Help</span>
            <div v-if="$page.url.startsWith('/help')" class="w-1.5 h-1.5 rounded-full bg-white shadow-xs"></div>
          </Link>
        </div>

      </nav>

      <!-- Sidebar Footer (Artistic Profile) -->
      <div class="p-4 border-t border-slate-200/80 dark:border-slate-800/80 space-y-3">
        
        <!-- User Profile Card -->
        <div class="p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between gap-2 shadow-2xs">
          <div class="flex items-center gap-2.5 overflow-hidden">
            <div class="relative w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-600 text-white font-black text-xs flex items-center justify-center shadow-md shadow-indigo-600/20 shrink-0">
              {{ getUserInitial(user?.name) }}
            </div>
            <div class="truncate">
              <div class="text-xs font-black text-slate-900 dark:text-white truncate leading-tight">
                {{ user?.name || 'Administrator' }}
              </div>
              <div class="text-[10px] text-indigo-600 dark:text-indigo-400 font-bold truncate">
                Sales Estimator
              </div>
            </div>
          </div>

          <button 
            @click="logout"
            title="Keluar dari Sistem"
            class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/50 transition cursor-pointer shrink-0 active:scale-95"
          >
            <LogOut class="w-4 h-4" />
          </button>
        </div>
      </div>
    </aside>

    <!-- Mobile Top Navigation Bar -->
    <header class="md:hidden flex items-center justify-between h-16 px-4 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 sticky top-0 z-30">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white">
          <FileText class="w-4 h-4" />
        </div>
        <span class="font-extrabold text-sm text-slate-900 dark:text-white">DevCalc</span>
      </div>
      <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-slate-600 dark:text-slate-400 cursor-pointer">
        <Menu v-if="!mobileMenuOpen" class="w-6 h-6" />
        <X v-else class="w-6 h-6" />
      </button>
    </header>

    <!-- Main Content Wrapper -->
    <div class="flex-1 md:pl-64 flex flex-col min-h-screen">
      
      <!-- ENTERPRISE TOP NAVBAR HEADER -->
      <header class="h-16 px-6 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 sticky top-0 z-30 flex items-center justify-center">
        
        <div class="max-w-7xl mx-auto w-full flex items-center justify-between gap-4">
          <!-- Left Area: Global Command Search Bar Trigger -->
          <div class="flex items-center gap-4 flex-1 max-w-md">
            <button
              @click="isSearchOpen = true"
              type="button"
              class="w-full px-3.5 py-1.5 rounded-xl bg-slate-100/90 dark:bg-slate-800/70 hover:bg-slate-200/70 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700/70 text-slate-400 dark:text-slate-500 flex items-center justify-between gap-3 text-xs transition cursor-pointer shadow-2xs group"
            >
              <div class="flex items-center gap-2.5 truncate">
                <Search class="w-4 h-4 text-slate-400 group-hover:text-indigo-500 transition" />
                <span class="truncate font-medium text-slate-500 dark:text-slate-400">Cari penawaran, klien, atau modul...</span>
              </div>
              <kbd class="hidden sm:inline-flex items-center px-1.5 py-0.5 text-[10px] font-mono font-bold bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-500 dark:text-slate-400 rounded-md shadow-2xs">
                ⌘K
              </kbd>
            </button>
          </div>

          <!-- Right Area: Live Pipeline Metric Chip + Theme Toggle -->
          <div class="flex items-center gap-2.5 sm:gap-3 shrink-0">
            
            <!-- Live Pipeline Metric Chip -->
            <div class="hidden lg:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50/80 dark:bg-emerald-950/50 border border-emerald-200/80 dark:border-emerald-800/80 text-emerald-900 dark:text-emerald-200 text-xs font-bold shadow-2xs">
              <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
              </span>
              <span>{{ navbarStats.total_projects }} Penawaran</span>
              <span class="text-emerald-300 dark:text-emerald-700">•</span>
              <span class="font-extrabold text-emerald-700 dark:text-emerald-300">{{ navbarStats.total_value_formatted }}</span>
            </div>

            <!-- Theme Toggle Button -->
            <button
              type="button"
              @click="toggleDarkMode"
              class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition cursor-pointer"
              title="Ubah Tema"
            >
              <Sun v-if="isDark" class="w-4 h-4 text-amber-400" />
              <Moon v-else class="w-4 h-4 text-slate-600" />
            </button>
          </div>
        </div>

      </header>

      <!-- Page Body Content -->
      <main class="flex-1 p-6">
        <slot />
      </main>

      <!-- Global Footer -->
      <footer class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 text-center text-xs text-slate-400 dark:text-slate-500">
        DevCalc Quotation System &copy; 2026. All rights reserved.
      </footer>
    </div>

    <!-- COMMAND PALETTE SEARCH MODAL (⌘K) -->
    <div
      v-if="isSearchOpen"
      class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-md flex items-start justify-center pt-16 sm:pt-24 px-4"
      @click.self="isSearchOpen = false"
    >
      <div class="w-full max-w-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[80vh]">
        
        <!-- Input Header -->
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center gap-3">
          <Search class="w-5 h-5 text-indigo-500 shrink-0" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Ketik nama klien, nomor penawaran (#QUO-...), atau modul..."
            class="w-full bg-transparent border-none outline-none text-sm font-semibold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-0"
            autofocus
          />
          <button
            @click="isSearchOpen = false"
            class="px-2 py-1 text-[11px] font-bold text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-800 rounded-lg transition"
          >
            ESC
          </button>
        </div>

        <!-- Search Results Body -->
        <div class="p-4 overflow-y-auto space-y-4 flex-1">
          
          <!-- Projects Result Group -->
          <div v-if="filteredProjects.length" class="space-y-2">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-2">Dokumen Penawaran Harga</span>
            <div class="space-y-1">
              <div
                v-for="p in filteredProjects"
                :key="p.id"
                @click="navigateToProject(p.id)"
                class="p-3 rounded-2xl hover:bg-indigo-50/70 dark:hover:bg-indigo-950/60 transition cursor-pointer flex items-center justify-between group border border-transparent hover:border-indigo-200 dark:hover:border-indigo-800"
              >
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                    <FileText class="w-4 h-4" />
                  </div>
                  <div>
                    <div class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                      {{ p.client_name }}
                    </div>
                    <div class="text-[10px] font-mono text-slate-400">
                      #{{ p.code }}
                    </div>
                  </div>
                </div>

                <div class="flex items-center gap-2">
                  <span class="text-xs font-black text-slate-900 dark:text-white">{{ p.grand_total_formatted }}</span>
                  <ChevronRight class="w-4 h-4 text-slate-400 group-hover:text-indigo-500 transition" />
                </div>
              </div>
            </div>
          </div>

          <!-- Modules Result Group -->
          <div v-if="filteredModules.length" class="space-y-2">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-2">Katalog Modul Master</span>
            <div class="space-y-1">
              <div
                v-for="m in filteredModules"
                :key="m.id"
                @click="navigateToModules"
                class="p-3 rounded-2xl hover:bg-emerald-50/70 dark:hover:bg-emerald-950/60 transition cursor-pointer flex items-center justify-between group border border-transparent hover:border-emerald-200 dark:hover:border-emerald-800"
              >
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <Layers class="w-4 h-4" />
                  </div>
                  <div>
                    <div class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition">
                      {{ m.name }}
                    </div>
                    <div class="text-[10px] text-slate-400">
                      Kategori: {{ m.category }}
                    </div>
                  </div>
                </div>

                <div class="flex items-center gap-2">
                  <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">{{ m.price_formatted }}</span>
                  <ChevronRight class="w-4 h-4 text-slate-400 group-hover:text-emerald-500 transition" />
                </div>
              </div>
            </div>
          </div>

          <!-- Empty Search State -->
          <div v-if="!filteredProjects.length && !filteredModules.length" class="py-8 text-center text-xs text-slate-400 space-y-1">
            <Search class="w-8 h-8 text-slate-300 dark:text-slate-700 mx-auto" />
            <p class="font-bold text-slate-500 dark:text-slate-400">Tidak ada penawaran atau modul yang cocok</p>
            <p class="text-[11px]">Coba cari dengan kata kunci nama klien lain</p>
          </div>

        </div>

        <!-- Footer Shortcuts Help -->
        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800 text-[11px] text-slate-400 flex items-center justify-between px-4">
          <span>Gunakan <kbd class="px-1 bg-white dark:bg-slate-900 border rounded font-mono">⌘K</kbd> untuk membuka pencarian cepat kapan saja.</span>
          <span>Esc untuk menutup</span>
        </div>

      </div>
    </div>

  </div>
</template>
