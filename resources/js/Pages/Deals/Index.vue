<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
  Kanban, 
  Plus, 
  Search, 
  TrendingUp, 
  CheckCircle2, 
  XCircle, 
  DollarSign, 
  Calendar, 
  Users, 
  FileText, 
  MessageSquare, 
  ChevronRight, 
  MoreVertical, 
  Building2, 
  Trash2, 
  Edit3, 
  ArrowRight,
  X,
  Target,
  Sparkles,
  Percent
} from 'lucide-vue-next';

const props = defineProps({
  kanbanColumns: {
    type: Object,
    required: true
  },
  stagesConfig: {
    type: Object,
    required: true
  },
  clients: {
    type: Array,
    default: () => []
  },
  users: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({ search: '', user_id: '', client_id: '' })
  },
  pipelineStats: {
    type: Object,
    default: () => ({
      total_deals: 0,
      active_deals_count: 0,
      pipeline_value_formatted: 'Rp 0',
      weighted_value_formatted: 'Rp 0',
      won_count: 0,
      won_value_formatted: 'Rp 0',
      win_rate: 0
    })
  }
});

const search = ref(props.filters.search || '');
const selectedUserId = ref(props.filters.user_id || '');
const selectedClientId = ref(props.filters.client_id || '');

const activeClient = computed(() => {
  if (!selectedClientId.value) return null;
  return props.clients.find(c => String(c.id) === String(selectedClientId.value));
});

const isAddDealModalOpen = ref(false);
const isEditDealModalOpen = ref(false);
const isLostModalOpen = ref(false);
const selectedDeal = ref(null);

const dealForm = ref({
  client_id: '',
  title: '',
  stage: 'discovery',
  expected_value: 0,
  probability: 20,
  expected_close_date: '',
  notes: '',
});

const editDealForm = ref({
  id: null,
  title: '',
  stage: 'discovery',
  expected_value: 0,
  probability: 20,
  expected_close_date: '',
  notes: '',
});

const lostForm = ref({
  deal_id: null,
  lost_reason: 'Budget Klien Tidak Mencukupi',
  custom_reason: '',
});

let searchDebounce = null;
function applyFilter() {
  clearTimeout(searchDebounce);
  searchDebounce = setTimeout(() => {
    router.get(
      '/deals',
      {
        search: search.value,
        user_id: selectedUserId.value,
        client_id: selectedClientId.value,
      },
      { preserveState: true, replace: true }
    );
  }, 300);
}

function clearSearch() {
  search.value = '';
  applyFilter();
}

function clearClientFilter() {
  selectedClientId.value = '';
  applyFilter();
}

function resetAllFilters() {
  search.value = '';
  selectedUserId.value = '';
  selectedClientId.value = '';
  applyFilter();
}

const hasActiveFilters = computed(() => {
  return !!search.value || !!selectedUserId.value || !!selectedClientId.value;
});

watch([search, selectedUserId, selectedClientId], () => {
  applyFilter();
});

function openAddDealModal(preselectedStage = 'discovery') {
  dealForm.value = {
    client_id: props.clients[0]?.id || '',
    title: '',
    stage: preselectedStage,
    expected_value: 0,
    probability: props.stagesConfig[preselectedStage]?.probability || 20,
    expected_close_date: '',
    notes: '',
  };
  isAddDealModalOpen.value = true;
}

function onStageChangeInForm() {
  const stage = dealForm.value.stage;
  if (props.stagesConfig[stage]) {
    dealForm.value.probability = props.stagesConfig[stage].probability;
  }
}

function submitAddDeal() {
  router.post('/deals', dealForm.value, {
    onSuccess: () => {
      isAddDealModalOpen.value = false;
    }
  });
}

function openEditDealModal(deal) {
  selectedDeal.value = deal;
  editDealForm.value = {
    id: deal.id,
    title: deal.title,
    stage: deal.stage,
    expected_value: deal.expected_value,
    probability: deal.probability,
    expected_close_date: deal.expected_close_date || '',
    notes: deal.notes || '',
  };
  isEditDealModalOpen.value = true;
}

