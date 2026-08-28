<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
  Building2, 
  Search, 
  Plus, 
  Phone, 
  Mail, 
  Globe, 
  MessageSquare, 
  ChevronRight, 
  Briefcase, 
  FileText, 
  CheckCircle2, 
  Clock, 
  Users, 
  Trash2, 
  Edit3, 
  X,
  ExternalLink,
  DollarSign,
  TrendingUp,
  Sparkles
} from 'lucide-vue-next';

const props = defineProps({
  clients: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({ search: '', status: '', industry: '', sort: 'latest' })
  },
  stats: {
    type: Object,
    default: () => ({ total_clients: 0, active_clients: 0, prospects: 0, pipeline_value_formatted: 'Rp 0' })
  },
  industries: {
    type: Array,
    default: () => []
  }
});

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');
const industry = ref(props.filters?.industry || '');
const sort = ref(props.filters?.sort || 'latest');

const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedClient = ref(null);

const clientForm = ref({
  name: '',
  industry: '',
  email: '',
  phone: '',
  website: '',
  address: '',
  status: 'prospect',
  notes: '',
  contact_name: '',
  contact_title: '',
  contact_email: '',
  contact_phone: '',
});

const editForm = ref({
  id: null,
  name: '',
  industry: '',
  email: '',
  phone: '',
  website: '',
  address: '',
  status: 'prospect',
  notes: '',
});

let debounceTimer = null;

function applyFilters() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    router.get(
      '/clients',
      {
        search: search.value,
        status: status.value,
        industry: industry.value,
        sort: sort.value,
      },
      { preserveState: true, replace: true }
    );
  }, 300);
}

function clearSearch() {
  search.value = '';
  applyFilters();
}

function resetAllFilters() {
  search.value = '';
  status.value = '';
  industry.value = '';
  sort.value = 'latest';
  applyFilters();
}

const hasActiveFilters = computed(() => {
  return !!search.value || !!status.value || !!industry.value || (!!sort.value && sort.value !== 'latest');
});

watch([search, status, industry, sort], () => {
  applyFilters();
});

function openCreateModal() {
  clientForm.value = {
    name: '',
    industry: '',
    email: '',
    phone: '',
    website: '',
    address: '',
    status: 'prospect',
    notes: '',
    contact_name: '',
    contact_title: '',
    contact_email: '',
    contact_phone: '',
  };
  isCreateModalOpen.value = true;
}

function submitCreateClient() {
  router.post('/clients', clientForm.value, {
    onSuccess: () => {
      isCreateModalOpen.value = false;
    }
  });
}

function openEditModal(client) {
  selectedClient.value = client;
  editForm.value = {
    id: client.id,
    name: client.name,
    industry: client.industry === 'Uncategorized' ? '' : client.industry,
    email: client.email === '-' ? '' : client.email,
    phone: client.phone === '-' ? '' : client.phone,
    website: client.website || '',
    address: client.address || '',
    status: client.status,
    notes: client.notes || '',
  };
  isEditModalOpen.value = true;
}

function submitEditClient() {
  router.put(`/clients/${editForm.value.id}`, editForm.value, {
    onSuccess: () => {
      isEditModalOpen.value = false;
    }
  });
}

function openDeleteModal(client) {
  selectedClient.value = client;
  isDeleteModalOpen.value = true;
}

function confirmDeleteClient() {
  if (!selectedClient.value) return;
  router.delete(`/clients/${selectedClient.value.id}`, {
    onSuccess: () => {
      isDeleteModalOpen.value = false;
      selectedClient.value = null;
    }
  });
}

