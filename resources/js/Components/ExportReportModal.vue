<script setup>
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import { 
  FileSpreadsheet, 
  FileText, 
  Download, 
  X, 
  Check
} from 'lucide-vue-next';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['close']);

const exportFormat = ref('csv'); // 'csv' | 'pdf'
const dateRange = ref('all'); // 'all' | 'month' | 'quarter' | 'year'
const billingType = ref(''); // '' | 'one_off' | 'subscription'
const status = ref(''); // '' | 'Generated' | 'Draft'

function doExport() {
  const params = new URLSearchParams();
  if (dateRange.value && dateRange.value !== 'all') params.append('date_range', dateRange.value);
  if (billingType.value) params.append('billing_type', billingType.value);
  if (status.value) params.append('status', status.value);

  const endpoint = exportFormat.value === 'csv' 
    ? `/projects/export/csv?${params.toString()}`
    : `/projects/export/pdf?${params.toString()}`;

  window.open(endpoint, '_blank');
  emit('close');
}
</script>

<template>
  <Modal :show="show" @close="emit('close')" maxWidth="md">
    <div class="p-6 space-y-5">
      
      <!-- Modal Header -->
      <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 flex items-center justify-center">
            <Download class="w-5 h-5" />
          </div>
          <div>
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Ekspor Laporan Penawaran</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Pilih format & filter data yang ingin diunduh</p>
          </div>
        </div>

        <button @click="emit('close')" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl transition cursor-pointer">
          <X class="w-4 h-4" />
        </button>
      </div>

      <!-- Export Format Selector Cards -->
      <div class="space-y-2">
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Format Berkas Ekspor</label>
        <div class="grid grid-cols-2 gap-3">
          
          <!-- CSV / Excel Card -->
          <div
            @click="exportFormat = 'csv'"
            class="p-3.5 rounded-2xl border transition-all cursor-pointer flex flex-col justify-between space-y-2 select-none"
            :class="exportFormat === 'csv'
              ? 'bg-emerald-50/70 dark:bg-emerald-950/40 border-emerald-500 text-emerald-950 dark:text-emerald-200 ring-2 ring-emerald-500/20'
              : 'bg-slate-50 dark:bg-slate-800/40 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:border-emerald-300'"
          >
            <div class="flex items-center justify-between">
              <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                <FileSpreadsheet class="w-4 h-4" />
              </div>
              <div v-if="exportFormat === 'csv'" class="w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center">
                <Check class="w-3 h-3 stroke-[3]" />
              </div>
            </div>
            <div>
              <div class="text-xs font-extrabold">Excel Spreadsheet (.csv)</div>
              <div class="text-[10px] text-slate-400 mt-0.5">Data mentah tabular terstruktur</div>
            </div>
          </div>

          <!-- Executive PDF Card -->
          <div
            @click="exportFormat = 'pdf'"
            class="p-3.5 rounded-2xl border transition-all cursor-pointer flex flex-col justify-between space-y-2 select-none"
            :class="exportFormat === 'pdf'
              ? 'bg-indigo-50/70 dark:bg-indigo-950/40 border-indigo-500 text-indigo-950 dark:text-indigo-200 ring-2 ring-indigo-500/20'
              : 'bg-slate-50 dark:bg-slate-800/40 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:border-indigo-300'"
          >
            <div class="flex items-center justify-between">
              <div class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                <FileText class="w-4 h-4" />
              </div>
              <div v-if="exportFormat === 'pdf'" class="w-4 h-4 rounded-full bg-indigo-600 text-white flex items-center justify-center">
                <Check class="w-3 h-3 stroke-[3]" />
              </div>
            </div>
            <div>
              <div class="text-xs font-extrabold">Executive PDF Report (.pdf)</div>
              <div class="text-[10px] text-slate-400 mt-0.5">Laporan visual ringkasan KPI</div>
            </div>
          </div>

        </div>
      </div>

      <!-- Filter Parameters -->
      <div class="space-y-3 pt-1">
        
        <!-- Date Range Filter -->
        <div class="space-y-1">
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Periode Waktu Laporan</label>
          <select v-model="dateRange" class="w-full px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
            <option value="all">Semua Periode Transaksi</option>
            <option value="month">Bulan Ini</option>
            <option value="quarter">Kuartal Ini (Q-Current)</option>
            <option value="year">Tahun Ini</option>
          </select>
        </div>

        <!-- Billing Scheme Filter -->
        <div class="space-y-1">
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Skema Kontrak</label>
          <select v-model="billingType" class="w-full px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
            <option value="">Semua Skema Kontrak</option>
            <option value="one_off">Beli Putus (One-Off Payment)</option>
            <option value="subscription">Langganan SaaS (Software as a Service)</option>
          </select>
        </div>

        <!-- Document Status Filter -->
        <div class="space-y-1">
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Status Dokumen</label>
          <select v-model="status" class="w-full px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
            <option value="">Semua Status Dokumen</option>
            <option value="Generated">Resmi (Generated)</option>
            <option value="Draft">Draft Berjalan</option>
          </select>
        </div>

      </div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
        <button
          type="button"
          @click="emit('close')"
          class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer"
        >
          Batal
        </button>

        <button
          type="button"
          @click="doExport"
          class="px-4 py-2 text-xs font-extrabold bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-md shadow-indigo-600/30 transition flex items-center gap-1.5 cursor-pointer active:scale-95"
        >
          <Download class="w-4 h-4" />
          <span>Unduh Berkas {{ exportFormat.toUpperCase() }}</span>
        </button>
      </div>

    </div>
  </Modal>
</template>
