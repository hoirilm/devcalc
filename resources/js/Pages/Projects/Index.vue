<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import Modal from '@/Components/Modal.vue';
import ConfirmDeleteModal from '@/Components/ConfirmDeleteModal.vue';
import ExportReportModal from '@/Components/ExportReportModal.vue';
import ActionMenu from '@/Components/ActionMenu.vue';
import { 
  Plus, 
  Search, 
  Printer, 
  Edit3, 
  Trash2, 
  FilePlus, 
  Filter,
  X,
  ArrowUpDown,
  RotateCcw,
  SlidersHorizontal,
  CheckCircle2,
  AlertCircle,
  FileText,
  Check,
  Minus,
  Download,
  FileDown
} from 'lucide-vue-next';

const props = defineProps({
  projects: Object,
  filters: Object,
});

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

const search = ref(props.filters?.search || '');
const billingTypeFilter = ref(props.filters?.billing_type || '');
const statusFilter = ref(props.filters?.status || '');
const sortFilter = ref(props.filters?.sort || 'latest');

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

function applyFilter() {
  router.get('/projects', {
    search: search.value,
    billing_type: billingTypeFilter.value,
    status: statusFilter.value,
    sort: sortFilter.value,
  }, { preserveState: true, replace: true });
}

function clearSearch() {
  search.value = '';
  applyFilter();
}

function setBillingType(type) {
  billingTypeFilter.value = type;
  applyFilter();
}

function setStatus(statusVal) {
  statusFilter.value = statusVal;
  applyFilter();
}

function resetAllFilters() {
  search.value = '';
  billingTypeFilter.value = '';
  statusFilter.value = '';
  sortFilter.value = 'latest';
  applyFilter();
}

const hasActiveFilters = computed(() => {
  return !!search.value || !!billingTypeFilter.value || !!statusFilter.value || (!!sortFilter.value && sortFilter.value !== 'latest');
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
    onSuccess: () => {
      addendumModalOpen.value = false;
      addendumForm.reset();
    }
  });
}

function getClientInitial(name) {
  if (!name) return 'C';
  const clean = name.replace(/^(PT\.|CV\.|UD\.|PT|CV|UD)\s*/i, '').trim();
  return (clean[0] || name[0] || 'C').toUpperCase();
}

function formatClientName(name) {
  if (!name) return '';
  return name.replace(/\w\S*/g, (txt) => txt.charAt(0).toUpperCase() + txt.substring(1));
}

// Single Delete Modal State
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
    onSuccess: (page) => {
      deleteModalOpen.value = false;
      targetProjectToDelete.value = null;
      flashSuccess.value = page.props.flash?.success || `Penawaran #${code} berhasil dihapus.`;
    },
    onFinish: () => {
      isDeleting.value = false;
    }
  });
}

// Bulk Selection State
const selectedIds = ref([]);
const isBulkDeleteModalOpen = ref(false);

const isAllSelected = computed(() => {
  return props.projects?.data?.length > 0 && selectedIds.value.length === props.projects.data.length;
});

const isSomeSelected = computed(() => {
  return selectedIds.value.length > 0 && !isAllSelected.value;
});

function toggleSelectAll() {
  if (isAllSelected.value) {
    selectedIds.value = [];
  } else {
    selectedIds.value = props.projects.data.map(p => p.id);
  }
}

function toggleSelectRow(id) {
  const index = selectedIds.value.indexOf(id);
  if (index > -1) {
    selectedIds.value.splice(index, 1);
  } else {
    selectedIds.value.push(id);
  }
}

function promptBulkDelete() {
  if (selectedIds.value.length === 0) return;
  isBulkDeleteModalOpen.value = true;
}

function confirmBulkDelete() {
  if (selectedIds.value.length === 0) return;
  const count = selectedIds.value.length;
  isDeleting.value = true;
  router.post('/projects/bulk-delete', { ids: selectedIds.value }, {
    onSuccess: (page) => {
      isBulkDeleteModalOpen.value = false;
      selectedIds.value = [];
      flashSuccess.value = page.props.flash?.success || `${count} penawaran berhasil dihapus.`;
    },
    onFinish: () => {
      isDeleting.value = false;
    }
  });
}
</script>