function getStatusBadge(st) {
  switch (st) {
    case 'active':
      return { label: 'Klien Aktif', class: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' };
    case 'prospect':
      return { label: 'Prospek', class: 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/70 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800' };
    case 'lead':
      return { label: 'Lead Masuk', class: 'bg-amber-50 text-amber-700 dark:bg-amber-950/70 dark:text-amber-300 border-amber-200 dark:border-amber-800' };
    default:
      return { label: 'Non-Aktif', class: 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border-slate-200 dark:border-slate-700' };
  }
}
</script>

<template>
  <AppLayout>
    <Head title="Klien & Kontak CRM" />

    <div class="space-y-6 max-w-7xl mx-auto">
      
      <!-- HEADER & METRICS -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Klien & Kontak CRM</h1>
            <span class="px-2 py-0.5 text-xs font-extrabold bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 rounded-lg border border-indigo-200 dark:border-indigo-800">B2B Directory</span>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Kelola data perusahaan klien, kontak PIC pengambil keputusan, total Lifetime Value (LTV), dan histori penawaran DevCalc.
          </p>
        </div>

        <button
          @click="openCreateModal"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-lg shadow-indigo-600/30 transition cursor-pointer active:scale-95 shrink-0"
        >
          <Plus class="w-4 h-4 stroke-[3]" />
          <span>Tambah Klien Baru</span>
        </button>
      </div>

      <!-- METRIC CARDS -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs flex items-center gap-3.5">
          <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
            <Building2 class="w-5 h-5" />
          </div>
          <div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Klien</div>
            <div class="text-xl font-black text-slate-900 dark:text-white">{{ stats.total_clients }}</div>
          </div>
        </div>

        <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs flex items-center gap-3.5">
          <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
            <CheckCircle2 class="w-5 h-5" />
          </div>
          <div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Klien Aktif</div>
            <div class="text-xl font-black text-emerald-600 dark:text-emerald-400">{{ stats.active_clients }}</div>
          </div>
        </div>

        <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs flex items-center gap-3.5">
          <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
            <Clock class="w-5 h-5" />
          </div>
          <div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Prospek / Lead</div>
            <div class="text-xl font-black text-amber-600 dark:text-amber-400">{{ stats.prospects }}</div>
          </div>
        </div>

        <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs flex items-center gap-3.5">
          <div class="w-10 h-10 rounded-2xl bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
            <TrendingUp class="w-5 h-5" />
          </div>
          <div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Pipeline Deals</div>
            <div class="text-base font-black text-purple-600 dark:text-purple-400 truncate">{{ stats.pipeline_value_formatted }}</div>
          </div>
        </div>
      </div>

      <!-- FILTER & SEARCH BAR -->
      <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
        
        <!-- Search -->
        <div class="relative flex-1">
          <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
          <input
            v-model="search"
            @input="applyFilters"
            type="text"
            placeholder="Cari nama perusahaan, industri, email, nomor kontak..."
            class="w-full pl-10 pr-9 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700/70 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
          />
          <button
            v-if="search"
            @click="clearSearch"
            type="button"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer p-1"
            title="Hapus pencarian"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>

        <!-- Filter Selects -->
        <div class="flex items-center gap-2.5 flex-wrap">
          <select
            v-model="status"
            @change="applyFilters"
            class="px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700/70 rounded-2xl text-xs font-bold text-slate-700 dark:text-slate-300 focus:outline-hidden focus:border-indigo-500 cursor-pointer"
          >
            <option value="">Semua Status</option>
            <option value="active">Klien Aktif</option>
            <option value="prospect">Prospek</option>
            <option value="lead">Lead Masuk</option>
            <option value="inactive">Non-Aktif</option>
          </select>

          <select
            v-model="industry"
            @change="applyFilters"
            class="px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700/70 rounded-2xl text-xs font-bold text-slate-700 dark:text-slate-300 focus:outline-hidden focus:border-indigo-500 cursor-pointer"
          >
            <option value="">Semua Industri</option>
            <option v-for="ind in industries" :key="ind" :value="ind">{{ ind }}</option>
          </select>

          <select
            v-model="sort"
            @change="applyFilters"
            class="px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700/70 rounded-2xl text-xs font-bold text-slate-700 dark:text-slate-300 focus:outline-hidden focus:border-indigo-500 cursor-pointer"
          >
            <option value="latest">Terbaru</option>
            <option value="oldest">Terlama</option>
            <option value="name_asc">Nama A-Z</option>
            <option value="name_desc">Nama Z-A</option>
          </select>

          <button
            v-if="hasActiveFilters"
            @click="resetAllFilters"
            type="button"
            class="px-3 py-2 bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/60 text-rose-600 dark:text-rose-400 rounded-2xl text-xs font-bold hover:bg-rose-100 dark:hover:bg-rose-900/60 transition cursor-pointer flex items-center gap-1.5 active:scale-95"
            title="Reset semua filter"
          >
            <X class="w-3.5 h-3.5" />
            <span>Reset Filter</span>
          </button>
        </div>

      </div>

      <!-- CLIENTS GRID / CARDS -->
      <div v-if="clients.data.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        
        <div
          v-for="c in clients.data"
          :key="c.id"
          class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 p-5 shadow-xs hover:shadow-md hover:border-indigo-200 dark:hover:border-indigo-800/70 transition-all flex flex-col justify-between group"
        >
          <div class="space-y-4">
            
            <!-- Top Card: Name & Status Badge -->
            <div class="flex items-start justify-between gap-3">
              <div class="flex items-center gap-3 overflow-hidden">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-black text-base flex items-center justify-center shrink-0 shadow-md shadow-indigo-500/20">
                  {{ c.name.charAt(0).toUpperCase() }}
                </div>
                <div class="truncate">
                  <Link
                    :href="`/clients/${c.id}`"
                    class="text-sm font-black text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition truncate block"
                  >
                    {{ c.name }}
                  </Link>
                  <span class="text-[11px] font-bold text-slate-400">{{ c.industry }}</span>
                </div>
              </div>

              <span 
                class="px-2.5 py-1 text-[10px] font-extrabold rounded-xl border shrink-0"
                :class="getStatusBadge(c.status).class"
              >
                {{ getStatusBadge(c.status).label }}
              </span>
            </div>

            <!-- PIC Contact Section with Direct WhatsApp Button -->
            <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800/80 space-y-2">
              <div class="flex items-center justify-between text-[11px]">
                <span class="font-bold text-slate-400 uppercase tracking-wider text-[9px]">Kontak PIC Utama</span>
                <span v-if="c.contacts_count > 1" class="text-[10px] font-bold text-indigo-500">+{{ c.contacts_count - 1 }} lainnya</span>
              </div>

              <div v-if="c.primary_contact" class="flex items-center justify-between gap-2">
                <div class="truncate">
                  <div class="text-xs font-black text-slate-800 dark:text-slate-200 truncate">{{ c.primary_contact.name }}</div>
                  <div class="text-[10px] text-slate-400 truncate">{{ c.primary_contact.title || 'PIC' }}</div>
                </div>

                <!-- WhatsApp Click to Chat Button -->
                <a
                  v-if="c.primary_contact.whatsapp_url"
                  :href="c.primary_contact.whatsapp_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-[11px] font-extrabold shadow-sm transition active:scale-95 shrink-0"
                  title="Hubungi via WhatsApp"
                >
                  <MessageSquare class="w-3.5 h-3.5 stroke-[2.5]" />
                  <span>WhatsApp</span>
                </a>
              </div>
              <div v-else class="text-[11px] text-slate-400 italic">
                Belum ada kontak PIC
              </div>
            </div>

            <!-- Metrics: Lifetime Value & Deals -->
            <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-100 dark:border-slate-800/80 text-xs">
              <div>
                <div class="text-[10px] font-bold text-slate-400">Total Lifetime Value (LTV)</div>
                <div class="text-xs font-black text-slate-900 dark:text-white">{{ c.ltv_formatted }}</div>
              </div>
              <div>
                <div class="text-[10px] font-bold text-slate-400">Deals & Penawaran</div>
                <div class="text-xs font-extrabold text-indigo-600 dark:text-indigo-400">
                  {{ c.active_deals_count }} Deal • {{ c.projects_count }} Quo
                </div>
              </div>
            </div>

          </div>

          <!-- Bottom Action Buttons -->
          <div class="pt-4 mt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
            
            <div class="flex items-center gap-1">
              <button
                @click="openEditModal(c)"
                class="p-1.5 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 rounded-xl transition cursor-pointer"
                title="Edit Klien"
              >
                <Edit3 class="w-4 h-4" />
              </button>
              <button
                @click="openDeleteModal(c)"
                class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-xl transition cursor-pointer"
                title="Hapus Klien"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>

            <div class="flex items-center gap-2">
              <Link
                v-if="!c.projects_count || c.projects_count === 0"
                :href="`/projects/create?client_id=${c.id}`"
                class="px-2.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950 text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 text-[11px] font-bold transition flex items-center gap-1"
                title="Buat Penawaran DevCalc Baru"
              >
                <FileText class="w-3.5 h-3.5" />
                <span>+ Penawaran</span>
              </Link>

              <Link
                :href="`/clients/${c.id}`"
                class="px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-600 hover:text-white font-extrabold text-[11px] transition flex items-center gap-1"
              >
                <span>360° Hub</span>
                <ChevronRight class="w-3.5 h-3.5" />
              </Link>
            </div>

          </div>

        </div>

      </div>

      <!-- Empty State -->
      <div v-else class="p-12 text-center rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 space-y-3">
        <Building2 class="w-12 h-12 text-slate-300 dark:text-slate-700 mx-auto" />
        <h3 class="text-base font-black text-slate-900 dark:text-white">Tidak ada data klien yang cocok</h3>
        <p class="text-xs text-slate-400 max-w-sm mx-auto">
          Coba atur ulang filter pencarian atau tambahkan perusahaan klien baru ke dalam direktori CRM.
        </p>
        <button
          @click="openCreateModal"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-indigo-600 text-white text-xs font-bold shadow-md shadow-indigo-600/30"
        >
          <Plus class="w-4 h-4" />
          <span>Tambah Klien Baru</span>
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="clients.links && clients.links.length > 3" class="flex justify-center gap-1 pt-4">
        <template v-for="(link, key) in clients.links" :key="key">
          <Link
            v-if="link.url"
            :href="link.url"
            class="px-3 py-1.5 rounded-xl text-xs font-bold transition"
            :class="link.active 
              ? 'bg-indigo-600 text-white shadow-sm' 
              : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800'"
            v-html="link.label"
          />
          <span
            v-else
            class="px-3 py-1.5 rounded-xl text-xs text-slate-400 opacity-50 select-none"
            v-html="link.label"
          />
        </template>
      </div>

    </div>

    <!-- MODAL CREATE CLIENT -->
    <div
      v-if="isCreateModalOpen"
      class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4"
      @click.self="isCreateModalOpen = false"
    >
      <div class="w-full max-w-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
              <Building2 class="w-4 h-4" />
            </div>
            <div>
              <h3 class="text-sm font-black text-slate-900 dark:text-white">Tambah Klien Baru</h3>
              <p class="text-[11px] text-slate-400">Registrasi perusahaan B2B & kontak PIC utama</p>
            </div>
          </div>
          <button @click="isCreateModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitCreateClient" class="p-5 space-y-4 overflow-y-auto custom-scrollbar flex-1">
          
          <div class="space-y-3">
            <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">1. Informasi Perusahaan</div>

            <div>
              <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Perusahaan / Klien *</label>
              <input
                v-model="clientForm.name"
                type="text"
                required
                placeholder="misal: PT Nusantara Teknologi Mandiri"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:border-indigo-500"
              />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Industri</label>
                <input
                  v-model="clientForm.industry"
                  type="text"
                  placeholder="misal: Fintech, F&B, Retail, Logistics"
                  class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:border-indigo-500"
                />
              </div>

              <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Status Klien</label>
                <select
                  v-model="clientForm.status"
                  class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 focus:outline-hidden focus:border-indigo-500"
                >
                  <option value="prospect">Prospek</option>
                  <option value="lead">Lead Masuk</option>
                  <option value="active">Klien Aktif</option>
                  <option value="inactive">Non-Aktif</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Email Perusahaan</label>
                <input
                  v-model="clientForm.email"
                  type="email"
                  placeholder="corporate@perusahaan.com"
                  class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:border-indigo-500"
                />
              </div>

              <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Website</label>
                <input
                  v-model="clientForm.website"
                  type="text"
                  placeholder="https://perusahaan.com"
                  class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:border-indigo-500"
                />
              </div>
            </div>

            <div>
              <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat Kantor</label>
              <textarea
                v-model="clientForm.address"
                rows="2"
                placeholder="Alamat kantor..."
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:border-indigo-500"
              ></textarea>
            </div>
          </div>

          <!-- PIC Section -->
          <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-3">
            <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">2. Kontak PIC Utama (WhatsApp)</div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Nama PIC</label>
                <input
                  v-model="clientForm.contact_name"
                  type="text"
                  placeholder="misal: Budi Santoso"
                  class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:border-indigo-500"
                />
              </div>

              <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Jabatan / Role</label>
                <input
                  v-model="clientForm.contact_title"
                  type="text"
                  placeholder="misal: CTO / Procurement Lead"
                  class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:border-indigo-500"
                />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp PIC</label>
                <input
                  v-model="clientForm.contact_phone"
                  type="text"
                  placeholder="081234567890"
                  class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:border-indigo-500"
                />
              </div>

              <div>
                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Email PIC</label>
                <input
                  v-model="clientForm.contact_email"
                  type="email"
                  placeholder="budi@perusahaan.com"
                  class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:border-indigo-500"
                />
              </div>
            </div>
          </div>

          <div class="p-4 bg-slate-50 dark:bg-slate-800/50 -mx-5 -mb-5 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-2">
            <button
              type="button"
              @click="isCreateModalOpen = false"
              class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition"
            >
              Batal
            </button>
            <button
              type="submit"
              class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-extrabold shadow-md shadow-indigo-600/30 hover:bg-indigo-700 transition"
            >
              Simpan Klien
            </button>
          </div>

        </form>

      </div>
    </div>

    <!-- MODAL EDIT CLIENT -->
    <div
      v-if="isEditModalOpen"
      class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4"
      @click.self="isEditModalOpen = false"
    >
      <div class="w-full max-w-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl overflow-hidden">
        
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
          <h3 class="text-sm font-black text-slate-900 dark:text-white">Edit Data Klien</h3>
          <button @click="isEditModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitEditClient" class="p-5 space-y-3">
          <div>
            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Perusahaan *</label>
            <input
              v-model="editForm.name"
              type="text"
              required
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:border-indigo-500"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Industri</label>
              <input
                v-model="editForm.industry"
                type="text"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
              />
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Status</label>
              <select
                v-model="editForm.status"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300"
              >
                <option value="active">Klien Aktif</option>
                <option value="prospect">Prospek</option>
                <option value="lead">Lead Masuk</option>
                <option value="inactive">Non-Aktif</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Email</label>
              <input
                v-model="editForm.email"
                type="email"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
              />
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Telepon</label>
              <input
                v-model="editForm.phone"
                type="text"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
              />
            </div>
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat</label>
            <textarea
              v-model="editForm.address"
              rows="2"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
            ></textarea>
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
            <button
              type="button"
              @click="isEditModalOpen = false"
              class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
            >
              Batal
            </button>
            <button
              type="submit"
              class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-extrabold shadow-md hover:bg-indigo-700"
            >
              Perbarui Klien
            </button>
          </div>
        </form>

      </div>
    </div>

    <!-- MODAL DELETE CONFIRMATION -->
    <div
      v-if="isDeleteModalOpen"
      class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4"
      @click.self="isDeleteModalOpen = false"
    >
      <div class="w-full max-w-md bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl p-6 space-y-4">
        <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto">
          <Trash2 class="w-6 h-6" />
        </div>
        <div class="text-center space-y-1">
          <h3 class="text-base font-black text-slate-900 dark:text-white">Hapus Klien Ini?</h3>
          <p class="text-xs text-slate-400">
            Apakah Anda yakin ingin menghapus <span class="font-bold text-slate-700 dark:text-slate-200">{{ selectedClient?.name }}</span>? Data penawaran DevCalc yang terhubung akan tetap tersimpan.
          </p>
        </div>
        <div class="flex items-center justify-center gap-3 pt-2">
          <button
            @click="isDeleteModalOpen = false"
            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
          >
            Batal
          </button>
          <button
            @click="confirmDeleteClient"
            class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-md shadow-rose-600/30"
          >
            Hapus Klien
          </button>
        </div>
      </div>
    </div>

  </AppLayout>
</template>
