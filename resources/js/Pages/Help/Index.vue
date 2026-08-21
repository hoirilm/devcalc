<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
  HelpCircle, 
  BookOpen, 
  ShieldCheck, 
  ChevronDown, 
  ChevronUp,
  Calculator,
  Printer,
  FileText,
  Layers,
  Sparkles,
  Users,
  Repeat,
  Info,
  BadgePercent,
  FilePlus,
  Scale
} from 'lucide-vue-next';

const activeTab = ref('formulas'); // 'formulas' | 'faq' | 'workflow'
const openFaq = ref(1);

function toggleFaq(idx) {
  openFaq.value = openFaq.value === idx ? null : idx;
}

const faqs = [
  {
    id: 1,
    question: 'Apa perbedaan antara Skema Beli Putus (One-Off) dan Langganan (SaaS)?',
    answer: 'Skema Beli Putus adalah pembayaran satu kali untuk penyerahan penuh kode sumber software dan lisensi pakai permanen. Skema Langganan SaaS adalah model sewa lisensi berbasis periode (bulanan/tahunan) yang mencakup infrastruktur server, pemeliharaan sistem, pembaruan fitur, dan lisensi pengguna.'
  },
  {
    id: 2,
    question: 'Bagaimana cara kerja perhitungan Bobot Kompleksitas?',
    answer: 'Setiap modul fitur dikalikan dengan Bobot Kompleksitas berdasarkan skala kesulitan teknis: 0.8x (Sederhana), 1.0x (Standar), 1.25x (Sedang), 1.5x (Kompleks), dan 2.0x (Enterprise / High Risk). Formula: Harga Fitur = Harga Dasar × Bobot Kompleksitas.'
  },
  {
    id: 3,
    question: 'Bagaimana skema diskon 20% untuk penagihan Tahunan (Annually)?',
    answer: 'Pada skema langganan SaaS dengan siklus pembayaran Tahunan (Annually), sistem memberikan diskon otomatis sebesar 20% dari total biaya sewa 12 bulan (Formula: Total Tahunan = Tarif Bulanan × 12 × 0.80).'
  },
  {
    id: 4,
    question: 'Bagaimana cara kerja metode penagihan SaaS (Flat, Per-User, Hybrid)?',
    answer: 'Flat Modular dihitung berdasarkan harga sewa bulanan modul. Per-User dihitung dari (Jumlah User × Tarif Per User). Hybrid mengombinasikan sewa modul ditambah tarif per user. Jika tarif sewa bulanan modul tidak diisi, sistem menghitung otomatis estimasi 8% per tahun dari total harga beli putus modul.'
  },
  {
    id: 5,
    question: 'Bagaimana cara membuat dan mengelola Dokumen Adendum?',
    answer: 'Pada daftar penawaran, klik tombol "Menu Aksi" -> "Buat Adendum". Anda dapat membuat 3 jenis adendum: (1) Penambahan Modul Fitur Baru, (2) Penambahan Kapasitas User Lisensi, atau (3) Perpanjangan Durasi Kontrak SaaS. Dokumen adendum akan terhubung secara otomatis ke nomor penawaran induk.'
  },
  {
    id: 6,
    question: 'Bagaimana ketentuan Garansi Maintenance SLA?',
    answer: 'Setiap penawaran dilengkapi dengan pilihan Masa Garansi Maintenance SLA: 1 Bulan, 3 Bulan (Standar SLA), 6 Bulan (Extended SLA), atau 12 Bulan (Full Year SLA). Selama periode SLA ini, dukungan perbaikan bug gratis pasca serah terima disertakan secara penuh dalam nilai kontrak.'
  },
  {
    id: 7,
    question: 'Bagaimana cara mengunduh berkas PDF resmi penawaran?',
    answer: 'Pada tabel daftar penawaran harga, klik ikon printer "Cetak PDF" pada menu aksi. Sistem akan membuat dokumen PDF resmi siap cetak lengkap dengan rincian biaya, skema pembayaran, dan klausul garansi SLA.'
  }
];
</script>

