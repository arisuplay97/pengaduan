@extends('layouts.nolana')

@section('content')
{{-- ═══════════════════════════════════════════════════════════════
     STATISTIK & ANALISIS GANGGUAN — SaaS Premium Dashboard
     Light Mode • Pastel • Soft • Tailwind CSS + ApexCharts
     ═══════════════════════════════════════════════════════════════ --}}

<div class="bg-gray-50/50 min-h-screen" id="statsDashboard">
<div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

  {{-- ══════════════════════════════════════════════════════════
       HEADER
  ══════════════════════════════════════════════════════════ --}}
  <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Statistik & Analisis Gangguan</h1>
      <p class="text-[13px] text-slate-400 mt-1">Monitoring real-time gangguan jaringan PDAM</p>
    </div>
    <div class="flex items-center gap-4">
      <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-full shadow-sm border border-slate-100">
        <span class="relative flex h-2.5 w-2.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span></span>
        <span class="text-xs font-semibold text-slate-500">Live</span>
      </div>
      <span class="text-xs text-slate-400 font-medium" id="lastUpdateTime">Update: --:--</span>
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════════════
       FILTER BAR
  ══════════════════════════════════════════════════════════ --}}
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100/80 p-5">
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
      <select id="fBulan" class="h-10 w-full rounded-xl border border-slate-200/60 bg-slate-50/50 px-3 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
        @for($m=1;$m<=12;$m++)
          <option value="{{$m}}" {{$m==now()->month?'selected':''}}>
            {{\Carbon\Carbon::createFromDate(null,$m,1)->translatedFormat('F')}}
          </option>
        @endfor
      </select>
      <select id="fTahun" class="h-10 w-full rounded-xl border border-slate-200/60 bg-slate-50/50 px-3 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
        @for($y=now()->year;$y>=now()->year-3;$y--)
          <option value="{{$y}}">{{$y}}</option>
        @endfor
      </select>
      <select id="fKecamatan" class="h-10 w-full rounded-xl border border-slate-200/60 bg-slate-50/50 px-3 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
        <option value="">Semua Kecamatan</option>
        @foreach($kecamatans as $kec)
          <option value="{{$kec->id}}">{{$kec->nama}}</option>
        @endforeach
      </select>
      <select id="fStatus" class="h-10 w-full rounded-xl border border-slate-200/60 bg-slate-50/50 px-3 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
        <option value="">Semua Status</option>
        <option value="working">Proses</option>
        <option value="done">Selesai</option>
        <option value="pending">Pending</option>
      </select>
      <select id="fJenis" class="h-10 w-full rounded-xl border border-slate-200/60 bg-slate-50/50 px-3 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
        <option value="">Semua Jenis</option>
        @foreach($categories as $cat)
          <option value="{{$cat}}">{{$cat}}</option>
        @endforeach
      </select>
      <button onclick="applyFilters()" class="h-10 w-full bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-sm shadow-indigo-600/20">
        <i class="ph-bold ph-funnel-simple text-sm"></i>
        Terapkan
      </button>
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════════════
       PRIMARY KPI CARDS (4 cards)
  ══════════════════════════════════════════════════════════ --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

    {{-- KPI 1: Total Gangguan Bulan Ini --}}
    <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100/40 rounded-2xl p-5 border border-blue-100/60 cursor-default transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-blue-500/10">
      <div class="flex items-start justify-between mb-4">
        <div class="w-11 h-11 rounded-xl bg-blue-500/10 flex items-center justify-center">
          <i class="ph-fill ph-chart-bar text-xl text-blue-600"></i>
        </div>
        <span id="kpiTrendBadge1" class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-white/60 text-slate-400 backdrop-blur-sm">— 0%</span>
      </div>
      <p class="text-3xl font-extrabold text-slate-800 tracking-tight" id="kpiTotalBulan">0</p>
      <p class="text-[12px] font-semibold text-blue-600/70 mt-1">Total Gangguan Bulan Ini</p>
      <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed" id="kpiTrendText1">Tidak ada perubahan dari bulan lalu</p>
    </div>

    {{-- KPI 2: SLA < 24 Jam --}}
    <div class="group relative bg-gradient-to-br from-emerald-50 to-emerald-100/40 rounded-2xl p-5 border border-emerald-100/60 cursor-default transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-emerald-500/10">
      <div class="flex items-start justify-between mb-4">
        <div class="w-11 h-11 rounded-xl bg-emerald-500/10 flex items-center justify-center">
          <i class="ph-fill ph-shield-check text-xl text-emerald-600"></i>
        </div>
        <span id="kpiTrendBadge5" class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-white/60 text-slate-400 backdrop-blur-sm">—</span>
      </div>
      <p class="text-3xl font-extrabold text-slate-800 tracking-tight"><span id="kpiSLA">0.0</span><span class="text-lg font-bold text-slate-300 ml-0.5">%</span></p>
      <p class="text-[12px] font-semibold text-emerald-600/70 mt-1">SLA &lt; 24 Jam</p>
      <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed" id="kpiTrendText5">0 dari 0 selesai &lt; 24 jam</p>
    </div>

    {{-- KPI 3: % Penyelesaian --}}
    <div class="group relative bg-gradient-to-br from-violet-50 to-violet-100/40 rounded-2xl p-5 border border-violet-100/60 cursor-default transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-violet-500/10">
      <div class="flex items-start justify-between mb-4">
        <div class="w-11 h-11 rounded-xl bg-violet-500/10 flex items-center justify-center">
          <i class="ph-fill ph-check-circle text-xl text-violet-600"></i>
        </div>
        <span id="kpiTrendBadge6" class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-white/60 text-slate-400 backdrop-blur-sm">—</span>
      </div>
      <p class="text-3xl font-extrabold text-slate-800 tracking-tight"><span id="kpiCompletion">0.0</span><span class="text-lg font-bold text-slate-300 ml-0.5">%</span></p>
      <p class="text-[12px] font-semibold text-violet-600/70 mt-1">Penyelesaian</p>
      <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed" id="kpiTrendText6">0 selesai dari 0 total laporan</p>
    </div>

    {{-- KPI 4: Rata-rata Selesai --}}
    <div class="group relative bg-gradient-to-br from-amber-50 to-amber-100/40 rounded-2xl p-5 border border-amber-100/60 cursor-default transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-amber-500/10">
      <div class="flex items-start justify-between mb-4">
        <div class="w-11 h-11 rounded-xl bg-amber-500/10 flex items-center justify-center">
          <i class="ph-fill ph-clock text-xl text-amber-600"></i>
        </div>
        <span id="kpiTrendBadge4" class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-white/60 text-slate-400 backdrop-blur-sm">—</span>
      </div>
      <p class="text-3xl font-extrabold text-slate-800 tracking-tight"><span id="kpiAvgCompletion">0.0</span> <span class="text-lg font-bold text-slate-300">jam</span></p>
      <p class="text-[12px] font-semibold text-amber-600/70 mt-1">Rata-rata Selesai</p>
      <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed" id="kpiTrendText4">Waktu penyelesaian rata-rata</p>
    </div>

  </div>

  {{-- ══════════════════════════════════════════════════════════
       SECONDARY KPI (2 mini cards)
  ══════════════════════════════════════════════════════════ --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    {{-- Total Tahun --}}
    <div class="group relative bg-gradient-to-br from-indigo-50 to-indigo-100/30 rounded-2xl p-5 border border-indigo-100/50 cursor-default transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md hover:shadow-indigo-500/8">
      <div class="flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center flex-shrink-0">
          <i class="ph-fill ph-calendar-blank text-lg text-indigo-500"></i>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-[11px] font-bold text-indigo-600/60 uppercase tracking-wider">Total Tahun Ini</p>
          <p class="text-2xl font-extrabold text-slate-800 mt-0.5" id="kpiTotalTahun">0</p>
        </div>
        <span id="kpiTrendBadge2" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/60 text-indigo-600 backdrop-blur-sm flex-shrink-0">0 total</span>
      </div>
    </div>
    {{-- Rata-rata per Hari --}}
    <div class="group relative bg-gradient-to-br from-sky-50 to-sky-100/30 rounded-2xl p-5 border border-sky-100/50 cursor-default transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md hover:shadow-sky-500/8">
      <div class="flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-sky-500/10 flex items-center justify-center flex-shrink-0">
          <i class="ph-fill ph-chart-line-up text-lg text-sky-500"></i>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-[11px] font-bold text-sky-600/60 uppercase tracking-wider">Rata-rata / Hari</p>
          <p class="text-2xl font-extrabold text-slate-800 mt-0.5"><span id="kpiAvgDay">0.0</span></p>
        </div>
        <span id="kpiTrendBadge3" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/60 text-sky-600 backdrop-blur-sm flex-shrink-0">0/hr</span>
      </div>
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════════════
       TREND CHART (2/3) + DONUT (1/3)
  ══════════════════════════════════════════════════════════ --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100/80 p-6">
      <div class="flex items-center justify-between mb-5">
        <div>
          <h2 class="text-sm font-bold text-slate-800">Tren Gangguan Harian</h2>
          <p class="text-xs text-slate-400 mt-0.5">Perbandingan 30 hari terakhir</p>
        </div>
        <div class="flex items-center gap-4 text-xs text-slate-400">
          <span class="flex items-center gap-1.5"><span class="w-3 h-0.5 rounded-full bg-indigo-500 inline-block"></span> Bulan Ini</span>
          <span class="flex items-center gap-1.5"><span class="w-3 h-0.5 rounded-full bg-slate-300 inline-block"></span> Bulan Lalu</span>
        </div>
      </div>
      <div id="chartTrend" style="height:320px;"></div>
    </div>
    <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-slate-100/80 p-6">
      <h2 class="text-sm font-bold text-slate-800">Status Penyelesaian</h2>
      <p class="text-xs text-slate-400 mt-0.5 mb-3">Distribusi status pekerjaan</p>
      <div id="chartDonut" style="height:240px;"></div>
      <div id="donutLegend" class="space-y-2 mt-3"></div>
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════════════
       3-COLUMN: Kecamatan + Kategori + Risk Table
  ══════════════════════════════════════════════════════════ --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100/80 p-6">
      <h2 class="text-sm font-bold text-slate-800 mb-4">Gangguan per Kecamatan</h2>
      <div id="chartKecamatan" style="height:220px;"></div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100/80 p-6">
      <h2 class="text-sm font-bold text-slate-800 mb-4">Kategori Gangguan</h2>
      <div id="chartCategory" style="height:220px;"></div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100/80 p-6">
      <h2 class="text-sm font-bold text-slate-800 mb-4">Top 5 Area Risiko</h2>
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-slate-100">
            <th class="pb-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">#</th>
            <th class="pb-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">Kecamatan</th>
            <th class="pb-3 text-right text-[10px] font-bold uppercase tracking-wider text-slate-400">Total</th>
            <th class="pb-3 text-right text-[10px] font-bold uppercase tracking-wider text-slate-400">Trend</th>
          </tr>
        </thead>
        <tbody id="riskTableBody">
          <tr><td colspan="4" class="py-8 text-center text-slate-400 text-xs">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════════════
       HOURLY + DAILY DISTRIBUTION
  ══════════════════════════════════════════════════════════ --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100/80 p-6">
      <h2 class="text-sm font-bold text-slate-800">Distribusi Jam</h2>
      <p class="text-xs text-slate-400 mt-0.5 mb-4">Pola gangguan per jam</p>
      <div id="chartHourly" style="height:200px;"></div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100/80 p-6">
      <h2 class="text-sm font-bold text-slate-800">Distribusi Hari</h2>
      <p class="text-xs text-slate-400 mt-0.5 mb-4">Senin — Minggu</p>
      <div id="chartDaily" style="height:200px;"></div>
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════════════
       MAP (2/3) + TIME ANALYSIS (1/3)
  ══════════════════════════════════════════════════════════ --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100/80 p-6">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h2 class="text-sm font-bold text-slate-800">Peta Konsentrasi</h2>
          <p class="text-xs text-slate-400 mt-0.5"><span id="mapCount">0</span> titik</p>
        </div>
        <div class="inline-flex bg-slate-100 rounded-xl overflow-hidden p-0.5">
          <button id="btnMarker" onclick="setMapMode('marker')" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-slate-700 shadow-sm transition-all">Marker</button>
          <button id="btnHeatmap" onclick="setMapMode('heatmap')" class="px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-400 hover:text-slate-600 transition-all">Heatmap</button>
        </div>
      </div>
      <div id="statsMap" style="height:320px;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;"></div>
    </div>

    <div class="lg:col-span-1 flex flex-col gap-5">
      {{-- Time Analysis --}}
      <div class="bg-white rounded-2xl shadow-sm border border-slate-100/80 p-6 flex-1">
        <h2 class="text-sm font-bold text-slate-800 mb-4">Analisis Waktu</h2>
        <div class="grid grid-cols-2 gap-3">
          <div class="bg-slate-50/80 rounded-xl p-4">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Rata-rata Respon</p>
            <p class="text-2xl font-extrabold text-slate-800"><span id="timeResponse">0</span> <span id="timeResponseUnit" class="text-xs font-semibold text-slate-400">mnt</span></p>
          </div>
          <div class="bg-slate-50/80 rounded-xl p-4">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Rata-rata Selesai</p>
            <p class="text-2xl font-extrabold text-slate-800"><span id="timeCompletion">0</span> <span id="timeCompletionUnit" class="text-xs font-semibold text-slate-400">mnt</span></p>
          </div>
          <div class="bg-emerald-50/60 rounded-xl p-4">
            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 mb-2">Tercepat</p>
            <p class="text-2xl font-extrabold text-emerald-600"><span id="timeMin">0</span> <span id="timeMinUnit" class="text-xs font-semibold text-emerald-400">mnt</span></p>
          </div>
          <div class="bg-rose-50/60 rounded-xl p-4">
            <p class="text-[10px] font-bold uppercase tracking-wider text-rose-500 mb-2">Terlama</p>
            <p class="text-2xl font-extrabold text-rose-500"><span id="timeMax">0</span> <span id="timeMaxUnit" class="text-xs font-semibold text-rose-300">mnt</span></p>
          </div>
        </div>
      </div>

      {{-- Export --}}
      <div class="bg-white rounded-2xl shadow-sm border border-slate-100/80 p-5">
        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-3">Export Data</p>
        <div class="flex gap-2">
          <a id="linkCsv" href="{{ route('statistics.export.csv') }}" class="flex-1 h-9 flex items-center justify-center gap-1.5 rounded-xl text-xs font-semibold bg-emerald-500 text-white hover:bg-emerald-600 transition-all shadow-sm shadow-emerald-500/20">
            <i class="ph-bold ph-file-csv text-sm"></i> CSV
          </a>
          <a id="linkExcel" href="{{ route('statistics.export.excel') }}" class="flex-1 h-9 flex items-center justify-center gap-1.5 rounded-xl text-xs font-semibold bg-indigo-500 text-white hover:bg-indigo-600 transition-all shadow-sm shadow-indigo-500/20">
            <i class="ph-bold ph-file-xls text-sm"></i> Excel
          </a>
          <button onclick="window.print()" class="flex-1 h-9 flex items-center justify-center gap-1.5 rounded-xl text-xs font-semibold bg-rose-500 text-white hover:bg-rose-600 transition-all shadow-sm shadow-rose-500/20">
            <i class="ph-bold ph-file-pdf text-sm"></i> PDF
          </button>
        </div>
      </div>
    </div>
  </div>

</div>
</div>

{{-- ═══════════════════════════════════════════════════
     CDN — ApexCharts + Leaflet
═══════════════════════════════════════════════════ --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.0/dist/apexcharts.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

<script>
// ═══════════════════════════════════════════════════════════
//  CONSTANTS
// ═══════════════════════════════════════════════════════════
const C_BLUE    = '#6366f1'; // indigo-500
const C_GREEN   = '#22c55e'; // green-500
const C_AMBER   = '#f59e0b'; // amber-500
const C_RED     = '#ef4444'; // red-500
const C_GRAY    = '#9ca3af'; // gray-400
const C_SLATE   = '#0f172a'; // slate-900

let charts = {};
let statsMap, markerLayer, heatLayer, mapMode = 'marker', heatData = [];

// ═══════════════════════════════════════════════════════════
//  APEX BASE OPTIONS
// ═══════════════════════════════════════════════════════════
const base = {
  chart: { fontFamily: "'Plus Jakarta Sans','Inter',sans-serif", toolbar: { show: false }, background: 'transparent', animations: { enabled: true, easing: 'easeinout', speed: 800 } },
  grid: { borderColor: '#f1f5f9', strokeDashArray: 4, padding: { left: 8 } },
  tooltip: { style: { fontSize: '12px' }, y: { formatter: v => v }, theme: 'light', marker: { show: true }, cssClass: 'apexcharts-tooltip-soft' },
  dataLabels: { enabled: false },
};

// ═══════════════════════════════════════════════════════════
//  COUNT UP
// ═══════════════════════════════════════════════════════════
function countUp(el, target, isFloat = false, dur = 800) {
  if (!el) return;
  const t0 = performance.now();
  (function step(now) {
    const p = Math.min((now - t0) / dur, 1);
    const e = 1 - Math.pow(1 - p, 3);
    const v = target * e;
    el.textContent = isFloat ? v.toFixed(1) : Math.round(v);
    if (p < 1) requestAnimationFrame(step);
  })(t0);
}

// ═══════════════════════════════════════════════════════════
//  INIT CHARTS
// ═══════════════════════════════════════════════════════════
function initCharts() {
  // — Trend Area —
  charts.trend = new ApexCharts(document.getElementById('chartTrend'), {
    ...base,
    chart: { ...base.chart, type: 'area', height: 320 },
    series: [{ name: 'Bulan Ini', data: [] }, { name: 'Bulan Lalu', data: [] }],
    colors: [C_BLUE, '#cbd5e1'],
    stroke: { curve: 'smooth', width: [2.5, 1.5], dashArray: [0, 5] },
    fill: { type: ['gradient', 'solid'], gradient: { shadeIntensity: 1, opacityFrom: 0.12, opacityTo: 0.0, stops: [0, 95] } },
    markers: { size: 0, hover: { size: 6, strokeWidth: 2, strokeColor: '#fff' } },
    xaxis: { categories: [], axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 500 } } },
    yaxis: { min: 0, forceNiceScale: true, labels: { style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 500 }, formatter: v => Math.round(v) } },
    legend: { show: false },
    grid: { ...base.grid, borderColor: '#f8fafc', strokeDashArray: 5 },
    tooltip: { ...base.tooltip, shared: true, intersect: false, x: { show: true, formatter: (v) => 'Hari ke-' + v }, y: { formatter: v => v + ' gangguan' } },
  });
  charts.trend.render();

  // — Donut —
  charts.donut = new ApexCharts(document.getElementById('chartDonut'), {
    ...base,
    chart: { ...base.chart, type: 'donut', height: 240 },
    series: [0, 0, 0],
    labels: ['Selesai', 'Proses', 'Pending'],
    colors: [C_GREEN, C_AMBER, C_GRAY],
    plotOptions: { pie: { donut: { size: '72%', labels: { show: true, name: { fontSize: '12px', color: '#64748b', offsetY: -4 }, value: { fontSize: '28px', fontWeight: 800, color: C_SLATE, offsetY: 4 }, total: { show: true, label: 'Total', fontSize: '11px', color: '#94a3b8', formatter: w => w.globals.seriesTotals.reduce((a, b) => a + b, 0) } } } } },
    legend: { show: false },
    stroke: { width: 3, colors: ['#fff'] },
    tooltip: { ...base.tooltip, y: { formatter: v => v + ' laporan' } },
  });
  charts.donut.render();

  // — Kecamatan Horizontal Bar —
  charts.kecamatan = new ApexCharts(document.getElementById('chartKecamatan'), {
    ...base,
    chart: { ...base.chart, type: 'bar', height: 220 },
    series: [{ name: 'Gangguan', data: [] }],
    colors: [C_BLUE],
    plotOptions: { bar: { horizontal: true, borderRadius: 6, barHeight: '55%' } },
    xaxis: { categories: [], labels: { style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 500 } } },
    yaxis: { labels: { style: { colors: '#334155', fontSize: '11px', fontWeight: 500 } } },
    grid: { ...base.grid, xaxis: { lines: { show: true } }, yaxis: { lines: { show: false } } },
    tooltip: { ...base.tooltip },
  });
  charts.kecamatan.render();

  // — Category Vertical Bar —
  charts.category = new ApexCharts(document.getElementById('chartCategory'), {
    ...base,
    chart: { ...base.chart, type: 'bar', height: 220 },
    series: [{ name: 'Gangguan', data: [] }],
    colors: ['#8b5cf6'],
    plotOptions: { bar: { borderRadius: 8, columnWidth: '45%' } },
    xaxis: { categories: [], labels: { style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 500 }, rotate: -30, rotateAlways: false } },
    yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 500 } } },
    grid: { ...base.grid },
    tooltip: { ...base.tooltip },
  });
  charts.category.render();

  // — Hourly —
  charts.hourly = new ApexCharts(document.getElementById('chartHourly'), {
    ...base,
    chart: { ...base.chart, type: 'bar', height: 200 },
    series: [{ name: 'Gangguan', data: [] }],
    colors: [C_BLUE],
    plotOptions: { bar: { borderRadius: 4, columnWidth: '65%' } },
    xaxis: { categories: [], labels: { style: { colors: '#94a3b8', fontSize: '9px', fontWeight: 500 }, rotate: -45, hideOverlappingLabels: true } },
    yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 500 } } },
    grid: { ...base.grid },
    tooltip: { ...base.tooltip },
  });
  charts.hourly.render();

  // — Daily —
  charts.daily = new ApexCharts(document.getElementById('chartDaily'), {
    ...base,
    chart: { ...base.chart, type: 'bar', height: 200 },
    series: [{ name: 'Gangguan', data: [] }],
    colors: ['#8b5cf6'],
    plotOptions: { bar: { borderRadius: 8, columnWidth: '50%' } },
    xaxis: { categories: [], labels: { style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 500 } } },
    yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 500 } } },
    grid: { ...base.grid },
    tooltip: { ...base.tooltip },
  });
  charts.daily.render();
}

