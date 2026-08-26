<script setup>
import { ref, computed, nextTick, watch } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CurrencyInput from '@/Components/CurrencyInput.vue';
import { 
  Plus, 
  Trash2, 
  ArrowLeft, 
  ArrowRight,
  Sparkles,
  Calculator,
  ShieldCheck,
  Building,
  CheckCircle2,
  Clock,
  Layers,
  Server,
  Cloud,
  FileText,
  Check,
  AlertCircle,
  CreditCard
} from 'lucide-vue-next';

const props = defineProps({
  modules: Array,
});

// Stepper State (1: Client Info, 2: Software Features, 3: Server & Hosting, 4: Billing Scheme & Finalize)
const currentStep = ref(1);
const stepValidationError = ref('');

const form = useForm({
  client_name: '',
  project_category: 'Web Application / SaaS Platform',
  estimated_timeline: '3 - 4 Minggu (Standar Pengerjaan)',
  billing_type: 'one_off',
  subscription_basis: 'modular',
  billing_cycle: 'monthly',
  apply_annual_discount: true,
  discount_percentage: 20,
  subscription_duration: 12,
  user_count: 10,
  price_per_user: 50000,
  setup_fee: 0,
  maintenance_months: 3,
  status: 'Draft',
  notes: '',
  items: [
    {
      module_id: '',
      item_name: '',
      base_price: 0,
      complexity_weight: 1.0,
      is_hosting: false,
    }
  ]
});

// Category & Timeline Preset Configurations
const categoryPresets = [
  'Web Application / SaaS Platform',
  'E-Commerce & Toko Online',
  'Company Profile & Landing Page',
  'Sistem Informasi Internal / ERP & CRM',
  'Custom Backend API & Integrasi',
  'Mobile Application (Android / iOS)',
];

const timelinePresets = [
  '1 - 2 Minggu (Fast-track MVP)',
  '3 - 4 Minggu (Standar Pengerjaan)',
  '1 - 2 Bulan (Skala Sedang)',
  '2 - 3 Bulan (Komprehensif Enterprise)',
  '3 - 6 Bulan (Multi-Phase Project)',
];

const selectedCategoryPreset = ref(
  categoryPresets.includes(form.project_category) ? form.project_category : (form.project_category ? '__custom__' : categoryPresets[0])
);

const selectedTimelinePreset = ref(
  timelinePresets.includes(form.estimated_timeline) ? form.estimated_timeline : (form.estimated_timeline ? '__custom__' : timelinePresets[1])
);

function onCategoryPresetChange(e) {
  const val = e.target.value;
  selectedCategoryPreset.value = val;
  if (val !== '__custom__') {
    form.project_category = val;
  } else {
    form.project_category = '';
  }
}

function onTimelinePresetChange(e) {
  const val = e.target.value;
  selectedTimelinePreset.value = val;
  if (val !== '__custom__') {
    form.estimated_timeline = val;
  } else {
    form.estimated_timeline = '';
  }
}

// Helper to determine if a module or item is Hosting & Infrastruktur
function isHostingCategory(cat) {
  if (!cat) return false;
  const lower = cat.toLowerCase();
  return lower.includes('hosting') || lower.includes('infrastruktur') || lower.includes('infrastructure');
}

function isHostingModule(item) {
  if (item.is_hosting) return true;
  if (!item.module_id) return false;
  const mod = (props.modules || []).find(m => m.id == item.module_id);
  return isHostingCategory(mod?.category);
}

// Separate catalogs
const softwareCatalog = computed(() => {
  return (props.modules || []).filter(m => !isHostingCategory(m.category));
});

const hostingCatalog = computed(() => {
  return (props.modules || []).filter(m => isHostingCategory(m.category));
});

// Step 2 Software Items
function addSoftwareItem() {
  form.items.push({
    module_id: '',
    item_name: '',
    base_price: 0,
    complexity_weight: 1.0,
    is_hosting: false,
  });
}

function removeSoftwareItem(index) {
  const softwareItems = form.items.filter(i => !isHostingModule(i));
  if (softwareItems.length > 1) {
    const itemToRemove = softwareItems[index];
    const actualIndex = form.items.indexOf(itemToRemove);
    if (actualIndex > -1) {
      form.items.splice(actualIndex, 1);
    }
  }
}

function onSoftwareModuleSelect(index, event) {
  const raw = event.target.value;
  const selectedId = raw ? Number(raw) : '';
  
  const softwareItems = form.items.filter(i => !isHostingModule(i));
  const targetItem = softwareItems[index];
  if (!targetItem) return;

  targetItem.module_id = selectedId;
  if (!selectedId) return;
  const mod = props.modules.find(m => m.id == selectedId);
  if (mod) {
    targetItem.item_name = mod.name;
    if (form.billing_type === 'subscription') {
      const sub = mod.subscription_price > 0 ? mod.subscription_price : Math.round(mod.base_price * 0.08);
      targetItem.base_price = sub;
    } else {
      targetItem.base_price = mod.base_price;
    }
  }
}

// Step 3 Hosting Mode State ('none' | 'shared' | 'vps')
const hostingMode = ref('none');
const hostingMonthlyRate = ref(50000);
const hostingDurationMonths = ref(12);

const currentHostingItem = computed(() => {
  return form.items.find(item => isHostingModule(item)) || null;
});

const hostingCalculationText = computed(() => {
  if (!currentHostingItem.value || hostingMode.value === 'none') return null;
  const monthly = Number(hostingMonthlyRate.value || 0);
  const months = form.billing_type === 'subscription' 
    ? (form.billing_cycle === 'yearly' ? (form.subscription_duration || 1) * 12 : (form.subscription_duration || 1))
    : Number(hostingDurationMonths.value || 12);
  const total = monthly * months;
  return {
    monthly,
    months,
    total,
    formatted: `${formatRupiah(monthly)} / bln × ${months} Bulan = ${formatRupiah(total)}`,
  };
});

function syncHostingBasePrice() {
  if (currentHostingItem.value) {
    if (form.billing_type === 'subscription') {
      currentHostingItem.value.base_price = Number(hostingMonthlyRate.value || 0);
    } else {
      currentHostingItem.value.base_price = Math.round(Number(hostingMonthlyRate.value || 0) * Number(hostingDurationMonths.value || 1));
    }
  }
}

watch([() => form.billing_type, () => form.billing_cycle, () => form.subscription_duration], () => {
  if (form.billing_type === 'subscription') {
    const totalMonths = form.billing_cycle === 'yearly' 
      ? Number(form.subscription_duration || 1) * 12 
      : Number(form.subscription_duration || 1);
    hostingDurationMonths.value = totalMonths;
  }
}, { immediate: true });

watch([hostingMonthlyRate, hostingDurationMonths, () => form.billing_type, () => form.billing_cycle, () => form.subscription_duration], () => {
  syncHostingBasePrice();
});

