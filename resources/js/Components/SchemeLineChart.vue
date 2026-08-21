<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
  oneOffCount: {
    type: Number,
    default: 0
  },
  subscriptionCount: {
    type: Number,
    default: 0
  },
  recentProjects: {
    type: Array,
    default: () => []
  }
});

const activeSeries = ref('all'); // 'all', 'one_off', 'subscription'

const total = computed(() => (props.oneOffCount + props.subscriptionCount) || 1);
const oneOffPercent = computed(() => Math.round((props.oneOffCount / total.value) * 100));
const subPercent = computed(() => Math.round((props.subscriptionCount / total.value) * 100));

// Process trend points from recent projects or sample timeline
const chartData = computed(() => {
  const list = [...props.recentProjects].reverse();
  if (list.length < 2) {
    return [
      { label: 'P-1', one_off: 1, subscription: 2 },
      { label: 'P-2', one_off: 2, subscription: 3 },
      { label: 'P-3', one_off: props.oneOffCount || 1, subscription: props.subscriptionCount || 4 },
    ];
  }

  let accOneOff = 0;
  let accSub = 0;
  return list.map((item, idx) => {
    if (item.billing_type === 'subscription') {
      accSub++;
    } else {
      accOneOff++;
    }
    return {
      label: item.code || `P-${idx + 1}`,
      one_off: accOneOff,
      subscription: accSub,
      client: item.client_name,
    };
  });
});

// Ultra-Sleek SVG Dimensions
const width = 600;
const height = 80;
const padding = 16;

const maxY = computed(() => {
  const max = Math.max(
    ...chartData.value.map(d => d.one_off),
    ...chartData.value.map(d => d.subscription),
    5
  );
  return Math.ceil(max * 1.2);
});

const pointsOneOff = computed(() => {
  const len = chartData.value.length;
  return chartData.value.map((d, i) => {
    const x = padding + (i * (width - 2 * padding)) / Math.max(len - 1, 1);
    const y = height - padding - ((d.one_off / maxY.value) * (height - 2 * padding));
    return { x, y, val: d.one_off, label: d.label };
  });
});

const pointsSub = computed(() => {
  const len = chartData.value.length;
  return chartData.value.map((d, i) => {
    const x = padding + (i * (width - 2 * padding)) / Math.max(len - 1, 1);
    const y = height - padding - ((d.subscription / maxY.value) * (height - 2 * padding));
    return { x, y, val: d.subscription, label: d.label };
  });
});

function createSmoothPath(points) {
  if (!points.length) return '';
  if (points.length === 1) return `M ${points[0].x} ${points[0].y}`;

  let path = `M ${points[0].x} ${points[0].y}`;
  for (let i = 0; i < points.length - 1; i++) {
    const curr = points[i];
    const next = points[i + 1];
    const mx = (curr.x + next.x) / 2;
    path += ` C ${mx} ${curr.y}, ${mx} ${next.y}, ${next.x} ${next.y}`;
  }
  return path;
}

const pathOneOff = computed(() => createSmoothPath(pointsOneOff.value));
const pathSub = computed(() => createSmoothPath(pointsSub.value));

const areaOneOff = computed(() => {
  if (!pointsOneOff.value.length) return '';
  const first = pointsOneOff.value[0];
  const last = pointsOneOff.value[pointsOneOff.value.length - 1];
  return `${pathOneOff.value} L ${last.x} ${height - padding} L ${first.x} ${height - padding} Z`;
});

const areaSub = computed(() => {
  if (!pointsSub.value.length) return '';
  const first = pointsSub.value[0];
  const last = pointsSub.value[pointsSub.value.length - 1];
  return `${pathSub.value} L ${last.x} ${height - padding} L ${first.x} ${height - padding} Z`;
});
</script>

