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
  Scale,
  FileDown,
  Server,
  Cloud,
  Clock,
  CheckCircle2,
  Calendar
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
    answer: 'Skema Beli Putus adalah pembayaran satu kali untuk penyerahan penuh kode sumber software, lisensi pakai permanen, dan sewa server sesuai durasi yang dipilih. Skema Langganan SaaS adalah model sewa lisensi berbasis periode (bulanan/tahunan) yang mencakup infrastruktur server, pemeliharaan sistem berkala, pembaruan fitur, dan lisensi pengguna.'
  },
  {
    id: 2,
    question: 'Bagaimana cara kerja perhitungan Bobot Kompleksitas Software?',
    answer: 'Setiap modul fitur software dikalikan dengan Bobot Kompleksitas berdasarkan skala kesulitan teknis: 0.8x (Sederhana), 1.0x (Standar), 1.25x (Sedang), 1.5x (Kompleks), dan 2.0x (Enterprise / High Risk). Formula: Harga Modul = Harga Dasar × Bobot Kompleksitas.'
  },
  {
    id: 3,
    question: 'Bagaimana cara kerja perhitungan Infrastruktur Server & Cloud Hosting?',
    answer: 'DevCalc menyediakan 2 opsi infrastruktur: (1) Server Mandiri Klien (Rp 0 jika klien sudah memiliki server sendiri), dan (2) Sewa Server Kustom (Shared Hosting cPanel untuk web company profile/toko online atau Cloud VPS Dedicated untuk Web App SaaS). Biaya server dihitung berdasarkan: Tarif Bulanan × Durasi Sewa (Bulan). Pada Beli Putus, durasi tersedia dalam pilihan 12, 24, atau 48 Bulan. Pada SaaS, durasi otomatis mengunci sesuai masa komitmen kontrak.'
  },
  {
    id: 4,
    question: 'Bagaimana skema diskon 20% untuk penagihan Tahunan (Annually)?',
    answer: 'Pada skema langganan SaaS dengan siklus pembayaran Tahunan (Yearly), sistem memberikan diskon komitmen di muka sebesar 20% dari total biaya sewa tahunan (Lisensi Software + User + Sewa Server). Formula: Tagihan Tahunan = Total Tahunan Normal × 0.80 (Hemat 20%).'
  },
  {
    id: 5,
    question: 'Bagaimana fungsi Kategori Solusi Sistem dan Estimasi Timeline Deliverable?',
    answer: 'Pada Langkah 1 pembuatan penawaran, Anda dapat memilih Kategori Solusi Sistem (misal: Web Enterprise, ERP, E-Commerce, Mobile Apps) dan Estimasi Target Timeline (misal: 2-4 Minggu MVP, 2-3 Bulan Standar, 3-6 Bulan Multi-Fase). Anda juga dapat mengetikkan teks kustom bebas. Informasi ini akan tercantum secara resmi pada surat penawaran harga PDF.'
  },
  {
    id: 6,
    question: 'Bagaimana ketentuan Jaminan Garansi & SLA Maintenance?',
    answer: 'Pada Skema Beli Putus, tersedia pilihan garansi perbaikan bug & pendampingan teknis gratis: 1 Bulan, 3 Bulan (Standar SLA Default), 6 Bulan, atau 12 Bulan pasca serah terima. Pada Skema SaaS, Garansi & SLA aktif penuh secara otomatis mengikuti seluruh masa komitmen kontrak langganan.'
  },
  {
    id: 7,
    question: 'Bagaimana cara membuat dan mengelola Dokumen Adendum?',
    answer: 'Pada daftar penawaran, klik tombol "Menu Aksi" -> "Buat Adendum". Anda dapat membuat 3 jenis adendum: (1) Penambahan Modul Fitur Baru, (2) Penambahan Kapasitas User Lisensi, atau (3) Perpanjangan Durasi Kontrak SaaS. Dokumen adendum akan terhubung secara otomatis ke nomor penawaran induk.'
  },
  {
    id: 8,
    question: 'Bagaimana cara mengekspor laporan rekapitulasi penawaran atau mencetak PDF resmi?',
    answer: 'Klik tombol "Ekspor Laporan" pada header daftar penawaran untuk mengunduh rekapitulasi format Excel (.csv) atau Executive PDF Report (.pdf). Untuk mencetak surat penawaran harga resmi per klien, klik tombol "Cetak PDF" pada tabel penawaran.'
  }
];
</script>

