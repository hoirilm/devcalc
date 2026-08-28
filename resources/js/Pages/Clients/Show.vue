<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
  Building2, 
  ArrowLeft, 
  Plus, 
  Phone, 
  Mail, 
  Globe, 
  MapPin, 
  MessageSquare, 
  FileText, 
  Kanban, 
  Clock, 
  CheckCircle2, 
  Trash2, 
  Edit3, 
  DollarSign, 
  Download, 
  ExternalLink,
  Users,
  Calendar,
  Send,
  X,
  Target,
  ChevronRight,
  ShieldCheck
} from 'lucide-vue-next';

const props = defineProps({
  client: {
    type: Object,
    required: true
  },
  contacts: {
    type: Array,
    default: () => []
  },
  deals: {
    type: Array,
    default: () => []
  },
  projects: {
    type: Array,
    default: () => []
  },
  activities: {
    type: Array,
    default: () => []
  },
  summary: {
    type: Object,
    default: () => ({ ltv_formatted: 'Rp 0', deals_count: 0, active_deals_count: 0, quotations_count: 0 })
  }
});

const isAddContactModalOpen = ref(false);
const isAddDealModalOpen = ref(false);
const isEditClientModalOpen = ref(false);

const contactForm = ref({
  name: '',
  title: '',
  email: '',
  phone: '',
  is_primary: false,
  notes: '',
});

const dealForm = ref({
  title: '',
  stage: 'discovery',
  expected_value: 0,
  probability: 20,
  expected_close_date: '',
  notes: '',
});

const activityForm = ref({
  type: 'note',
  title: '',
  description: '',
  client_id: props.client.id,
});

const editClientForm = ref({
  name: props.client.name,
  industry: props.client.industry === 'Uncategorized' ? '' : props.client.industry,
  email: props.client.email === '-' ? '' : props.client.email,
  phone: props.client.phone === '-' ? '' : props.client.phone,
  website: props.client.website || '',
  address: props.client.address || '',
  status: props.client.status,
  notes: props.client.notes || '',
});

function submitAddContact() {
  router.post(`/clients/${props.client.id}/contacts`, contactForm.value, {
    onSuccess: () => {
      isAddContactModalOpen.value = false;
      contactForm.value = { name: '', title: '', email: '', phone: '', is_primary: false, notes: '' };
    }
  });
}

function deleteContact(contactId) {
  if (confirm('Hapus kontak PIC ini?')) {
    router.delete(`/contacts/${contactId}`);
  }
}

function submitAddDeal() {
  router.post('/deals', {
    ...dealForm.value,
    client_id: props.client.id,
  }, {
    onSuccess: () => {
      isAddDealModalOpen.value = false;
      dealForm.value = { title: '', stage: 'discovery', expected_value: 0, probability: 20, expected_close_date: '', notes: '' };
    }
  });
}

function submitActivity() {
  if (!activityForm.value.title.trim()) return;
  router.post('/activities', activityForm.value, {
    onSuccess: () => {
      activityForm.value.title = '';
      activityForm.value.description = '';
    }
  });
}

