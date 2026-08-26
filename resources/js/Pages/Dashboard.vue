<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CurrencyInput from '@/Components/CurrencyInput.vue';
import Badge from '@/Components/Badge.vue';
import Modal from '@/Components/Modal.vue';
import ConfirmDeleteModal from '@/Components/ConfirmDeleteModal.vue';
import ExportReportModal from '@/Components/ExportReportModal.vue';
import SchemeBarChart from '@/Components/SchemeBarChart.vue';
import ActionMenu from '@/Components/ActionMenu.vue';
import { 
  Calculator, 
  Banknote, 
  RefreshCw, 
  Plus, 
  HelpCircle, 
  Printer, 
  ExternalLink,
  FileText,
  Briefcase,
  Sparkles,
  TrendingUp,
  LayoutDashboard,
  X,
  CheckCircle2,
  AlertCircle,
  Download,
  FileDown
} from 'lucide-vue-next';

const props = defineProps({
  recentProjects: Array,
  stats: Object,
  modules: Array,
});

// Flash Messages State
const page = usePage();
const flashSuccess = ref(page.props.flash?.success || '');
const flashError = ref(page.props.flash?.error || '');

watch(() => page.props.flash, (newFlash) => {
  if (newFlash?.success) {
    flashSuccess.value = newFlash.success;
  }
  if (newFlash?.error) {
    flashError.value = newFlash.error;
  }
}, { deep: true, immediate: true });

// Addendum Modal State
const addendumModalOpen = ref(false);
const targetProject = ref(null);
const showExportModal = ref(false);

const addendumForm = useForm({
  addendum_type: 'module_expansion',
  remaining_duration: 6,
  new_user_count: 10,
  addendum_notes: '',
});

function openAddendumModal(item) {
  targetProject.value = item;
  addendumForm.remaining_duration = item.subscription_duration || 6;
  addendumForm.new_user_count = item.user_count || 10;
  addendumModalOpen.value = true;
}

function submitAddendum() {
  if (!targetProject.value) return;
  addendumForm.post(`/projects/${targetProject.value.id}/addendum`, {
    onSuccess: (pageRes) => {
      addendumModalOpen.value = false;
      addendumForm.reset();
      flashSuccess.value = pageRes.props.flash?.success || 'Dokumen Adendum berhasil diterbitkan!';
    }
  });
}

// Delete Modal State
const deleteModalOpen = ref(false);
const targetProjectToDelete = ref(null);
const isDeleting = ref(false);

function promptDeleteProject(param1, param2) {
  if (typeof param1 === 'object' && param1 !== null) {
    targetProjectToDelete.value = { id: param1.id, code: param1.code || param1.id };
  } else {
    targetProjectToDelete.value = { id: param1, code: param2 || param1 };
  }
  deleteModalOpen.value = true;
}

function confirmDeleteProject() {
  if (!targetProjectToDelete.value || !targetProjectToDelete.value.id) return;
  const projectId = targetProjectToDelete.value.id;
  const code = targetProjectToDelete.value.code || projectId;
  isDeleting.value = true;
  router.delete(`/projects/${projectId}`, {
    onSuccess: (pageRes) => {
      deleteModalOpen.value = false;
      targetProjectToDelete.value = null;
      flashSuccess.value = pageRes.props.flash?.success || `Penawaran #${code} berhasil dihapus.`;
    },
    onFinish: () => {
      isDeleting.value = false;
    }
  });
}

// Quick Calculator Simulator State
const mode = ref('one_off'); // 'one_off' or 'subscription'
const basePrice = ref(2000000);
const complexity = ref(1.0);
const setupFee = ref(0);
const maintenanceMonths = ref(3);

function getClientInitial(name) {
  if (!name) return 'C';
  const clean = name.replace(/^(PT\.|CV\.|UD\.|PT|CV|UD)\s*/i, '').trim();
  return (clean[0] || name[0] || 'C').toUpperCase();
}

function formatClientName(name) {
  if (!name) return '';
  return name.replace(/\w\S*/g, (txt) => txt.charAt(0).toUpperCase() + txt.substring(1));
}

// SaaS specific state
const subBasis = ref('modular'); // 'modular', 'per_user', 'hybrid'
const subCycle = ref('monthly'); // 'monthly', 'yearly'
const subDuration = ref(12);
const userCount = ref(10);
const pricePerUser = ref(50000);