<template>
  <Head title="Panduan Penggunaan & Rumus Bisnis" />

  <AppLayout title="Panduan Penggunaan System & Rumus Perhitungan Bisnis">
    <div class="max-w-7xl mx-auto space-y-6">
      
      <!-- HEADER & ACTIONS -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Panduan & Rumus Bisnis</h1>
            <span class="px-2 py-0.5 text-xs font-extrabold bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 rounded-lg border border-amber-200 dark:border-amber-800">Dokumentasi</span>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Dokumentasi lengkap alur wizard 3 langkah, rumus kalkulator harga, skema server hosting, dan ketentuan garansi SLA.
          </p>
        </div>

        <Link
          href="/projects/create"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-lg shadow-indigo-600/30 transition cursor-pointer active:scale-95 shrink-0 self-start sm:self-auto"
        >
          <Calculator class="w-4 h-4 stroke-[2.5]" />
          <span>Mulai Buat Penawaran</span>
        </Link>
      </div>

      <!-- Quick Feature Cards Overview -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
        <div class="p-4.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-2.5 transition hover:border-indigo-300 dark:hover:border-indigo-800">
          <div class="w-9 h-9 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/60 flex items-center justify-center">
            <Calculator class="w-4.5 h-4.5" />
          </div>
          <h3 class="text-xs font-extrabold text-slate-900 dark:text-white">1. Kalkulator Otomatis</h3>
          <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Estimasi instan biaya Beli Putus & Langganan SaaS.</p>
        </div>

        <div class="p-4.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-2.5 transition hover:border-sky-300 dark:hover:border-sky-800">
          <div class="w-9 h-9 rounded-2xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800/60 flex items-center justify-center">
            <Server class="w-4.5 h-4.5" />
          </div>
          <h3 class="text-xs font-extrabold text-slate-900 dark:text-white">2. Server & Hosting</h3>
          <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Kalkulasi sewa bulanan Shared Hosting & Cloud VPS.</p>
        </div>

        <div class="p-4.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-2.5 transition hover:border-emerald-300 dark:hover:border-emerald-800">
          <div class="w-9 h-9 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60 flex items-center justify-center">
            <ShieldCheck class="w-4.5 h-4.5" />
          </div>
          <h3 class="text-xs font-extrabold text-slate-900 dark:text-white">3. Garansi SLA</h3>
          <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Jaminan perbaikan bug gratis (1, 3, 6, 12 Bln / SaaS).</p>
        </div>

        <div class="p-4.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-2.5 transition hover:border-amber-300 dark:hover:border-amber-800">
          <div class="w-9 h-9 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60 flex items-center justify-center">
            <FilePlus class="w-4.5 h-4.5" />
          </div>
          <h3 class="text-xs font-extrabold text-slate-900 dark:text-white">4. Manajemen Adendum</h3>
          <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Penyesuaian penawaran untuk modul, user, atau durasi.</p>
        </div>

        <div class="p-4.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-2.5 transition hover:border-purple-300 dark:hover:border-purple-800">
          <div class="w-9 h-9 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800/60 flex items-center justify-center">
            <Printer class="w-4.5 h-4.5" />
          </div>
          <h3 class="text-xs font-extrabold text-slate-900 dark:text-white">5. Cetak & Ekspor</h3>
          <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Unduh PDF Penawaran Resmi, Excel CSV & Executive PDF.</p>
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
          <span>Panduan Alur Kerja (3 Langkah)</span>
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
              <p class="text-xs text-slate-500 dark:text-slate-400">Pembayaran satu kali untuk penyerahan penuh kode sumber software & sewa server berjangka.</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-1">
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 space-y-2">
              <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Harga Modul Software</span>
              <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 font-mono text-xs text-slate-900 dark:text-white font-bold">
                Harga Fitur = Harga Dasar × Bobot
              </div>
              <p class="text-[11px] text-slate-500 dark:text-slate-400">
                Bobot: 0.8x (Sederhana), 1.0x (Standar), 1.25x (Sedang), 1.5x (Kompleks), 2.0x (Enterprise).
              </p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 space-y-2">
              <span class="text-[11px] font-extrabold uppercase tracking-wider text-sky-600 dark:text-sky-400">Sewa Server Hosting</span>
              <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 font-mono text-xs text-slate-900 dark:text-white font-bold">
                Total Server = Tarif/Bulan × Durasi
              </div>
              <p class="text-[11px] text-slate-500 dark:text-slate-400">
                Opsi durasi sewa: 12 Bulan (1 Thn), 24 Bulan (2 Thn), atau 48 Bulan (4 Thn).
              </p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 space-y-2">
              <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Total Nilai Kontrak</span>
              <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 font-mono text-xs text-slate-900 dark:text-white font-bold">
                Grand Total = Fitur + Server + Setup Fee
              </div>
              <p class="text-[11px] text-slate-500 dark:text-slate-400">
                Termasuk garansi SLA perbaikan bug gratis (1, 3, 6, atau 12 Bulan).
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
              <p class="text-xs text-slate-500 dark:text-slate-400">Model sewa berkala yang menggabungkan lisensi software, server hosting, & user seat.</p>
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
                  Biaya/Bln = Σ(Modul) + Server
                </div>
                <p class="text-[10px] text-slate-500 dark:text-slate-400">
                  Dihitung dari tarif sewa bulanan modul software ditambah biaya server hosting.
                </p>
              </div>

              <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 space-y-1.5">
                <span class="text-[11px] font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                  <Users class="w-3.5 h-3.5 text-sky-500" />
                  <span>B. Per-User Only</span>
                </span>
                <div class="p-2.5 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 font-mono text-[11px] text-slate-800 dark:text-slate-200 font-bold">
                  Biaya/Bln = (User × Tarif) + Server
                </div>
                <p class="text-[10px] text-slate-500 dark:text-slate-400">
                  Fitur software termasuk dalam lisensi, dihitung murni kapasitas user + server.
                </p>
              </div>

              <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 space-y-1.5">
                <span class="text-[11px] font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                  <Sparkles class="w-3.5 h-3.5 text-amber-500" />
                  <span>C. Hybrid (Kombinasi)</span>
                </span>
                <div class="p-2.5 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 font-mono text-[11px] text-slate-800 dark:text-slate-200 font-bold">
                  Biaya/Bln = Modul + (User × Tarif) + Server
                </div>
                <p class="text-[10px] text-slate-500 dark:text-slate-400">
                  Kombinasi sewa modul inti, kapasitas lisensi user, dan server hosting.
                </p>
              </div>
            </div>

            <!-- Discount & Cycle Formula -->
            <div class="p-4 rounded-2xl bg-indigo-50/60 dark:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-800/80 space-y-2">
              <div class="flex items-center gap-2 text-xs font-bold text-indigo-900 dark:text-indigo-200">
                <BadgePercent class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                <span>Diskon Siklus Pembayaran Tahunan (Yearly 20% OFF)</span>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-indigo-200/60 dark:border-indigo-900/60 font-mono text-[11px] font-bold text-slate-900 dark:text-white">
                  Tagihan Bulanan = Biaya/Bulan × Durasi
                </div>
                <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-indigo-200/60 dark:border-indigo-900/60 font-mono text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                  Tagihan Tahunan = (Biaya/Bulan × 12) × 0.80 × Durasi
                </div>
              </div>
              <p class="text-[11px] text-indigo-700 dark:text-indigo-300">
                Total Kontrak SaaS = Setup Fee + (Tarif Siklus Bersih × Durasi Periode).
              </p>
            </div>
          </div>
        </div>

      </div>

      <!-- TAB 2: PANDUAN ALUR KERJA (3 LANGKAH) -->
      <div v-if="activeTab === 'workflow'" class="space-y-6">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
          <div>
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
              Alur Pengisian Penawaran Harga (Wizard 3 Langkah Terpadu)
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
              Proses pembuatan surat penawaran harga yang ringkas, terstruktur, dan terintegrasi otomatis.
            </p>
          </div>

          <div class="space-y-6 pt-2">
            <!-- Step 1 -->
            <div class="flex gap-4 items-start">
              <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-md shadow-indigo-600/30">
                1
              </div>
              <div class="space-y-1.5">
                <h4 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                  <span>Langkah 1: Info Klien, Kategori Solusi & Target Timeline</span>
                </h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                  Isi nama perusahaan klien. Pilih <b>Kategori Solusi Sistem</b> (rekomendasi preset seperti Web Enterprise, ERP, E-Commerce, dsb. atau ketik kustom) dan tentukan <b>Target Estimasi Timeline Deliverable</b> (misal: 2-4 Minggu MVP, 2-3 Bulan Standar).
                </p>
              </div>
            </div>

            <!-- Step 2 -->
            <div class="flex gap-4 items-start">
              <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-md shadow-indigo-600/30">
                2
              </div>
              <div class="space-y-1.5">
                <h4 class="text-sm font-bold text-slate-900 dark:text-white">
                  <span>Langkah 2: Fitur & Modul Software Engineering</span>
                </h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                  Pilih modul fitur software dari katalog master data dengan kategori otomatis, atau tambahkan modul kustom bebas. Sesuaikan <b>Bobot Kompleksitas Teknis</b> untuk tiap modul (0.8x hingga 2.0x).
                </p>
              </div>
            </div>

            <!-- Step 3 -->
            <div class="flex gap-4 items-start">
              <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-md shadow-indigo-600/30">
                3
              </div>
              <div class="space-y-1.5">
                <h4 class="text-sm font-bold text-slate-900 dark:text-white">
                  <span>Langkah 3: Skema Komersial, Server & Finalisasi Penawaran</span>
                </h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                  Tentukan <b>Skema Pembayaran</b> (Beli Putus atau SaaS Berlangganan), konfigurasi <b>Infrastruktur Server Hosting</b> (Server Klien Rp 0 vs Sewa Server Shared/VPS dengan perhitungan durasi sewa), biaya setup onboarding, catatan penawaran, serta periksa ringkasan sebelum menyimpan Draft atau menerbitkan Penawaran Resmi.
                </p>
              </div>
            </div>

            <!-- After Step -->
            <div class="flex gap-4 items-start pt-2 border-t border-slate-100 dark:border-slate-800">
              <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-md shadow-emerald-600/30">
                <CheckCircle2 class="w-4 h-4" />
              </div>
              <div class="space-y-1.5">
                <h4 class="text-sm font-bold text-slate-900 dark:text-white">
                  <span>Cetak PDF Resmi, Buat Adendum & Ekspor Laporan</span>
                </h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                  Dokumen penawaran harga dapat langsung dicetak ke format PDF resmi. Bila terdapat perubahan lingkup di masa depan, gunakan fitur <b>Buat Adendum</b>, atau ekspor rekapitulasi data ke format <b>Excel CSV</b> dan <b>Executive PDF Report</b>.
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
