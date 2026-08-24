<script setup>
import { ref, computed, watch } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CurrencyInput from '@/Components/CurrencyInput.vue';
import Badge from '@/Components/Badge.vue';
import Modal from '@/Components/Modal.vue';
import ConfirmDeleteModal from '@/Components/ConfirmDeleteModal.vue';
import { 
  Plus, 
  Search, 
  Edit3, 
  Trash2, 
  Layers,
  Tag,
  X,
  RotateCcw,
  SlidersHorizontal,
  CheckCircle2,
  AlertCircle
} from 'lucide-vue-next';

const props = defineProps({
  modules: Object,
  categories: Array,
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
const activeCategory = ref(props.filters?.category || '');

// Modal State
const modalOpen = ref(false);
const editingModule = ref(null);
const detailModalOpen = ref(false);
const selectedModule = ref(null);

function openDetailModal(module) {
  selectedModule.value = module;
  detailModalOpen.value = true;
}

const form = useForm({
  name: '',
  category: 'Umum',
  description: '',
  base_price: 1000000,
  subscription_price: 0,
});

function applySearch() {
  router.get('/modules', {
    search: search.value,
    category: activeCategory.value,
  }, { preserveState: true, replace: true });
}

function clearSearch() {
  search.value = '';
  applySearch();
}

function selectCategory(cat) {
  activeCategory.value = cat;
  applySearch();
}

function resetAllFilters() {
  search.value = '';
  activeCategory.value = '';
  applySearch();
}

const hasActiveFilters = computed(() => {
  return !!search.value || !!activeCategory.value;
});

function openCreateModal() {
  editingModule.value = null;
  form.reset();
  form.clearErrors();
  modalOpen.value = true;
}

function openEditModal(module) {
  editingModule.value = module;
  form.name = module.name;
  form.category = module.category;
  form.description = module.description || '';
  form.base_price = module.base_price;
  form.subscription_price = module.subscription_price;
  form.clearErrors();
  modalOpen.value = true;
}

function saveModule() {
  if (editingModule.value) {
    form.put(`/modules/${editingModule.value.id}`, {
      onSuccess: (page) => {
        modalOpen.value = false;
        flashSuccess.value = page.props.flash?.success || 'Modul berhasil diperbarui!';
      }
    });
  } else {
    form.post('/modules', {
      onSuccess: (page) => {
        modalOpen.value = false;
        flashSuccess.value = page.props.flash?.success || 'Modul baru berhasil ditambahkan!';
      }
    });
  }
}

const deleteModalOpen = ref(false);
const targetModule = ref(null);
const isDeleting = ref(false);

function promptDeleteModule(id, name) {
  targetModule.value = { id, name };
  deleteModalOpen.value = true;
}

function confirmDeleteModule() {
  if (!targetModule.value) return;
  const moduleName = targetModule.value.name;
  isDeleting.value = true;
  router.delete(`/modules/${targetModule.value.id}`, {
    onSuccess: (page) => {
      deleteModalOpen.value = false;
      targetModule.value = null;
      flashSuccess.value = page.props.flash?.success || `Modul "${moduleName}" berhasil dihapus dari katalog.`;
    },
    onFinish: () => {
      isDeleting.value = false;
    }
  });
}
</script>

<template>
  <Head title="Katalog Modul Master" />

  <AppLayout title="Katalog Modul Fitur Master Data">
    <div class="space-y-6 max-w-7xl mx-auto">
      
      <!-- Top Header (Simple Style matching sidebar) -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/80 border border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shadow-sm shrink-0">
              <Layers class="w-5 h-5" />
            </div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
              Katalog Modul Master
            </h2>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400">
            Kelola katalog modul standar software dan tarif lisensi acuan.
          </p>
        </div>

        <button
          @click="openCreateModal"
          class="px-4.5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/30 transition flex items-center justify-center gap-2 self-start sm:self-auto cursor-pointer shrink-0"
        >
          <Plus class="w-4 h-4" />
          <span>Tambah Modul Baru</span>
        </button>
      </div>

      <!-- Interactive Filters & Control Panel Card (Matching Penawaran Harga Theme) -->
      <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-4">
        <!-- Top Row: Search Input & Reset Button -->
        <div class="flex flex-col md:flex-row items-center gap-3">
          <!-- Search Input with Clear Button -->
          <div class="relative flex-1 w-full">
            <Search class="w-4 h-4 absolute left-3.5 top-3 text-slate-400" />
            <input
              v-model="search"
              @input="applySearch"
              type="text"
              placeholder="Cari nama modul, kategori domain, atau deskripsi..."
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

          <!-- Reset Filter Button -->
          <button
            v-if="hasActiveFilters"
            @click="resetAllFilters"
            title="Reset Semua Filter"
            class="px-3.5 py-2.5 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 font-bold text-xs rounded-xl transition flex items-center gap-1.5 cursor-pointer shrink-0"
          >
            <RotateCcw class="w-3.5 h-3.5" />
            <span>Reset</span>
          </button>
        </div>

        <!-- Bottom Row: Interactive Category Segmented Pills -->
        <div class="flex items-center gap-2 flex-wrap pt-3 border-t border-slate-100 dark:border-slate-800/80 text-xs">
          <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1 flex items-center gap-1">
            <SlidersHorizontal class="w-3 h-3 text-indigo-500" />
            <span>Kategori Domain:</span>
          </span>

          <button
            type="button"
            @click="selectCategory('')"
            :class="activeCategory === '' 
              ? 'bg-indigo-600 text-white font-bold shadow-sm shadow-indigo-600/30' 
              : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 font-semibold'"
            class="px-3 py-1.5 rounded-xl transition cursor-pointer"
          >
            Semua Modul
          </button>

          <button
            v-for="cat in categories"
            :key="cat"
            type="button"
            @click="selectCategory(cat)"
            :class="activeCategory === cat 
              ? 'bg-indigo-600 text-white font-bold shadow-sm shadow-indigo-600/30' 
              : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 font-semibold'"
            class="px-3 py-1.5 rounded-xl transition cursor-pointer"
          >
            {{ cat }}
          </button>
        </div>
      </div>

      <!-- Simple Notification Alert (Below Filter Card) -->
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

      <!-- Modules Table -->
      <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold uppercase text-slate-400">
                <th class="py-3 px-4">Nama Modul</th>
                <th class="py-3 px-4">Kategori Domain</th>
                <th class="py-3 px-4">Harga Beli Putus</th>
                <th class="py-3 px-4">Langganan / Bulan</th>
                <th class="py-3 px-4 text-right">Menu Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs font-semibold">
              <tr 
                v-for="item in modules.data" 
                :key="item.id" 
                @click="openDetailModal(item)"
                class="hover:bg-indigo-50/50 dark:hover:bg-slate-800/60 transition cursor-pointer group"
              >
                <td class="py-3.5 px-4">
                  <div class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                      <Layers class="w-4 h-4" />
                    </div>
                    <span class="group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">{{ item.name }}</span>
                  </div>
                </td>

                <td class="py-3.5 px-4">
                  <Badge variant="sky">
                    <Tag class="w-3 h-3" />
                    <span>{{ item.category }}</span>
                  </Badge>
                </td>

                <td class="py-3.5 px-4 font-extrabold text-slate-900 dark:text-white">
                  {{ item.base_price_formatted }}
                </td>

                <td class="py-3.5 px-4 font-bold text-emerald-600 dark:text-emerald-400">
                  {{ item.subscription_price_formatted }}
                </td>

                <td class="py-3.5 px-4 text-right">
                  <div class="inline-flex items-center justify-end gap-1.5" @click.stop>
                    <!-- Ubah Modul Button -->
                    <div class="relative group/btn">
                      <button
                        type="button"
                        @click="openEditModal(item)"
                        class="w-8 h-8 rounded-xl bg-slate-100/80 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-500 hover:border-indigo-500 transition-all duration-200 flex items-center justify-center border border-slate-200/80 dark:border-slate-700/80 shadow-xs active:scale-95 cursor-pointer"
                      >
                        <Edit3 class="w-4 h-4" />
                      </button>
                      <div class="opacity-0 group-hover/btn:opacity-100 transition-all duration-150 pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-0.5 text-[10px] font-bold rounded-md bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 shadow-xl whitespace-nowrap z-50 border border-slate-800 dark:border-slate-200">
                        Ubah Modul
                      </div>
                    </div>

                    <!-- Hapus Modul Button -->
                    <div class="relative group/btn">
                      <button
                        type="button"
                        @click="promptDeleteModule(item.id, item.name)"
                        class="w-8 h-8 rounded-xl bg-slate-100/80 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 hover:bg-rose-600 hover:text-white dark:hover:bg-rose-500 hover:border-rose-500 transition-all duration-200 flex items-center justify-center border border-slate-200/80 dark:border-slate-700/80 shadow-xs active:scale-95 cursor-pointer"
                      >
                        <Trash2 class="w-4 h-4" />
                      </button>
                      <div class="opacity-0 group-hover/btn:opacity-100 transition-all duration-150 pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-0.5 text-[10px] font-bold rounded-md bg-rose-900 text-rose-100 shadow-xl whitespace-nowrap z-50 border border-rose-800">
                        Hapus
                      </div>
                    </div>
                  </div>
                </td>
              </tr>

              <tr v-if="!modules.data.length">
                <td colspan="5" class="py-12 text-center text-slate-400">
                  Belum ada data modul dalam katalog.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- View Detail Modal -->
    <Modal :show="detailModalOpen" max-width="lg" @close="detailModalOpen = false">
      <div class="p-6 space-y-5">
        
        <!-- Modal Header -->
        <div class="flex items-start justify-between gap-3 border-b border-slate-100 dark:border-slate-800/80 pb-4">
          <div class="space-y-1.5">
            <div class="flex items-center gap-2">
              <Badge variant="sky">
                <Tag class="w-3 h-3" />
                <span>{{ selectedModule?.category || 'Umum' }}</span>
              </Badge>
              <span class="text-[10px] font-mono text-slate-400">ID: #MOD-{{ selectedModule?.id }}</span>
            </div>
            <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white leading-snug">
              {{ selectedModule?.name }}
            </h3>
          </div>

          <button 
            type="button"
            @click="detailModalOpen = false" 
            class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer shrink-0"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Pricing Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <!-- One-off Price -->
          <div class="p-4 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-800/60 space-y-1">
            <div class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">
              Harga Beli Putus (One-Off)
            </div>
            <div class="text-base font-black text-slate-900 dark:text-white">
              {{ selectedModule?.base_price_formatted }}
            </div>
            <div class="text-[10px] text-slate-500 dark:text-slate-400">
              Tarif dasar acuan sebelum bobot
            </div>
          </div>

          <!-- Subscription Price -->
          <div class="p-4 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-800/60 space-y-1">
            <div class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
              Skema Langganan / Bulan
            </div>
            <div class="text-base font-black text-emerald-600 dark:text-emerald-400">
              {{ selectedModule?.subscription_price_formatted }}
            </div>
            <div class="text-[10px] text-slate-500 dark:text-slate-400">
              {{ selectedModule?.subscription_price > 0 ? 'Tarif tetap per bulan' : 'Otomatis 8% dari total one-off' }}
            </div>
          </div>
        </div>

        <!-- Description & Scope Section -->
        <div class="space-y-2">
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[11px]">
            Deskripsi & Spesifikasi Fitur
          </label>
          <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 text-xs text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">
            <p v-if="selectedModule?.description">
              {{ selectedModule.description }}
            </p>
            <p v-else class="text-slate-400 italic text-[11px]">
              Belum ada rincian deskripsi spesifik untuk modul ini.
            </p>
          </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="flex items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-slate-800/80">
          <div class="text-[11px] text-slate-400">
            Dibuat: {{ selectedModule?.created_at_formatted || '-' }}
          </div>

          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="detailModalOpen = false"
              class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer"
            >
              Tutup
            </button>

            <button
              type="button"
              @click="detailModalOpen = false; openEditModal(selectedModule)"
              class="px-4 py-2 text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-md shadow-indigo-600/30 transition flex items-center gap-1.5 cursor-pointer"
            >
              <Edit3 class="w-3.5 h-3.5" />
              <span>Ubah Modul</span>
            </button>
          </div>
        </div>

      </div>
    </Modal>

    <!-- Create / Edit Modal -->
    <Modal :show="modalOpen" max-width="md" @close="modalOpen = false">
      <div class="p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
          <h3 class="text-base font-bold text-slate-900 dark:text-white">
            {{ editingModule ? 'Ubah Modul Katalog' : 'Tambah Modul Katalog Baru' }}
          </h3>
          <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="saveModule" class="space-y-4">
          <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Modul *</label>
            <input
              v-model="form.name"
              type="text"
              required
              placeholder="e.g. Authentication & RBAC"
              class="w-full px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl"
            />
          </div>

          <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Kategori / Domain</label>
            <input
              v-model="form.category"
              type="text"
              placeholder="e.g. Core Security, Fintech, Backend & API"
              class="w-full px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl"
            />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <CurrencyInput v-model="form.base_price" label="Harga Beli Putus (One-Off)" />
            <CurrencyInput v-model="form.subscription_price" label="Harga Langganan / Bulan" helperText="Isi 0 jika mengikuti 8%" />
          </div>

          <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Deskripsi (Opsional)</label>
            <textarea
              v-model="form.description"
              rows="3"
              placeholder="Cakupan fitur modul standar..."
              class="w-full px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl"
            ></textarea>
          </div>

          <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-200 dark:border-slate-800">
            <button type="button" @click="modalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
              Batal
            </button>
            <button type="submit" :disabled="form.processing" class="px-4 py-2 text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-md transition disabled:opacity-50 cursor-pointer">
              Simpan Modul
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Confirm Delete Modal -->
    <ConfirmDeleteModal
      :show="deleteModalOpen"
      title="Hapus Modul Katalog"
      message="Apakah Anda yakin ingin menghapus modul ini dari katalog master data?"
      :item-name="targetModule?.name"
      :processing="isDeleting"
      @close="deleteModalOpen = false"
      @confirm="confirmDeleteModule"
    />
  </AppLayout>
</template>