<template>
  <Head title="Daftar Penawaran Harga" />

  <AppLayout title="Daftar Penawaran Harga & Kontrak">
    <div class="space-y-6 max-w-7xl mx-auto">
      
      <!-- Top Action Bar Header (Simple Style) -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/80 border border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shadow-sm shrink-0">
              <FileText class="w-5 h-5" />
            </div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
              Daftar Penawaran Harga
            </h2>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400">
            Kelola estimasi biaya proyek software, skema berlangganan SaaS, dan dokumen adendum.
          </p>
        </div>

        <div class="flex items-center gap-2.5 self-start sm:self-auto shrink-0">
          <button
            @click="showExportModal = true"
            class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-extrabold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition flex items-center justify-center gap-2 cursor-pointer active:scale-95 shadow-xs"
          >
            <FileDown class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
            <span>Ekspor Laporan</span>
          </button>

          <Link
            href="/projects/create"
            class="px-4.5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/30 transition flex items-center justify-center gap-2 cursor-pointer active:scale-95 shrink-0"
          >
            <Plus class="w-4 h-4" />
            <span>Buat Penawaran Baru</span>
          </Link>
        </div>
      </div>

      <!-- Interactive Filters & Control Panel -->
      <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
        
        <!-- Top Row: Search Input & Sort Selector -->
        <div class="flex flex-col md:flex-row items-center gap-3">
          <!-- Search Input with Clear Button -->
          <div class="relative flex-1 w-full">
            <Search class="w-4 h-4 absolute left-3.5 top-3 text-slate-400" />
            <input
              v-model="search"
              @input="applyFilter"
              type="text"
              placeholder="Cari nama klien, perusahaan, atau nomor ID penawaran (#QUO)..."
              class="w-full pl-10 pr-9 py-2.5 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 border border-slate-200 dark:border-slate-700/80 rounded-xl focus:ring-2 focus:ring-indigo-500 transition"
            />
            <button
              v-if="search"
              type="button"
              @click="clearSearch"
              title="Bersihkan Pencarian"
              class="absolute right-3 top-2.5 p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg transition cursor-pointer z-10"
            >
              <X class="w-3.5 h-3.5" />
            </button>
          </div>

          <!-- Sort Order Select -->
          <div class="flex items-center gap-2 w-full md:w-auto">
            <div class="relative w-full md:w-48">
              <ArrowUpDown class="w-3.5 h-3.5 absolute left-3 top-3 text-slate-400 pointer-events-none" />
              <select
                v-model="sortFilter"
                @change="applyFilter"
                class="w-full pl-8 pr-8 py-2.5 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 border border-slate-200 dark:border-slate-700/80 rounded-xl focus:ring-2 focus:ring-indigo-500 cursor-pointer"
              >
                <option value="latest">Terbaru</option>
                <option value="oldest">Terlama</option>
                <option value="amount_desc">Nilai Tertinggi</option>
                <option value="amount_asc">Nilai Terendah</option>
              </select>
            </div>

            <!-- Reset Filters Button -->
            <button
              v-if="hasActiveFilters"
              @click="resetAllFilters"
              title="Reset Semua Filter"
              class="px-3.5 py-2.5 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 font-bold text-xs rounded-xl transition flex items-center gap-1.5 cursor-pointer shrink-0"
            >
              <RotateCcw class="w-3.5 h-3.5" />
              <span class="hidden sm:inline">Reset</span>
            </button>
          </div>
        </div>

        <!-- Bottom Row: Interactive Segmented Pill Toggles -->
        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-slate-800/80 text-xs">
          <!-- Skema Kontrak Pills -->
          <div class="flex items-center gap-2 flex-wrap">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1 flex items-center gap-1">
              <SlidersHorizontal class="w-3 h-3 text-indigo-500" />
              <span>Skema:</span>
            </span>

            <button
              type="button"
              @click="setBillingType('')"
              :class="billingTypeFilter === '' 
                ? 'bg-indigo-600 text-white font-bold shadow-sm shadow-indigo-600/30' 
                : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 font-semibold'"
              class="px-3 py-1.5 rounded-xl transition cursor-pointer"
            >
              Semua
            </button>

            <button
              type="button"
              @click="setBillingType('one_off')"
              :class="billingTypeFilter === 'one_off' 
                ? 'bg-indigo-600 text-white font-bold shadow-sm shadow-indigo-600/30' 
                : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 font-semibold'"
              class="px-3 py-1.5 rounded-xl transition cursor-pointer"
            >
              Putus Kontrak (One-Off)
            </button>

            <button
              type="button"
              @click="setBillingType('subscription')"
              :class="billingTypeFilter === 'subscription' 
                ? 'bg-indigo-600 text-white font-bold shadow-sm shadow-indigo-600/30' 
                : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 font-semibold'"
              class="px-3 py-1.5 rounded-xl transition cursor-pointer"
            >
              Berlangganan (SaaS)
            </button>
          </div>

          <!-- Status Penawaran Pills -->
          <div class="flex items-center gap-2 flex-wrap">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1">Status:</span>

            <button
              type="button"
              @click="setStatus('')"
              :class="statusFilter === '' 
                ? 'bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 font-bold shadow-sm' 
                : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 font-semibold'"
              class="px-3 py-1.5 rounded-xl transition cursor-pointer"
            >
              Semua Status
            </button>

            <button
              type="button"
              @click="setStatus('Draft')"
              :class="statusFilter === 'Draft' 
                ? 'bg-amber-500 text-white font-bold shadow-sm shadow-amber-500/30' 
                : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 font-semibold'"
              class="px-3 py-1.5 rounded-xl transition cursor-pointer"
            >
              Draft Berjalan
            </button>

            <button
              type="button"
              @click="setStatus('Generated')"
              :class="statusFilter === 'Generated' 
                ? 'bg-emerald-600 text-white font-bold shadow-sm shadow-emerald-600/30' 
                : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 font-semibold'"
              class="px-3 py-1.5 rounded-xl transition cursor-pointer"
            >
              Resmi (Generated)
            </button>
          </div>
        </div>
      </div>

      <!-- Simple Notification Alert (Below Filter & Search Card) -->
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

      <!-- Floating Bulk Action Dock (Fixed Bottom Center) -->
      <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 translate-y-8 scale-95"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 translate-y-8 scale-95"
      >
        <div
          v-if="selectedIds.length > 0"
          class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 p-2 px-3 rounded-2xl bg-slate-900/95 dark:bg-slate-950/95 text-white border border-slate-700/80 dark:border-slate-800 shadow-2xl backdrop-blur-xl flex items-center gap-2"
        >
          <button
            @click="selectedIds = []"
            class="px-3.5 py-2 text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800/80 rounded-xl transition cursor-pointer"
          >
            Batal
          </button>

          <button
            @click="promptBulkDelete"
            class="px-4 py-2 text-xs font-extrabold bg-rose-600 hover:bg-rose-500 text-white rounded-xl shadow-md shadow-rose-600/30 transition flex items-center gap-2 cursor-pointer active:scale-95 shrink-0"
          >
            <Trash2 class="w-4 h-4" />
            <span>Hapus ({{ selectedIds.length }})</span>
          </button>
        </div>
      </Transition>

      <!-- Main Projects Table -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold uppercase text-slate-400">
                <th class="py-3 px-3 w-12 text-center select-none">
                  <div 
                    @click="toggleSelectAll" 
                    class="w-5 h-5 rounded-lg border transition-all duration-150 flex items-center justify-center cursor-pointer mx-auto"
                    :class="isAllSelected || isSomeSelected 
                      ? 'bg-rose-600 border-rose-600 text-white shadow-xs shadow-rose-600/40' 
                      : 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 hover:border-rose-400'"
                  >
                    <Check v-if="isAllSelected" class="w-3.5 h-3.5 stroke-[3]" />
                    <Minus v-else-if="isSomeSelected" class="w-3.5 h-3.5 stroke-[3]" />
                  </div>
                </th>
                <th class="py-3 px-4">Penawaran & Klien</th>
                <th class="py-3 px-4">Skema & Tipe</th>
                <th class="py-3 px-4">Nilai Penawaran</th>
                <th class="py-3 px-4">Estimator</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4 text-right">Menu Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs font-semibold">
              <tr 
                v-for="item in projects.data" 
                :key="item.id" 
                @click="router.get(`/projects/${item.id}/edit`)"
                class="hover:bg-indigo-50/50 dark:hover:bg-indigo-950/30 transition cursor-pointer group"
                :class="selectedIds.includes(item.id) ? 'bg-rose-50/80 dark:bg-rose-950/50 border-l-4 border-l-rose-500 dark:border-l-rose-500 hover:bg-rose-100/60 dark:hover:bg-rose-950/70' : ''"
              >
                <!-- Custom Checkbox Column -->
                <td class="py-3.5 px-3 w-12 text-center" @click.stop>
                  <div 
                    @click.stop="toggleSelectRow(item.id)" 
                    class="w-5 h-5 rounded-lg border transition-all duration-150 flex items-center justify-center cursor-pointer mx-auto"
                    :class="selectedIds.includes(item.id) 
                      ? 'bg-rose-600 border-rose-600 text-white shadow-xs shadow-rose-600/40 scale-105' 
                      : 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 hover:border-rose-400 opacity-60 group-hover:opacity-100'"
                  >
                    <Check v-if="selectedIds.includes(item.id)" class="w-3.5 h-3.5 stroke-[3]" />
                  </div>
                </td>

                <!-- Client & ID -->
                <td class="py-3.5 px-4">
                  <div>
                    <div class="font-bold text-slate-900 dark:text-white text-sm group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition leading-snug">
                      {{ formatClientName(item.client_name) }}
                    </div>
                    <div v-if="item.project_category" class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-0.5">
                      {{ item.project_category }}
                    </div>
                    <div class="text-[11px] font-mono text-indigo-600 dark:text-indigo-400 mt-0.5 flex items-center gap-1.5">
                      <span>#{{ item.code }}</span>
                      <span class="text-slate-300 dark:text-slate-700">•</span>
                      <span class="text-slate-500 dark:text-slate-400 font-sans">{{ item.created_at_formatted }}</span>
                    </div>
                    <div v-if="item.quotation_type === 'addendum'" class="text-[11px] font-bold text-amber-600 dark:text-amber-400 mt-0.5">
                      📑 Adendum (Induk: #{{ item.parent_code }})
                    </div>
                  </div>
                </td>

                <!-- Billing Type -->
                <td class="py-3 px-4">
                  <Badge :variant="item.billing_type === 'subscription' ? 'emerald' : 'info'">
                    {{ item.billing_type === 'subscription' ? `Langganan (${item.subscription_basis})` : 'Putus Kontrak' }}
                  </Badge>
                  <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                    {{ item.billing_type === 'subscription' ? `${item.subscription_duration} ${item.billing_cycle === 'yearly' ? 'Tahunan' : 'Bulanan'}` : `${item.items_count} Modul Terhitung` }}
                  </div>
                </td>

                <!-- Grand Total -->
                <td class="py-3 px-4">
                  <div class="font-extrabold text-slate-900 dark:text-white text-sm">{{ item.grand_total_formatted }}</div>
                  <div class="text-[11px] text-slate-500 dark:text-slate-400">Garansi: {{ item.maintenance_months }} Bln SLA</div>
                </td>

                <!-- Estimator -->
                <td class="py-3 px-4">
                  <Badge variant="gray">{{ item.estimator_name }}</Badge>
                </td>

                <!-- Status -->
                <td class="py-3 px-4">
                  <Badge :variant="item.status === 'Generated' ? 'emerald' : 'amber'">
                    {{ item.status }}
                  </Badge>
                </td>

                <!-- Action Menu Column -->
                <td class="py-3 px-4 text-right">
                  <ActionMenu
                    :project="item"
                    @open-addendum="openAddendumModal"
                    @delete-project="promptDeleteProject"
                  />
                </td>
              </tr>

              <tr v-if="!projects.data || !projects.data.length">
                <td colspan="7" class="py-12 text-center text-slate-400">
                  Belum ada data penawaran yang sesuai dengan filter.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Bar -->
        <div v-if="projects.links && projects.links.length > 3" class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-800">
          <div class="text-xs text-slate-500 dark:text-slate-400 font-semibold">
            Menampilkan {{ projects.from }} sampai {{ projects.to }} dari {{ projects.total }} Penawaran
          </div>

          <div class="flex items-center gap-1.5">
            <Link
              v-for="(link, i) in projects.links"
              :key="i"
              :href="link.url || '#'"
              v-html="link.label"
              class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer"
              :class="link.active 
                ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' 
                : link.url ? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' : 'text-slate-300 dark:text-slate-700 cursor-not-allowed'"
            />
          </div>
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

    <!-- Single Delete Modal -->
    <ConfirmDeleteModal
      :show="deleteModalOpen"
      title="Hapus Penawaran Harga"
      message="Apakah Anda yakin ingin menghapus dokumen penawaran harga ini?"
      :item-name="targetProjectToDelete ? `Penawaran #${targetProjectToDelete.code}` : ''"
      :processing="isDeleting"
      @close="deleteModalOpen = false"
      @confirm="confirmDeleteProject"
    />

    <!-- Bulk Delete Modal -->
    <ConfirmDeleteModal
      :show="isBulkDeleteModalOpen"
      title="Hapus Penawaran Harga Terpilih"
      message="Apakah Anda yakin ingin menghapus seluruh dokumen penawaran terpilih ini sekaligus?"
      :item-name="`${selectedIds.length} Dokumen Penawaran Dipilih`"
      :processing="isDeleting"
      @close="isBulkDeleteModalOpen = false"
      @confirm="confirmBulkDelete"
    />

    <!-- Export Report Modal -->
    <ExportReportModal
      :show="showExportModal"
      @close="showExportModal = false"
    />
  </AppLayout>
</template>