// Calculations
const calculatedBasePrice = computed(() => {
  return Math.round((basePrice.value || 0) * (complexity.value || 1.0));
});

const slaText = computed(() => {
  switch (maintenanceMonths.value) {
    case 1: return '1 Bln SLA (Standard Bugfix)';
    case 6: return '6 Bln Extended SLA (Priority Support)';
    case 12: return '12 Bln Full Year SLA (Enterprise SLA)';
    default: return '3 Bln Standar SLA (Included Free)';
  }
});

// One-Off Grand Total
const oneOffTotal = computed(() => {
  return calculatedBasePrice.value;
});

// SaaS Calculations
const saasRecurring = computed(() => {
  if (subBasis.value === 'per_user') {
    return Math.round((userCount.value || 0) * (pricePerUser.value || 0));
  }
  if (subBasis.value === 'hybrid') {
    const modulesVal = calculatedBasePrice.value;
    const usersVal = Math.round((userCount.value || 0) * (pricePerUser.value || 0));
    return modulesVal + usersVal;
  }
  // Modular
  return calculatedBasePrice.value;
});

const saasGrandTotal = computed(() => {
  const recurring = saasRecurring.value;
  const duration = subDuration.value || 1;
  const setup = setupFee.value || 0;
  return setup + (recurring * duration);
});

// Analytics Calculations
const generatedProjectsCount = computed(() => {
  return props.recentProjects.filter(p => p.status === 'Generated').length;
});

const draftProjectsCount = computed(() => {
  return props.recentProjects.filter(p => p.status === 'Draft').length;
});

const generatedPercentage = computed(() => {
  const total = props.recentProjects.length || 1;
  return Math.round((generatedProjectsCount.value / total) * 100);
});

const draftPercentage = computed(() => {
  const total = props.recentProjects.length || 1;
  return Math.round((draftProjectsCount.value / total) * 100);
});

const oneOffPercentage = computed(() => {
  const total = props.stats.total_projects || 1;
  return Math.round(((props.stats.one_off_count || 0) / total) * 100);
});

const subscriptionPercentage = computed(() => {
  const total = props.stats.total_projects || 1;
  return Math.round(((props.stats.subscription_count || 0) / total) * 100);
});

function formatRupiah(num) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num || 0);
}

function goToCreate() {
  router.get('/projects/create');
}

function goToHelp() {
  router.get('/help');
}
</script>