function setHostingMode(mode) {
  hostingMode.value = mode;
  const nonHosting = form.items.filter(item => !isHostingModule(item));
  
  if (mode === 'none') {
    form.items = nonHosting;
  } else if (mode === 'shared') {
    hostingMonthlyRate.value = 50000;
    hostingDurationMonths.value = 12;
    const initialBase = form.billing_type === 'subscription' ? 50000 : 50000 * 12;
    nonHosting.push({
      module_id: null,
      item_name: 'Shared Hosting & Setup Domain (.com / .id)',
      base_price: initialBase,
      complexity_weight: 1.0,
      is_hosting: true,
    });
    form.items = nonHosting;
  } else if (mode === 'vps') {
    hostingMonthlyRate.value = 150000;
    hostingDurationMonths.value = 12;
    const initialBase = form.billing_type === 'subscription' ? 150000 : 150000 * 12;
    nonHosting.push({
      module_id: null,
      item_name: 'Cloud VPS Server Deployment (Linux Ubuntu + Nginx)',
      base_price: initialBase,
      complexity_weight: 1.0,
      is_hosting: true,
    });
    form.items = nonHosting;
  }
}

// Update item base prices & validate SLA when billing type changes (One-Off vs Subscription)
watch(() => form.billing_type, (newType) => {
  if (newType === 'one_off') {
    const validSlaOptions = [1, 3, 6, 12];
    if (!validSlaOptions.includes(Number(form.maintenance_months))) {
      form.maintenance_months = 3;
    }
  }

  form.items.forEach(item => {
    if (item.module_id) {
      const mod = props.modules.find(m => m.id == item.module_id);
      if (mod) {
        if (newType === 'subscription') {
          item.base_price = mod.subscription_price > 0 ? mod.subscription_price : Math.round(mod.base_price * 0.08);
        } else {
          item.base_price = mod.base_price;
        }
      }
    }
  });
});

// Live Calculations
const softwareItemsTotal = computed(() => {
  return form.items
    .filter(item => !isHostingModule(item))
    .reduce((sum, item) => {
      const base = item.base_price || 0;
      const comp = item.complexity_weight || 1.0;
      return sum + Math.round(base * comp);
    }, 0);
});

const hostingDurationTotal = computed(() => {
  if (hostingMode.value === 'none' || !currentHostingItem.value) return 0;
  const monthly = Number(hostingMonthlyRate.value || 0);
  const months = Number(hostingDurationMonths.value || 12);
  return monthly * months;
});

const hostingItemsTotal = computed(() => {
  return hostingDurationTotal.value;
});

const itemsTotal = computed(() => {
  return softwareItemsTotal.value + hostingDurationTotal.value;
});

const monthlyBaseRecurring = computed(() => {
  const softwareTotal = softwareItemsTotal.value;
  const hostingMonthly = (hostingMode.value !== 'none' && currentHostingItem.value)
    ? Number(hostingMonthlyRate.value || 0)
    : 0;
  const userTotal = (form.subscription_basis === 'per_user' || form.subscription_basis === 'hybrid')
    ? Math.round((form.user_count || 0) * (form.price_per_user || 0))
    : 0;

  if (form.subscription_basis === 'per_user') return userTotal + hostingMonthly;
  if (form.subscription_basis === 'hybrid') return softwareTotal + userTotal + hostingMonthly;
  return softwareTotal + hostingMonthly; // modular
});

const userRecurringTotal = computed(() => {
  if (form.subscription_basis === 'per_user' || form.subscription_basis === 'hybrid') {
    const monthly = Math.round((form.user_count || 0) * (form.price_per_user || 0));
    return form.billing_cycle === 'yearly' ? monthly * 12 : monthly;
  }
  return 0;
});

const annualSavings = computed(() => {
  if (form.billing_cycle === 'yearly' && form.apply_annual_discount) {
    const yearlyFull = monthlyBaseRecurring.value * 12;
    const pct = Number(form.discount_percentage || 20);
    return Math.round(yearlyFull * (pct / 100));
  }
  return 0;
});

const recurringPerCycle = computed(() => {
  if (form.billing_cycle === 'yearly') {
    const yearlyFull = monthlyBaseRecurring.value * 12;
    if (form.apply_annual_discount) {
      const pct = Number(form.discount_percentage || 20);
      return Math.round(yearlyFull * ((100 - pct) / 100));
    }
    return yearlyFull;
  }
  return monthlyBaseRecurring.value;
});

const originalGrandTotal = computed(() => {
  const setup = form.setup_fee || 0;
  if (form.billing_type === 'subscription') {
    const duration = form.subscription_duration || 1;
    const cycleFull = form.billing_cycle === 'yearly' ? (monthlyBaseRecurring.value * 12) : monthlyBaseRecurring.value;
    return setup + (cycleFull * duration);
  }
  return itemsTotal.value + setup;
});

const calculatedGrandTotal = computed(() => {
  const setup = form.setup_fee || 0;
  if (form.billing_type === 'subscription') {
    const duration = form.subscription_duration || 1;
    return setup + (recurringPerCycle.value * duration);
  }
  return itemsTotal.value + setup;
});

watch([() => form.billing_type, () => form.subscription_duration, () => form.billing_cycle], () => {
  if (form.billing_type === 'subscription') {
    const dur = form.subscription_duration || 1;
    form.maintenance_months = form.billing_cycle === 'yearly' ? dur * 12 : dur;
  }
}, { immediate: true });

function formatRupiah(num) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num || 0);
}

