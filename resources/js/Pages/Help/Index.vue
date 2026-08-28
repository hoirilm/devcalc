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
  Calendar,
  Building2,
  Kanban,
  Target,
  TrendingUp,
  ArrowRight,
  Send,
  Workflow
} from 'lucide-vue-next';

const activeTab = ref('workflow'); // 'workflow' | 'crm' | 'formulas' | 'faq'
const openFaq = ref(1);

function toggleFaq(idx) {
  openFaq.value = openFaq.value === idx ? null : idx;
}

const faqs = [
  {
    id: 1,
    category: 'CRM & Pipeline',
    question: 'Bagaimana alur otomatisasi antara Pembuatan Penawaran (CPQ) dan Sales Kanban?',
    answer: 'DevCalc menggunakan alur CPQ-Driven CRM. Setiap kali Anda membuat atau memperbarui dokumen penawaran harga di wizard CPQ, sistem otomatis men-generate kartu Deal di Sales Kanban dengan nominal yang sama persis dengan grand total kalkulasi. Jika status penawaran adalah Draft, deal otomatis masuk ke stage "Scoping & Draf (30%)". Jika status resmi Generated, deal otomatis masuk ke stage "Proposal Terkirim (60%)". Anda tidak perlu lagi menginput data deal secara manual.'
  },
  {
    id: 2,
    category: 'CRM & Pipeline',
    question: 'Apa saja 5 tahapan (stages) pada Sales Pipeline Kanban?',
    answer: '1. Scoping & Draf (30%): Tahap pematangan draf penawaran modul bersama tim teknis/klien.\n2. Proposal Terkirim (60%): Surat penawaran harga resmi telah diterbitkan ke klien.\n3. Negosiasi & Review (80%): Tahap negosiasi termin komersial, review NDA/kontrak, dan persetujuan SPK.\n4. Closed Won (100%): Penawaran disetujui, deal berhasil closing.\n5. Closed Lost (0%): Deal dibatalkan dengan pencatatan alasan (Lost Reason).'
  },
  {
    id: 3,
    category: 'Skema Finansial',
    question: 'Apa perbedaan antara Skema Beli Putus (One-Off) dan Langganan (SaaS)?',
    answer: 'Skema Beli Putus adalah pembayaran satu kali untuk penyerahan penuh kode sumber software, lisensi pakai permanen, dan sewa server sesuai durasi yang dipilih (12, 24, atau 48 bulan). Skema Langganan SaaS adalah model sewa lisensi berkala (bulanan/tahunan) yang mencakup infrastruktur server, pemeliharaan berkala, pembaruan fitur, dan kapasitas lisensi user.'
  },
  {
    id: 4,
    category: 'Skema Finansial',
    question: 'Bagaimana cara kerja perhitungan Bobot Kompleksitas Software?',
    answer: 'Setiap modul fitur software dikalikan dengan Bobot Kompleksitas teknis: 0.8x (Sederhana), 1.0x (Standar), 1.25x (Sedang), 1.5x (Kompleks), dan 2.0x (Enterprise / High Risk). Formula: Harga Modul = Harga Dasar × Bobot Kompleksitas.'
  },
  {
    id: 5,
    category: 'Infrastruktur',
    question: 'Bagaimana cara kerja perhitungan Sewa Server & Cloud Hosting?',
    answer: 'DevCalc menyediakan 2 opsi infrastruktur: (1) Server Mandiri Klien (Rp 0 jika klien menyediakan server sendiri), dan (2) Sewa Server Kustom (Shared Hosting cPanel atau Cloud VPS Dedicated). Biaya server dihitung: Tarif Bulanan × Durasi Sewa (Bulan). Pada SaaS, durasi otomatis mengunci sesuai masa kontrak.'
  },
  {
    id: 6,
    category: 'Skema Finansial',
    question: 'Bagaimana skema diskon komitmen 20% untuk penagihan Tahunan (Yearly SaaS)?',
    answer: 'Pada skema langganan SaaS tahunan (Yearly), sistem secara otomatis memberikan diskon komitmen di muka sebesar 20% dari total biaya sewa tahunan (Lisensi Software + User + Server). Formula: Tagihan Tahunan = (Biaya Bulanan × 12) × 0.80 × Durasi Tahun.'
  },
  {
    id: 7,
    category: 'Garansi & SLA',
    question: 'Bagaimana ketentuan Jaminan Garansi & SLA Maintenance?',
    answer: 'Pada Beli Putus, tersedia garansi bugfix gratis pilihan: 1 Bulan, 3 Bulan (Standar SLA Default), 6 Bulan, atau 12 Bulan pasca serah terima. Pada SaaS, Garansi & SLA aktif penuh secara otomatis mengikuti seluruh masa komitmen kontrak langganan.'
  },
  {
    id: 8,
    category: 'Operasional',
    question: 'Bagaimana cara menerbitkan Dokumen Adendum resmi?',
    answer: 'Pada daftar penawaran harga, klik tombol aksi "Menu Aksi" -> "Buat Adendum". Anda dapat memilih 3 jenis adendum: (1) Penambahan Modul Fitur Baru, (2) Penambahan Kapasitas User Lisensi, atau (3) Perpanjangan Durasi Kontrak SaaS. Nomor adendum akan otomatis terhubung ke nomor penawaran induk.'
  },
];
</script>

