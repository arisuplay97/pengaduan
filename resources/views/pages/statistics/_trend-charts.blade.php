{{-- resources/views/pages/statistics/_trend-charts.blade.php --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    {{-- Line Chart: Daily Trend --}}
    <div class="lg:col-span-2 chart-card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="ph-fill ph-chart-line text-cyan-500"></i> Tren Harian (30 Hari)
            </h3>
            <div class="flex items-center gap-3 text-[10px] font-bold">
                <span class="flex items-center gap-1"><span class="w-3 h-0.5 bg-cyan-500 rounded-full"></span>Bulan Ini</span>
                <span class="flex items-center gap-1"><span class="w-3 h-0.5 bg-gray-300 rounded-full"></span>Bulan Lalu</span>
            </div>
        </div>
        <div class="h-64">
            <canvas id="chartTrend"></canvas>
        </div>
    </div>

    {{-- Donut Chart: Status --}}
    <div class="chart-card">
        <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="ph-fill ph-chart-donut text-purple-500"></i> Status Penyelesaian
        </h3>
        <div class="h-52 flex items-center justify-center">
            <canvas id="chartStatus"></canvas>
        </div>
        <div class="flex justify-center gap-4 mt-3 text-[10px] font-bold">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>Selesai</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>Proses</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>Pending</span>
        </div>
    </div>
</div>