// ═══════════════════════════════════════════════════════════
//  MAP
// ═══════════════════════════════════════════════════════════
function initMap() {
  statsMap = L.map('statsMap').setView([-8.72, 116.28], 11);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OSM', maxZoom: 18 }).addTo(statsMap);
  markerLayer = L.layerGroup().addTo(statsMap);
  heatLayer = L.heatLayer([], { radius: 40, blur: 22, maxZoom: 20, minOpacity: 0.4, gradient: { 0.2: '#818cf8', 0.4: C_BLUE, 0.6: C_AMBER, 0.8: '#f97316', 1.0: C_RED } });
}
function setMapMode(m) {
  mapMode = m;
  document.getElementById('btnMarker').className  = m === 'marker'  ? 'px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-slate-700 shadow-sm transition-all' : 'px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-400 hover:text-slate-600 transition-all';
  document.getElementById('btnHeatmap').className = m === 'heatmap' ? 'px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-slate-700 shadow-sm transition-all' : 'px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-400 hover:text-slate-600 transition-all';
  updateMap(heatData);
}
function updateMap(data) {
  heatData = data;
  markerLayer.clearLayers();
  statsMap.removeLayer(heatLayer);
  document.getElementById('mapCount').textContent = data.length;
  if (mapMode === 'marker') {
    data.forEach(p => {
      const c = p.status === 'selesai' ? C_GREEN : p.status === 'on_progress' ? C_AMBER : C_GRAY;
      const label = p.status === 'selesai' ? 'Selesai' : p.status === 'on_progress' ? 'Proses' : 'Pending';
      // Outer glow ring
      L.circleMarker([p.lat, p.lng], { radius: 16, fillColor: c, color: c, weight: 0, fillOpacity: 0.15 }).addTo(markerLayer);
      // Main marker
      const m = L.circleMarker([p.lat, p.lng], { radius: 8, fillColor: c, color: '#fff', weight: 2.5, fillOpacity: 0.95 }).addTo(markerLayer);
      if (p.address || label) m.bindTooltip(`<b>${label}</b>${p.address ? '<br>' + p.address : ''}`, { direction: 'top', offset: [0, -10], className: 'leaflet-tooltip-custom' });
    });
  } else {
    heatLayer.setLatLngs(data.map(p => [p.lat, p.lng, 1.0]));
    heatLayer.addTo(statsMap);
  }
}

