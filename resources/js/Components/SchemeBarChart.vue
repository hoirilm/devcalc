<script setup>
import { computed, ref } from 'vue';
import { 
  Briefcase, 
  Repeat, 
  DollarSign, 
  TrendingUp, 
  Layers, 
  CheckCircle2,
  PieChart,
  FileCheck,
  FileEdit,
  ArrowUpRight,
  ShieldCheck
} from 'lucide-vue-next';

const props = defineProps({
  oneOffCount: {
    type: Number,
    default: 0
  },
  subscriptionCount: {
    type: Number,
    default: 0
  },
  stats: {
    type: Object,
    default: () => ({})
  },
  recentProjects: {
    type: Array,
    default: () => []
  }
});

const activeTab = ref('valuation'); // 'valuation' | 'status'

// Calculations
const totalCount = computed(() => (props.oneOffCount + props.subscriptionCount) || 1);
const oneOffPercent = computed(() => Math.round((props.oneOffCount / totalCount.value) * 100));
const subPercent = computed(() => Math.round((props.subscriptionCount / totalCount.value) * 100));

// Valuations from props.stats or fallback calculation
const totalOneOffVal = computed(() => {
  if (props.stats?.one_off_value !== undefined) return props.stats.one_off_value;
  return props.recentProjects
    .filter(p => p.billing_type === 'one_off')
    .reduce((sum, p) => sum + (p.grand_total || 0), 0);
});

const totalSubVal = computed(() => {
  if (props.stats?.subscription_value !== undefined) return props.stats.subscription_value;
  return props.recentProjects
    .filter(p => p.billing_type === 'subscription')
    .reduce((sum, p) => sum + (p.grand_total || 0), 0);
});

const totalAllVal = computed(() => {
  if (props.stats?.total_value !== undefined && props.stats.total_value > 0) {
    return props.stats.total_value;
  }
  return totalOneOffVal.value + totalSubVal.value || 1;
});

const oneOffValPercent = computed(() => Math.round((totalOneOffVal.value / totalAllVal.value) * 100));
const subValPercent = computed(() => Math.round((totalSubVal.value / totalAllVal.value) * 100));

// SVG Donut Calculations (Circumference r=40, C=2*pi*40 = 251.327)
const circumference = 251.327;
const oneOffStrokeDash = computed(() => {
  const pct = oneOffValPercent.value;
  return `${(pct / 100) * circumference} ${circumference}`;
});
const subStrokeDash = computed(() => {
  const pct = subValPercent.value;
  return `${(pct / 100) * circumference} ${circumference}`;
});
const subStrokeOffset = computed(() => {
  const pct = oneOffValPercent.value;
  return -((pct / 100) * circumference);
});

// Status Stats
const draftCount = computed(() => props.stats?.draft_count || props.recentProjects.filter(p => p.status === 'Draft').length);
const officialCount = computed(() => props.stats?.official_count || props.recentProjects.filter(p => p.status !== 'Draft').length);
const draftPercent = computed(() => Math.round((draftCount.value / totalCount.value) * 100));
const officialPercent = computed(() => Math.round((officialCount.value / totalCount.value) * 100));

const avgDealSizeFormatted = computed(() => {
  if (props.stats?.avg_deal_size_formatted) return props.stats.avg_deal_size_formatted;
  return formatRupiah(totalAllVal.value / totalCount.value);
});

const avgMaintenanceFormatted = computed(() => {
  if (props.stats?.avg_maintenance_formatted) return props.stats.avg_maintenance_formatted;
  if (props.stats?.avg_maintenance_months) return `Rata-rata ${props.stats.avg_maintenance_months} Bulan SLA`;
  return 'Rata-rata 3 Bulan SLA';
});

function formatRupiah(num) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num || 0);
}
</script>

