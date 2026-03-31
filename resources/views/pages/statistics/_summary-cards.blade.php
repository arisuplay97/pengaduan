{{-- resources/views/pages/statistics/_summary-cards.blade.php --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
    {{-- Card 1: Total Laporan --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100/50 relative overflow-hidden group hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="ph-fill ph-file-text text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Bulan Ini</span>
            </div>
            <div class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-50 text-slate-400" id="cardTrend">
                ---
            </div>
        </div>
        <div>
            <h3 class="text-3xl font-black text-slate-800 tracking-tight" id="cardTotalBulan">0</h3>
            <p class="text-[10px] text-slate-400 mt-1 font-medium leading-tight">Tidak ada perubahan dari bulan lalu</p>
        </div>
    </div>

    {{-- Card 2: Total Tahun --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100/50 relative overflow-hidden group hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <i class="ph-fill ph-calendar-blank text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Tahun Ini</span>
            </div>
            <div class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-600">
                0 total
            </div>
        </div>
        <div>
            <h3 class="text-3xl font-black text-slate-800 tracking-tight" id="cardTotalTahun">0</h3>
            <p class="text-[10px] text-slate-400 mt-1 font-medium leading-tight">Kumulatif tahun berjalan</p>
        </div>
    </div>

    {{-- Card 3: Rata-rata per Hari --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100/50 relative overflow-hidden group hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-sky-50 flex items-center justify-center text-sky-600">
                    <i class="ph-bold ph-trend-up text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-tight">Rata-rata / Hari</span>
            </div>
            <div class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-50 text-sky-600">
                0/hr
            </div>
        </div>
        <div>
            <h3 class="text-3xl font-black text-slate-800 tracking-tight" id="cardAvgDay">0.0</h3>
            <p class="text-[10px] text-slate-400 mt-1 font-medium leading-tight">Rata-rata laporan per hari kerja</p>
        </div>
    </div>

    {{-- Card 4: Rata-rata Selesai --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border-2 border-amber-100 relative overflow-hidden group hover:shadow-md transition-all duration-300 shadow-amber-500/10">
        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-amber-50 to-transparent rounded-bl-full -z-10"></div>
        <div class="flex items-center justify-between mb-3 relative z-10">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                    <i class="ph-fill ph-clock text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-tight">Avg. Selesai</span>
            </div>
            <div class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-600 flex items-center gap-1">
                <i class="ph-bold ph-check"></i> Cepat
            </div>
        </div>
        <div class="relative z-10">
            <h3 class="text-3xl font-black text-slate-800 tracking-tight"><span id="cardAvgCompletion">0.0</span> <span class="text-sm text-slate-300 font-bold ml-1">jam</span></h3>
            <p class="text-[10px] text-slate-400 mt-1 font-medium leading-tight">Waktu penyelesaian rata-rata 0 jam</p>
        </div>
    </div>

    {{-- Card 5: SLA < 24 Waktu --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100/50 relative overflow-hidden group hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <i class="ph-bold ph-shield-check text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-tight">SLA &lt; 24 Jam</span>
            </div>
            <div class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600 flex items-center gap-1">
                <i class="ph-bold ph-x"></i> Buruk
            </div>
        </div>
        <div>
            <h3 class="text-3xl font-black text-slate-800 tracking-tight"><span id="cardSLA">0.0</span><span class="text-xl text-slate-300 font-bold">%</span></h3>
            <p class="text-[10px] text-slate-400 mt-1 font-medium leading-tight">0 dari 0 selesai &lt; 24 jam</p>
        </div>
    </div>

    {{-- Card 6: Penyelesaian --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100/50 relative overflow-hidden group hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600">
                    <i class="ph-bold ph-check-circle text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-tight">Penyelesaian</span>
            </div>
            <div class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600 flex items-center gap-1">
                <i class="ph-bold ph-x"></i> Rendah
            </div>
        </div>
        <div>
            <h3 class="text-3xl font-black text-slate-800 tracking-tight"><span id="cardCompletion">0.0</span><span class="text-xl text-slate-300 font-bold">%</span></h3>
            <p class="text-[10px] text-slate-400 mt-1 font-medium leading-tight">0 selesai dari 0 total laporan</p>
        </div>
    </div>
</div>