<template>
  <Head title="Panduan Penggunaan & Rumus Bisnis" />

  <AppLayout title="Panduan Penggunaan System & Rumus Perhitungan Bisnis">
    <div class="max-w-7xl mx-auto space-y-6">
      
      <!-- Top Guide Header (Clean Simple Style) -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/80 border border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shadow-sm shrink-0">
              <HelpCircle class="w-5 h-5" />
            </div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
              Panduan System & Rumus Perhitungan Bisnis
            </h2>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400">
            Dokumentasi lengkap alur aplikasi, rumus kalkulator harga, skema kontrak SaaS, dan ketentuan garansi SLA.
          </p>
        </div>

        <Link
          href="/projects/create"
          class="px-4.5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/30 transition flex items-center justify-center gap-2 self-start sm:self-auto cursor-pointer shrink-0"
        >
          <Calculator class="w-4 h-4" />
          <span>Mulai Buat Penawaran</span>
        </Link>
      </div>

      <!-- Quick Feature Cards Overview -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-2.5 transition hover:border-indigo-300 dark:hover:border-indigo-800">
          <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/60 flex items-center justify-center">
            <Calculator class="w-5 h-5" />
          </div>
          <h3 class="text-sm font-bold text-slate-900 dark:text-white">1. Kalkulator Otomatis</h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Estimasi instan biaya Beli Putus & Langganan SaaS berdasarkan bobot kompleksitas.</p>
        </div>

        <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-2.5 transition hover:border-emerald-300 dark:hover:border-emerald-800">
          <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60 flex items-center justify-center">
            <ShieldCheck class="w-5 h-5" />
          </div>
          <h3 class="text-sm font-bold text-slate-900 dark:text-white">2. Garansi Maintenance SLA</h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Pilihan garansi perbaikan bug gratis (1, 3, 6, 12 Bulan) tercantum otomatis dalam dokumen.</p>
        </div>

        <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-2.5 transition hover:border-amber-300 dark:hover:border-amber-800">
          <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60 flex items-center justify-center">
            <FilePlus class="w-5 h-5" />
          </div>
          <h3 class="text-sm font-bold text-slate-900 dark:text-white">3. Manajemen Adendum</h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Penyesuaian penawaran berjalan untuk tambah modul, tambah user, atau perpanjangan kontrak.</p>
        </div>

        <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-2.5 transition hover:border-sky-300 dark:hover:border-sky-800">
          <div class="w-10 h-10 rounded-2xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800/60 flex items-center justify-center">
            <Printer class="w-5 h-5" />
          </div>
          <h3 class="text-sm font-bold text-slate-900 dark:text-white">4. Cetak PDF Resmi</h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Hasilkan berkas penawaran PDF berkualitas tinggi dengan rincian biaya & klausal garansi.</p>
        </div>
      </div>

      <!-- Navigation Segmented Tabs -->
      <div class="flex items-center gap-2 p-1.5 bg-slate-100 dark:bg-slate-800/80 rounded-2xl border border-slate-200/80 dark:border-slate-700/80">
        <button
          @click="activeTab = 'formulas'"
          :class="activeTab === 'formulas' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400 font-semibold hover:text-slate-900 dark:hover:text-white'"
          class="flex-1 py-2.5 px-4 rounded-xl text-xs transition cursor-pointer flex items-center justify-center gap-2"
        >
          <Scale class="w-4 h-4" />
          <span>Rumus Perhitungan Bisnis</span>
        </button>

        <button
          @click="activeTab = 'workflow'"
          :class="activeTab === 'workflow' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400 font-semibold hover:text-slate-900 dark:hover:text-white'"
          class="flex-1 py-2.5 px-4 rounded-xl text-xs transition cursor-pointer flex items-center justify-center gap-2"
        >
          <BookOpen class="w-4 h-4" />
          <span>Panduan Alur Kerja</span>
        </button>

        <button
          @click="activeTab = 'faq'"
          :class="activeTab === 'faq' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400 font-semibold hover:text-slate-900 dark:hover:text-white'"
          class="flex-1 py-2.5 px-4 rounded-xl text-xs transition cursor-pointer flex items-center justify-center gap-2"
        >
          <HelpCircle class="w-4 h-4" />
          <span>FAQ & Ketentuan SLA</span>
        </button>
      </div>

      <!-- TAB 1: RUMUS PERHITUNGAN BISNIS -->
      <div v-if="activeTab === 'formulas'" class="space-y-6">
        
        <!-- Beli Putus Formula Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
          <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
              <Calculator class="w-5 h-5" />
            </div>
            <div>
              <h3 class="text-base font-extrabold text-slate-900 dark:text-white">1. Rumus Skema Beli Putus (One-Off Payment)</h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">Pembayaran satu kali untuk kepemilikan penuh software & kode sumber.</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 space-y-2">
              <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Perhitungan Harga Tiap Modul</span>
              <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 font-mono text-xs text-slate-900 dark:text-white font-bold">
                Harga Fitur = Harga Dasar Modul × Bobot Kompleksitas
              </div>
              <p class="text-[11px] text-slate-500 dark:text-slate-400">
                Bobot Kompleksitas: 0.8x (Sederhana), 1.0x (Standar), 1.25x (Sedang), 1.5x (Kompleks), 2.0x (Enterprise).
              </p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 space-y-2">
              <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Total Biaya Beli Putus</span>
              <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 font-mono text-xs text-slate-900 dark:text-white font-bold">
                Grand Total = Σ(Harga Fitur) + Biaya Setup (Implementasi)
              </div>
              <p class="text-[11px] text-slate-500 dark:text-slate-400">
                Sudah mencakup garansi perbaikan bug gratis selama masa SLA yang dipilih.
              </p>
            </div>
          </div>
        </div>

        <!-- Langganan SaaS Formula Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
          <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
              <Repeat class="w-5 h-5" />
            </div>
            <div>
              <h3 class="text-base font-extrabold text-slate-900 dark:text-white">2. Rumus Skema Langganan SaaS (Software as a Service)</h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">Model sewa berbasis periode yang mencakup server, maintenance, & lisensi user.</p>
            </div>
          </div>

          <div class="space-y-4 pt-1">
            <!-- 3 Billing Basis -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
              <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 space-y-1.5">
                <span class="text-[11px] font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                  <Layers class="w-3.5 h-3.5 text-indigo-500" />
                  <span>A. Flat Modular</span>
                </span>
                <div class="p-2.5 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 font-mono text-[11px] text-slate-800 dark:text-slate-200 font-bold">
                  SaaS/Bulan = Σ(Sewa Modul)
                </div>
                <p class="text-[10px] text-slate-500 dark:text-slate-400">
                  Default tarif sewa modul per bulan = (8% × Harga Beli Putus) ÷ 12 bulan.
                </p>
              </div>

              <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 space-y-1.5">
                <span class="text-[11px] font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                  <Users class="w-3.5 h-3.5 text-sky-500" />
                  <span>B. Per-User Only</span>
                </span>
                <div class="p-2.5 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 font-mono text-[11px] text-slate-800 dark:text-slate-200 font-bold">
                  SaaS/Bulan = User × Tarif User
                </div>
                <p class="text-[10px] text-slate-500 dark:text-slate-400">
                  Dihitung murni berdasarkan kapasitas jumlah pengguna lisensi aktif.
                </p>
              </div>

              <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 space-y-1.5">
                <span class="text-[11px] font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                  <Sparkles class="w-3.5 h-3.5 text-amber-500" />
                  <span>C. Hybrid (Kombinasi)</span>
                </span>
                <div class="p-2.5 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 font-mono text-[11px] text-slate-800 dark:text-slate-200 font-bold">
                  SaaS/Bulan = Flat + (User × Tarif)
                </div>
                <p class="text-[10px] text-slate-500 dark:text-slate-400">
                  Kombinasi biaya sewa modul dasar ditambah tarif tambahan per pengguna.
                </p>
              </div>
            </div>

            <!-- Discount & Cycle Formula -->
            <div class="p-4 rounded-2xl bg-indigo-50/60 dark:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-800/80 space-y-2">
              <div class="flex items-center gap-2 text-xs font-bold text-indigo-900 dark:text-indigo-200">
                <BadgePercent class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                <span>Diskon Siklus Pembayaran Tahunan (Annually 20% OFF)</span>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-indigo-200/60 dark:border-indigo-900/60 font-mono text-[11px] font-bold text-slate-900 dark:text-white">
                  Bulanan (Monthly) = SaaS/Bulan
                </div>
                <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-indigo-200/60 dark:border-indigo-900/60 font-mono text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                  Tahunan (Annually) = (SaaS/Bulan × 12) × 0.80
                </div>
              </div>
              <p class="text-[11px] text-indigo-700 dark:text-indigo-300">
                Total Kontrak SaaS = Setup Fee + (Tarif Siklus × Durasi Periode).
              </p>
            </div>
          </div>
        </div>

      </div>

      <!-- TAB 2: PANDUAN ALUR KERJA -->
      <div v-if="activeTab === 'workflow'" class="space-y-6">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
          <h3 class="text-base font-extrabold text-slate-900 dark:text-white pb-3 border-b border-slate-200 dark:border-slate-800">
            Alur Pengisian Penawaran Harga Baru (Step-by-Step)
          </h3>

          <div class="space-y-6">
            <div class="flex gap-4 items-start">
              <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-md shadow-indigo-600/30">
                1
              </div>
              <div class="space-y-1">
                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Lengkapi Informasi Klien & Skema Kontrak</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                  Isi nama klien/perusahaan, tentukan Skema Pembayaran (Beli Putus atau SaaS), metode penagihan (Flat/User/Hybrid), dan durasi garansi SLA Maintenance (1, 3, 6, 12 Bulan).
                </p>
              </div>
            </div>

            <div class="flex gap-4 items-start">
              <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-md shadow-indigo-600/30">
                2
              </div>
              <div class="space-y-1">
                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Pilih Modul dari Katalog atau Tambah Custom Feature</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                  Gunakan dropdown template katalog untuk mengisi modul standar secara otomatis, atau ketik nama fitur kustom secara langsung. Sesuaikan bobot kompleksitas tiap fitur.
                </p>
              </div>
            </div>

            <div class="flex gap-4 items-start">
              <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-md shadow-indigo-600/30">
                3
              </div>
              <div class="space-y-1">
                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Pantau Ringkasan Nilai Kontrak di Sidebar Live Summary</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                  Sidebar kanan akan menghitung secara real-time total biaya pengembangan, diskon siklus tahunan, biaya sewa bulanan, dan tanggal berakhir garansi maintenance SLA.
                </p>
              </div>
            </div>

            <div class="flex gap-4 items-start">
              <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-md shadow-indigo-600/30">
                4
              </div>
              <div class="space-y-1">
                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Simpan Draft atau Terbitkan Dokumen PDF Resmi</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                  Simpan sebagai draft untuk diedit kemudian, atau terbitkan penawaran harga resmi dan unduh dokumen PDF siap cetak.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB 3: FAQ & KETENTUAN SLA -->
      <div v-if="activeTab === 'faq'" class="space-y-6">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
          <h3 class="text-base font-extrabold text-slate-900 dark:text-white pb-3 border-b border-slate-200 dark:border-slate-800">
            Pertanyaan Umum (FAQ) & Ketentuan Garansi SLA
          </h3>

          <div class="space-y-3">
            <div
              v-for="faq in faqs"
              :key="faq.id"
              class="border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden transition"
            >
              <button
                @click="toggleFaq(faq.id)"
                class="w-full px-5 py-4 text-left font-bold text-xs text-slate-900 dark:text-white bg-slate-50/50 dark:bg-slate-800/40 hover:bg-slate-100 dark:hover:bg-slate-800 transition flex items-center justify-between gap-4 cursor-pointer"
              >
                <span>{{ faq.question }}</span>
                <ChevronUp v-if="openFaq === faq.id" class="w-4 h-4 text-indigo-600 shrink-0" />
                <ChevronDown v-else class="w-4 h-4 text-slate-400 shrink-0" />
              </button>

              <div v-if="openFaq === faq.id" class="p-5 text-xs leading-relaxed text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800">
                {{ faq.answer }}
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>