<template>
  <div class="space-y-6">
    
    <!-- Top Header Bar for Widget -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
      <div class="space-y-0.5">
        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
          <PieChart class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
          <span>Analisis Model Bisnis & Distribusi Valuasi</span>
        </h3>
        <p class="text-xs text-slate-500 dark:text-slate-400">
          Perbandingan pendapatan Beli Putus (One-Off) vs Nilai Kontrak Berlangganan (SaaS).
        </p>
      </div>

      <!-- Segment Toggle View -->
      <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl border border-slate-200/80 dark:border-slate-700/80 self-start sm:self-auto text-xs">
        <button
          @click="activeTab = 'valuation'"
          :class="activeTab === 'valuation' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 font-semibold hover:text-slate-900 dark:hover:text-white'"
          class="px-3 py-1.5 rounded-lg transition cursor-pointer"
        >
          Valuasi Skema
        </button>

        <button
          @click="activeTab = 'status'"
          :class="activeTab === 'status' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 font-semibold hover:text-slate-900 dark:hover:text-white'"
          class="px-3 py-1.5 rounded-lg transition cursor-pointer"
        >
          Kesiapan Status
        </button>
      </div>
    </div>

    <!-- VIEW 1: VALUASI SKEMA (Donut Chart + Side Breakdown Cards) -->
    <div v-if="activeTab === 'valuation'" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
      
      <!-- Left Column: Modern SVG Donut Ring Chart -->
      <div class="lg:col-span-5 h-full flex flex-col items-center justify-center p-5 bg-slate-50/60 dark:bg-slate-800/40 rounded-3xl border border-slate-200/60 dark:border-slate-800/60 relative shadow-2xs">
        <div class="relative w-44 h-44 flex items-center justify-center">
          <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
            <!-- Background Ring -->
            <circle
              cx="50"
              cy="50"
              r="40"
              stroke-width="12"
              class="stroke-slate-200 dark:stroke-slate-700/60 fill-none"
            />

            <!-- Beli Putus Ring Segment (Indigo) -->
            <circle
              cx="50"
              cy="50"
              r="40"
              stroke-width="12"
              class="stroke-indigo-600 transition-all duration-700 ease-out fill-none"
              :stroke-dasharray="oneOffStrokeDash"
              stroke-linecap="round"
            />

            <!-- SaaS Ring Segment (Emerald) -->
            <circle
              cx="50"
              cy="50"
              r="40"
              stroke-width="12"
              class="stroke-emerald-500 transition-all duration-700 ease-out fill-none"
              :stroke-dasharray="subStrokeDash"
              :stroke-dashoffset="subStrokeOffset"
              stroke-linecap="round"
            />
          </svg>

          <!-- Center Summary Text -->
          <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-2">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Valuasi</span>
            <span class="text-sm font-black text-slate-900 dark:text-white tracking-tight leading-tight mt-0.5">
              {{ formatRupiah(totalAllVal) }}
            </span>
            <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 mt-0.5">
              {{ totalCount }} Penawaran
            </span>
          </div>
        </div>

        <!-- Donut Legend -->
        <div class="flex items-center gap-4 mt-3 text-xs font-bold">
          <div class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-md bg-indigo-600"></span>
            <span class="text-slate-700 dark:text-slate-300">Beli Putus ({{ oneOffValPercent }}%)</span>
          </div>
          <div class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-md bg-emerald-500"></span>
            <span class="text-slate-700 dark:text-slate-300">SaaS ({{ subValPercent }}%)</span>
          </div>
        </div>
      </div>

      <!-- Right Column: Model Breakdown Cards -->
      <div class="lg:col-span-7 space-y-4">
        
        <!-- Beli Putus Model Card -->
        <div class="p-4 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800/80 space-y-2.5 transition hover:shadow-sm">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                <Briefcase class="w-4 h-4" />
              </div>
              <div>
                <h4 class="text-xs font-black text-slate-900 dark:text-white">Skema Beli Putus (One-Off)</h4>
                <p class="text-[10px] text-slate-500 dark:text-slate-400">{{ oneOffCount }} Dokumen Penawaran Terdaftar</p>
              </div>
            </div>

            <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300">
              {{ oneOffValPercent }}% Share Valuasi
            </span>
          </div>

          <div class="flex items-baseline justify-between pt-1">
            <span class="text-lg font-black text-slate-900 dark:text-white">
              {{ formatRupiah(totalOneOffVal) }}
            </span>
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">
              Porsi Dokumen: {{ oneOffPercent }}%
            </span>
          </div>

          <!-- Progress Bar -->
          <div class="w-full h-2 bg-indigo-200/60 dark:bg-indigo-900/50 rounded-full overflow-hidden">
            <div
              class="h-full bg-indigo-600 rounded-full transition-all duration-500"
              :style="{ width: oneOffValPercent + '%' }"
            ></div>
          </div>
        </div>

        <!-- SaaS Model Card -->
        <div class="p-4 rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/80 space-y-2.5 transition hover:shadow-sm">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                <Repeat class="w-4 h-4" />
              </div>
              <div>
                <h4 class="text-xs font-black text-slate-900 dark:text-white">Skema Berlangganan (SaaS)</h4>
                <p class="text-[10px] text-slate-500 dark:text-slate-400">{{ subscriptionCount }} Dokumen Kontrak SaaS Berulang</p>
              </div>
            </div>

            <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300">
              {{ subValPercent }}% Share Valuasi
            </span>
          </div>

          <div class="flex items-baseline justify-between pt-1">
            <span class="text-lg font-black text-slate-900 dark:text-white">
              {{ formatRupiah(totalSubVal) }}
            </span>
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">
              Porsi Dokumen: {{ subPercent }}%
            </span>
          </div>

          <!-- Progress Bar -->
          <div class="w-full h-2 bg-emerald-200/60 dark:bg-emerald-900/50 rounded-full overflow-hidden">
            <div
              class="h-full bg-emerald-500 rounded-full transition-all duration-500"
              :style="{ width: subValPercent + '%' }"
            ></div>
          </div>
        </div>

      </div>
    </div>

    <!-- VIEW 2: KESIAPAN STATUS DOKUMEN -->
    <div v-else-if="activeTab === 'status'" class="space-y-4">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        
        <!-- Draft Status Card -->
        <div class="p-5 rounded-2xl bg-amber-50/50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/80 space-y-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
              <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                <FileEdit class="w-4.5 h-4.5" />
              </div>
              <div>
                <h4 class="text-xs font-black text-slate-900 dark:text-white">Status Draft Berjalan</h4>
                <p class="text-[10px] text-slate-500 dark:text-slate-400">Penawaran dalam tahap kalkulasi / revisi</p>
              </div>
            </div>
            <span class="text-xs font-black text-amber-700 dark:text-amber-300 px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-900/60">
              {{ draftCount }} Dokumen
            </span>
          </div>

          <div class="space-y-1">
            <div class="flex items-center justify-between text-xs font-bold">
              <span class="text-slate-600 dark:text-slate-400">Porsi Dokumen Draft</span>
              <span class="text-amber-600 dark:text-amber-400 font-black">{{ draftPercent }}%</span>
            </div>
            <div class="w-full h-2.5 bg-amber-200/60 dark:bg-amber-900/50 rounded-full overflow-hidden">
              <div class="h-full bg-amber-500 rounded-full transition-all duration-500" :style="{ width: draftPercent + '%' }"></div>
            </div>
          </div>
        </div>

        <!-- Official Status Card -->
        <div class="p-5 rounded-2xl bg-sky-50/50 dark:bg-sky-950/30 border border-sky-200 dark:border-sky-800/80 space-y-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
              <div class="w-9 h-9 rounded-xl bg-sky-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                <FileCheck class="w-4.5 h-4.5" />
              </div>
              <div>
                <h4 class="text-xs font-black text-slate-900 dark:text-white">Status Resmi (Generated)</h4>
                <p class="text-[10px] text-slate-500 dark:text-slate-400">Penawaran resmi siap cetak / terbit PDF</p>
              </div>
            </div>
            <span class="text-xs font-black text-sky-700 dark:text-sky-300 px-2.5 py-1 rounded-full bg-sky-100 dark:bg-sky-900/60">
              {{ officialCount }} Dokumen
            </span>
          </div>

          <div class="space-y-1">
            <div class="flex items-center justify-between text-xs font-bold">
              <span class="text-slate-600 dark:text-slate-400">Porsi Dokumen Resmi</span>
              <span class="text-sky-600 dark:text-sky-400 font-black">{{ officialPercent }}%</span>
            </div>
            <div class="w-full h-2.5 bg-sky-200/60 dark:bg-sky-900/50 rounded-full overflow-hidden">
              <div class="h-full bg-sky-600 rounded-full transition-all duration-500" :style="{ width: officialPercent + '%' }"></div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Bottom KPI Metric Strip -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
      <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between">
        <div class="space-y-0.5">
          <span class="text-[10px] font-extrabold uppercase text-slate-400">Rata-rata Deal Size</span>
          <div class="text-xs font-black text-slate-900 dark:text-white">{{ avgDealSizeFormatted }}</div>
        </div>
        <TrendingUp class="w-4 h-4 text-indigo-500 shrink-0" />
      </div>

      <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between">
        <div class="space-y-0.5">
          <span class="text-[10px] font-extrabold uppercase text-slate-400">Rasio Model Bisnis</span>
          <div class="text-xs font-black text-slate-900 dark:text-white">{{ oneOffValPercent }}% Beli Putus : {{ subValPercent }}% SaaS</div>
        </div>
        <Layers class="w-4 h-4 text-emerald-500 shrink-0" />
      </div>

      <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between">
        <div class="space-y-0.5">
          <span class="text-[10px] font-extrabold uppercase text-slate-400">Rata-rata Durasi SLA</span>
          <div class="text-xs font-black text-slate-900 dark:text-white">{{ avgMaintenanceFormatted }}</div>
        </div>
        <ShieldCheck class="w-4 h-4 text-sky-500 shrink-0" />
      </div>
    </div>

  </div>
</template>