function submitEditDeal() {
  router.put(`/deals/${editDealForm.value.id}`, editDealForm.value, {
    onSuccess: () => {
      isEditDealModalOpen.value = false;
    }
  });
}

function moveStage(deal, newStage) {
  if (newStage === 'lost') {
    selectedDeal.value = deal;
    lostForm.value.deal_id = deal.id;
    isLostModalOpen.value = true;
    return;
  }

  router.patch(`/deals/${deal.id}/stage`, { stage: newStage });
}

function submitMarkLost() {
  const reason = lostForm.value.lost_reason === 'Lainnya' 
    ? lostForm.value.custom_reason 
    : lostForm.value.lost_reason;

  router.patch(`/deals/${lostForm.value.deal_id}/stage`, {
    stage: 'lost',
    lost_reason: reason,
  }, {
    onSuccess: () => {
      isLostModalOpen.value = false;
    }
  });
}

function deleteDeal(dealId) {
  if (confirm('Hapus peluang deal ini?')) {
    router.delete(`/deals/${dealId}`);
  }
}

function getStageBgHeader(key) {
  switch (key) {
    case 'discovery':
      return 'border-indigo-500/40 bg-indigo-50/50 dark:bg-indigo-950/30';
    case 'scoping':
      return 'border-blue-500/40 bg-blue-50/50 dark:bg-blue-950/30';
    case 'proposal_sent':
      return 'border-amber-500/40 bg-amber-50/50 dark:bg-amber-950/30';
    case 'negotiation':
      return 'border-purple-500/40 bg-purple-50/50 dark:bg-purple-950/30';
    case 'won':
      return 'border-emerald-500/40 bg-emerald-50/50 dark:bg-emerald-950/30';
    case 'lost':
      return 'border-rose-500/40 bg-rose-50/50 dark:bg-rose-950/30';
    default:
      return 'border-slate-500/40 bg-slate-50/50 dark:bg-slate-950/30';
  }
}

function getStageDotColor(key) {
  switch (key) {
    case 'discovery': return 'bg-indigo-500';
    case 'scoping': return 'bg-blue-500';
    case 'proposal_sent': return 'bg-amber-500';
    case 'negotiation': return 'bg-purple-500';
    case 'won': return 'bg-emerald-500';
    case 'lost': return 'bg-rose-500';
    default: return 'bg-slate-500';
  }
}
</script>