function submitEditClient() {
  router.put(`/clients/${props.client.id}`, editClientForm.value, {
    onSuccess: () => {
      isEditClientModalOpen.value = false;
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

function getActivityIconClass(type) {
  switch (type) {
    case 'meeting':
      return 'bg-purple-100 text-purple-600 dark:bg-purple-950 dark:text-purple-400';
    case 'call':
      return 'bg-blue-100 text-blue-600 dark:bg-blue-950 dark:text-blue-400';
    case 'whatsapp':
      return 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400';
    case 'email':
      return 'bg-amber-100 text-amber-600 dark:bg-amber-950 dark:text-amber-400';
    default:
      return 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400';
  }
}
</script>

<template>
  <AppLayout>
    <Head :title="`${client.name} - 360° Profile CRM`" />

    <div class="space-y-6 max-w-7xl mx-auto">
      
      <!-- BACK BUTTON & BREADCRUMB -->
      <div class="flex items-center justify-between">
        <Link
          href="/clients"
          class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition"
        >
          <ArrowLeft class="w-4 h-4" />
          <span>Kembali ke Direktori Klien</span>
        </Link>

        <div class="flex items-center gap-2">
          <button
            @click="isEditClientModalOpen = true"
            class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition flex items-center gap-1.5"
          >
            <Edit3 class="w-3.5 h-3.5" />
            <span>Edit Data</span>
          </button>

          <Link
            :href="`/projects/create?client_id=${client.id}`"
            class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-md shadow-indigo-600/30 transition flex items-center gap-1.5"
          >
            <FileText class="w-3.5 h-3.5 stroke-[2.5]" />
            <span>+ Buat Penawaran DevCalc</span>
          </Link>
        </div>
      </div>

      <!-- 360° HERO CARD -->
      <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs relative overflow-hidden">
        
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
          
          <!-- Company Info -->
          <div class="flex items-start gap-4">
            <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-indigo-600 to-purple-600 text-white font-black text-2xl flex items-center justify-center shadow-lg shadow-indigo-600/30 shrink-0">
              {{ client.name.charAt(0).toUpperCase() }}
            </div>
            
            <div class="space-y-1.5">
              <div class="flex items-center gap-2.5 flex-wrap">
                <h1 class="text-xl font-black text-slate-900 dark:text-white">{{ client.name }}</h1>
                <span 
                  class="px-2.5 py-0.5 text-xs font-extrabold rounded-xl border"
                  :class="getStatusBadge(client.status).class"
                >
                  {{ getStatusBadge(client.status).label }}
                </span>
              </div>

              <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400 flex-wrap">
                <span class="font-bold text-slate-700 dark:text-slate-300">{{ client.industry }}</span>
                <span v-if="client.email !== '-'" class="flex items-center gap-1">
                  <Mail class="w-3.5 h-3.5" />
                  {{ client.email }}
                </span>
                <span v-if="client.phone !== '-'" class="flex items-center gap-1">
                  <Phone class="w-3.5 h-3.5" />
                  {{ client.phone }}
                </span>
                <a
                  v-if="client.website"
                  :href="client.website"
                  target="_blank"
                  rel="noopener"
                  class="flex items-center gap-1 text-indigo-600 dark:text-indigo-400 hover:underline"
                >
                  <Globe class="w-3.5 h-3.5" />
                  <span>{{ client.website.replace('https://', '') }}</span>
                </a>
              </div>

              <p v-if="client.address" class="text-[11px] text-slate-400 flex items-center gap-1">
                <MapPin class="w-3.5 h-3.5 shrink-0" />
                <span>{{ client.address }}</span>
              </p>
            </div>
          </div>

          <!-- Quick Summary LTV & Deals -->
          <div class="flex items-center gap-4 border-t lg:border-t-0 lg:border-l border-slate-100 dark:border-slate-800 pt-4 lg:pt-0 lg:pl-6 shrink-0">
            <div>
              <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Lifetime Value (LTV)</div>
              <div class="text-xl font-black text-emerald-600 dark:text-emerald-400">{{ summary.ltv_formatted }}</div>
              <div class="text-[10px] text-slate-400">Dari penawaran resmi yang disetujui</div>
            </div>

            <div class="w-px h-10 bg-slate-200 dark:bg-slate-800"></div>

            <div>
              <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pipeline Deals</div>
              <div class="text-xl font-black text-indigo-600 dark:text-indigo-400">{{ summary.active_deals_count }} Aktif</div>
              <div class="text-[10px] text-slate-400">{{ summary.quotations_count }} Total Quotation</div>
            </div>
          </div>

        </div>

      </div>

      <!-- MAIN CONTENT 2 COLUMNS -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- LEFT COLUMN (2 SPANS): QUOTATIONS & DEALS -->
        <div class="lg:col-span-2 space-y-6">
          
          <!-- SECTION 1: DOKUMEN PENAWARAN DEVCALC -->
          <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs space-y-4">
            
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <FileText class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                <h2 class="text-sm font-black text-slate-900 dark:text-white">Dokumen Penawaran DevCalc ({{ projects.length }})</h2>
              </div>

              <Link
                :href="`/projects/create?client_id=${client.id}`"
                class="px-3 py-1 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 text-xs font-bold hover:bg-indigo-100 transition"
              >
                + Buat Penawaran
              </Link>
            </div>

            <!-- List Projects -->
            <div v-if="projects.length" class="space-y-2.5">
              <div
                v-for="p in projects"
                :key="p.id"
                class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/70 dark:border-slate-800 flex items-center justify-between gap-4 hover:border-indigo-200 dark:hover:border-indigo-800 transition"
              >
                <div class="space-y-1">
                  <div class="flex items-center gap-2">
                    <span class="font-mono text-xs font-black text-indigo-600 dark:text-indigo-400">#{{ p.code }}</span>
                    <span
                      class="px-2 py-0.5 text-[9px] font-extrabold rounded-md uppercase"
                      :class="p.status === 'Draft' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300'"
                    >
                      {{ p.status }}
                    </span>
                    <span class="text-[10px] text-slate-400 font-bold">
                      {{ p.billing_type === 'subscription' ? 'Langganan' : 'Beli Putus' }}
                    </span>
                  </div>
                  <div class="text-xs text-slate-500 dark:text-slate-400">
                    {{ p.items_count }} Modul/Fitur • Estimator: {{ p.estimator_name }} • {{ p.created_at_formatted }}
                  </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                  <span class="text-sm font-black text-slate-900 dark:text-white">{{ p.grand_total_formatted }}</span>
                  <a
                    :href="`/projects/${p.id}/pdf`"
                    target="_blank"
                    class="p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-indigo-600 transition"
                    title="Unduh PDF Resmi"
                  >
                    <Download class="w-4 h-4" />
                  </a>
                  <Link
                    :href="`/projects/${p.id}/edit`"
                    class="p-2 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-600 hover:text-white transition"
                    title="Edit Penawaran"
                  >
                    <ChevronRight class="w-4 h-4" />
                  </Link>
                </div>
              </div>
            </div>

            <div v-else class="py-6 text-center text-xs text-slate-400 italic">
              Belum ada dokumen penawaran harga DevCalc untuk klien ini.
            </div>

          </div>

          <!-- SECTION 2: PIPELINE DEALS -->
          <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs space-y-4">
            
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <Kanban class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                <h2 class="text-sm font-black text-slate-900 dark:text-white">Peluang Proyek & Deals ({{ deals.length }})</h2>
              </div>

              <button
                @click="isAddDealModalOpen = true"
                class="px-3 py-1 rounded-xl bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400 text-xs font-bold hover:bg-purple-100 transition"
              >
                + Tambah Deal
              </button>
            </div>

            <!-- List Deals -->
            <div v-if="deals.length" class="space-y-2.5">
              <div
                v-for="d in deals"
                :key="d.id"
                class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/70 dark:border-slate-800 flex items-center justify-between gap-4"
              >
                <div class="space-y-1">
                  <div class="text-xs font-black text-slate-900 dark:text-white">{{ d.title }}</div>
                  <div class="flex items-center gap-2 text-[10px] text-slate-400">
                    <span class="font-bold text-purple-600 dark:text-purple-400">{{ d.stage_label }} ({{ d.probability }}%)</span>
                    <span>•</span>
                    <span>Target: {{ d.expected_close_date_formatted }}</span>
                    <span>•</span>
                    <span>Sales: {{ d.sales_name }}</span>
                  </div>
                </div>

                <div class="text-right shrink-0">
                  <div class="text-xs font-black text-slate-900 dark:text-white">{{ d.expected_value_formatted }}</div>
                  <Link
                    :href="`/deals?client_id=${client.id}`"
                    class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-1"
                  >
                    <span>Buka di Kanban</span>
                    <span>&rarr;</span>
                  </Link>
                </div>
              </div>
            </div>

            <div v-else class="py-6 text-center text-xs text-slate-400 italic">
              Belum ada deal aktif untuk klien ini.
            </div>

          </div>

          <!-- SECTION 3: AKTIVITAS & FOLLOW-UP LOG -->
          <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs space-y-4">
            <div class="flex items-center gap-2">
              <Clock class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
              <h2 class="text-sm font-black text-slate-900 dark:text-white">Log Aktivitas & Catatan Follow-up</h2>
            </div>

            <!-- Form Tambah Catatan Cepat -->
            <form @submit.prevent="submitActivity" class="space-y-2.5 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/70 dark:border-slate-800">
              <div class="flex items-center gap-2">
                <select
                  v-model="activityForm.type"
                  class="px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300"
                >
                  <option value="whatsapp">WhatsApp</option>
                  <option value="call">Telepon / Call</option>
                  <option value="meeting">Meeting Scoping</option>
                  <option value="email">Email</option>
                  <option value="note">Catatan Internal</option>
                </select>

                <input
                  v-model="activityForm.title"
                  type="text"
                  required
                  placeholder="Judul aktivitas (misal: Follow-up penawaran via WA...)"
                  class="flex-1 px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-hidden"
                />

                <button
                  type="submit"
                  class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold flex items-center gap-1 shrink-0"
                >
                  <Send class="w-3.5 h-3.5" />
                  <span>Kirim</span>
                </button>
              </div>

              <textarea
                v-model="activityForm.description"
                rows="2"
                placeholder="Rincian hasil pembicaraan atau catatan requirement..."
                class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-900 dark:text-white focus:outline-hidden"
              ></textarea>
            </form>

            <!-- Activity Timeline Feed -->
            <div v-if="activities.length" class="space-y-3 pt-2">
              <div
                v-for="act in activities"
                :key="act.id"
                class="flex items-start gap-3 p-3 rounded-2xl bg-white dark:bg-slate-800/30 border border-slate-100 dark:border-slate-800/70"
              >
                <div 
                  class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs shrink-0"
                  :class="getActivityIconClass(act.type)"
                >
                  <MessageSquare v-if="act.type === 'whatsapp'" class="w-4 h-4" />
                  <Phone v-else-if="act.type === 'call'" class="w-4 h-4" />
                  <Users v-else-if="act.type === 'meeting'" class="w-4 h-4" />
                  <Mail v-else-if="act.type === 'email'" class="w-4 h-4" />
                  <FileText v-else class="w-4 h-4" />
                </div>

                <div class="flex-1 space-y-1">
                  <div class="flex items-center justify-between">
                    <span class="text-xs font-black text-slate-900 dark:text-white">{{ act.title }}</span>
                    <span class="text-[10px] text-slate-400 font-mono">{{ act.performed_at_formatted }}</span>
                  </div>
                  <p v-if="act.description" class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">{{ act.description }}</p>
                  <div class="text-[10px] text-slate-400 font-bold">Oleh: {{ act.user_name }}</div>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-4 text-xs text-slate-400 italic">
              Belum ada catatan aktivitas.
            </div>

          </div>

        </div>

        <!-- RIGHT COLUMN (1 SPAN): KONTAK PIC & INFO -->
        <div class="space-y-6">
          
          <!-- PIC Contacts Card -->
          <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs space-y-4">
            
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <Users class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                <h2 class="text-sm font-black text-slate-900 dark:text-white">Kontak PIC ({{ contacts.length }})</h2>
              </div>

              <button
                @click="isAddContactModalOpen = true"
                class="px-2.5 py-1 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 text-xs font-bold hover:bg-indigo-100 transition"
              >
                + Tambah
              </button>
            </div>

            <!-- Contacts List -->
            <div v-if="contacts.length" class="space-y-3">
              <div
                v-for="c in contacts"
                :key="c.id"
                class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/70 dark:border-slate-800 space-y-2 relative group"
              >
                <div class="flex items-start justify-between gap-2">
                  <div>
                    <div class="flex items-center gap-1.5">
                      <span class="text-xs font-black text-slate-900 dark:text-white">{{ c.name }}</span>
                      <span v-if="c.is_primary" class="px-1.5 py-0.2 text-[8px] font-extrabold bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 rounded-md border border-indigo-200">UTAMA</span>
                    </div>
                    <div class="text-[10px] text-slate-400 font-bold">{{ c.title }}</div>
                  </div>

                  <button
                    @click="deleteContact(c.id)"
                    class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-rose-600 transition p-1"
                    title="Hapus Kontak"
                  >
                    <Trash2 class="w-3.5 h-3.5" />
                  </button>
                </div>

                <div class="space-y-1 text-xs text-slate-600 dark:text-slate-300">
                  <div v-if="c.email !== '-'" class="flex items-center gap-1.5 text-[11px]">
                    <Mail class="w-3 h-3 text-slate-400" />
                    <span>{{ c.email }}</span>
                  </div>
                  <div v-if="c.phone !== '-'" class="flex items-center gap-1.5 text-[11px]">
                    <Phone class="w-3 h-3 text-slate-400" />
                    <span>{{ c.phone }}</span>
                  </div>
                </div>

                <!-- WhatsApp Click to Chat Button -->
                <a
                  v-if="c.whatsapp_url"
                  :href="c.whatsapp_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="w-full mt-2 py-1.5 px-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-xs flex items-center justify-center gap-1.5 shadow-sm transition active:scale-95"
                >
                  <MessageSquare class="w-3.5 h-3.5 stroke-[2.5]" />
                  <span>Chat WhatsApp</span>
                </a>
              </div>
            </div>

            <div v-else class="text-center py-4 text-xs text-slate-400 italic">
              Belum ada kontak PIC.
            </div>

          </div>

          <!-- Internal Client Notes -->
          <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xs space-y-2">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Catatan Khusus Klien</h3>
            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed italic">
              {{ client.notes || 'Tidak ada catatan khusus untuk klien ini.' }}
            </p>
          </div>

        </div>

      </div>

    </div>

    <!-- MODAL TAMBAH KONTAK PIC -->
    <div
      v-if="isAddContactModalOpen"
      class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4"
      @click.self="isAddContactModalOpen = false"
    >
      <div class="w-full max-w-md bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl p-6 space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-black text-slate-900 dark:text-white">Tambah Kontak PIC</h3>
          <button @click="isAddContactModalOpen = false" class="text-slate-400 hover:text-slate-600">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitAddContact" class="space-y-3">
          <div>
            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Nama PIC *</label>
            <input
              v-model="contactForm.name"
              type="text"
              required
              placeholder="Nama lengkap PIC"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
            />
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Jabatan / Role</label>
            <input
              v-model="contactForm.title"
              type="text"
              placeholder="misal: CTO, Head of IT, PM"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
            />
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp / HP</label>
            <input
              v-model="contactForm.phone"
              type="text"
              placeholder="081234567890"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
            />
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Email</label>
            <input
              v-model="contactForm.email"
              type="email"
              placeholder="pic@perusahaan.com"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
            />
          </div>

          <div class="flex items-center gap-2 pt-1">
            <input
              v-model="contactForm.is_primary"
              id="is_primary_checkbox"
              type="checkbox"
              class="rounded text-indigo-600 focus:ring-indigo-500"
            />
            <label for="is_primary_checkbox" class="text-xs font-bold text-slate-700 dark:text-slate-300">Set sebagai PIC Utama</label>
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
            <button
              type="button"
              @click="isAddContactModalOpen = false"
              class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl"
            >
              Batal
            </button>
            <button
              type="submit"
              class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-extrabold shadow-md hover:bg-indigo-700"
            >
              Simpan PIC
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL TAMBAH DEAL BARU -->
    <div
      v-if="isAddDealModalOpen"
      class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4"
      @click.self="isAddDealModalOpen = false"
    >
      <div class="w-full max-w-md bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl p-6 space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-black text-slate-900 dark:text-white">Tambah Peluang Proyek (Deal)</h3>
          <button @click="isAddDealModalOpen = false" class="text-slate-400 hover:text-slate-600">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitAddDeal" class="space-y-3">
          <div>
            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Proyek / Deal *</label>
            <input
              v-model="dealForm.title"
              type="text"
              required
              placeholder="misal: Pengembangan Web E-Commerce V2"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Stage Pipeline</label>
              <select
                v-model="dealForm.stage"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300"
              >
                <option value="discovery">Discovery</option>
                <option value="scoping">Scoping Requirement</option>
                <option value="proposal_sent">Proposal Sent</option>
                <option value="negotiation">Negotiation</option>
                <option value="won">Deal Won</option>
                <option value="lost">Deal Lost</option>
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

          <div>
            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Target Closing</label>
            <input
              v-model="dealForm.expected_close_date"
              type="date"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
            />
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
              class="px-4 py-2 rounded-xl bg-purple-600 text-white text-xs font-extrabold shadow-md hover:bg-purple-700"
            >
              Simpan Deal
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL EDIT DATA KLIEN -->
    <div
      v-if="isEditClientModalOpen"
      class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4"
      @click.self="isEditClientModalOpen = false"
    >
      <div class="w-full max-w-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl p-6 space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-black text-slate-900 dark:text-white">Edit Data Klien</h3>
          <button @click="isEditClientModalOpen = false" class="text-slate-400 hover:text-slate-600">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitEditClient" class="space-y-3">
          <div>
            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Perusahaan *</label>
            <input
              v-model="editClientForm.name"
              type="text"
              required
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Industri</label>
              <input
                v-model="editClientForm.industry"
                type="text"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
              />
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Status</label>
              <select
                v-model="editClientForm.status"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300"
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
                v-model="editClientForm.email"
                type="email"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
              />
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Telepon</label>
              <input
                v-model="editClientForm.phone"
                type="text"
                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
              />
            </div>
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat</label>
            <textarea
              v-model="editClientForm.address"
              rows="2"
              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white"
            ></textarea>
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
            <button
              type="button"
              @click="isEditClientModalOpen = false"
              class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl"
            >
              Batal
            </button>
            <button
              type="submit"
              class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-extrabold shadow-md hover:bg-indigo-700"
            >
              Simpan Perubahan
            </button>
          </div>
        </form>
      </div>
    </div>

  </AppLayout>
</template>
