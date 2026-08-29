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
  Building2,
  Kanban,
  Target,
  Percent,
  MessageSquare,
  Phone,
  ArrowRight,
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
  AlertCircle,
  Download,
  Activity,
  User,
  FileDown,
  Clock,
  ArrowUpRight,
  CheckCircle2,
  X
} from 'lucide-vue-next';

const props = defineProps({
  recentProjects: {
    type: Array,
    default: () => []
  },
  stats: {
    type: Object,
    default: () => ({})
  },
  crmStats: {
    type: Object,
    default: () => ({ total_clients: 0, active_clients: 0, total_deals: 0, active_deals_count: 0, pipeline_value_formatted: 'Rp 0', won_value_formatted: 'Rp 0', win_rate: 0 })
  },
  recentDeals: {
    type: Array,
    default: () => []
  },
  recentActivities: {
    type: Array,
    default: () => []
  },
  modules: {
    type: Array,
    default: () => []
  },
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

// Modals State
const addendumModalOpen = ref(false);
const targetProject = ref(null);
const showExportModal = ref(false);
const isQuickSimulatorOpen = ref(false);

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

function formatRupiah(num) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num || 0);
}

function goToCreate() {
  isQuickSimulatorOpen.value = false;
  router.get('/projects/create');
}

function goToHelp() {
  isQuickSimulatorOpen.value = false;
  router.get('/help');
}

