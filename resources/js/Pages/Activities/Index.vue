<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
  Activity, 
  Search, 
  Filter, 
  Clock, 
  FileText, 
  Kanban, 
  Building2, 
  Layers, 
  MessageSquare, 
  Calendar, 
  Phone, 
  Mail, 
  CheckCircle2, 
  AlertCircle, 
  TrendingUp, 
  Sparkles,
  ArrowRight,
  User,
  ChevronLeft,
  ChevronRight,
  ShieldCheck,
  FilePlus,
  Trash2
} from 'lucide-vue-next';

const props = defineProps({
  activities: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({ search: '', category: 'all', user_id: '' }),
  },
  stats: {
    type: Object,
    default: () => ({
      total_today: 0,
      total_week: 0,
      total_projects: 0,
      total_deals: 0,
      total_all: 0,
    }),
  },
  users: {
    type: Array,
    default: () => [],
  },
});

const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || 'all');
const selectedUserId = ref(props.filters.user_id || '');

const categories = [
  { key: 'all', label: 'Semua Log', icon: Activity },
  { key: 'projects', label: 'Penawaran CPQ', icon: FileText },
  { key: 'deals', label: 'Kanban & Deals', icon: Kanban },
  { key: 'clients', label: 'Klien & Kontak', icon: Building2 },
  { key: 'modules', label: 'Katalog Modul', icon: Layers },
  { key: 'notes', label: 'Catatan Interaksi', icon: MessageSquare },
];