<template>
  <div class="space-y-2.5">
    <!-- Compact Legend & Series Toggle Header -->
    <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
      <div class="flex items-center gap-2">
        <!-- One-Off Legend -->
        <button
          @click="activeSeries = activeSeries === 'one_off' ? 'all' : 'one_off'"
          class="flex items-center gap-1.5 font-bold transition px-2 py-0.5 rounded-md text-[10px] cursor-pointer"
          :class="activeSeries === 'one_off' || activeSeries === 'all' ? 'text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800' : 'text-slate-400 opacity-50 border border-transparent'"
        >
          <span class="w-1.5 h-1.5 rounded-full bg-sky-500 shadow-sm shadow-sky-500/50"></span>
          <span>Beli Putus: {{ oneOffCount }} ({{ oneOffPercent }}%)</span>
        </button>

        <!-- SaaS Legend -->
        <button
          @click="activeSeries = activeSeries === 'subscription' ? 'all' : 'subscription'"
          class="flex items-center gap-1.5 font-bold transition px-2 py-0.5 rounded-md text-[10px] cursor-pointer"
          :class="activeSeries === 'subscription' || activeSeries === 'all' ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800' : 'text-slate-400 opacity-50 border border-transparent'"
        >
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500/50"></span>
          <span>SaaS: {{ subscriptionCount }} ({{ subPercent }}%)</span>
        </button>
      </div>

      <span class="text-[10px] text-slate-400 font-semibold">Grafik Tren Skema</span>
    </div>

    <!-- Ultra-Sleek SVG Line Chart Canvas -->
    <div class="relative w-full overflow-hidden rounded-xl bg-slate-50/60 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800 p-2">
      <svg :viewBox="`0 0 ${width} ${height}`" class="w-full h-auto overflow-visible">
        <defs>
          <!-- One Off Sky Gradient -->
          <linearGradient id="skyGrad" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#38bdf8" stop-opacity="0.25" />
            <stop offset="100%" stop-color="#38bdf8" stop-opacity="0.0" />
          </linearGradient>

          <!-- SaaS Emerald Gradient -->
          <linearGradient id="emeraldGrad" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#10b981" stop-opacity="0.25" />
            <stop offset="100%" stop-color="#10b981" stop-opacity="0.0" />
          </linearGradient>
        </defs>

        <!-- Horizontal Grid Lines -->
        <line x1="10" y1="15" :x2="width - 10" y2="15" class="stroke-slate-200 dark:stroke-slate-800/80" stroke-dasharray="3 3" stroke-width="0.75" />
        <line x1="10" y1="40" :x2="width - 10" y2="40" class="stroke-slate-200 dark:stroke-slate-800/80" stroke-dasharray="3 3" stroke-width="0.75" />
        <line x1="10" y1="65" :x2="width - 10" y2="65" class="stroke-slate-200 dark:stroke-slate-800/80" stroke-dasharray="3 3" stroke-width="0.75" />

        <!-- Area Fills -->
        <path v-if="activeSeries === 'all' || activeSeries === 'one_off'" :d="areaOneOff" fill="url(#skyGrad)" />
        <path v-if="activeSeries === 'all' || activeSeries === 'subscription'" :d="areaSub" fill="url(#emeraldGrad)" />

        <!-- Line Paths (Ultra-Sleek 1.5px Width) -->
        <path
          v-if="activeSeries === 'all' || activeSeries === 'one_off'"
          :d="pathOneOff"
          fill="none"
          stroke="#0284c7"
          stroke-width="1.5"
          stroke-linecap="round"
          stroke-linejoin="round"
        />

        <path
          v-if="activeSeries === 'all' || activeSeries === 'subscription'"
          :d="pathSub"
          fill="none"
          stroke="#059669"
          stroke-width="1.5"
          stroke-linecap="round"
          stroke-linejoin="round"
        />

        <!-- Data Point Circles - One Off (Ultra-Sleek r=1.75) -->
        <g v-if="activeSeries === 'all' || activeSeries === 'one_off'">
          <circle
            v-for="(pt, i) in pointsOneOff"
            :key="`one-${i}`"
            :cx="pt.x"
            :cy="pt.y"
            r="1.75"
            class="fill-white stroke-sky-600 dark:stroke-sky-400 stroke-[1.5] transition duration-200 hover:r-3 cursor-pointer"
          >
            <title>Beli Putus: {{ pt.val }} ({{ pt.label }})</title>
          </circle>
        </g>

        <!-- Data Point Circles - SaaS (Ultra-Sleek r=1.75) -->
        <g v-if="activeSeries === 'all' || activeSeries === 'subscription'">
          <circle
            v-for="(pt, i) in pointsSub"
            :key="`sub-${i}`"
            :cx="pt.x"
            :cy="pt.y"
            r="1.75"
            class="fill-white stroke-emerald-600 dark:stroke-emerald-400 stroke-[1.5] transition duration-200 hover:r-3 cursor-pointer"
          >
            <title>SaaS: {{ pt.val }} ({{ pt.label }})</title>
          </circle>
        </g>

        <!-- X Axis Labels (Ultra-Sleek 7.5px font) -->
        <text
          v-for="(pt, i) in pointsSub"
          :key="`lbl-${i}`"
          :x="pt.x"
          :y="height - 3"
          text-anchor="middle"
          class="text-[7.5px] font-mono font-medium fill-slate-400 dark:fill-slate-500"
        >
          {{ pt.label }}
        </text>
      </svg>
    </div>

    <!-- Sleek Bottom Summary Info -->
    <div class="px-3 py-1.5 rounded-lg bg-indigo-50/40 dark:bg-indigo-950/20 border border-indigo-100/50 dark:border-indigo-900/40 text-indigo-900 dark:text-indigo-200 text-[11px] flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="font-bold text-[9px] uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Total Kontrak:</span>
        <span class="font-extrabold text-slate-900 dark:text-white">{{ total }} Dokumen Penawaran</span>
      </div>
      <span class="text-[9px] font-semibold text-slate-500 dark:text-slate-400">Dominan: {{ subPercent >= oneOffPercent ? 'SaaS' : 'Beli Putus' }}</span>
    </div>
  </div>
</template>