// ═══════════════════════════════════════════════════════════
//  FILTERS + FETCH
// ═══════════════════════════════════════════════════════════
function getFilters() {
  return { bulan: document.getElementById('fBulan').value, tahun: document.getElementById('fTahun').value, kecamatan_id: document.getElementById('fKecamatan').value, status: document.getElementById('fStatus').value, problem_type: document.getElementById('fJenis').value };
}
function qs(f) { return Object.entries(f).filter(([,v])=>v).map(([k,v])=>`${k}=${encodeURIComponent(v)}`).join('&'); }

function applyFilters() {
  const f = getFilters(), q = qs(f);
  document.getElementById('linkCsv').href   = `{{ route('statistics.export.csv') }}?${q}`;
  document.getElementById('linkExcel').href = `{{ route('statistics.export.excel') }}?${q}`;

  fetch(`{{ route('api.statistics') }}?${q}`)
    .then(r => r.ok ? r.json() : Promise.reject(r.status))
    .then(d => {
      document.getElementById('lastUpdateTime').textContent = 'Update: ' + new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

      // ─── KPIs ───
      const s = d.summary;
      countUp(document.getElementById('kpiTotalBulan'),    s.total_bulan);
      countUp(document.getElementById('kpiTotalTahun'),    s.total_tahun);
      countUp(document.getElementById('kpiAvgDay'),        s.avg_per_day, true);
      countUp(document.getElementById('kpiAvgCompletion'), s.avg_completion_hours, true);
      countUp(document.getElementById('kpiSLA'),           s.sla_percent, true);
      countUp(document.getElementById('kpiCompletion'),    s.completion_percent, true);

      // ─── Trend Badges ───
      // Card 1: Total Bulan
      const b1 = document.getElementById('kpiTrendBadge1');
      const t1 = document.getElementById('kpiTrendText1');
      if (b1) {
        if (s.change_percent > 0) { b1.className = 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-600'; b1.innerHTML = '<i class="ph-bold ph-arrow-up text-[9px]"></i> +' + s.change_percent + '%'; }
        else if (s.change_percent < 0) { b1.className = 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600'; b1.innerHTML = '<i class="ph-bold ph-arrow-down text-[9px]"></i> ' + s.change_percent + '%'; }
        else { b1.className = 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500'; b1.innerHTML = '<i class="ph-bold ph-minus text-[9px]"></i> Stabil'; }
      }
      if (t1) {
        if (s.change_percent > 0) t1.innerHTML = `<span class="text-red-500 font-semibold">+${s.change_absolute}</span> gangguan vs bulan lalu`;
        else if (s.change_percent < 0) t1.innerHTML = `<span class="text-emerald-500 font-semibold">${s.change_absolute}</span> gangguan vs bulan lalu`;
        else t1.textContent = 'Tidak ada perubahan dari bulan lalu';
      }

      // Card 2: Total Tahun
      const b2 = document.getElementById('kpiTrendBadge2');
      if (b2) { b2.className = 'px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/60 text-indigo-600 backdrop-blur-sm'; b2.textContent = s.total_tahun + ' total'; }

      // Card 3: Avg/Day
      const b3 = document.getElementById('kpiTrendBadge3');
      if (b3) { b3.className = 'px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/60 text-sky-600 backdrop-blur-sm'; b3.textContent = s.avg_per_day + '/hr'; }

      // Card 4: Avg Selesai
      const b4 = document.getElementById('kpiTrendBadge4');
      const t4 = document.getElementById('kpiTrendText4');
      if (b4) {
        if (s.avg_completion_hours <= 24) { b4.className = 'px-2 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-600'; b4.innerHTML = '✓ Cepat'; }
        else { b4.className = 'px-2 py-0.5 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-600'; b4.innerHTML = '⚠ Lambat'; }
      }
      if (t4) t4.textContent = `Waktu penyelesaian rata-rata ${s.avg_completion_hours} jam`;

      // Card 5: SLA
      const b5 = document.getElementById('kpiTrendBadge5');
      const t5 = document.getElementById('kpiTrendText5');
      if (b5) {
        if (s.sla_percent >= 90) { b5.className = 'px-2 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-600'; b5.innerHTML = '✓ Baik'; }
        else if (s.sla_percent >= 70) { b5.className = 'px-2 py-0.5 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-600'; b5.innerHTML = '⚠ Perlu Perbaikan'; }
        else { b5.className = 'px-2 py-0.5 rounded-lg text-[10px] font-bold bg-red-50 text-red-600'; b5.innerHTML = '✗ Buruk'; }
      }
      if (t5) t5.textContent = `${s.done_count} dari ${s.total_bulan} selesai < 24 jam`;

      // Card 6: Completion
      const b6 = document.getElementById('kpiTrendBadge6');
      const t6 = document.getElementById('kpiTrendText6');
      if (b6) {
        if (s.completion_percent >= 80) { b6.className = 'px-2 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-600'; b6.innerHTML = '✓ Optimal'; }
        else if (s.completion_percent >= 50) { b6.className = 'px-2 py-0.5 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-600'; b6.innerHTML = '⚠ Progres'; }
        else { b6.className = 'px-2 py-0.5 rounded-lg text-[10px] font-bold bg-red-50 text-red-600'; b6.innerHTML = '✗ Rendah'; }
      }
      if (t6) t6.textContent = `${s.done_count} selesai dari ${s.total_bulan} total laporan`;

      // ─── Trend ───
      charts.trend.updateOptions({ xaxis: { categories: d.daily_trend.labels } }, false, false);
      charts.trend.updateSeries([{ name:'Bulan Ini', data: d.daily_trend.current }, { name:'Bulan Lalu', data: d.daily_trend.previous }]);

      // ─── Donut ───
      const sd = d.status_distribution || { done: 0, working: 0, pending: 0 };
      const sDone = sd.done || 0;
      const sWorking = sd.working || 0;
      const sPending = sd.pending || 0;
      charts.donut.updateSeries([sDone, sWorking, sPending]);
      const tot = sDone + sWorking + sPending || 1;
      document.getElementById('donutLegend').innerHTML = [
        { l:'Selesai', v:sDone, c:C_GREEN },
        { l:'Proses',  v:sWorking, c:C_AMBER },
        { l:'Pending', v:sPending, c:C_GRAY },
      ].map(x => { const dim = x.v === 0 ? ' opacity-40' : ''; return `<div class="flex items-center justify-between py-1.5${dim}"><span class="flex items-center gap-2.5 text-[13px] text-slate-600"><span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:${x.c}"></span>${x.l}</span><span class="text-[13px] font-bold text-slate-800 tabular-nums">${x.v} <span class="text-slate-400 font-normal text-[11px]">(${Math.round(x.v/tot*100)}%)</span></span></div>`; }).join('');

      // ─── Kecamatan ───
      charts.kecamatan.updateOptions({ xaxis: { categories: d.by_kecamatan.labels } }, false, false);
      charts.kecamatan.updateSeries([{ data: d.by_kecamatan.values }]);

      // ─── Category ───
      charts.category.updateOptions({ xaxis: { categories: d.by_category.labels } }, false, false);
      charts.category.updateSeries([{ data: d.by_category.values }]);

      // ─── Risk Table ───
      const tb = document.getElementById('riskTableBody');
      if (!d.top_risk.length) tb.innerHTML = '<tr><td colspan="4" class="py-8 text-center text-slate-400 text-xs">Belum ada data</td></tr>';
      else tb.innerHTML = d.top_risk.map((r, i) => {
        const rankIcon = i === 0 ? '🥇' : i === 1 ? '🥈' : (i+1);
        const trendBg = r.trend === 'up' ? 'bg-red-50 text-red-500' : r.trend === 'down' ? 'bg-emerald-50 text-emerald-500' : 'bg-slate-50 text-slate-400';
        const arrow = r.trend === 'up' ? '↑' : r.trend === 'down' ? '↓' : '—';
        return `<tr class="border-b border-slate-50/80 last:border-0 hover:bg-slate-50/50 transition-colors"><td class="py-3.5 w-8 text-center">${rankIcon}</td><td class="py-3.5 font-semibold text-slate-700">${r.kecamatan}</td><td class="py-3.5 text-right font-bold text-slate-800">${r.total}</td><td class="py-3.5 text-right"><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold ${trendBg}">${arrow} ${r.change > 0 ? '+' : ''}${r.change}%</span></td></tr>`;
      }).join('');

      // ─── Hourly ───
      charts.hourly.updateOptions({ xaxis: { categories: d.hourly.labels } }, false, false);
      charts.hourly.updateSeries([{ data: d.hourly.values }]);

      // ─── Daily ───
      charts.daily.updateOptions({ xaxis: { categories: d.daily.labels } }, false, false);
      charts.daily.updateSeries([{ data: d.daily.values }]);

      // ─── Time ───
      function formatTime(mins, prefix) {
        const valEl = document.getElementById(prefix);
        const unitEl = document.getElementById(prefix + 'Unit');
        if (!valEl || !unitEl) return;
        let val = mins, unit = 'mnt';
        if (mins >= 1440) { val = (mins / 1440).toFixed(1); unit = 'hari'; }
        else if (mins >= 60) { val = (mins / 60).toFixed(1); unit = 'jam'; }
        countUp(valEl, val, true);
        unitEl.textContent = unit;
      }
      formatTime(d.time_analysis.avg_response_min, 'timeResponse');
      formatTime(d.time_analysis.avg_completion_min, 'timeCompletion');
      formatTime(d.time_analysis.min_completion_min, 'timeMin');
      formatTime(d.time_analysis.max_completion_min, 'timeMax');

      // ─── Map ───
      updateMap(d.heatmap);
    })
    .catch(e => console.error('Stats error:', e));
}

// ═══════════════════════════════════════════════════════════
//  BOOT
// ═══════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
  initCharts();
  initMap();
  applyFilters();
  setInterval(applyFilters, 60000);
});
</script>
@endsection