function applyFilters() {
  router.get('/activities', {
    search: searchQuery.value || undefined,
    category: selectedCategory.value !== 'all' ? selectedCategory.value : undefined,
    user_id: selectedUserId.value || undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
}

function setCategory(catKey) {
  selectedCategory.value = catKey;
  applyFilters();
}

function handleSearchKeyup() {
  applyFilters();
}

function resetFilters() {
  searchQuery.value = '';
  selectedCategory.value = 'all';
  selectedUserId.value = '';
  router.get('/activities');
}

// Activity Type Styling Helper
function getActivityMeta(type) {
  switch (type) {
    case 'project_created':
      return {
        bg: 'bg-emerald-50 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800/80',
        badgeBg: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300',
        label: 'CPQ Baru',
        icon: FilePlus,
      };
    case 'project_updated':
      return {
        bg: 'bg-indigo-50 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800/80',
        badgeBg: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300',
        label: 'CPQ Update',
        icon: FileText,
      };
    case 'project_deleted':
      return {
        bg: 'bg-rose-50 dark:bg-rose-950/70 text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-800/80',
        badgeBg: 'bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-300',
        label: 'CPQ Dihapus',
        icon: Trash2,
      };
    case 'addendum_created':
      return {
        bg: 'bg-amber-50 dark:bg-amber-950/70 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800/80',
        badgeBg: 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300',
        label: 'Adendum',
        icon: ShieldCheck,
      };
    case 'stage_change':
      return {
        bg: 'bg-purple-50 dark:bg-purple-950/70 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800/80',
        badgeBg: 'bg-purple-100 text-purple-800 dark:bg-purple-900/60 dark:text-purple-300',
        label: 'Stage Kanban',
        icon: Kanban,
      };
    case 'deal_updated':
      return {
        bg: 'bg-purple-50 dark:bg-purple-950/70 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800/80',
        badgeBg: 'bg-purple-100 text-purple-800 dark:bg-purple-900/60 dark:text-purple-300',
        label: 'Deal Update',
        icon: Kanban,
      };
    case 'client_created':
    case 'client_updated':
    case 'client_deleted':
      return {
        bg: 'bg-sky-50 dark:bg-sky-950/70 text-sky-600 dark:text-sky-400 border-sky-200 dark:border-sky-800/80',
        badgeBg: 'bg-sky-100 text-sky-800 dark:bg-sky-900/60 dark:text-sky-300',
        label: 'Klien B2B',
        icon: Building2,
      };
    case 'contact_created':
    case 'contact_updated':
    case 'contact_deleted':
      return {
        bg: 'bg-cyan-50 dark:bg-cyan-950/70 text-cyan-600 dark:text-cyan-400 border-cyan-200 dark:border-cyan-800/80',
        badgeBg: 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/60 dark:text-cyan-300',
        label: 'PIC Klien',
        icon: User,
      };
    case 'module_created':
    case 'module_updated':
    case 'module_deleted':
      return {
        bg: 'bg-amber-50 dark:bg-amber-950/70 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800/80',
        badgeBg: 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300',
        label: 'Modul Katalog',
        icon: Layers,
      };
    case 'meeting':
      return {
        bg: 'bg-blue-50 dark:bg-blue-950/70 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800/80',
        badgeBg: 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300',
        label: 'Meeting',
        icon: Calendar,
      };
    case 'call':
      return {
        bg: 'bg-emerald-50 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800/80',
        badgeBg: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300',
        label: 'Telepon',
        icon: Phone,
      };
    case 'whatsapp':
      return {
        bg: 'bg-teal-50 dark:bg-teal-950/70 text-teal-600 dark:text-teal-400 border-teal-200 dark:border-teal-800/80',
        badgeBg: 'bg-teal-100 text-teal-800 dark:bg-teal-900/60 dark:text-teal-300',
        label: 'WhatsApp',
        icon: MessageSquare,
      };
    default:
      return {
        bg: 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700',
        badgeBg: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
        label: 'Catatan',
        icon: Activity,
      };
  }
}
</script>

<template>
  <Head title="Log Aktivitas & Audit Trail Sistem" />

  <AppLayout title="Riwayat Log Aktivitas & Audit Trail Operasional">
    <div class="max-w-7xl mx-auto space-y-6">
      
      <!-- HEADER -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Log Aktivitas & Audit Trail</h1>
            <span class="px-2 py-0.5 text-xs font-extrabold bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 rounded-lg border border-indigo-200 dark:border-indigo-800">Live Feed</span>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Rekam jejak transparan seluruh aksi pembuatan, pembaruan, penghapusan, dan perpindahan stage sistem.
          </p>
        </div>

        <div class="flex items-center gap-2.5">
          <Link
            href="/dashboard"
            class="px-4 py-2 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition"
          >
            &larr; Ke Dasbor Utama
          </Link>
        </div>
      </div>

      <!-- METRIC CARDS -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs space-y-1.5">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-[11px] font-extrabold uppercase tracking-wider">Aktivitas Hari Ini</span>
            <Clock class="w-4 h-4 text-indigo-500" />
          </div>
          <div class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.total_today }}</div>
          <div class="text-[11px] text-slate-400 font-medium">Aksi diproses hari ini</div>
        </div>

        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs space-y-1.5">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-[11px] font-extrabold uppercase tracking-wider">Aktivitas Minggu Ini</span>
            <TrendingUp class="w-4 h-4 text-sky-500" />
          </div>
          <div class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.total_week }}</div>
          <div class="text-[11px] text-slate-400 font-medium">Dalam 7 hari terakhir</div>
        </div>

        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs space-y-1.5">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-[11px] font-extrabold uppercase tracking-wider">Aksi Penawaran CPQ</span>
            <FileText class="w-4 h-4 text-emerald-500" />
          </div>
          <div class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.total_projects }}</div>
          <div class="text-[11px] text-slate-400 font-medium">Buat, edit, adendum & hapus</div>
        </div>

        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs space-y-1.5">
          <div class="flex items-center justify-between text-slate-400">
            <span class="text-[11px] font-extrabold uppercase tracking-wider">Aksi Deals & Kanban</span>
            <Kanban class="w-4 h-4 text-purple-500" />
          </div>
          <div class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.total_deals }}</div>
          <div class="text-[11px] text-slate-400 font-medium">Perpindahan pipeline stage</div>
        </div>
      </div>

      <!-- FILTER CARD -->
      <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs space-y-4">
        
        <!-- Search & User Filter Row -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div class="relative flex-1">
            <Search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
            <input
              v-model="searchQuery"
              @keyup.enter="handleSearchKeyup"
              type="text"
              placeholder="Cari judul aktivitas, deskripsi aksi, nama klien, atau pengguna..."
              class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div class="flex items-center gap-2">
            <select
              v-model="selectedUserId"
              @change="applyFilters"
              class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-bold text-slate-700 dark:text-slate-300 focus:outline-hidden cursor-pointer"
            >
              <option value="">Semua Anggota Tim</option>
              <option v-for="u in users" :key="u.id" :value="u.id">
                {{ u.name }}
              </option>
            </select>

            <button
              v-if="searchQuery || selectedCategory !== 'all' || selectedUserId"
              @click="resetFilters"
              class="px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-2xl transition cursor-pointer"
            >
              Reset Filter
            </button>
          </div>
        </div>

        <!-- Category Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 custom-scrollbar">
          <button
            v-for="cat in categories"
            :key="cat.key"
            @click="setCategory(cat.key)"
            class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0 cursor-pointer"
            :class="selectedCategory === cat.key
              ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30'
              : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'"
          >
            <component :is="cat.icon" class="w-3.5 h-3.5" />
            <span>{{ cat.label }}</span>
          </button>
        </div>

      </div>

      <!-- TIMELINE LIST -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 rounded-3xl p-6 shadow-xs space-y-4">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
          <div class="text-xs font-black uppercase tracking-wider text-slate-400">
            Daftar Riwayat Aksi ({{ activities.total }} Catatan)
          </div>
          <div class="text-xs text-slate-400">
            Halaman {{ activities.current_page }} dari {{ activities.last_page }}
          </div>
        </div>

        <!-- Activities Feed -->
        <div v-if="activities.data.length" class="space-y-3 pt-1">
          <div
            v-for="act in activities.data"
            :key="act.id"
            class="p-4.5 rounded-2xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-200/70 dark:border-slate-800/80 flex flex-col sm:flex-row sm:items-start justify-between gap-4 transition hover:border-slate-300 dark:hover:border-slate-700"
          >
            <div class="flex items-start gap-3.5">
              <!-- Icon Box -->
              <div
                class="w-10 h-10 rounded-2xl border flex items-center justify-center shrink-0 shadow-2xs"
                :class="getActivityMeta(act.type).bg"
              >
                <component :is="getActivityMeta(act.type).icon" class="w-5 h-5 stroke-[2.2]" />
              </div>

              <!-- Content Area -->
              <div class="space-y-1">
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="text-xs font-black text-slate-900 dark:text-white">
                    {{ act.title }}
                  </span>
                  <span
                    class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wider"
                    :class="getActivityMeta(act.type).badgeBg"
                  >
                    {{ getActivityMeta(act.type).label }}
                  </span>
                </div>

                <p v-if="act.description" class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl">
                  {{ act.description }}
                </p>

                <!-- Tags / Linked entities -->
                <div class="flex items-center gap-3 pt-1 text-[11px] text-slate-400 flex-wrap">
                  <span class="flex items-center gap-1 font-semibold text-slate-700 dark:text-slate-300">
                    <User class="w-3 h-3 text-slate-400" />
                    <span>{{ act.user_name }}</span>
                  </span>

                  <span v-if="act.client_name" class="flex items-center gap-1 font-semibold text-indigo-600 dark:text-indigo-400">
                    <Building2 class="w-3 h-3" />
                    <Link v-if="act.client_id" :href="`/clients/${act.client_id}`" class="hover:underline">
                      {{ act.client_name }}
                    </Link>
                    <span v-else>{{ act.client_name }}</span>
                  </span>

                  <span v-if="act.deal_title" class="flex items-center gap-1 font-semibold text-purple-600 dark:text-purple-400">
                    <Kanban class="w-3 h-3" />
                    <Link href="/deals" class="hover:underline">
                      {{ act.deal_title }}
                    </Link>
                  </span>
                </div>
              </div>
            </div>

            <!-- Time Column -->
            <div class="text-left sm:text-right shrink-0 space-y-0.5 pt-1 sm:pt-0">
              <div class="text-xs font-extrabold text-slate-700 dark:text-slate-300">
                {{ act.time_ago }}
              </div>
              <div class="text-[10px] text-slate-400 font-mono">
                {{ act.performed_at_formatted }}
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="py-16 text-center space-y-3">
          <Activity class="w-12 h-12 text-slate-300 dark:text-slate-700 mx-auto" />
          <h3 class="text-sm font-black text-slate-900 dark:text-white">Tidak ada catatan aktivitas</h3>
          <p class="text-xs text-slate-400 max-w-sm mx-auto">
            Belum ada log yang sesuai dengan kriteria filter saat ini.
          </p>
        </div>

        <!-- Pagination -->
        <div v-if="activities.total > activities.per_page" class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800">
          <Link
            v-if="activities.prev_page_url"
            :href="activities.prev_page_url"
            class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-200 flex items-center gap-1"
          >
            <ChevronLeft class="w-4 h-4" />
            <span>Sebelumnya</span>
          </Link>
          <div v-else></div>

          <div class="text-xs text-slate-400 font-semibold">
            Halaman {{ activities.current_page }} dari {{ activities.last_page }}
          </div>

          <Link
            v-if="activities.next_page_url"
            :href="activities.next_page_url"
            class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-200 flex items-center gap-1"
          >
            <span>Selanjutnya</span>
            <ChevronRight class="w-4 h-4" />
          </Link>
          <div v-else></div>
        </div>

      </div>

    </div>
  </AppLayout>
</template>
