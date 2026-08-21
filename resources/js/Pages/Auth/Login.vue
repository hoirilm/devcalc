<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import { 
  FileText, 
  LogIn, 
  Mail, 
  Lock, 
  Eye, 
  EyeOff, 
  Check, 
  ShieldCheck
} from 'lucide-vue-next';

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const showPassword = ref(false);

function submit() {
  form.post('/login');
}
</script>

<template>
  <Head title="Masuk - DevCalc Quotation System" />

  <div class="min-h-screen relative overflow-hidden bg-slate-950 text-slate-100 flex items-center justify-center p-4 select-none">
    
    <!-- Dynamic Glowing Backdrop Ambient Orbs -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-600/20 rounded-full blur-[128px] pointer-events-none animate-pulse"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-purple-600/20 rounded-full blur-[128px] pointer-events-none animate-pulse delay-1000"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-emerald-500/10 rounded-full blur-[140px] pointer-events-none"></div>

    <!-- Main Floating Glass Container -->
    <div class="relative z-10 w-full max-w-md bg-slate-900/80 backdrop-blur-2xl border border-slate-800/80 rounded-3xl p-8 shadow-2xl shadow-slate-950/80 space-y-6">
      
      <!-- Brand Logo Header -->
      <div class="text-center space-y-3">
        <div class="relative inline-flex items-center justify-center">
          <div class="absolute inset-0 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 blur-md opacity-60 animate-pulse"></div>
          <div class="relative w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 to-indigo-500 flex items-center justify-center text-white shadow-xl shadow-indigo-500/30 border border-indigo-400/30">
            <FileText class="w-7 h-7" />
          </div>
        </div>
        <div>
          <h1 class="text-2xl font-black text-white tracking-tight">
            DevCalc
          </h1>
          <p class="text-xs text-slate-400 font-medium mt-1">Software Quotation Estimator System</p>
        </div>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="submit" class="space-y-4 pt-2">
        
        <!-- Email Input -->
        <div class="space-y-1.5">
          <label class="block text-xs font-semibold text-slate-300">Email Administrator</label>
          <div class="relative flex items-center">
            <Mail class="w-4 h-4 text-slate-500 absolute left-3.5 pointer-events-none" />
            <input
              v-model="form.email"
              type="email"
              required
              placeholder="admin@devcalc.id"
              class="w-full pl-10 pr-4 py-2.5 bg-slate-800/70 text-white text-xs font-semibold rounded-xl border border-slate-700/80 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition placeholder-slate-500"
            />
          </div>
          <span v-if="form.errors.email" class="text-[11px] text-rose-400 font-medium mt-1 block">
            {{ form.errors.email }}
          </span>
        </div>

        <!-- Password Input -->
        <div class="space-y-1.5">
          <label class="block text-xs font-semibold text-slate-300">Kata Sandi</label>
          <div class="relative flex items-center">
            <Lock class="w-4 h-4 text-slate-500 absolute left-3.5 pointer-events-none" />
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              required
              placeholder="••••••••"
              class="w-full pl-10 pr-10 py-2.5 bg-slate-800/70 text-white text-xs font-semibold rounded-xl border border-slate-700/80 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition placeholder-slate-500"
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-3 text-slate-500 hover:text-slate-300 transition cursor-pointer p-1"
            >
              <Eye v-if="!showPassword" class="w-4 h-4" />
              <EyeOff v-else class="w-4 h-4" />
            </button>
          </div>
          <span v-if="form.errors.password" class="text-[11px] text-rose-400 font-medium mt-1 block">
            {{ form.errors.password }}
          </span>
        </div>

        <!-- Remember Me Checkbox -->
        <div class="flex items-center justify-between pt-1">
          <label class="flex items-center gap-2 cursor-pointer select-none" @click.prevent="form.remember = !form.remember">
            <div 
              class="w-4 h-4 rounded-md border transition-all duration-150 flex items-center justify-center cursor-pointer"
              :class="form.remember 
                ? 'bg-indigo-600 border-indigo-600 text-white shadow-xs shadow-indigo-600/40' 
                : 'border-slate-700 bg-slate-800/80'"
            >
              <Check v-if="form.remember" class="w-3 h-3 stroke-[3]" />
            </div>
            <span class="text-xs font-medium text-slate-300">Ingat Sesi Saya</span>
          </label>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="form.processing"
          class="w-full py-3 bg-gradient-to-r from-indigo-600 via-indigo-500 to-indigo-600 hover:from-indigo-500 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 cursor-pointer active:scale-98"
        >
          <LogIn v-if="!form.processing" class="w-4 h-4" />
          <span>{{ form.processing ? 'Memverifikasi Akses...' : 'Masuk ke Sistem Estimator' }}</span>
        </button>
      </form>

      <!-- Footer Info -->
      <div class="pt-4 border-t border-slate-800/80 text-center text-slate-500 text-[11px] flex items-center justify-center gap-1.5">
        <ShieldCheck class="w-3.5 h-3.5 text-emerald-500" />
        <span>Kredensial Terenkripsi & Protected System</span>
      </div>

    </div>
  </div>
</template>