<template>
  <Head title="Dasbor Utama" />

  <AppLayout title="Dasbor Utama & Simulator Kalkulator">
    <div class="space-y-8 max-w-7xl mx-auto">
      
      <!-- Top Header (Simple Style matching sidebar) -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/80 border border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shadow-sm shrink-0">
              <LayoutDashboard class="w-5 h-5" />
            </div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
              Dasbor Utama
            </h2>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400">
            Ringkasan metrik aktivitas penawaran harga, estimasi nilai kontrak, dan kalkulator cepat.
          </p>
        </div>

        <Link
          href="/projects/create"
          class="px-4.5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/30 transition flex items-center justify-center gap-2 self-start sm:self-auto cursor-pointer shrink-0"
        >
          <Plus class="w-4 h-4" />
          <span>Buat Penawaran Baru</span>
        </Link>
      </div>

      <!-- Simple Notification Alert (Below Header) -->
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 -translate-y-1 scale-98"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 -translate-y-1 scale-98"
      >
        <div
          v-if="flashSuccess"
          class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 text-emerald-900 dark:text-emerald-200 flex items-center justify-between gap-3 shadow-sm"
        >
          <div class="flex items-center gap-2.5">
            <CheckCircle2 class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400 shrink-0" />
            <span class="text-xs font-bold">{{ flashSuccess }}</span>
          </div>
          <button
            @click="flashSuccess = ''"
            class="p-1 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 rounded-lg transition cursor-pointer"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>

        <div
          v-else-if="flashError"
          class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/80 text-rose-900 dark:text-rose-200 flex items-center justify-between gap-3 shadow-sm"
        >
          <div class="flex items-center gap-2.5">
            <AlertCircle class="w-4.5 h-4.5 text-rose-600 dark:text-rose-400 shrink-0" />
            <span class="text-xs font-bold">{{ flashError }}</span>
          </div>
          <button
            @click="flashError = ''"
            class="p-1 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 rounded-lg transition cursor-pointer"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>
      </Transition>

      <!-- Top Stat Overview Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 rounded-2xl shadow-sm flex items-center gap-4 transition hover:border-indigo-300 dark:hover:border-indigo-800">
          <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
            <FileText class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Penawaran</p>
            <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mt-0.5">{{ stats.total_projects }}</h3>
          </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 rounded-2xl shadow-sm flex items-center gap-4 transition hover:border-emerald-300 dark:hover:border-emerald-800">
          <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
            <Banknote class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Nilai Kontrak</p>
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white mt-0.5">{{ stats.total_value_formatted }}</h3>
          </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 rounded-2xl shadow-sm flex items-center gap-4 transition hover:border-sky-300 dark:hover:border-sky-800">
          <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
            <Briefcase class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Putus Kontrak (One-Off)</p>
            <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mt-0.5">{{ stats.one_off_count }}</h3>
          </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 rounded-2xl shadow-sm flex items-center gap-4 transition hover:border-amber-300 dark:hover:border-amber-800">
          <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
            <RefreshCw class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Berlangganan SaaS</p>
            <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mt-0.5">{{ stats.subscription_count }}</h3>
          </div>
        </div>
      </div>

      <!-- Stacked Widgets (Atas-Bawah): Quick Calculator & Scheme Line Chart -->
      <div class="space-y-8">
        
        <!-- Widget 1: Quick Calculator Simulator Widget -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
          <!-- Header -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-600/20 shrink-0">
                <Calculator class="w-5 h-5" />
              </div>
              <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Kalkulator Penawaran Cepat</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Simulasi instan biaya proyek pengembangan software & lisensi berulang.</p>
              </div>
            </div>

            <!-- Mode Switcher Tabs -->
            <div class="flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl w-full sm:w-auto gap-1">
              <button
                @click="mode = 'one_off'"
                class="flex-1 sm:flex-none px-4 py-2 text-xs font-bold rounded-lg transition duration-150 flex items-center justify-center gap-2 cursor-pointer"
                :class="mode === 'one_off' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
              >
                <Banknote class="w-4 h-4" />
                <span>Beli Putus (One-Off)</span>
              </button>

              <button
                @click="mode = 'subscription'"
                class="flex-1 sm:flex-none px-4 py-2 text-xs font-bold rounded-lg transition duration-150 flex items-center justify-center gap-2 cursor-pointer"
                :class="mode === 'subscription' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
              >
                <RefreshCw class="w-4 h-4" />
                <span>Langganan SaaS</span>
              </button>
            </div>
          </div>

          <!-- Simulator Form Inputs -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 rounded-2xl">
            <!-- Common Inputs -->
            <CurrencyInput v-model="basePrice" label="Harga Dasar Modul" helperText="Estimasi harga katalog modul" />

            <div class="space-y-1.5">
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Bobot Kompleksitas</label>
              <select
                v-model.number="complexity"
                class="w-full px-3 py-2 text-xs font-semibold bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500"
              >
                <option :value="0.8">0.8x (Sederhana)</option>
                <option :value="1.0">1.0x (Standar)</option>
                <option :value="1.25">1.25x (Sedang)</option>
                <option :value="1.5">1.5x (Kompleks)</option>
                <option :value="2.0">2.0x (Enterprise / High Risk)</option>
              </select>
            </div>

            <CurrencyInput v-model="setupFee" label="Biaya Setup Awal" helperText="Biaya satu kali onboarding" />

            <div class="space-y-1.5">
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Garansi Maintenance (SLA)</label>
              <select
                v-model.number="maintenanceMonths"
                class="w-full px-3 py-2 text-xs font-semibold bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500"
              >
                <option :value="1">1 Bulan</option>
                <option :value="3">3 Bulan (Standar SLA)</option>
                <option :value="6">6 Bulan (Extended SLA)</option>
                <option :value="12">12 Bulan (Full Year SLA)</option>
              </select>
            </div>
          </div>

          <!-- SaaS Extra Inputs -->
          <div v-if="mode === 'subscription'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800">
            <div class="space-y-1.5">
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Metode Langganan</label>
              <select v-model="subBasis" class="w-full px-3 py-2 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg">
                <option value="modular">Flat Modular</option>
                <option value="per_user">Per-User</option>
                <option value="hybrid">Hybrid (Modul + User)</option>
              </select>
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Siklus Penagihan</label>
              <select v-model="subCycle" class="w-full px-3 py-2 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg">
                <option value="monthly">Bulanan</option>
                <option value="yearly">Tahunan</option>
              </select>
            </div>

            <div v-if="subBasis === 'per_user' || subBasis === 'hybrid'" class="space-y-1.5">
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Kapasitas User</label>
              <input v-model.number="userCount" type="number" min="1" class="w-full px-3 py-2 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg" />
            </div>

            <CurrencyInput v-if="subBasis === 'per_user' || subBasis === 'hybrid'" v-model="pricePerUser" label="Tarif / User" />
          </div>

          <!-- Result Box -->
          <div class="p-6 rounded-2xl border transition-all duration-300" :class="mode === 'one_off' ? 'bg-gradient-to-br from-indigo-50/80 to-slate-50 dark:from-indigo-950/40 dark:to-slate-900 border-indigo-200 dark:border-indigo-800/60' : 'bg-gradient-to-br from-emerald-50/80 to-slate-50 dark:from-emerald-950/40 dark:to-slate-900 border-emerald-200 dark:border-emerald-800/60'">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
              <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                  <Sparkles class="w-3.5 h-3.5 text-indigo-500" />
                  <span>{{ mode === 'one_off' ? 'TOTAL BELI PUTUS TERHITUNG' : 'TOTAL KONTRAK SAAS TERHITUNG' }}</span>
                </span>
                <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1">
                  {{ formatRupiah(mode === 'one_off' ? oneOffTotal : saasGrandTotal) }}
                </h3>
                <p v-if="mode === 'subscription'" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 mt-1">
                  Berulang: {{ formatRupiah(saasRecurring) }} {{ subCycle === 'yearly' ? '/ tahun' : '/ bulan' }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                  Garansi: <span class="font-bold text-slate-700 dark:text-slate-300">{{ slaText }}</span>
                </p>
              </div>

              <!-- Action Buttons -->
              <div class="flex items-center gap-3">
                <button
                  @click="goToCreate"
                  class="px-5 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center gap-2 cursor-pointer"
                >
                  <Plus class="w-4 h-4" />
                  <span>Buat Penawaran</span>
                </button>

                <button
                  @click="goToHelp"
                  class="px-4 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-xs rounded-xl border border-slate-300 dark:border-slate-700 transition flex items-center gap-2 cursor-pointer"
                >
                  <HelpCircle class="w-4 h-4" />
                  <span>Panduan</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Widget 2: Financial Model Analytics Widget Section -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
          <SchemeBarChart
            :oneOffCount="stats.one_off_count"
            :subscriptionCount="stats.subscription_count"
            :stats="stats"
            :recentProjects="recentProjects"
          />
        </div>

      </div>

      <!-- Recent Projects Table Section -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-bold text-slate-900 dark:text-white">Penawaran Terbaru & Aksi Cepat</h3>
          <div class="flex items-center gap-3">
            <button
              @click="showExportModal = true"
              class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-extrabold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition flex items-center gap-1.5 cursor-pointer active:scale-95 shadow-xs"
            >
              <FileDown class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" />
              <span>Ekspor Laporan</span>
            </button>
            <Link href="/projects" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
              Lihat Semua &rarr;
            </Link>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold uppercase text-slate-400">
                <th class="py-3 px-4">#ID</th>
                <th class="py-3 px-4">Nama Klien</th>
                <th class="py-3 px-4">Estimator</th>
                <th class="py-3 px-4">Grand Total</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4">Tanggal</th>
                <th class="py-3 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs font-semibold">
              <tr 
                v-for="item in recentProjects" 
                :key="item.id" 
                @click="router.get(`/projects/${item.id}/edit`)"
                class="hover:bg-indigo-50/50 dark:hover:bg-indigo-950/30 transition cursor-pointer group"
              >
                <td class="py-3 px-4 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                  #{{ item.code }}
                </td>
                <td class="py-3 px-4 text-slate-900 dark:text-white font-bold group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                  {{ formatClientName(item.client_name) }}
                </td>
                <td class="py-3 px-4">
                  <Badge variant="gray">{{ item.estimator_name }}</Badge>
                </td>
                <td class="py-3 px-4 font-extrabold text-slate-900 dark:text-white">
                  {{ item.grand_total_formatted }}
                </td>
                <td class="py-3 px-4">
                  <Badge :variant="item.status === 'Generated' ? 'emerald' : 'amber'">
                    {{ item.status }}
                  </Badge>
                </td>
                <td class="py-3 px-4 text-slate-500 dark:text-slate-400">
                  {{ item.created_at_formatted }}
                </td>
                <td class="py-3 px-4 text-right">
                  <ActionMenu
                    :project="item"
                    @open-addendum="openAddendumModal"
                    @delete-project="promptDeleteProject"
                  />
                </td>
              </tr>

              <tr v-if="!recentProjects.length">
                <td colspan="7" class="py-8 text-center text-slate-400">
                  Belum ada penawaran. Klik "Buat Penawaran" untuk memulai.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- Create Addendum Modal -->
    <Modal :show="addendumModalOpen" @close="addendumModalOpen = false" maxWidth="lg">
      <div class="p-6 space-y-4">
        <h3 class="text-base font-bold text-slate-900 dark:text-white">
          Buat Adendum Penawaran #{{ targetProject?.code }}
        </h3>

        <form @submit.prevent="submitAddendum" class="space-y-4 pt-2">
          <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Tipe Perubahan Adendum</label>
            <select v-model="addendumForm.addendum_type" class="w-full px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl">
              <option value="module_expansion">Penambahan Modul Fitur Baru</option>
              <option value="user_capacity">Penambahan Kapasitas User Lisensi</option>
              <option value="contract_renewal">Perpanjangan Durasi Kontrak SaaS</option>
            </select>
            <span v-if="addendumForm.errors.addendum_type" class="text-[11px] font-bold text-rose-500">{{ addendumForm.errors.addendum_type }}</span>
          </div>

          <div v-if="addendumForm.addendum_type === 'contract_renewal'" class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Perpanjangan Durasi (Bulan)</label>
            <input v-model.number="addendumForm.remaining_duration" type="number" min="1" class="w-full px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl" />
            <span v-if="addendumForm.errors.remaining_duration" class="text-[11px] font-bold text-rose-500">{{ addendumForm.errors.remaining_duration }}</span>
          </div>

          <div v-if="addendumForm.addendum_type === 'user_capacity'" class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Tambah Kapasitas User</label>
            <input v-model.number="addendumForm.new_user_count" type="number" min="1" class="w-full px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl" />
            <span v-if="addendumForm.errors.new_user_count" class="text-[11px] font-bold text-rose-500">{{ addendumForm.errors.new_user_count }}</span>
          </div>

          <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Catatan Ringkasan Adendum</label>
            <textarea
              v-model="addendumForm.addendum_notes"
              rows="3"
              placeholder="Jelaskan alasan dan ruang lingkup perubahan adendum..."
              class="w-full px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl"
            ></textarea>
            <span v-if="addendumForm.errors.addendum_notes" class="text-[11px] font-bold text-rose-500">{{ addendumForm.errors.addendum_notes }}</span>
          </div>

          <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-200 dark:border-slate-800">
            <button type="button" @click="addendumModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
              Batal
            </button>
            <button type="submit" :disabled="addendumForm.processing" class="px-4 py-2 text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-md transition disabled:opacity-50 cursor-pointer">
              {{ addendumForm.processing ? 'Memproses...' : 'Terbitkan Adendum' }}
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Confirm Delete Modal -->
    <ConfirmDeleteModal
      :show="deleteModalOpen"
      title="Hapus Penawaran Harga"
      message="Apakah Anda yakin ingin menghapus dokumen penawaran harga ini?"
      :item-name="targetProjectToDelete ? `Penawaran #${targetProjectToDelete.code}` : ''"
      :processing="isDeleting"
      @close="deleteModalOpen = false"
      @confirm="confirmDeleteProject"
    />

    <!-- Export Report Modal -->
    <ExportReportModal
      :show="showExportModal"
      @close="showExportModal = false"
    />
  </AppLayout>
</template>
