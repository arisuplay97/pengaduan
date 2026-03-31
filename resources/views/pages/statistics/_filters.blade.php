{{-- resources/views/pages/statistics/_filters.blade.php --}}
<div class="stat-filters bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl border border-gray-100 dark:border-gray-700 p-5 mb-6 shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div class="flex items-center gap-3">
            <h2 class="text-lg font-extrabold text-gray-900 dark:text-white tracking-tight flex items-center gap-2">
                <i class="ph-bold ph-funnel text-cyan-500"></i> Filter Data
            </h2>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-500/10 border border-red-500/20 text-red-500 text-[10px] font-black uppercase tracking-widest animate-pulse">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Live Data
            </span>
        </div>
        <div id="lastUpdate" class="text-xs font-bold text-gray-400 dark:text-gray-500">
            Update terakhir: <span id="lastUpdateTime">--:--</span> WIB
        </div>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <select id="fBulan" class="filter-input">
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                </option>
            @endfor
        </select>
        <select id="fTahun" class="filter-input">
            @for($y = now()->year; $y >= now()->year - 3; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
        <select id="fKecamatan" class="filter-input">
            <option value="">Semua Kecamatan</option>
            @foreach($kecamatans as $kec)
                <option value="{{ $kec->id }}">{{ $kec->nama }}</option>
            @endforeach
        </select>
        <select id="fStatus" class="filter-input">
            <option value="">Semua Status</option>
            <option value="working">Proses</option>
            <option value="done">Selesai</option>
            <option value="pending">Pending</option>
        </select>
        <select id="fJenis" class="filter-input">
            <option value="">Semua Jenis</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
        </select>
        <button onclick="applyFilters()" class="px-4 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:shadow-lg hover:shadow-cyan-500/25 transition-all active:scale-95">
            <i class="ph-bold ph-magnifying-glass mr-1"></i> Terapkan
        </button>
    </div>
</div>