function formatLogText(text) {
  if (!text) return '';
  let safe = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  safe = safe.replace(
    /berpindah dari stage (.+?) ke (.+?)(?=(\s\((Probabilitas:|\.|$))|$)/gi,
    (match, fromStage, toStage) => `berpindah dari stage <span class="font-extrabold text-slate-800 dark:text-slate-200">${fromStage.trim()}</span> &rarr; <span class="font-extrabold text-purple-600 dark:text-purple-400">${toStage.trim()}</span>`
  );
  safe = safe.replace(/#(QUO-[A-Za-z0-9\-]+)/g, '<span class="font-mono font-bold text-indigo-600 dark:text-indigo-400">#$1</span>');
  safe = safe.replace(/\bRp\s?([0-9\.,]+)/g, '<span class="font-bold text-emerald-600 dark:text-emerald-400">Rp $1</span>');
  safe = safe.replace(/'([^']+)'/g, '<span class="font-bold text-slate-900 dark:text-white">$1</span>');
  return safe;
}
</script>

<template>
  <Head title="Dasbor Eksekutif CRM" />

  <AppLayout title="Dasbor Eksekutif CRM & CPQ System">
    <div class="space-y-8 max-w-7xl mx-auto">
      
      <!-- TOP HEADER & EXECUTIVE ACTIONS -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Dasbor Eksekutif CRM</h1>
            <span class="px-2 py-0.5 text-xs font-extrabold bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 rounded-lg border border-indigo-200 dark:border-indigo-800">IT Agency Hub</span>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Monitoring pipa penjualan deals, aktivitas klien B2B, dan ringkasan nilai kontrak penawaran harga software.
          </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap self-start sm:self-auto shrink-0">
          <button
            @click="isQuickSimulatorOpen = true"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200 font-extrabold text-xs border border-slate-200/80 dark:border-slate-800/80 shadow-xs transition cursor-pointer active:scale-95"
          >
            <Calculator class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
            <span>⚡ Simulasi Cepat</span>
          </button>

          <Link
            href="/deals"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-purple-50 dark:bg-purple-950/80 hover:bg-purple-100 dark:hover:bg-purple-900/60 text-purple-700 dark:text-purple-300 font-extrabold text-xs border border-purple-200 dark:border-purple-800 transition cursor-pointer active:scale-95"
          >
            <Kanban class="w-4 h-4 stroke-[2.5]" />
            <span>Sales Kanban</span>
          </Link>

          <Link
            href="/projects/create"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-lg shadow-indigo-600/30 transition cursor-pointer active:scale-95"
          >
            <Plus class="w-4 h-4 stroke-[3]" />
            <span>Buat Penawaran</span>
          </Link>
        </div>
      </div>

      <!-- ONBOARDING GUIDE BANNER (Tampil jika workspace masih bersih/0 data) -->
      <div 
        v-if="crmStats.total_deals === 0 && stats.total_projects === 0 && crmStats.total_clients === 0" 
        class="p-6 rounded-3xl bg-gradient-to-r from-indigo-900 via-indigo-800 to-purple-900 text-white shadow-xl relative overflow-hidden space-y-4"
      >
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
          <div class="space-y-1.5 max-w-xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-white text-xs font-black backdrop-blur-xs border border-white/20">
              <Sparkles class="w-3.5 h-3.5 text-amber-300" />
              <span>Workspace CRM Siap Digunakan</span>
            </div>
            <h2 class="text-xl font-black tracking-tight">Selamat Datang di DevCalc Agency CRM & CPQ</h2>
            <p class="text-xs text-indigo-200 leading-relaxed">
              Mulai alur penjualan software agensi Anda: Daftarkan klien B2B, kalkulasikan harga proyek & modul fitur (CPQ), lalu pantau tahapan deal hingga closing di sales Kanban.
            </p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full lg:w-auto shrink-0">
            <Link
              href="/clients"
              class="p-4 rounded-2xl bg-white/10 hover:bg-white/20 backdrop-blur-xs border border-white/15 transition flex flex-col justify-between space-y-2 group"
            >
              <div class="flex items-center justify-between">
                <span class="w-6 h-6 rounded-lg bg-white/20 text-white font-black text-xs flex items-center justify-center">1</span>
                <Building2 class="w-4 h-4 text-indigo-300 group-hover:scale-110 transition" />
              </div>
              <div>
                <div class="text-xs font-black text-white">Tambah Klien B2B</div>
                <div class="text-[10px] text-indigo-200">Daftarkan perusahaan & PIC</div>
              </div>
            </Link>

            <Link
              href="/projects/create"
              class="p-4 rounded-2xl bg-white/10 hover:bg-white/20 backdrop-blur-xs border border-white/15 transition flex flex-col justify-between space-y-2 group"
            >
              <div class="flex items-center justify-between">
                <span class="w-6 h-6 rounded-lg bg-white/20 text-white font-black text-xs flex items-center justify-center">2</span>
                <FileText class="w-4 h-4 text-emerald-300 group-hover:scale-110 transition" />
              </div>
              <div>
                <div class="text-xs font-black text-white">Buat Proposal CPQ</div>
                <div class="text-[10px] text-indigo-200">Kalkulasi harga & modul</div>
              </div>
            </Link>

            <Link
              href="/deals"
              class="p-4 rounded-2xl bg-white/10 hover:bg-white/20 backdrop-blur-xs border border-white/15 transition flex flex-col justify-between space-y-2 group"
            >
              <div class="flex items-center justify-between">
                <span class="w-6 h-6 rounded-lg bg-white/20 text-white font-black text-xs flex items-center justify-center">3</span>
                <Kanban class="w-4 h-4 text-purple-300 group-hover:scale-110 transition" />
              </div>
              <div>
                <div class="text-xs font-black text-white">Tracking Deal Kanban</div>
                <div class="text-[10px] text-indigo-200">Monitor closing & negosiasi</div>
              </div>
            </Link>
          </div>
        </div>
      </div>

      <!-- 4 TOP EXECUTIVE METRICS CARDS -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Card 1: Pipeline Value -->
        <div class="p-4.5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs flex items-center gap-3.5 transition hover:border-indigo-300 dark:hover:border-indigo-800">
          <div class="w-11 h-11 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
            <TrendingUp class="w-5 h-5" />
          </div>
          <div class="truncate">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Pipeline Aktif</div>
            <div class="text-lg font-black text-slate-900 dark:text-white truncate">{{ crmStats.pipeline_value_formatted }}</div>
            <div class="text-[10px] text-slate-400 font-semibold">{{ crmStats.active_deals_count }} Peluang Berjalan</div>
          </div>
        </div>

        <!-- Card 2: Revenue Won -->
        <div class="p-4.5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs flex items-center gap-3.5 transition hover:border-emerald-300 dark:hover:border-emerald-800">
          <div class="w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
            <CheckCircle2 class="w-5 h-5" />
          </div>
          <div class="truncate">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Deals Won (Closing)</div>
            <div class="text-lg font-black text-emerald-600 dark:text-emerald-400 truncate">{{ crmStats.won_value_formatted }}</div>
            <div class="text-[10px] text-slate-400 font-semibold">{{ crmStats.won_count }} Deal Disetujui</div>
          </div>
        </div>

        <!-- Card 3: B2B Clients -->
        <div class="p-4.5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs flex items-center gap-3.5 transition hover:border-sky-300 dark:hover:border-sky-800">
          <div class="w-11 h-11 rounded-2xl bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
            <Building2 class="w-5 h-5" />
          </div>
          <div class="truncate">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Klien B2B Dikelola</div>
            <div class="text-lg font-black text-slate-900 dark:text-white truncate">{{ crmStats.total_clients }} Perusahaan</div>
            <div class="text-[10px] text-slate-400 font-semibold">{{ crmStats.active_clients }} Klien Aktif</div>
          </div>
        </div>

        <!-- Card 4: Quotations & Win Rate -->
        <div class="p-4.5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs flex items-center gap-3.5 transition hover:border-purple-300 dark:hover:border-purple-800">
          <div class="w-11 h-11 rounded-2xl bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
            <FileText class="w-5 h-5" />
          </div>
          <div class="truncate">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Penawaran & Win Rate</div>
            <div class="text-lg font-black text-purple-600 dark:text-purple-400 truncate">{{ stats.total_projects }} Dokumen</div>
            <div class="text-[10px] text-slate-400 font-semibold">Win Rate: {{ crmStats.win_rate }}%</div>
          </div>
        </div>

      </div>

      <!-- PRIMARY CRM SECTION: SALES PIPELINE & ACTIVITY HUB (Posisi Utama di Atas) -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Recent Deals in Pipeline (2 Cols) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold">
                <Kanban class="w-4 h-4" />
              </div>
              <div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white">Pipeline Deals Terkini</h3>
                <p class="text-[11px] text-slate-400">Peluang proyek yang sedang berjalan di sales stage</p>
              </div>
            </div>
            <Link href="/deals" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
              <span>Buka Kanban</span>
              <ArrowRight class="w-3.5 h-3.5" />
            </Link>
          </div>

          <div v-if="recentDeals.length" class="divide-y divide-slate-100 dark:divide-slate-800">
            <div
              v-for="deal in recentDeals"
              :key="deal.id"
              class="py-3 flex items-center justify-between gap-4 hover:bg-slate-50/50 dark:hover:bg-slate-800/30 rounded-xl px-2 transition group"
            >
              <div class="space-y-0.5 truncate">
                <div class="text-xs font-black text-slate-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                  {{ deal.title }}
                </div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5 flex-wrap">
                  <span class="font-bold text-slate-700 dark:text-slate-300">{{ deal.client_name }}</span>
                  <span>•</span>
                  <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ deal.stage_label }} ({{ deal.probability }}%)</span>
                  <span>•</span>
                  <span>Target: {{ deal.expected_close_date_formatted }}</span>
                </div>
              </div>

              <div class="text-right shrink-0">
                <div class="text-xs font-black text-slate-900 dark:text-white">{{ deal.expected_value_formatted }}</div>
                <Link 
                  v-if="deal.client_id"
                  :href="`/deals?client_id=${deal.client_id}`" 
                  class="text-[10px] font-bold text-indigo-500 hover:underline"
                >
                  Lihat di Kanban &rarr;
                </Link>
                <div v-else class="text-[10px] text-slate-400">Sales: {{ deal.sales_name }}</div>
              </div>
            </div>
          </div>
          
          <div v-else class="text-center py-8 px-4 rounded-2xl bg-slate-50/50 dark:bg-slate-800/20 border border-dashed border-slate-200 dark:border-slate-800 space-y-2">
            <Kanban class="w-8 h-8 mx-auto text-slate-300 dark:text-slate-600" />
            <div class="text-xs font-bold text-slate-600 dark:text-slate-300">Belum ada deal aktif di pipeline</div>
            <p class="text-[11px] text-slate-400 max-w-sm mx-auto">Catat peluang proyek dari klien Anda untuk memantau tahapan negosiasi.</p>
            <Link
              href="/deals"
              class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 text-xs font-extrabold hover:bg-indigo-100 transition mt-1"
            >
              + Tambah Deal Pertama
            </Link>
          </div>
        </div>

        <!-- Right: CRM Stats & Recent Activities (1 Col) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4 flex flex-col justify-between">
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                <Activity class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                <span>Log Aktivitas Sistem</span>
              </h3>
              <Link href="/activities" class="text-[11px] font-bold text-indigo-500 hover:underline">Semua Log &rarr;</Link>
            </div>

            <!-- Mini stats summary -->
            <div class="grid grid-cols-2 gap-2 text-center">
              <div class="p-3 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-800/60">
                <div class="text-base font-black text-indigo-600 dark:text-indigo-400">{{ crmStats.total_clients }}</div>
                <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Klien Terdaftar</div>
              </div>
              <div class="p-3 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-800/60">
                <div class="text-base font-black text-emerald-600 dark:text-emerald-400">{{ crmStats.win_rate }}%</div>
                <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Closing Rate</div>
              </div>
            </div>

            <!-- Activities Timeline -->
            <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
              <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Aktivitas & Log Terkini</div>
              <div v-if="recentActivities.length" class="space-y-2">
                <div v-for="act in recentActivities.slice(0, 4)" :key="act.id" class="text-xs space-y-1 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-800/60">
                  <div class="font-bold text-slate-800 dark:text-slate-200 truncate" v-html="formatLogText(act.title)"></div>
                  <div class="text-[10px] text-slate-400 flex items-center justify-between">
                    <span class="truncate max-w-[140px] text-slate-500 font-semibold">{{ act.client_name || act.user_name }}</span>
                    <span>{{ act.performed_at_formatted }}</span>
                  </div>
                </div>
              </div>
              <div v-else class="text-[11px] text-slate-400 italic text-center py-4">
                Belum ada catatan aktivitas aksi.
              </div>
            </div>
          </div>

          <Link
            href="/activities"
            class="w-full py-2.5 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950 text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 text-xs font-bold text-center transition block cursor-pointer"
          >
            Buka Riwayat Log Lengkap &rarr;
          </Link>
        </div>

      </div>

      <!-- ANALISIS MODEL BISNIS & DISTRIBUSI VALUASI SECTION -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <SchemeBarChart
          :oneOffCount="stats.one_off_count"
          :subscriptionCount="stats.subscription_count"
          :stats="stats"
          :recentProjects="recentProjects"
        />
      </div>

      <!-- RECENT PROJECTS / QUOTATIONS TABLE SECTION -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
              <FileText class="w-4 h-4" />
            </div>
            <div>
              <h3 class="text-sm font-black text-slate-900 dark:text-white">Penawaran Terbaru & Aksi Cepat</h3>
              <p class="text-[11px] text-slate-400">Dokumen estimasi dan proposal penawaran harga software terbaru</p>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <button
              @click="showExportModal = true"
              class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-extrabold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition flex items-center gap-1.5 cursor-pointer active:scale-95 shadow-xs"
            >
              <FileDown class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" />
              <span>Ekspor Laporan</span>
            </button>
            <Link href="/projects" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
              <span>Lihat Semua</span>
              <ArrowRight class="w-3.5 h-3.5" />
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
                  Belum ada dokumen penawaran harga. Klik "Buat Penawaran" untuk memulai proposal baru.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- QUICK CALCULATOR SIMULATOR MODAL (Opsi B: On-Demand Interactive Modal) -->
    <Modal :show="isQuickSimulatorOpen" @close="isQuickSimulatorOpen = false" maxWidth="2xl">
      <div class="p-6 space-y-6">
        
        <!-- Header Modal -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-600/30">
              <Calculator class="w-5 h-5" />
            </div>
            <div>
              <h3 class="text-base font-black text-slate-900 dark:text-white">Kalkulator Simulasi Penawaran Cepat</h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">Eksplorasi estimasi biaya Beli Putus & Langganan SaaS secara instan</p>
            </div>
          </div>
          <button @click="isQuickSimulatorOpen = false" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl cursor-pointer">
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Mode Switcher Tabs -->
        <div class="flex p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl gap-1">
          <button
            @click="mode = 'one_off'"
            class="flex-1 py-2 text-xs font-extrabold rounded-xl transition duration-150 flex items-center justify-center gap-2 cursor-pointer"
            :class="mode === 'one_off' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
          >
            <Banknote class="w-4 h-4" />
            <span>Beli Putus (One-Off Project)</span>
          </button>

          <button
            @click="mode = 'subscription'"
            class="flex-1 py-2 text-xs font-extrabold rounded-xl transition duration-150 flex items-center justify-center gap-2 cursor-pointer"
            :class="mode === 'subscription' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
          >
            <RefreshCw class="w-4 h-4" />
            <span>Berlangganan (SaaS Recurring)</span>
          </button>
        </div>

        <!-- Simulator Form Inputs -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <CurrencyInput v-model="basePrice" label="Estimasi Harga Dasar Modul" helperText="Total nilai modul dasar" />

          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Faktor Kompleksitas</label>
            <select
              v-model.number="complexity"
              class="w-full px-3 py-2 text-xs font-bold bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500"
            >
              <option :value="0.8">0.8x (Sederhana)</option>
              <option :value="1.0">1.0x (Standar)</option>
              <option :value="1.25">1.25x (Sedang)</option>
              <option :value="1.5">1.5x (Kompleks)</option>
              <option :value="2.0">2.0x (Enterprise / High Risk)</option>
            </select>
          </div>

          <CurrencyInput v-model="setupFee" label="Biaya Setup Awal (Onboarding)" helperText="Biaya satu kali deploy" />

          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Garansi Maintenance (SLA)</label>
            <select
              v-model.number="maintenanceMonths"
              class="w-full px-3 py-2 text-xs font-bold bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500"
            >
              <option :value="1">1 Bulan SLA</option>
              <option :value="3">3 Bulan (Standar SLA)</option>
              <option :value="6">6 Bulan (Extended SLA)</option>
              <option :value="12">12 Bulan (Full Year SLA)</option>
            </select>
          </div>
        </div>

        <!-- SaaS Extra Inputs -->
        <div v-if="mode === 'subscription'" class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800">
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Metode Tagihan</label>
            <select v-model="subBasis" class="w-full px-3 py-2 text-xs font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl">
              <option value="modular">Flat Modular</option>
              <option value="per_user">Per-User</option>
              <option value="hybrid">Hybrid (Modul + User)</option>
            </select>
          </div>

          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Siklus Penagihan</label>
            <select v-model="subCycle" class="w-full px-3 py-2 text-xs font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl">
              <option value="monthly">Bulanan</option>
              <option value="yearly">Tahunan</option>
            </select>
          </div>

          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Durasi Kontrak (Bulan)</label>
            <input v-model.number="subDuration" type="number" min="1" class="w-full px-3 py-2 text-xs font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl" />
          </div>
        </div>

        <!-- Result Box -->
        <div 
          class="p-5 rounded-2xl border transition-all duration-300" 
          :class="mode === 'one_off' ? 'bg-gradient-to-br from-indigo-50/80 to-slate-50 dark:from-indigo-950/40 dark:to-slate-900 border-indigo-200 dark:border-indigo-800/60' : 'bg-gradient-to-br from-emerald-50/80 to-slate-50 dark:from-emerald-950/40 dark:to-slate-900 border-emerald-200 dark:border-emerald-800/60'"
        >
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                <Sparkles class="w-3.5 h-3.5 text-indigo-500" />
                <span>{{ mode === 'one_off' ? 'TOTAL ESTIMASI BELI PUTUS' : 'TOTAL ESTIMASI KONTRAK SAAS' }}</span>
              </span>
              <h4 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                {{ formatRupiah(mode === 'one_off' ? oneOffTotal : saasGrandTotal) }}
              </h4>
              <p v-if="mode === 'subscription'" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">
                Tagihan: {{ formatRupiah(saasRecurring) }} {{ subCycle === 'yearly' ? '/ tahun' : '/ bulan' }}
              </p>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Garansi SLA: <span class="font-bold text-slate-700 dark:text-slate-300">{{ slaText }}</span>
              </p>
            </div>

            <div class="flex items-center gap-2.5">
              <button
                @click="goToCreate"
                class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center gap-2 cursor-pointer"
              >
                <Plus class="w-4 h-4 stroke-[3]" />
                <span>Transfer ke Proposal</span>
              </button>
            </div>
          </div>
        </div>

      </div>
    </Modal>

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