<template>
  <Head title="Pusat Panduan & Dokumentasi Sistem" />

  <AppLayout title="Pusat Panduan Operasional CRM & CPQ System">
    <div class="max-w-7xl mx-auto space-y-6">
      
      <!-- HEADER & ACTIONS -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Pusat Panduan & Dokumentasi</h1>
            <span class="px-2 py-0.5 text-xs font-extrabold bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 rounded-lg border border-indigo-200 dark:border-indigo-800">Knowledge Hub</span>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Panduan lengkap alur CRM terpadu, otomatisasi Kanban 5 tahap, rumus estimasi CPQ, dan ketentuan SLA garansi.
          </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap self-start sm:self-auto shrink-0">
          <Link
            href="/deals"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-purple-50 dark:bg-purple-950/80 hover:bg-purple-100 text-purple-700 dark:text-purple-300 font-extrabold text-xs border border-purple-200 dark:border-purple-800 transition cursor-pointer active:scale-95"
          >
            <Kanban class="w-4 h-4 stroke-[2.5]" />
            <span>Buka Sales Kanban</span>
          </Link>

          <Link
            href="/projects/create"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-lg shadow-indigo-600/30 transition cursor-pointer active:scale-95"
          >
            <Calculator class="w-4 h-4 stroke-[2.5]" />
            <span>Buat Penawaran CPQ</span>
          </Link>
        </div>
      </div>

      <!-- 4 CORE SYSTEM PILLARS CARDS -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Pillar 1: CRM & Klien 360° -->
        <div 
          @click="activeTab = 'crm'"
          class="p-5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 rounded-3xl shadow-xs space-y-3 transition hover:border-indigo-300 dark:hover:border-indigo-800 cursor-pointer group"
        >
          <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center group-hover:scale-110 transition">
              <Building2 class="w-5 h-5" />
            </div>
            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-400">Pilar 1</span>
          </div>
          <div>
            <h3 class="text-xs font-black text-slate-900 dark:text-white">1. Manajemen Klien 360°</h3>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
              Direktori perusahaan B2B, kontak PIC pengambil keputusan, dan riwayat aktivitas follow-up.
            </p>
          </div>
        </div>

        <!-- Pillar 2: Sales Pipeline Kanban -->
        <div 
          @click="activeTab = 'crm'"
          class="p-5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 rounded-3xl shadow-xs space-y-3 transition hover:border-purple-300 dark:hover:border-purple-800 cursor-pointer group"
        >
          <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-2xl bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400 flex items-center justify-center group-hover:scale-110 transition">
              <Kanban class="w-5 h-5" />
            </div>
            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-purple-50 dark:bg-purple-950/80 text-purple-600 dark:text-purple-400">Pilar 2</span>
          </div>
          <div>
            <h3 class="text-xs font-black text-slate-900 dark:text-white">2. Sales Pipeline 5-Tahap</h3>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
              Otomasi CPQ sync: Scoping (30%), Proposal Sent (60%), Negosiasi (80%), hingga Closed Won (100%).
            </p>
          </div>
        </div>

        <!-- Pillar 3: CPQ Estimator Engine -->
        <div 
          @click="activeTab = 'formulas'"
          class="p-5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 rounded-3xl shadow-xs space-y-3 transition hover:border-emerald-300 dark:hover:border-emerald-800 cursor-pointer group"
        >
          <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:scale-110 transition">
              <Calculator class="w-5 h-5" />
            </div>
            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400">Pilar 3</span>
          </div>
          <div>
            <h3 class="text-xs font-black text-slate-900 dark:text-white">3. Kalkulator CPQ & Rumus</h3>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
              Kalkulasi bobot modul (0.8x - 2.0x), sewa server Cloud VPS, dan diskon langganan tahunan 20%.
            </p>
          </div>
        </div>

        <!-- Pillar 4: SLA, Adendum & Ekspor -->
        <div 
          @click="activeTab = 'faq'"
          class="p-5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 rounded-3xl shadow-xs space-y-3 transition hover:border-amber-300 dark:hover:border-amber-800 cursor-pointer group"
        >
          <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center group-hover:scale-110 transition">
              <ShieldCheck class="w-5 h-5" />
            </div>
            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-amber-50 dark:bg-amber-950/80 text-amber-600 dark:text-amber-400">Pilar 4</span>
          </div>
          <div>
            <h3 class="text-xs font-black text-slate-900 dark:text-white">4. SLA, Adendum & PDF</h3>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
              Jaminan bugfix SLA, 3 jenis adendum resmi, cetak dokumen PDF, serta ekspor laporan Excel.
            </p>
          </div>
        </div>

      </div>

      <!-- NAVIGATION SEGMENTED TABS -->
      <div class="flex items-center gap-1.5 p-1.5 bg-slate-100 dark:bg-slate-800/80 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 overflow-x-auto custom-scrollbar">
        <button
          @click="activeTab = 'workflow'"
          :class="activeTab === 'workflow' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-sm font-extrabold' : 'text-slate-600 dark:text-slate-400 font-bold hover:text-slate-900 dark:hover:text-white'"
          class="flex-1 min-w-[170px] py-2.5 px-4 rounded-xl text-xs transition cursor-pointer flex items-center justify-center gap-2 shrink-0"
        >
          <Workflow class="w-4 h-4" />
          <span>Alur Terpadu End-to-End</span>
        </button>

        <button
          @click="activeTab = 'crm'"
          :class="activeTab === 'crm' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-sm font-extrabold' : 'text-slate-600 dark:text-slate-400 font-bold hover:text-slate-900 dark:hover:text-white'"
          class="flex-1 min-w-[170px] py-2.5 px-4 rounded-xl text-xs transition cursor-pointer flex items-center justify-center gap-2 shrink-0"
        >
          <Kanban class="w-4 h-4" />
          <span>CRM & Pipeline 5 Tahap</span>
        </button>

        <button
          @click="activeTab = 'formulas'"
          :class="activeTab === 'formulas' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-sm font-extrabold' : 'text-slate-600 dark:text-slate-400 font-bold hover:text-slate-900 dark:hover:text-white'"
          class="flex-1 min-w-[170px] py-2.5 px-4 rounded-xl text-xs transition cursor-pointer flex items-center justify-center gap-2 shrink-0"
        >
          <Scale class="w-4 h-4" />
          <span>Rumus Kalkulator CPQ</span>
        </button>

        <button
          @click="activeTab = 'faq'"
          :class="activeTab === 'faq' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-sm font-extrabold' : 'text-slate-600 dark:text-slate-400 font-bold hover:text-slate-900 dark:hover:text-white'"
          class="flex-1 min-w-[170px] py-2.5 px-4 rounded-xl text-xs transition cursor-pointer flex items-center justify-center gap-2 shrink-0"
        >
          <HelpCircle class="w-4 h-4" />
          <span>FAQ & Ketentuan SLA</span>
        </button>
      </div>

      <!-- TAB 1: ALUR KERJA TERPADU END-TO-END -->
      <div v-if="activeTab === 'workflow'" class="space-y-6">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
          <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
            <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
              <Workflow class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
              <span>Alur Kerja Penjualan Software Agensi (Quotation-First Flow)</span>
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
              Panduan langkah demi langkah menggunakan DevCalc secara efisien mulai dari prospek klien, kalkulasi harga CPQ, pemantauan negosiasi, hingga deal disetujui (*Closing Won*).
            </p>
          </div>

          <div class="space-y-6 pt-1">
            <!-- Step 1 -->
            <div class="flex gap-4 items-start">
              <div class="w-9 h-9 rounded-2xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-md shadow-indigo-600/30">
                1
              </div>
              <div class="space-y-1.5 flex-1">
                <div class="flex items-center justify-between">
                  <h4 class="text-sm font-black text-slate-900 dark:text-white">
                    Langkah 1: Daftarkan Klien B2B & PIC Pengambil Keputusan
                  </h4>
                  <Link href="/clients" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                    <span>Menu Klien</span> &rarr;
                  </Link>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                  Buka menu <b>Klien & Kontak</b> (`/clients`). Catat nama perusahaan klien, industri, status prospek, kontak utama (*Primary PIC*), nomor WhatsApp, serta email resmi.
                </p>
              </div>
            </div>

            <!-- Step 2 -->
            <div class="flex gap-4 items-start">
              <div class="w-9 h-9 rounded-2xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-md shadow-indigo-600/30">
                2
              </div>
              <div class="space-y-1.5 flex-1">
                <div class="flex items-center justify-between">
                  <h4 class="text-sm font-black text-slate-900 dark:text-white">
                    Langkah 2: Buat Proposal Penawaran Harga CPQ (Wizard 3 Langkah)
                  </h4>
                  <Link href="/projects/create" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                    <span>+ Buat Penawaran</span> &rarr;
                  </Link>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                  Buka menu <b>Penawaran Harga</b> (`/projects/create`):
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-1.5 text-xs">
                  <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/70 dark:border-slate-700/70 space-y-1">
                    <div class="font-bold text-slate-900 dark:text-white">1. Pilih Klien & Profil</div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400">Pilih klien CRM, tentukan kategori sistem & target timeline pengerjaan.</div>
                  </div>
                  <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/70 dark:border-slate-700/70 space-y-1">
                    <div class="font-bold text-slate-900 dark:text-white">2. Pilih Modul Fitur</div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400">Pilih modul dari master data & atur bobot kompleksitas (0.8x - 2.0x).</div>
                  </div>
                  <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/70 dark:border-slate-700/70 space-y-1">
                    <div class="font-bold text-slate-900 dark:text-white">3. Skema & Server</div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400">Tentukan Beli Putus vs SaaS, sewa server Cloud VPS, SLA, & terbitkan penawaran.</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step 3 -->
            <div class="flex gap-4 items-start">
              <div class="w-9 h-9 rounded-2xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-md shadow-indigo-600/30">
                3
              </div>
              <div class="space-y-1.5 flex-1">
                <div class="flex items-center justify-between">
                  <h4 class="text-sm font-black text-slate-900 dark:text-white">
                    Langkah 3: Tracking Deal Otomatis di Sales Kanban Board
                  </h4>
                  <Link href="/deals" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                    <span>Buka Kanban</span> &rarr;
                  </Link>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                  Begitu penawaran diterbitkan, sistem <b>otomatis men-generate kartu Deal</b> di Kanban (`/deals`). Geser kartu deal sesuai kemajuan negosiasi klien (dari <i>Proposal Terkirim 60%</i> &rarr; <i>Negosiasi & Review 80%</i> &rarr; <i>Closed Won 100%</i>).
                </p>
              </div>
            </div>

            <!-- Post Closing -->
            <div class="flex gap-4 items-start pt-2 border-t border-slate-100 dark:border-slate-800">
              <div class="w-9 h-9 rounded-2xl bg-emerald-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-md shadow-emerald-600/30">
                <CheckCircle2 class="w-5 h-5" />
              </div>
              <div class="space-y-1.5 flex-1">
                <h4 class="text-sm font-black text-slate-900 dark:text-white">
                  Pasca Closing: Cetak PDF Resmi, Adendum & Ekspor Laporan
                </h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                  Cetak surat penawaran resmi berformat PDF. Bila terdapat penambahan fitur atau perpanjangan kontrak di masa depan, gunakan tombol <b>Buat Adendum</b> untuk menghasilkan dokumen amandemen resmi yang terhubung ke nomor penawaran induk.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB 2: CRM & SALES PIPELINE 5 TAHAP -->
      <div v-if="activeTab === 'crm'" class="space-y-6">
        
        <!-- 5 Stages Explained Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
          <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
            <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
              <Kanban class="w-5 h-5 text-purple-600 dark:text-purple-400" />
              <span>5 Tahapan Sales Pipeline Kanban (CPQ-Driven CRM)</span>
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
              Setiap tahapan memiliki bobot probabilitas yang digunakan untuk menghitung nilai pendapatan tertimbang (*Weighted Revenue Pipeline*).
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-5 gap-3 pt-1">
            
            <!-- Stage 1 -->
            <div class="p-4 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 space-y-2">
              <div class="flex items-center justify-between">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                <span class="text-[10px] font-black px-1.5 py-0.5 rounded-md bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300">30% Prob</span>
              </div>
              <div class="font-black text-xs text-slate-900 dark:text-white">1. Scoping & Draf</div>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                Penawaran berstatus *Draft*. Tim sedang mematangkan ruang lingkup modul teknis bersama klien.
              </p>
            </div>

            <!-- Stage 2 -->
            <div class="p-4 rounded-2xl bg-blue-50/50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800 space-y-2">
              <div class="flex items-center justify-between">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                <span class="text-[10px] font-black px-1.5 py-0.5 rounded-md bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">60% Prob</span>
              </div>
              <div class="font-black text-xs text-slate-900 dark:text-white">2. Proposal Terkirim</div>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                Dokumen penawaran harga resmi (*Official*) sudah diterbitkan dan dikirimkan ke PIC klien.
              </p>
            </div>

            <!-- Stage 3 -->
            <div class="p-4 rounded-2xl bg-purple-50/50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800 space-y-2">
              <div class="flex items-center justify-between">
                <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                <span class="text-[10px] font-black px-1.5 py-0.5 rounded-md bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300">80% Prob</span>
              </div>
              <div class="font-black text-xs text-slate-900 dark:text-white">3. Negosiasi & Review</div>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                Klien setuju fitur; sedang negosiasi termin pembayaran, review legal NDA/kontrak, & approval PO/SPK.
              </p>
            </div>

            <!-- Stage 4 -->
            <div class="p-4 rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 space-y-2">
              <div class="flex items-center justify-between">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span class="text-[10px] font-black px-1.5 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300">100% Won</span>
              </div>
              <div class="font-black text-xs text-slate-900 dark:text-white">4. Closed Won</div>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                Penawaran disepakati resmi, kontrak/PO terbit, dan proyek siap masuk ke fase eksekusi engineering.
              </p>
            </div>

            <!-- Stage 5 -->
            <div class="p-4 rounded-2xl bg-rose-50/50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 space-y-2">
              <div class="flex items-center justify-between">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                <span class="text-[10px] font-black px-1.5 py-0.5 rounded-md bg-rose-100 dark:bg-rose-900 text-rose-700 dark:text-rose-300">0% Lost</span>
              </div>
              <div class="font-black text-xs text-slate-900 dark:text-white">5. Closed Lost</div>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                Deal dibatalkan (misal: kendala budget atau penundaan proyek) beserta catatan alasan kegagalan.
              </p>
            </div>

          </div>
        </div>

        <!-- CRM 360 Feature Explanation -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-2.5">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-xl bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-400 flex items-center justify-center">
                <Users class="w-4 h-4" />
              </div>
              <h4 class="text-xs font-black text-slate-900 dark:text-white">Multi-PIC & Kontak Klien</h4>
            </div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
              Anda dapat mencatat lebih dari satu PIC per perusahaan (misal: Direktur Utama, Tech Lead, Manajer Finance) lengkap dengan nomor WhatsApp untuk memudahkan komunikasi.
            </p>
          </div>

          <div class="p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm space-y-2.5">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                <Clock class="w-4 h-4" />
              </div>
              <h4 class="text-xs font-black text-slate-900 dark:text-white">Log Aktivitas & Riwayat Follow-up</h4>
            </div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
              Setiap panggilan telepon, chat WhatsApp, meeting scoping, atau pengiriman proposal dicatat dalam timeline terpadu sehingga seluruh tim sales memiliki visibilitas penuh.
            </p>
          </div>
        </div>

      </div>

      <!-- TAB 3: RUMUS PERHITUNGAN BISNIS (CPQ ENGINE) -->
      <div v-if="activeTab === 'formulas'" class="space-y-6">
        
        <!-- Beli Putus Formula Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
          <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
              <Calculator class="w-5 h-5" />
            </div>
            <div>
              <h3 class="text-base font-extrabold text-slate-900 dark:text-white">1. Rumus Skema Beli Putus (One-Off Project)</h3>
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
              <h3 class="text-base font-extrabold text-slate-900 dark:text-white">2. Rumus Skema Langganan SaaS (Recurring)</h3>
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

      <!-- TAB 4: FAQ & KETENTUAN SLA -->
      <div v-if="activeTab === 'faq'" class="space-y-6">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
          <div class="border-b border-slate-200 dark:border-slate-800 pb-3">
            <h3 class="text-base font-black text-slate-900 dark:text-white">
              Pertanyaan Umum (FAQ) & Ketentuan Operasional
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
              Jawaban cepat untuk pertanyaan teknis, ketentuan garansi SLA, dan integrasi CRM.
            </p>
          </div>

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
                <div class="flex items-center gap-2">
                  <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                    {{ faq.category }}
                  </span>
                  <span>{{ faq.question }}</span>
                </div>
                <ChevronUp v-if="openFaq === faq.id" class="w-4 h-4 text-indigo-600 shrink-0" />
                <ChevronDown v-else class="w-4 h-4 text-slate-400 shrink-0" />
              </button>

              <div v-if="openFaq === faq.id" class="p-5 text-xs leading-relaxed text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 whitespace-pre-line">
                {{ faq.answer }}
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>
