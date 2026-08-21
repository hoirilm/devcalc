<script setup>
import { ref, computed, nextTick } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CurrencyInput from '@/Components/CurrencyInput.vue';
import { 
  Plus, 
  Trash2, 
  ArrowLeft, 
  Sparkles,
  Calculator,
  ShieldCheck,
  Building,
  CheckCircle2,
  Clock,
  Layers,
  FileText,
  AlertTriangle
} from 'lucide-vue-next';

const props = defineProps({
  project: Object,
  modules: Array,
});

const form = useForm({
  client_name: props.project.client_name,
  billing_type: props.project.billing_type,
  subscription_basis: props.project.subscription_basis || 'modular',
  billing_cycle: props.project.billing_cycle || 'monthly',
  subscription_duration: props.project.subscription_duration || 12,
  user_count: props.project.user_count || 10,
  price_per_user: props.project.price_per_user || 50000,
  setup_fee: props.project.setup_fee || 0,
  maintenance_months: props.project.maintenance_months || 3,
  status: props.project.status,
  notes: props.project.notes || '',
  addendum_notes: props.project.addendum_notes || '',
  items: props.project.items.map(item => ({
    id: item.id,
    module_id: item.module_id,
    item_name: item.item_name,
    base_price: item.base_price,
    complexity_weight: item.complexity_weight,
  }))
});

function addItem() {
  form.items.push({
    module_id: null,
    item_name: '',
    base_price: 1000000,
    complexity_weight: 1.0,
  });

  nextTick(() => {
    window.scrollTo({
      top: document.body.scrollHeight,
      behavior: 'smooth'
    });
  });
}

function removeItem(index) {
  if (form.items.length > 1) {
    form.items.splice(index, 1);
  }
}

function onModuleSelect(index, event) {
  const val = event.target.value;
  const selectedId = val ? Number(val) : null;
  form.items[index].module_id = selectedId;
  if (!selectedId) return;
  const mod = props.modules.find(m => m.id == selectedId);
  if (mod) {
    form.items[index].item_name = mod.name;
    if (form.billing_type === 'subscription') {
      const sub = mod.subscription_price > 0 ? mod.subscription_price : Math.round(mod.base_price * 0.08);
      form.items[index].base_price = sub;
    } else {
      form.items[index].base_price = mod.base_price;
    }
  }
}

// Live Calculations
const itemsTotal = computed(() => {
  return form.items.reduce((sum, item) => {
    const base = item.base_price || 0;
    const comp = item.complexity_weight || 1.0;
    return sum + Math.round(base * comp);
  }, 0);
});

const userRecurringTotal = computed(() => {
  if (form.subscription_basis === 'per_user' || form.subscription_basis === 'hybrid') {
    return Math.round((form.user_count || 0) * (form.price_per_user || 0));
  }
  return 0;
});

const recurringPerCycle = computed(() => {
  if (form.subscription_basis === 'per_user') {
    return userRecurringTotal.value;
  }
  if (form.subscription_basis === 'hybrid') {
    return itemsTotal.value + userRecurringTotal.value;
  }
  return itemsTotal.value; // modular
});

const calculatedGrandTotal = computed(() => {
  if (form.billing_type === 'subscription') {
    const duration = form.subscription_duration || 1;
    const setup = form.setup_fee || 0;
    return setup + (recurringPerCycle.value * duration);
  }
  return itemsTotal.value;
});

function formatRupiah(num) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num || 0);
}

function submit(targetStatus) {
  if (targetStatus) {
    form.status = targetStatus;
  }
  form.put(`/projects/${props.project.id}`);
}
</script>