<template>
  <AppLayout>
    <Head title="Pipeline Deals CRM" />

    <div class="space-y-6 max-w-7xl mx-auto w-full min-w-0">
      
      <!-- HEADER & ACTIONS -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Pipeline Deals & Peluang</h1>
            <span class="px-2 py-0.5 text-xs font-extrabold bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400 rounded-lg border border-purple-200 dark:border-purple-800">Kanban Board</span>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Pantau dan gerakkan deal penjualan mulai dari Discovery, Scoping, Pembuatan Penawaran DevCalc, Negosiasi, hingga Closed Won.
          </p>
        </div>

        <button
          @click="openAddDealModal('discovery')"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-lg shadow-indigo-600/30 transition cursor-pointer active:scale-95 shrink-0"
        >
          <Plus class="w-4 h-4 stroke-[3]" />
          <span>Tambah Deal Baru</span>
        </button>
      </div>

      <!-- METRIC SUMMARY CARDS -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs flex items-center gap-3.5">
          <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
            <TrendingUp class="w-5 h-5" />
          </div>
          <div class="truncate">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Pipeline Aktif</div>
            <div class="text-lg font-black text-slate-900 dark:text-white truncate">{{ pipelineStats.pipeline_value_formatted }}</div>
            <div class="text-[10px] text-slate-400">{{ pipelineStats.active_deals_count }} Peluang Berjalan</div>
          </div>
        </div>

        <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs flex items-center gap-3.5">
          <div class="w-10 h-10 rounded-2xl bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
            <Percent class="w-5 h-5" />
          </div>
          <div class="truncate">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Weighted Revenue</div>
            <div class="text-lg font-black text-purple-600 dark:text-purple-400 truncate">{{ pipelineStats.weighted_value_formatted }}</div>
            <div class="text-[10px] text-slate-400">Probabilitas tertimbang</div>
          </div>
        </div>

        <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs flex items-center gap-3.5">
          <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
            <CheckCircle2 class="w-5 h-5" />
          </div>
          <div class="truncate">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Deals Won (Closing)</div>
            <div class="text-lg font-black text-emerald-600 dark:text-emerald-400 truncate">{{ pipelineStats.won_value_formatted }}</div>
            <div class="text-[10px] text-slate-400">{{ pipelineStats.won_count }} Deal Disetujui</div>
          </div>
        </div>

        <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs flex items-center gap-3.5">
          <div class="w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
            <Target class="w-5 h-5" />
          </div>
          <div class="truncate">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sales Win Rate</div>
            <div class="text-lg font-black text-blue-600 dark:text-blue-400 truncate">{{ pipelineStats.win_rate }}%</div>
            <div class="text-[10px] text-slate-400">Dari total deal selesai</div>
          </div>
        </div>

      </div>

      <!-- FILTER & SEARCH BAR -->
      <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        
        <div class="relative flex-1 max-w-md">
          <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
          <input
            v-model="search"
            @input="applyFilter"
            type="text"
            placeholder="Cari nama deal atau nama klien..."
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

        <div class="flex items-center gap-2 flex-wrap">
          <select
            v-model="selectedClientId"
            @change="applyFilter"
            class="px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700/70 rounded-2xl text-xs font-bold text-slate-700 dark:text-slate-300 focus:outline-hidden focus:border-indigo-500 cursor-pointer max-w-[180px] truncate"
          >
            <option value="">Semua Klien B2B</option>
            <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>

          <select
            v-model="selectedUserId"
            @change="applyFilter"
            class="px-3 py-2 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700/70 rounded-2xl text-xs font-bold text-slate-700 dark:text-slate-300 focus:outline-hidden focus:border-indigo-500 cursor-pointer"
          >
            <option value="">Semua Sales Rep</option>
            <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
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

      <!-- ACTIVE CLIENT FILTER BANNER (If filtered by specific client) -->
      <div v-if="activeClient" class="p-3.5 rounded-2xl bg-indigo-50/80 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800/80 flex items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2">
          <Building2 class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
          <span class="text-slate-600 dark:text-slate-300">Menampilkan Pipeline Deals untuk Klien:</span>
          <strong class="text-indigo-700 dark:text-indigo-300 font-black">{{ activeClient.name }}</strong>
        </div>
        <button
          @click="clearClientFilter"
          class="px-2.5 py-1 rounded-xl bg-white dark:bg-slate-900 border border-indigo-200 dark:border-indigo-800 text-[11px] font-extrabold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/40 transition cursor-pointer flex items-center gap-1"
        >
          <X class="w-3 h-3" />
          <span>Tampilkan Semua Klien</span>
        </button>
      </div>

      <!-- KANBAN BOARD 6 COLUMNS (Self-contained Horizontal Scroll) -->
      <div class="w-full min-w-0 overflow-x-auto custom-scrollbar pb-6 rounded-3xl">
        <div class="flex gap-4 min-w-max items-start pb-2">
          
          <div
            v-for="(col, colKey) in kanbanColumns"
            :key="colKey"
            class="w-[280px] shrink-0 flex flex-col rounded-3xl bg-slate-100/70 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80 overflow-hidden shadow-2xs"
          >
            
            <!-- Column Header -->
            <div 
              class="p-3.5 border-b flex items-center justify-between"
              :class="getStageBgHeader(col.key)"
            >
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full" :class="getStageDotColor(col.key)"></span>
                <span class="text-xs font-black text-slate-900 dark:text-white">{{ col.label }}</span>
                <span class="px-1.5 py-0.2 text-[10px] font-black rounded-lg bg-white/80 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 shadow-2xs">
                  {{ col.count }}
                </span>
              </div>

              <button
                v-if="col.key !== 'lost' && col.key !== 'won'"
                @click="openAddDealModal(col.key)"
                class="p-1 rounded-lg text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-white/50 dark:hover:bg-slate-800/50 transition cursor-pointer"
                title="Tambah Deal di Stage Ini"
              >
                <Plus class="w-3.5 h-3.5 stroke-[3]" />
              </button>
            </div>

            <!-- Stage Subheader (Total Rp) -->
            <div class="px-3.5 py-2 bg-white/40 dark:bg-slate-900/40 border-b border-slate-200/50 dark:border-slate-800/50 flex items-center justify-between text-[11px]">
              <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Subtotal Stage</span>
              <span class="font-black text-slate-800 dark:text-slate-200">{{ col.total_value_formatted }}</span>
            </div>

            <!-- Column Cards Body -->
            <div class="p-3 space-y-3 min-h-[450px] max-h-[700px] overflow-y-auto custom-scrollbar">
              
              <div
                v-for="deal in col.deals"
                :key="deal.id"
                class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-2xs hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-700/80 transition-all space-y-3 group"
              >
                <!-- Card Header: Title & Dropdown Actions -->
                <div class="space-y-1">
                  <div class="flex items-start justify-between gap-2">
                    <span class="text-xs font-black text-slate-900 dark:text-white leading-snug group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                      {{ deal.title }}
                    </span>
                    <button
                      @click="openEditDealModal(deal)"
                      class="text-slate-300 hover:text-slate-600 dark:hover:text-slate-200 p-0.5"
                      title="Edit Deal"
                    >
                      <Edit3 class="w-3.5 h-3.5" />
                    </button>
                  </div>

                  <!-- Client Link -->
                  <Link
                    :href="`/clients/${deal.client.id}`"
                    class="text-[11px] font-bold text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1 transition"
                  >
                    <Building2 class="w-3 h-3" />
                    <span>{{ deal.client.name }}</span>
                  </Link>
                </div>

                <!-- Deal Value & Weighted -->
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                  <div>
                    <div class="text-[9px] font-bold text-slate-400 uppercase">Nilai Kontrak</div>
                    <div class="text-xs font-black text-slate-900 dark:text-white">{{ deal.expected_value_formatted }}</div>
                  </div>
                  <div class="text-right">
                    <div class="text-[9px] font-bold text-slate-400 uppercase">Probabilitas</div>
                    <div class="text-xs font-extrabold text-indigo-600 dark:text-indigo-400">{{ deal.probability }}%</div>
                  </div>
                </div>

                <!-- PIC WhatsApp Chat Quick Action -->
                <div v-if="deal.client.primary_contact?.whatsapp_url" class="flex items-center justify-between gap-2 pt-1 text-[11px]">
                  <div class="truncate text-[10px] text-slate-400">
                    PIC: {{ deal.client.primary_contact.name }}
                  </div>
                  <a
                    :href="deal.client.primary_contact.whatsapp_url"
                    target="_blank"
                    rel="noopener"
                    class="px-2 py-0.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-500 hover:text-white text-[10px] font-bold flex items-center gap-1 transition"
                  >
                    <MessageSquare class="w-3 h-3" />
                    <span>WA</span>
                  </a>
                </div>

                <!-- Target Close Date & Quotation Status -->
                <div class="flex items-center justify-between text-[10px] text-slate-400 pt-1 border-t border-slate-100 dark:border-slate-800">
                  <div class="flex items-center gap-1">
                    <Calendar class="w-3 h-3" />
                    <span>{{ deal.expected_close_date_formatted || 'No deadline' }}</span>
                  </div>
                  <span v-if="deal.quotations_count" class="font-bold text-indigo-500">
                    {{ deal.quotations_count }} Penawaran
                  </span>
                  <span v-else class="text-slate-300 dark:text-slate-600">Belum ada Quo</span>
                </div>

                <!-- Action: Create DevCalc Quotation directly -->
                <div class="pt-2 flex items-center gap-1.5">
                  <Link
                    :href="`/projects/create?client_id=${deal.client.id}&deal_id=${deal.id}`"
                    class="flex-1 py-1.5 px-2 rounded-xl bg-indigo-50 dark:bg-indigo-950/70 hover:bg-indigo-600 hover:text-white text-indigo-600 dark:text-indigo-400 font-extrabold text-[10px] transition text-center flex items-center justify-center gap-1"
                  >
                    <FileText class="w-3 h-3" />
                    <span>+ Penawaran DevCalc</span>
                  </Link>
                </div>

                <!-- Move Stage Dropdown -->
                <div class="pt-2 flex items-center justify-between gap-1 border-t border-slate-100 dark:border-slate-800">
                  <span class="text-[10px] font-bold text-slate-400">Pindah Stage:</span>
                  <select
                    :value="deal.stage"
                    @change="moveStage(deal, $event.target.value)"
                    class="px-2 py-1 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-[10px] font-bold text-slate-700 dark:text-slate-300 focus:outline-hidden"
                  >
                    <option v-for="(sMeta, sKey) in stagesConfig" :key="sKey" :value="sKey">
                      {{ sMeta.label }}
                    </option>
                  </select>
                </div>

                <!-- Lost Reason banner if stage is lost -->
                <div v-if="deal.stage === 'lost' && deal.lost_reason" class="p-2 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800/80 text-[10px] text-rose-700 dark:text-rose-300">
                  <span class="font-bold">Alasan Lost:</span> {{ deal.lost_reason }}
                </div>

              </div>

              <div v-if="!col.deals.length" class="py-12 text-center text-xs text-slate-400 italic">
                Kosong
              </div>

            </div>

          </div>

        </div>
      </div>

    </div>

    <!-- MODAL CREATE DEAL -->
    <div
      v-if="isAddDealModalOpen"
      class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4"
      @click.self="isAddDealModalOpen = false"
    >
      <div class="w-full max-w-md bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl p-6 space-y-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <Kanban class="w-4 h-4 text-purple-600" />
            <h3 class="text-sm font-black text-slate-900 dark:text-white">Tambah Peluang Deal Baru</h3>
          </div>
          <button @click="isAddDealModalOpen = false" class="text-slate-400 hover:text-slate-600">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitAddDeal" class="space-y-3">
          <div>
            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Perusahaan Klien *</label>
            <select
              v-model="dealForm.client_id"
              required
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200"
            >
              <option v-for="c in clients" :key="c.id" :value="c.id">
                {{ c.name }} ({{ c.industry || 'Klien' }})
              </option>
            </select>
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Proyek / Deal *</label>
            <input
              v-model="dealForm.title"
              type="text"
              required
              placeholder="misal: Core Banking Integration & Mobile App"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Stage Awal</label>
              <select
                v-model="dealForm.stage"
                @change="onStageChangeInForm"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300"
              >
                <option v-for="(sMeta, sKey) in stagesConfig" :key="sKey" :value="sKey">
                  {{ sMeta.label }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Estimasi Nilai (Rp)</label>
              <input
                v-model="dealForm.expected_value"
                type="number"
                min="0"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Probabilitas (%)</label>
              <input
                v-model="dealForm.probability"
                type="number"
                min="0"
                max="100"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
              />
            </div>

            <div>
              <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Target Closing</label>
              <input
                v-model="dealForm.expected_close_date"
                type="date"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
              />
            </div>
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Kebutuhan</label>
            <textarea
              v-model="dealForm.notes"
              rows="2"
              placeholder="Catatan brief / kebutuhan sistem..."
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
            ></textarea>
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
            <button
              type="button"
              @click="isAddDealModalOpen = false"
              class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl"
            >
              Batal
            </button>
            <button
              type="submit"
              class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-extrabold shadow-md hover:bg-indigo-700"
            >
              Simpan Deal
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL EDIT DEAL -->
    <div
      v-if="isEditDealModalOpen"
      class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4"
      @click.self="isEditDealModalOpen = false"
    >
      <div class="w-full max-w-md bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl p-6 space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-black text-slate-900 dark:text-white">Edit Peluang Deal</h3>
          <button @click="isEditDealModalOpen = false" class="text-slate-400 hover:text-slate-600">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitEditDeal" class="space-y-3">
          <div>
            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Deal *</label>
            <input
              v-model="editDealForm.title"
              type="text"
              required
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Stage</label>
              <select
                v-model="editDealForm.stage"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300"
              >
                <option v-for="(sMeta, sKey) in stagesConfig" :key="sKey" :value="sKey">
                  {{ sMeta.label }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Estimasi Nilai (Rp)</label>
              <input
                v-model="editDealForm.expected_value"
                type="number"
                min="0"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Probabilitas (%)</label>
              <input
                v-model="editDealForm.probability"
                type="number"
                min="0"
                max="100"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
              />
            </div>

            <div>
              <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Target Closing</label>
              <input
                v-model="editDealForm.expected_close_date"
                type="date"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
              />
            </div>
          </div>

          <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
            <button
              type="button"
              @click="deleteDeal(editDealForm.id)"
              class="text-rose-600 hover:text-rose-700 text-xs font-bold flex items-center gap-1"
            >
              <Trash2 class="w-3.5 h-3.5" />
              <span>Hapus</span>
            </button>

            <div class="flex items-center gap-2">
              <button
                type="button"
                @click="isEditDealModalOpen = false"
                class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl"
              >
                Batal
              </button>
              <button
                type="submit"
                class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-extrabold shadow-md hover:bg-indigo-700"
              >
                Perbarui
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL MARK AS LOST -->
    <div
      v-if="isLostModalOpen"
      class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4"
      @click.self="isLostModalOpen = false"
    >
      <div class="w-full max-w-md bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl p-6 space-y-4">
        <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto">
          <XCircle class="w-6 h-6" />
        </div>

        <div class="text-center space-y-1">
          <h3 class="text-base font-black text-slate-900 dark:text-white">Tandai Deal Sebagai Lost</h3>
          <p class="text-xs text-slate-400">
            Catat alasan kenapa peluang proyek ini tidak closing untuk evaluasi pipeline.
          </p>
        </div>

        <form @submit.prevent="submitMarkLost" class="space-y-3">
          <div>
            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Alasan Utama Lost</label>
            <select
              v-model="lostForm.lost_reason"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300"
            >
              <option value="Budget Klien Tidak Mencukupi">Budget Klien Tidak Mencukupi</option>
              <option value="Klien Memilih Vendor Lain">Klien Memilih Vendor Lain</option>
              <option value="Klien Memutuskan In-house Development">Klien Memutuskan In-house Development</option>
              <option value="Proyek Dibatalkan / Ditunda oleh Manajemen Klien">Proyek Dibatalkan / Ditunda oleh Manajemen</option>
              <option value="Timeline Tidak Sesuai Kebutuhan Klien">Timeline Tidak Sesuai Kebutuhan Klien</option>
              <option value="Lainnya">Lainnya...</option>
            </select>
          </div>

          <div v-if="lostForm.lost_reason === 'Lainnya'">
            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Rincian Alasan</label>
            <input
              v-model="lostForm.custom_reason"
              type="text"
              placeholder="Jelaskan alasan..."
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
            />
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
            <button
              type="button"
              @click="isLostModalOpen = false"
              class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl"
            >
              Batal
            </button>
            <button
              type="submit"
              class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-md"
            >
              Konfirmasi Lost
            </button>
          </div>
        </form>
      </div>
    </div>

  </AppLayout>
</template>