// Stepper Navigation & Validation
function goToStep(step) {
  stepValidationError.value = '';
  
  if (step > 1 && !form.client_name.trim()) {
    stepValidationError.value = 'Silakan isi Nama Perusahaan / Klien terlebih dahulu.';
    currentStep.value = 1;
    return;
  }

  if (step > 2) {
    const validSoftwareItems = form.items.filter(i => !isHostingModule(i) && i.item_name.trim());
    if (validSoftwareItems.length === 0) {
      stepValidationError.value = 'Silakan isi minimal 1 modul fitur software.';
      currentStep.value = 2;
      return;
    }
  }

  currentStep.value = step;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function nextStep() {
  goToStep(currentStep.value + 1);
}

function prevStep() {
  if (currentStep.value > 1) {
    currentStep.value--;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
}

function submit(targetStatus) {
  stepValidationError.value = '';
  if (!form.client_name.trim()) {
    stepValidationError.value = 'Nama Perusahaan / Klien wajib diisi.';
    currentStep.value = 1;
    return;
  }

  const validItems = form.items.filter(i => i.item_name.trim());
  if (validItems.length === 0) {
    stepValidationError.value = 'Minimal harus ada 1 fitur software dalam penawaran.';
    currentStep.value = 2;
    return;
  }

  if (targetStatus) {
    form.status = targetStatus;
  }
  form.post('/projects');
}
</script>

<template>
  <Head title="Buat Penawaran Harga Baru" />

  <AppLayout title="Buat Penawaran Harga Baru">
    <div class="max-w-7xl mx-auto space-y-6">
      
      <!-- Top Action Bar Header -->
      <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
          <button
            @click="router.get('/projects')"
            class="w-11 h-11 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 hover:border-indigo-300 dark:hover:border-indigo-800 transition-all duration-200 cursor-pointer shadow-xs flex items-center justify-center shrink-0 active:scale-95 group"
            title="Kembali ke Daftar Penawaran"
          >
            <ArrowLeft class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" />
          </button>

          <div class="space-y-0.5">
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
              Buat Penawaran Harga Baru
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
              Alur 4 langkah: 1. Info Klien &bull; 2. Fitur Software &bull; 3. Hosting & Server &bull; 4. Skema & Finalisasi.
            </p>
          </div>
        </div>

        <div class="hidden sm:flex items-center gap-2">
          <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Langkah {{ currentStep }} dari 4</span>
        </div>
      </div>

      <!-- WIZARD STEPPER PROGRESS BAR -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-3 shadow-xs">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
          
          <!-- Step 1 Tab Button -->
          <button
            type="button"
            @click="goToStep(1)"
            class="flex items-center gap-3 p-3 rounded-2xl transition-all duration-200 text-left cursor-pointer"
            :class="currentStep === 1 
              ? 'bg-indigo-50 dark:bg-indigo-950/70 border border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 shadow-xs' 
              : currentStep > 1 
                ? 'bg-slate-50 dark:bg-slate-800/60 hover:bg-slate-100 dark:hover:bg-slate-800 text-emerald-700 dark:text-emerald-400' 
                : 'bg-transparent text-slate-400 dark:text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800/40'"
          >
            <div 
              class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0"
              :class="currentStep === 1 
                ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' 
                : currentStep > 1 
                  ? 'bg-emerald-500 text-white' 
                  : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400'"
            >
              <Check v-if="currentStep > 1" class="w-4 h-4" />
              <span v-else>1</span>
            </div>
            <div class="min-w-0">
              <div class="text-[10px] font-bold uppercase tracking-wider opacity-70">Langkah 1</div>
              <div class="text-xs font-black truncate">Info Klien & Profil</div>
            </div>
          </button>

          <!-- Step 2 Tab Button -->
          <button
            type="button"
            @click="goToStep(2)"
            class="flex items-center gap-3 p-3 rounded-2xl transition-all duration-200 text-left cursor-pointer"
            :class="currentStep === 2 
              ? 'bg-indigo-50 dark:bg-indigo-950/70 border border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 shadow-xs' 
              : currentStep > 2 
                ? 'bg-slate-50 dark:bg-slate-800/60 hover:bg-slate-100 dark:hover:bg-slate-800 text-emerald-700 dark:text-emerald-400' 
                : 'bg-transparent text-slate-400 dark:text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800/40'"
          >
            <div 
              class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0"
              :class="currentStep === 2 
                ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' 
                : currentStep > 2 
                  ? 'bg-emerald-500 text-white' 
                  : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400'"
            >
              <Check v-if="currentStep > 2" class="w-4 h-4" />
              <span v-else>2</span>
            </div>
            <div class="min-w-0">
              <div class="text-[10px] font-bold uppercase tracking-wider opacity-70">Langkah 2</div>
              <div class="text-xs font-black truncate">Fitur Software</div>
            </div>
          </button>

          <!-- Step 3 Tab Button -->
          <button
            type="button"
            @click="goToStep(3)"
            class="flex items-center gap-3 p-3 rounded-2xl transition-all duration-200 text-left cursor-pointer"
            :class="currentStep === 3 
              ? 'bg-indigo-50 dark:bg-indigo-950/70 border border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 shadow-xs' 
              : 'bg-transparent text-slate-400 dark:text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800/40'"
          >
            <div 
              class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0"
              :class="currentStep === 3 
                ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' 
                : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400'"
            >
              <span>3</span>
            </div>
            <div class="min-w-0">
              <div class="text-[10px] font-bold uppercase tracking-wider opacity-70">Langkah 3</div>
              <div class="text-xs font-black truncate">Skema, Server & Finalisasi</div>
            </div>
          </button>

        </div>
      </div>

      <!-- Step Validation Alert -->
      <div v-if="stepValidationError" class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-bold flex items-center gap-2.5 animate-shake">
        <AlertCircle class="w-4 h-4 shrink-0" />
        <span>{{ stepValidationError }}</span>
      </div>

      <!-- Main Form (2-Column Grid Layout) -->
      <form @submit.prevent="submit(form.status)" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Main Column (Step Content) -->
        <div class="lg:col-span-8 space-y-6">
          
          <!-- ========================================================================= -->
          <!-- STEP 1: INFORMASI KLIEN & PROYEK -->
          <!-- ========================================================================= -->
          <div v-if="currentStep === 1" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
              <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                  <Building class="w-4 h-4 text-indigo-500" />
                  <span>1. Informasi Proyek & Klien</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                  Masukkan identitas klien dan parameter umum proyek untuk penawaran ini.
                </p>
              </div>
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Step 1 of 4</span>
            </div>

            <div class="space-y-5">
              <!-- Client Name -->
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                  Nama Perusahaan / Klien / Instansi *
                </label>
                <input
                  v-model="form.client_name"
                  type="text"
                  required
                  autofocus
                  placeholder="e.g. PT Maju Bersama Digital atau CV Sinar Jaya"
                  class="w-full px-4 py-3 text-sm font-semibold bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 text-slate-900 dark:text-white transition shadow-2xs"
                />
                <span v-if="form.errors.client_name" class="text-[11px] text-rose-500 block">{{ form.errors.client_name }}</span>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                  Nama ini akan dicetak tebal pada kepala surat penawaran (*Quotation To: Client Name*).
                </p>
              </div>

              <!-- Context Details: Target Timeline & Solution Type -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                <!-- Solution Category -->
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                    Kategori Solusi / Tipe Sistem (Opsional)
                  </label>
                  <select
                    :value="selectedCategoryPreset"
                    @change="onCategoryPresetChange"
                    class="w-full px-3.5 py-2.5 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-xl text-slate-900 dark:text-white cursor-pointer"
                  >
                    <option v-for="cat in categoryPresets" :key="cat" :value="cat">
                      {{ cat }}
                    </option>
                    <option value="__custom__">✍️ Kustom / Ketik Kategori Sendiri...</option>
                  </select>
                  
                  <!-- Custom text input when custom option selected -->
                  <input
                    v-if="selectedCategoryPreset === '__custom__'"
                    v-model="form.project_category"
                    type="text"
                    autofocus
                    placeholder="Ketik kategori sistem (e.g. AI Chatbot Assistant, Web3 dApp)"
                    class="w-full px-3.5 py-2 text-xs font-semibold bg-white dark:bg-slate-900 border border-indigo-300 dark:border-indigo-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition"
                  />
                  <p class="text-[11px] text-slate-400">Pilih dari rekomendasi atau ketik kategori sistem sendiri.</p>
                </div>

                <!-- Estimated Timeline -->
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                    Estimasi Target Timeline Deliverable (Opsional)
                  </label>
                  <select
                    :value="selectedTimelinePreset"
                    @change="onTimelinePresetChange"
                    class="w-full px-3.5 py-2.5 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-xl text-slate-900 dark:text-white cursor-pointer"
                  >
                    <option v-for="time in timelinePresets" :key="time" :value="time">
                      {{ time }}
                    </option>
                    <option value="__custom__">✍️ Kustom / Ketik Target Waktu Sendiri...</option>
                  </select>

                  <!-- Custom text input when custom option selected -->
                  <input
                    v-if="selectedTimelinePreset === '__custom__'"
                    v-model="form.estimated_timeline"
                    type="text"
                    autofocus
                    placeholder="Ketik target waktu (e.g. 5 Hari Kerja Kilat atau 6 Bulan Multi-Fase)"
                    class="w-full px-3.5 py-2 text-xs font-semibold bg-white dark:bg-slate-900 border border-indigo-300 dark:border-indigo-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition"
                  />
                  <p class="text-[11px] text-slate-400">Target pengerjaan yang disepakati bersama klien.</p>
                </div>
              </div>

              <!-- Quick Info Card -->
              <div class="p-4 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50 flex items-start gap-3 text-xs">
                <Sparkles class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0 mt-0.5" />
                <div class="space-y-1 text-slate-600 dark:text-slate-300">
                  <div class="font-bold text-slate-900 dark:text-white">Alur Estimasi DevCalc:</div>
                  <div>Setelah mengisi profil klien, lanjutkan ke <b>Langkah 2</b> untuk memilih fitur software. Di <b>Langkah 3</b>, Anda dapat menentukan skema pembayaran (Beli Putus atau SaaS), konfigurasi server hosting, serta langsung menerbitkan surat penawaran harga resmi.</div>
                </div>
              </div>
            </div>

            <!-- Step 1 Bottom Nav Button -->
            <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-800">
              <button
                type="button"
                @click="nextStep"
                class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-black rounded-2xl shadow-md shadow-indigo-600/30 transition flex items-center gap-2 cursor-pointer active:scale-95"
              >
                <span>Lanjut ke Step 2: Fitur Software</span>
                <ArrowRight class="w-4 h-4" />
              </button>
            </div>
          </div>

          <!-- ========================================================================= -->
          <!-- STEP 2: FITUR & MODUL APLIKASI (SOFTWARE ENGINEERING SCOPE) -->
          <!-- ========================================================================= -->
          <div v-if="currentStep === 2" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
              <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                  <Layers class="w-4 h-4 text-indigo-500" />
                  <span>2. Rincian Fitur & Modul Software</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                  Pilih modul fungsional software dari master catalog atau ketik kustom tugas rekayasa.
                </p>
              </div>
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Step 2 of 4</span>
            </div>

            <!-- Software Items List -->
            <div class="space-y-4">
              <div
                v-for="(item, index) in form.items.filter(i => !isHostingModule(i))"
                :key="index"
                class="p-5 rounded-3xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/90 dark:border-slate-700/80 space-y-4 transition hover:border-indigo-200 dark:hover:border-indigo-900/60 shadow-2xs"
              >
                <!-- Row 1: Header + Remove -->
                <div class="flex items-center justify-between pb-2 border-b border-slate-200/60 dark:border-slate-700/60">
                  <span class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-lg bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-extrabold text-[11px]">
                      #{{ index + 1 }}
                    </span>
                    <span>Item Fitur Software</span>
                  </span>

                  <button
                    type="button"
                    @click="removeSoftwareItem(index)"
                    :disabled="form.items.filter(i => !isHostingModule(i)).length <= 1"
                    class="text-xs font-semibold text-rose-500 hover:text-rose-700 dark:hover:text-rose-400 transition flex items-center gap-1 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                  >
                    <Trash2 class="w-3.5 h-3.5" />
                    <span>Hapus Fitur</span>
                  </button>
                </div>

                <!-- Row 2: Inputs Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Preset Template Selector -->
                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Pilih Template Master Modul (Opsional)</label>
                    <select
                      v-model="item.module_id"
                      @change="onSoftwareModuleSelect(index, $event)"
                      class="w-full px-3.5 py-2.5 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white cursor-pointer"
                    >
                      <option value="">-- Kustom / Ketik Manual --</option>
                      <option v-for="mod in softwareCatalog" :key="mod.id" :value="mod.id">
                        {{ mod.name }} ({{ formatRupiah(form.billing_type === 'subscription' ? (mod.subscription_price > 0 ? mod.subscription_price : mod.base_price * 0.08) : mod.base_price) }})
                      </option>
                    </select>
                  </div>

                  <!-- Item Name Input -->
                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Fitur / Deskripsi Tugas *</label>
                    <input
                      v-model="item.item_name"
                      type="text"
                      required
                      placeholder="e.g. Modul Autentikasi atau CRUD Produk"
                      class="w-full px-3.5 py-2.5 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white"
                    />
                  </div>

                  <!-- Base Price Currency Input -->
                  <div class="space-y-1.5">
                    <CurrencyInput
                      v-model="item.base_price"
                      :label="form.billing_type === 'subscription' ? 'Tarif Sewa Modul / Bulan' : 'Harga Dasar Modul'"
                    />
                  </div>

                  <!-- Complexity Weight Selector -->
                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Bobot Kompleksitas *</label>
                    <select
                      v-model.number="item.complexity_weight"
                      class="w-full px-3.5 py-2.5 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white cursor-pointer"
                    >
                      <option :value="0.8">0.8x - Sederhana (UI Only / Sederhana)</option>
                      <option :value="1.0">1.0x - Standar (Standard Module / Medium Complexity)</option>
                      <option :value="1.25">1.25x - Sedang (Logic Lanjutan / Integrasi Database)</option>
                      <option :value="1.5">1.5x - Kompleks (Payment Gateway, Webhook & Role Ketat)</option>
                      <option :value="2.0">2.0x - Sangat Kompleks (Enterprise Multi-Tenant / High Risk)</option>
                    </select>
                  </div>
                </div>

                <!-- Sub-Calculation Output -->
                <div class="flex items-center justify-between pt-2 border-t border-slate-200/60 dark:border-slate-700/60 text-xs">
                  <span class="text-[11px] text-slate-400">
                    Rumus Kalkulasi: Harga Dasar &times; Bobot Kompleksitas
                  </span>
                  <div class="text-right">
                    <span class="text-xs font-extrabold text-indigo-600 dark:text-indigo-400">
                      Terhitung: {{ formatRupiah(Math.round(item.base_price * item.complexity_weight)) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Add Software Feature Button -->
            <div class="pt-2">
              <button
                type="button"
                @click="addSoftwareItem"
                class="w-full py-3.5 border-2 border-dashed border-indigo-200 dark:border-indigo-900/60 hover:border-indigo-400 dark:hover:border-indigo-700 rounded-2xl text-xs font-extrabold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50/50 dark:hover:bg-indigo-950/20 transition flex items-center justify-center gap-2 cursor-pointer active:scale-99"
              >
                <Plus class="w-4 h-4" />
                <span>+ Tambah Fitur / Modul Baru</span>
              </button>
            </div>

              <!-- Step 2 Navigation Buttons -->
              <div class="flex justify-between items-center pt-5 border-t border-slate-100 dark:border-slate-800">
                <button
                  type="button"
                  @click="prevStep"
                  class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-2xl transition flex items-center gap-2 cursor-pointer"
                >
                  <ArrowLeft class="w-4 h-4" />
                  <span>Kembali ke Step 1</span>
                </button>

                <button
                  type="button"
                  @click="nextStep"
                  class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-black rounded-2xl shadow-md shadow-indigo-600/30 transition flex items-center gap-2 cursor-pointer active:scale-95"
                >
                  <span>Lanjut ke Step 3: Skema, Server & Finalisasi</span>
                  <ArrowRight class="w-4 h-4" />
                </button>
              </div>
            </div>

            <!-- ========================================================================= -->
            <!-- STEP 3: SKEMA PEMBAYARAN, SERVER HOSTING & FINALISASI -->
            <!-- ========================================================================= -->
            <div v-if="currentStep === 3" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
              <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                <div>
                  <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <CreditCard class="w-4 h-4 text-indigo-500" />
                    <span>3. Skema Komersial, Server & Finalisasi Penawaran</span>
                  </h3>
                  <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Tentukan model kontrak pembayaran, infrastruktur server hosting, biaya setup, dan catatan penawaran resmi.
                  </p>
                </div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Step 3 of 3</span>
              </div>

              <!-- Bagian 1: Model Kontrak & Pembayaran -->
              <div class="p-5 rounded-2xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/90 dark:border-slate-700/80 space-y-4">
                <h4 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                  <Sparkles class="w-3.5 h-3.5 text-indigo-500" />
                  <span>1. Model Kontrak & Skema Pembayaran</span>
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Billing Type -->
                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Skema Pembayaran *</label>
                    <select v-model="form.billing_type" class="w-full px-3.5 py-2.5 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white transition cursor-pointer">
                      <option value="one_off">Putus Kontrak (One-Off Build)</option>
                      <option value="subscription">Berlangganan / SaaS (Subscription)</option>
                    </select>
                  </div>

                  <!-- Maintenance SLA (One-Off: Selectable, SaaS: Active for duration) -->
                  <div v-if="form.billing_type === 'one_off'" class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Garansi Maintenance (SLA) *</label>
                    <select v-model.number="form.maintenance_months" class="w-full px-3.5 py-2.5 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white transition cursor-pointer">
                      <option :value="1">1 Bulan Gratis (Minimal)</option>
                      <option :value="3">3 Bulan Gratis (Standar SLA)</option>
                      <option :value="6">6 Bulan Gratis (Extended SLA)</option>
                      <option :value="12">12 Bulan Gratis (Full Year SLA)</option>
                    </select>
                  </div>
                  <div v-else class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Garansi Maintenance (SLA)</label>
                    <div class="px-3.5 py-2.5 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/70 rounded-xl text-emerald-800 dark:text-emerald-300 text-xs font-bold flex items-center gap-2">
                      <ShieldCheck class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" />
                      <span>Garansi & SLA Aktif Mengikuti Masa Kontrak</span>
                    </div>
                  </div>

                  <!-- Setup Fee -->
                  <div class="space-y-1.5 md:col-span-2">
                    <CurrencyInput v-model="form.setup_fee" label="Biaya Setup / Onboarding (Opsional)" helperText="Biaya satu kali implementasi, konfigurasi server, atau instalasi database awal" />
                  </div>
                </div>

                <!-- SaaS Detailed Parameters (if subscription) -->
                <div v-if="form.billing_type === 'subscription'" class="p-4 rounded-2xl bg-indigo-50/60 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900/60 space-y-4 mt-3">
                  <h5 class="text-[11px] font-extrabold text-indigo-700 dark:text-indigo-300 uppercase tracking-wider">
                    Konfigurasi Langganan SaaS
                  </h5>

                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Metode Tagihan</label>
                      <select v-model="form.subscription_basis" class="w-full px-3 py-2 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        <option value="modular">Flat Modular (Sewa Modul)</option>
                        <option value="per_user">Per-User (Kapasitas User)</option>
                        <option value="hybrid">Hybrid (Modul + User)</option>
                      </select>
                    </div>

                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Siklus Penagihan</label>
                      <select v-model="form.billing_cycle" class="w-full px-3 py-2 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        <option value="monthly">Bulanan (Monthly)</option>
                        <option value="yearly">Tahunan (Yearly)</option>
                      </select>
                    </div>

                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Komitmen Kontrak ({{ form.billing_cycle === 'yearly' ? 'Tahun' : 'Bulan' }})
                      </label>
                      <input v-model.number="form.subscription_duration" type="number" min="1" class="w-full px-3 py-2 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white" />
                    </div>

                    <div v-if="form.subscription_basis === 'per_user' || form.subscription_basis === 'hybrid'" class="space-y-1.5">
                      <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Jumlah Kapasitas User</label>
                      <input v-model.number="form.user_count" type="number" min="1" class="w-full px-3 py-2 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white" />
                    </div>

                    <CurrencyInput v-if="form.subscription_basis === 'per_user' || form.subscription_basis === 'hybrid'" v-model="form.price_per_user" label="Tarif per User / Bulan" />

                    <!-- Annual Discount Toggle Block (Exclusive for Yearly Billing) -->
                    <div v-if="form.billing_cycle === 'yearly'" class="col-span-1 md:col-span-3 p-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-emerald-200 dark:border-emerald-800/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                      <div class="flex items-start sm:items-center gap-3">
                        <input
                          id="annual_discount_toggle"
                          v-model="form.apply_annual_discount"
                          type="checkbox"
                          class="w-4 h-4 mt-0.5 sm:mt-0 text-emerald-600 rounded border-slate-300 dark:border-slate-700 focus:ring-emerald-500 cursor-pointer shrink-0"
                        />
                        <label for="annual_discount_toggle" class="cursor-pointer select-none">
                          <div class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                            <span>Terapkan Diskon Pembayaran Tahunan</span>
                            <span class="px-1.5 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 text-[10px] font-black">{{ form.discount_percentage || 20 }}% OFF</span>
                          </div>
                          <div class="text-[11px] text-slate-500 dark:text-slate-400">
                            Memberikan potongan harga hemat 20% untuk komitmen pembayaran di muka.
                          </div>
                        </label>
                      </div>

                      <div v-if="form.apply_annual_discount && annualSavings > 0" class="px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs font-black text-right shrink-0">
                        Hemat {{ formatRupiah(annualSavings) }} / thn
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Bagian 2: Infrastruktur Server & Cloud Hosting -->
              <div class="p-5 rounded-2xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/90 dark:border-slate-700/80 space-y-4">
                <h4 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                  <Server class="w-3.5 h-3.5 text-emerald-500" />
                  <span>2. Infrastruktur & Cloud Hosting</span>
                </h4>

                <div class="space-y-4">
                  <!-- Option 1: Server Disediakan Mandiri oleh Klien (Rp 0) -->
                  <div
                    @click="setHostingMode('none')"
                    class="p-4.5 rounded-2xl border-2 transition-all cursor-pointer flex items-center justify-between gap-4"
                    :class="hostingMode === 'none' 
                      ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30 shadow-xs' 
                      : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-900'"
                  >
                    <div class="flex items-center gap-3.5">
                      <div 
                        class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0"
                        :class="hostingMode === 'none' ? 'bg-emerald-500 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-500'"
                      >
                        <ShieldCheck class="w-5 h-5" />
                      </div>
                      <div>
                        <div class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-2">
                          <span>Server Disediakan Mandiri oleh Klien</span>
                          <span v-if="hostingMode === 'none'" class="px-2 py-0.5 rounded-md bg-emerald-500 text-white text-[10px] font-extrabold">Aktif</span>
                        </div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                          Klien sudah memiliki infrastruktur sendiri (VPS / cPanel). Tidak ada biaya sewa server dari tim pengembang.
                        </div>
                      </div>
                    </div>
                    <div class="text-right shrink-0">
                      <span class="text-xs font-black text-slate-900 dark:text-white">Rp 0</span>
                    </div>
                  </div>

                  <!-- Option 2: Sediakan Layanan Server Hosting Kustom -->
                  <div
                    class="p-5 rounded-2xl border-2 transition-all space-y-4"
                    :class="hostingMode !== 'none' 
                      ? 'border-indigo-500 bg-indigo-50/20 dark:bg-indigo-950/20 shadow-xs' 
                      : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900'"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-3">
                        <div 
                          class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0"
                          :class="hostingMode !== 'none' ? 'bg-indigo-600 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-500'"
                        >
                          <Cloud class="w-5 h-5" />
                        </div>
                        <div>
                          <div class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <span>Sewa & Konfigurasi Server Hosting</span>
                            <span v-if="hostingMode !== 'none'" class="px-2 py-0.5 rounded-md bg-indigo-600 text-white text-[10px] font-extrabold">Aktif</span>
                          </div>
                          <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                            Pilih jenis infrastruktur hosting yang akan dimasukkan ke dalam rincian penawaran.
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Infrastructure Type Switcher: Shared Hosting vs Cloud VPS -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                      <!-- Shared Hosting Option -->
                      <button
                        type="button"
                        @click="setHostingMode('shared')"
                        class="p-4 rounded-xl border-2 text-left transition-all cursor-pointer space-y-2"
                        :class="hostingMode === 'shared' 
                          ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-950/70 text-indigo-950 dark:text-indigo-100 shadow-xs' 
                          : 'border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-800/80 hover:border-slate-300 text-slate-700 dark:text-slate-300'"
                      >
                        <div class="flex items-center justify-between">
                          <span class="text-xs font-black flex items-center gap-1.5">
                            <span>🌐 Shared Hosting / cPanel</span>
                          </span>
                          <span v-if="hostingMode === 'shared'" class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-snug">
                          Cocok untuk Landing Page, Company Profile, Blog, atau Toko Online UMKM standar.
                        </p>
                      </button>

                      <!-- Cloud VPS Option -->
                      <button
                        type="button"
                        @click="setHostingMode('vps')"
                        class="p-4 rounded-xl border-2 text-left transition-all cursor-pointer space-y-2"
                        :class="hostingMode === 'vps' 
                          ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-950/70 text-indigo-950 dark:text-indigo-100 shadow-xs' 
                          : 'border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-800/80 hover:border-slate-300 text-slate-700 dark:text-slate-300'"
                      >
                        <div class="flex items-center justify-between">
                          <span class="text-xs font-black flex items-center gap-1.5">
                            <span>🚀 Cloud VPS / Dedicated Server</span>
                          </span>
                          <span v-if="hostingMode === 'vps'" class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-snug">
                          Server mandiri (Linux Ubuntu + Nginx / Docker) untuk Web App SaaS & sistem dinamis.
                        </p>
                      </button>
                    </div>

                    <!-- Custom Hosting Details Form (if active) -->
                    <div v-if="currentHostingItem" class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-indigo-100 dark:border-indigo-900/60 space-y-4 mt-2 shadow-xs">
                      <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="text-xs font-bold text-indigo-900 dark:text-indigo-300 flex items-center gap-1.5">
                          <Sparkles class="w-3.5 h-3.5 text-indigo-500" />
                          <span>Rincian Nama & Biaya Server:</span>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-400">
                          Perhitungan Biaya Berdasarkan Tarif Bulanan
                        </span>
                      </div>

                      <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                        <div class="md:col-span-1 space-y-1">
                          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Layanan / Spesifikasi Server *</label>
                          <input
                            v-model="currentHostingItem.item_name"
                            type="text"
                            required
                            placeholder="e.g. Hostinger paket A / Cloud VPS 2 vCPU 4GB RAM"
                            class="w-full px-3.5 py-2.5 text-xs font-semibold bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white"
                          />
                        </div>

                        <div class="space-y-1">
                          <CurrencyInput
                            v-model="hostingMonthlyRate"
                            label="Tarif Sewa Server / Bulan *"
                          />
                        </div>

                        <div class="space-y-1">
                          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                            Durasi Sewa Server (Bulan) *
                          </label>
                          <!-- Dropdown for One-Off: 12, 24, 48 Bulan -->
                          <select
                            v-if="form.billing_type === 'one_off'"
                            v-model.number="hostingDurationMonths"
                            class="w-full px-3.5 py-2.5 text-xs font-semibold bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white cursor-pointer"
                          >
                            <option :value="12">12 Bulan (1 Tahun)</option>
                            <option :value="24">24 Bulan (2 Tahun)</option>
                            <option :value="48">48 Bulan (4 Tahun)</option>
                          </select>

                          <!-- Auto-synced display for Subscription -->
                          <div
                            v-else
                            class="px-3.5 py-2.5 bg-slate-100 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-200 text-xs font-bold flex items-center justify-between"
                            title="Durasi sewa server otomatis mengikuti masa komitmen kontrak SaaS"
                          >
                            <span>{{ hostingDurationMonths }} Bulan</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-300 font-extrabold">
                              Mengikuti SaaS
                            </span>
                          </div>
                        </div>
                      </div>

                      <!-- Live Monthly Math Banner -->
                      <div class="p-3.5 rounded-xl bg-indigo-50/80 dark:bg-indigo-950/60 border border-indigo-200/80 dark:border-indigo-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                        <div class="flex items-center gap-2">
                          <Calculator class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0" />
                          <span class="text-slate-600 dark:text-slate-300">
                            Rumus Perhitungan: <b>{{ formatRupiah(hostingMonthlyRate) }} / bln</b> &times; <b>{{ hostingDurationMonths }} Bulan</b>
                          </span>
                        </div>
                        <div class="text-right font-black text-indigo-700 dark:text-indigo-300 text-xs">
                          Subtotal Biaya Server: {{ formatRupiah(hostingCalculationText?.total || 0) }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Bagian 3: Catatan Penawaran / Scope Notes -->
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Catatan Penawaran / Scope Notes (Opsional)</label>
                <textarea
                  v-model="form.notes"
                  rows="3"
                  placeholder="Tuliskan catatan syarat & ketentuan khusus, batasan garansi, atau ruang lingkup yang akan dicetak pada surat penawaran."
                  class="w-full px-3.5 py-2.5 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-xl focus:ring-2 focus:ring-indigo-500 text-slate-900 dark:text-white transition"
                ></textarea>
              </div>

              <!-- Bagian 4: Final Review Summary Cards -->
              <div class="space-y-3 pt-2">
                <h4 class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                  Ringkasan Rekapitulasi Penawaran:
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                  <!-- Card 1: Client & Model -->
                  <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 space-y-2">
                    <div class="text-[10px] font-extrabold text-slate-400 uppercase">Klien & Model Pembayaran</div>
                    <div class="font-black text-slate-900 dark:text-white text-sm">{{ form.client_name || 'Belum diisi' }}</div>
                    <div class="text-[11px] text-indigo-600 dark:text-indigo-400 font-bold">
                      {{ form.project_category }} &bull; {{ form.estimated_timeline }}
                    </div>
                    <div class="text-[11px] text-slate-600 dark:text-slate-300">
                      Skema: <span class="font-bold">{{ form.billing_type === 'subscription' ? 'SaaS Berlangganan' : 'Beli Putus (One-Off)' }}</span>
                    </div>
                    <div v-if="form.billing_type === 'subscription'" class="text-[11px] text-slate-500">
                      Siklus: {{ form.billing_cycle === 'yearly' ? 'Tahunan (Yearly)' : 'Bulanan (Monthly)' }} &bull; Durasi: {{ form.subscription_duration }} {{ form.billing_cycle === 'yearly' ? 'Tahun' : 'Bulan' }}
                    </div>
                    <div v-else class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold">
                      Garansi SLA: {{ form.maintenance_months }} Bulan Gratis
                    </div>
                  </div>

                  <!-- Card 2: Modules & Server -->
                  <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 space-y-2">
                    <div class="text-[10px] font-extrabold text-slate-400 uppercase">Lingkup Fitur & Server</div>
                    <div class="font-black text-slate-900 dark:text-white">
                      {{ form.items.filter(i => !isHostingModule(i)).length }} Modul Software ({{ formatRupiah(softwareItemsTotal) }})
                    </div>
                    <div class="text-[11px] text-slate-600 dark:text-slate-300 space-y-0.5">
                      <div>Hosting: <span class="font-bold">{{ currentHostingItem ? currentHostingItem.item_name : 'Server Disediakan Klien (Rp 0)' }}</span></div>
                      <div v-if="currentHostingItem && hostingCalculationText" class="text-indigo-600 dark:text-indigo-400 font-bold text-[10.5px]">
                        Kalkulasi: {{ hostingCalculationText.formatted }}
                      </div>
                    </div>
                    <div v-if="form.setup_fee > 0" class="text-[11px] text-slate-500">
                      Setup Fee: {{ formatRupiah(form.setup_fee) }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Step 3 Navigation & Submission Buttons -->
              <div class="flex justify-between items-center pt-5 border-t border-slate-100 dark:border-slate-800">
                <button
                  type="button"
                  @click="prevStep"
                  class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-2xl transition flex items-center gap-2 cursor-pointer"
                >
                  <ArrowLeft class="w-4 h-4" />
                  <span>Kembali ke Step 2</span>
                </button>

                <div class="flex items-center gap-2.5">
                  <button
                    type="button"
                    @click="submit('Draft')"
                    :disabled="form.processing"
                    class="px-4.5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-2xl transition flex items-center gap-2 cursor-pointer"
                  >
                    <Clock class="w-4 h-4 text-amber-500" />
                    <span>Simpan Draft</span>
                  </button>

                  <button
                    type="button"
                    @click="submit('Generated')"
                    :disabled="form.processing"
                    class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-600 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-indigo-500/30 transition flex items-center gap-2 cursor-pointer active:scale-98"
                  >
                    <CheckCircle2 class="w-4 h-4 text-emerald-400" />
                    <span>Simpan & Terbitkan Resmi</span>
                  </button>
                </div>
              </div>
            </div>

        </div>

        <!-- Right Sticky Sidebar Column (Live Summary throughout all steps) -->
        <div class="lg:col-span-4 sticky top-20 space-y-4">
          <div class="bg-white dark:bg-slate-900 text-slate-900 dark:text-white rounded-3xl p-6 shadow-xl border border-slate-200 dark:border-slate-800 space-y-6">
            
            <!-- Summary Header -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
              <span class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold flex items-center gap-2">
                <Calculator class="w-4 h-4 text-indigo-500 dark:text-indigo-400" />
                <span>Ringkasan Kalkulasi</span>
              </span>
              <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-500/30">
                LIVE
              </span>
            </div>

            <!-- Grand Total Main Display with Strikethrough Price when discounted -->
            <div class="space-y-1">
              <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold block">Total Estimasi Nilai Kontrak</span>
              <div class="flex items-baseline flex-wrap gap-2.5">
                <h3 
                  class="text-3xl font-black tracking-tight"
                  :class="form.billing_type === 'subscription' && form.billing_cycle === 'yearly' && form.apply_annual_discount && annualSavings > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-900 dark:text-white'"
                >
                  {{ formatRupiah(calculatedGrandTotal) }}
                </h3>
                <span 
                  v-if="form.billing_type === 'subscription' && form.billing_cycle === 'yearly' && form.apply_annual_discount && annualSavings > 0" 
                  class="text-base font-bold text-slate-400 dark:text-slate-500 line-through decoration-rose-500/70"
                >
                  {{ formatRupiah(originalGrandTotal) }}
                </span>
              </div>
              <div v-if="form.billing_type === 'subscription' && form.billing_cycle === 'yearly' && form.apply_annual_discount && annualSavings > 0" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1 mt-0.5">
                <span>🎉 Hemat total {{ formatRupiah(annualSavings * (form.subscription_duration || 1)) }} (Diskon {{ form.discount_percentage || 20 }}%)</span>
              </div>
            </div>

            <!-- Detailed Breakdown List -->
            <div class="space-y-2.5 pt-2 text-xs border-t border-slate-200 dark:border-slate-800">
              <div class="flex justify-between items-center text-slate-600 dark:text-slate-300">
                <span class="text-slate-500 dark:text-slate-400">Modul Fitur Software:</span>
                <span class="font-bold text-emerald-600 dark:text-emerald-400" v-if="form.billing_type === 'subscription' && form.subscription_basis === 'per_user'">Termasuk Paket Lisensi</span>
                <span class="font-bold text-slate-900 dark:text-white" v-else>{{ formatRupiah(softwareItemsTotal) }}</span>
              </div>

              <!-- Lisensi User (SaaS Per-User / Hybrid) -->
              <div v-if="form.billing_type === 'subscription' && (form.subscription_basis === 'per_user' || form.subscription_basis === 'hybrid')" class="flex justify-between items-center text-slate-600 dark:text-slate-300">
                <span class="text-slate-500 dark:text-slate-400">Lisensi ({{ form.user_count }} User):</span>
                <span class="font-bold text-slate-900 dark:text-white">{{ formatRupiah(userRecurringTotal) }} / {{ form.billing_cycle === 'yearly' ? 'thn' : 'bln' }}</span>
              </div>

              <!-- Infrastruktur Server -->
              <div class="flex justify-between items-start text-slate-600 dark:text-slate-300">
                <div class="space-y-0.5">
                  <span class="text-slate-500 dark:text-slate-400 block">Infrastruktur Server/Hosting:</span>
                  <span v-if="currentHostingItem && hostingCalculationText" class="text-[10px] text-slate-400 block font-sans">
                    {{ currentHostingItem.item_name }} ({{ formatRupiah(hostingMonthlyRate) }}/bln &times; {{ hostingDurationMonths }} bln)
                  </span>
                </div>
                <div class="text-right shrink-0">
                  <span class="font-bold block" :class="hostingDurationTotal > 0 ? 'text-slate-900 dark:text-white' : 'text-slate-400'">
                    {{ hostingDurationTotal > 0 ? formatRupiah(hostingDurationTotal) : 'Server Klien (Rp 0)' }}
                  </span>
                </div>
              </div>

              <!-- SaaS Subtotal, Diskon & Tagihan Bersih -->
              <template v-if="form.billing_type === 'subscription'">
                <!-- If annual discount applied: show Subtotal Normal -> Diskon -> Biaya Bersih -->
                <template v-if="form.billing_cycle === 'yearly' && form.apply_annual_discount && annualSavings > 0">
                  <div class="flex justify-between items-center text-slate-500 dark:text-slate-400 pt-1.5 border-t border-dashed border-slate-200 dark:border-slate-800">
                    <span>Subtotal Biaya Tahunan:</span>
                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ formatRupiah(monthlyBaseRecurring * 12) }} / thn</span>
                  </div>

                  <div class="flex justify-between items-center text-emerald-600 dark:text-emerald-400 font-bold">
                    <span>Diskon Tahunan ({{ form.discount_percentage || 20 }}% OFF):</span>
                    <span>-{{ formatRupiah(annualSavings) }} / thn</span>
                  </div>

                  <div class="flex justify-between items-center text-indigo-600 dark:text-indigo-300 font-extrabold pb-0.5">
                    <span>Biaya Tagihan Tahunan:</span>
                    <span class="font-black text-slate-900 dark:text-white">{{ formatRupiah(recurringPerCycle) }} / thn</span>
                  </div>
                </template>

                <!-- If monthly or no discount -->
                <template v-else>
                  <div class="flex justify-between items-center text-indigo-600 dark:text-indigo-300 font-semibold pt-1.5 border-t border-dashed border-slate-200 dark:border-slate-800">
                    <span>Biaya Berulang ({{ form.billing_cycle === 'yearly' ? 'Tahunan' : 'Bulanan' }}):</span>
                    <span class="font-black text-slate-900 dark:text-white">{{ formatRupiah(recurringPerCycle) }} / {{ form.billing_cycle === 'yearly' ? 'thn' : 'bln' }}</span>
                  </div>
                </template>

                <div v-if="form.setup_fee > 0" class="flex justify-between items-center text-slate-600 dark:text-slate-300">
                  <span class="text-slate-500 dark:text-slate-400">Biaya Setup / Onboarding:</span>
                  <span class="font-bold text-slate-900 dark:text-white">{{ formatRupiah(form.setup_fee) }}</span>
                </div>

                <div class="flex justify-between items-center text-slate-600 dark:text-slate-300 pt-0.5">
                  <span class="text-slate-500 dark:text-slate-400">Durasi Komitmen:</span>
                  <span class="font-bold text-slate-900 dark:text-white">{{ form.subscription_duration }} {{ form.billing_cycle === 'yearly' ? 'Tahun' : 'Bulan' }}</span>
                </div>
              </template>

              <!-- One-off Setup Fee -->
              <template v-else>
                <div v-if="form.setup_fee > 0" class="flex justify-between items-center text-slate-600 dark:text-slate-300 pt-1.5 border-t border-dashed border-slate-200 dark:border-slate-800">
                  <span class="text-slate-500 dark:text-slate-400">Biaya Setup / Onboarding:</span>
                  <span class="font-bold text-slate-900 dark:text-white">{{ formatRupiah(form.setup_fee) }}</span>
                </div>
              </template>
            </div>

            <!-- Prominent SLA Guarantee Information Card in Sidebar -->
            <div class="p-4.5 rounded-2xl bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-200/90 dark:border-emerald-800/80 space-y-2">
              <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-300 font-extrabold text-xs">
                <ShieldCheck class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" />
                <span>Jaminan Garansi & SLA</span>
              </div>
              <div class="text-[11px] text-emerald-800 dark:text-emerald-200/90 leading-relaxed font-medium">
                <template v-if="form.billing_type === 'subscription'">
                  Dukungan teknis, pemeliharaan sistem, & garansi SLA <b>aktif penuh selama {{ form.subscription_duration }} {{ form.billing_cycle === 'yearly' ? 'Tahun' : 'Bulan' }}</b> masa kontrak langganan SaaS.
                </template>
                <template v-else>
                  Garansi perbaikan bug & pendampingan teknis gratis selama <b>{{ form.maintenance_months }} Bulan</b> pasca serah terima proyek.
                </template>
              </div>
            </div>

          </div>
        </div>

      </form>
    </div>
  </AppLayout>
</template>