<template>
  <Head :title="`Ubah Penawaran #${project.code}`" />

  <AppLayout :title="`Ubah Penawaran #${project.code}`">
    <div class="max-w-7xl mx-auto space-y-6">
      
      <!-- Top Action Bar Header (Clean & Balanced Style) -->
      <div class="flex items-center gap-4">
        <button
          @click="router.get('/projects')"
          class="w-11 h-11 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 hover:border-indigo-300 dark:hover:border-indigo-800 transition-all duration-200 cursor-pointer shadow-xs flex items-center justify-center shrink-0 active:scale-95 group"
          title="Kembali ke Daftar Penawaran"
        >
          <ArrowLeft class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" />
        </button>

        <div class="space-y-0.5">
          <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
            Ubah Penawaran #{{ project.code }}
          </h2>
          <p class="text-xs text-slate-500 dark:text-slate-400">
            Ubah rincian penawaran harga, skema kontrak, dan daftar fitur software.
          </p>
        </div>
      </div>

      <!-- Addendum Banner if applicable -->
      <div v-if="project.is_addendum || project.parent_id" class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 space-y-2">
        <div class="flex items-center gap-2 font-bold text-xs">
          <AlertTriangle class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" />
          <span>Dokumen Adendum Resmi: #{{ project.code }}</span>
        </div>
        <p class="text-xs text-amber-800 dark:text-amber-300">
          Penawaran ini merupakan dokumen adendum yang mengacu pada Kontrak Induk penawaran awal.
        </p>

        <div class="pt-2">
          <label class="block text-xs font-semibold">Catatan / Ruang Lingkup Perubahan Adendum</label>
          <textarea
            v-model="form.addendum_notes"
            rows="2"
            placeholder="Jelaskan penyesuaian ruang lingkup atau kuota pengguna pada adendum ini."
            class="w-full mt-1 px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-700 rounded-xl text-slate-900 dark:text-white"
          ></textarea>
        </div>
      </div>

      <!-- Main Form (2-Column Grid Layout) -->
      <form @submit.prevent="submit(form.status)" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Main Column (Form Inputs) -->
        <div class="lg:col-span-8 space-y-6">
          
          <!-- Section 1: Client & Contract Scheme -->
          <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
              <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <Building class="w-4 h-4 text-indigo-500" />
                <span>1. Informasi Proyek & Skema Kontrak</span>
              </h3>
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Data Utama</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Client Name -->
              <div class="space-y-1.5 md:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Perusahaan / Klien *</label>
                <input
                  v-model="form.client_name"
                  type="text"
                  required
                  placeholder="e.g. PT Maju Bersama Digital"
                  class="w-full px-3.5 py-2.5 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-xl focus:ring-2 focus:ring-indigo-500 text-slate-900 dark:text-white transition"
                />
                <span v-if="form.errors.client_name" class="text-[11px] text-rose-500 block">{{ form.errors.client_name }}</span>
              </div>

              <!-- Billing Type -->
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Skema Pembayaran *</label>
                <select v-model="form.billing_type" class="w-full px-3.5 py-2.5 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-xl text-slate-900 dark:text-white transition cursor-pointer">
                  <option value="one_off">Putus Kontrak (One-Off Build)</option>
                  <option value="subscription">Berlangganan / SaaS (Subscription)</option>
                </select>
              </div>

              <!-- Maintenance SLA -->
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Garansi Maintenance (SLA) *</label>
                <select v-model.number="form.maintenance_months" class="w-full px-3.5 py-2.5 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-xl text-slate-900 dark:text-white transition cursor-pointer">
                  <option :value="1">1 Bulan</option>
                  <option :value="3">3 Bulan (Standar SLA)</option>
                  <option :value="6">6 Bulan (Extended SLA)</option>
                  <option :value="12">12 Bulan (Full Year SLA)</option>
                </select>
              </div>

              <!-- Notes Input -->
              <div class="space-y-1.5 md:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Catatan Penawaran / Scope Notes (Opsional)</label>
                <textarea
                  v-model="form.notes"
                  rows="2"
                  placeholder="Tuliskan catatan tambahan, syarat khusus, atau ruang lingkup yang akan dicetak pada surat penawaran."
                  class="w-full px-3.5 py-2.5 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-xl focus:ring-2 focus:ring-indigo-500 text-slate-900 dark:text-white transition"
                ></textarea>
              </div>
            </div>

            <!-- SaaS Detailed Configurations -->
            <div v-if="form.billing_type === 'subscription'" class="p-4 rounded-2xl bg-indigo-50/60 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900/60 space-y-4 mt-2 transition-all">
              <h4 class="text-xs font-extrabold text-indigo-700 dark:text-indigo-300 uppercase tracking-wider flex items-center gap-2">
                <Sparkles class="w-3.5 h-3.5" />
                <span>Konfigurasi Berlangganan SaaS</span>
              </h4>

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

                <CurrencyInput v-model="form.setup_fee" label="Biaya Setup / Onboarding" helperText="Biaya satu kali implementasi" />
              </div>
            </div>
          </div>

          <!-- Section 2: Line Items Repeater -->
          <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
            <div class="border-b border-slate-200 dark:border-slate-800 pb-3">
              <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <Layers class="w-4 h-4 text-indigo-500" />
                <span>2. Rincian Fitur & Lingkup Kerja</span>
              </h3>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Ubah atau tambahkan modul fitur software.
              </p>
            </div>

            <!-- Repeater Items List (Spacious 2-Row Stacked Card Layout) -->
            <div class="space-y-4">
              <div
                v-for="(item, idx) in form.items"
                :key="idx"
                class="p-4 sm:p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/60 space-y-4 relative group"
              >
                <!-- Item Card Header -->
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-200/60 dark:border-slate-700/40">
                  <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-extrabold text-[11px] flex items-center justify-center border border-indigo-200/60 dark:border-indigo-800/60">
                      #{{ idx + 1 }}
                    </span>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Item Fitur / Modul Kerja</span>
                  </div>

                  <button
                    type="button"
                    @click="removeItem(idx)"
                    :disabled="form.items.length <= 1"
                    title="Hapus Item"
                    class="px-2.5 py-1 text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-950/40 rounded-lg transition disabled:opacity-30 flex items-center gap-1 cursor-pointer"
                  >
                    <Trash2 class="w-3.5 h-3.5" />
                    <span>Hapus Fitur</span>
                  </button>
                </div>

                <!-- Input Grid (2-Row Layout for Max Legibility) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Row 1 Left: Template Katalog -->
                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Pilih Template Katalog Modul (Opsional)</label>
                    <select
                      v-model="item.module_id"
                      @change="e => onModuleSelect(idx, e)"
                      class="w-full px-3.5 py-2.5 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white cursor-pointer"
                    >
                      <option :value="null">-- Kustom / Tanpa Katalog --</option>
                      <option v-for="m in modules" :key="m.id" :value="m.id">{{ m.name }}</option>
                    </select>
                  </div>

                  <!-- Row 1 Right: Nama Fitur -->
                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Fitur / Deskripsi Tugas *</label>
                    <input
                      v-model="item.item_name"
                      type="text"
                      required
                      placeholder="e.g. Authentication & Role-Based Access Control"
                      class="w-full px-3.5 py-2.5 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white"
                    />
                  </div>

                  <!-- Row 2 Left: Harga Dasar -->
                  <div class="space-y-1.5">
                    <CurrencyInput v-model="item.base_price" label="Harga Dasar Modul" />
                  </div>

                  <!-- Row 2 Right: Bobot Kompleksitas -->
                  <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Bobot Kompleksitas *</label>
                    <select v-model.number="item.complexity_weight" class="w-full px-3.5 py-2.5 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white cursor-pointer">
                      <option :value="0.8">0.8x - Ringan (Simple Task / Minor Flow)</option>
                      <option :value="1.0">1.0x - Standar (Standard Module / Medium Complexity)</option>
                      <option :value="1.25">1.25x - Sedang (Extended Logic / Multi-Entity Flow)</option>
                      <option :value="1.5">1.5x - Kompleks (High Complexity / Third-Party Integration)</option>
                      <option :value="2.0">2.0x - Sangat Kompleks (Architecture Heavy / Custom Engine)</option>
                    </select>
                  </div>
                </div>

                <!-- Footer Summary Badge Row -->
                <div class="flex items-center justify-between pt-2.5 border-t border-slate-200/60 dark:border-slate-700/40 text-xs">
                  <span class="text-[11px] text-slate-400 font-medium">Rumus Kalkulasi: Harga Dasar × Bobot Kompleksitas</span>
                  <span class="font-black text-indigo-600 dark:text-indigo-400 text-sm">
                    Terhitung: {{ formatRupiah(Math.round((item.base_price || 0) * (item.complexity_weight || 1))) }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Add New Feature Button (Bottom Center) -->
            <div class="pt-2 flex justify-center">
              <button
                type="button"
                @click="addItem"
                class="w-full sm:w-auto px-6 py-3 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 text-indigo-600 dark:text-indigo-300 font-extrabold text-xs rounded-2xl border-2 border-dashed border-indigo-200 dark:border-indigo-800 transition flex items-center justify-center gap-2 cursor-pointer shadow-sm active:scale-98"
              >
                <Plus class="w-4 h-4 text-indigo-500" />
                <span>+ Tambah Fitur / Modul Baru</span>
              </button>
            </div>
          </div>

        </div>

        <!-- Right Sticky Sidebar Column (Live Summary & Actions) -->
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

            <!-- Grand Total Main Display -->
            <div class="space-y-1">
              <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold block">Total Estimasi Nilai Kontrak</span>
              <h3 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ formatRupiah(calculatedGrandTotal) }}</h3>
            </div>

            <!-- Detailed Breakdown List -->
            <div class="space-y-2.5 pt-2 text-xs border-t border-slate-200 dark:border-slate-800">
              <div class="flex justify-between items-center text-slate-600 dark:text-slate-300">
                <span class="text-slate-500 dark:text-slate-400">Fitur & Modul ({{ form.items.length }} Item):</span>
                <span class="font-bold text-slate-900 dark:text-white">{{ formatRupiah(itemsTotal) }}</span>
              </div>

              <template v-if="form.billing_type === 'subscription'">
                <div v-if="form.subscription_basis === 'per_user' || form.subscription_basis === 'hybrid'" class="flex justify-between items-center text-slate-600 dark:text-slate-300">
                  <span class="text-slate-500 dark:text-slate-400">Lisensi ({{ form.user_count }} User):</span>
                  <span class="font-bold text-slate-900 dark:text-white">{{ formatRupiah(userRecurringTotal) }}</span>
                </div>

                <div class="flex justify-between items-center text-indigo-600 dark:text-indigo-300 font-semibold pt-1">
                  <span>Biaya Berulang ({{ form.billing_cycle === 'yearly' ? 'Tahunan' : 'Bulanan' }}):</span>
                  <span class="font-black text-slate-900 dark:text-white">{{ formatRupiah(recurringPerCycle) }}</span>
                </div>

                <div v-if="form.setup_fee > 0" class="flex justify-between items-center text-slate-600 dark:text-slate-300">
                  <span class="text-slate-500 dark:text-slate-400">Biaya Setup / Onboarding:</span>
                  <span class="font-bold text-slate-900 dark:text-white">{{ formatRupiah(form.setup_fee) }}</span>
                </div>

                <div class="flex justify-between items-center text-slate-600 dark:text-slate-300">
                  <span class="text-slate-500 dark:text-slate-400">Durasi Komitmen:</span>
                  <span class="font-bold text-slate-900 dark:text-white">{{ form.subscription_duration }} {{ form.billing_cycle === 'yearly' ? 'Tahun' : 'Bulan' }}</span>
                </div>
              </template>

              <div class="flex justify-between items-center text-slate-600 dark:text-slate-300 pt-1">
                <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1">
                  <ShieldCheck class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                  <span>Garansi SLA:</span>
                </span>
                <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ form.maintenance_months }} Bulan</span>
              </div>
            </div>

            <!-- Action Buttons (Stacked Top-and-Bottom) -->
            <div class="space-y-2.5 pt-4 border-t border-slate-200 dark:border-slate-800">
              <button
                type="button"
                @click="submit('Draft')"
                :disabled="form.processing"
                class="w-full px-5 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800/90 dark:hover:bg-slate-800 text-slate-700 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white font-bold text-xs rounded-2xl transition duration-150 flex items-center justify-center gap-2 border border-slate-300 dark:border-slate-700/60 cursor-pointer active:scale-98"
              >
                <Clock class="w-4 h-4 text-amber-500 dark:text-amber-400" />
                <span>Simpan Perubahan Draft</span>
              </button>

              <button
                type="button"
                @click="submit('Generated')"
                :disabled="form.processing"
                class="w-full px-5 py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-600 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-indigo-500/30 transition duration-150 flex items-center justify-center gap-2 cursor-pointer active:scale-98"
              >
                <CheckCircle2 class="w-4 h-4 text-emerald-400" />
                <span>Simpan & Terbitkan Resmi</span>
              </button>
            </div>

          </div>
        </div>

      </form>

    </div>
  </AppLayout>
</template>
