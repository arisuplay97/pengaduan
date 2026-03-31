{{-- resources/views/pages/statistics/_risk-table.blade.php --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    {{-- Top 5 Risk Areas --}}
    <div class="chart-card">
        <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="ph-fill ph-trophy text-amber-500"></i> Top 5 Area Risiko Tinggi
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="text-left py-2.5 text-[10px] font-black uppercase tracking-widest text-gray-400">#</th>
                        <th class="text-left py-2.5 text-[10px] font-black uppercase tracking-widest text-gray-400">Kecamatan</th>
                        <th class="text-center py-2.5 text-[10px] font-black uppercase tracking-widest text-gray-400">Total</th>
                        <th class="text-center py-2.5 text-[10px] font-black uppercase tracking-widest text-gray-400">% Perubahan</th>
                        <th class="text-center py-2.5 text-[10px] font-black uppercase tracking-widest text-gray-400">Trend</th>
                    </tr>
                </thead>
                <tbody id="riskTableBody">
                    <tr><td colspan="5" class="text-center py-8 text-gray-400 text-xs">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Time Analysis --}}
    <div class="chart-card">
        <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="ph-fill ph-hourglass-medium text-teal-500"></i> Analisis Waktu
        </h3>
        <div class="grid grid-cols-2 gap-3">
            <div class="time-metric-card">
                <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Rata-rata Respon</div>
                <div class="text-xl font-black text-gray-900 dark:text-white"><span id="timeAvgResponse">0</span> <span id="timeAvgResponseUnit" class="text-xs text-gray-400">menit</span></div>
                <div class="text-[10px] text-gray-400 mt-1">created → started</div>
            </div>
            <div class="time-metric-card">
                <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Rata-rata Selesai</div>
                <div class="text-xl font-black text-gray-900 dark:text-white"><span id="timeAvgCompletion">0</span> <span id="timeAvgCompletionUnit" class="text-xs text-gray-400">menit</span></div>
                <div class="text-[10px] text-gray-400 mt-1">started → finished</div>
            </div>
            <div class="time-metric-card border-emerald-200 dark:border-emerald-800">
                <div class="text-[10px] font-black uppercase tracking-widest text-emerald-500 mb-2">Tercepat</div>
                <div class="text-xl font-black text-emerald-600"><span id="timeMin">0</span> <span id="timeMinUnit" class="text-xs text-emerald-400">menit</span></div>
            </div>
            <div class="time-metric-card border-red-200 dark:border-red-800">
                <div class="text-[10px] font-black uppercase tracking-widest text-red-500 mb-2">Terlama</div>
                <div class="text-xl font-black text-red-600"><span id="timeMax">0</span> <span id="timeMaxUnit" class="text-xs text-red-400">menit</span></div>
            </div>
        </div>
    </div>
</div>
