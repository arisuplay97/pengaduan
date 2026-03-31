{{-- resources/views/pages/statistics/_breakdown-charts.blade.php --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    {{-- Bar Chart: Per Kecamatan --}}
    <div class="chart-card">
        <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="ph-fill ph-map-trifold text-blue-500"></i> Gangguan per Kecamatan
        </h3>
        <div class="h-72">
            <canvas id="chartKecamatan"></canvas>
        </div>
    </div>

    {{-- Bar Chart: Per Category --}}
    <div class="chart-card">
        <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
            <i class="ph-fill ph-tag text-orange-500"></i> Kategori Gangguan
        </h3>
        <div class="h-72">
            <canvas id="chartCategory"></canvas>
        </div>
    </div>
</div>
