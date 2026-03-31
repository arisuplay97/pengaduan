{{-- resources/views/pages/statistics/_time-charts.blade.php --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    {{-- Hourly Distribution --}}
    <div class="chart-card">
        <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="ph-fill ph-clock-countdown text-red-500"></i> Jam Rawan Gangguan
        </h3>
        <div class="h-56">
            <canvas id="chartHourly"></canvas>
        </div>
    </div>

    {{-- Daily Distribution --}}
    <div class="chart-card">
        <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="ph-fill ph-calendar text-indigo-500"></i> Hari Rawan Gangguan
        </h3>
        <div class="h-56">
            <canvas id="chartDaily"></canvas>
        </div>
    </div>
</div>
