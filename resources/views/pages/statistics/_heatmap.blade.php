{{-- resources/views/pages/statistics/_heatmap.blade.php --}}
<div class="chart-card mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="ph-fill ph-map-pin text-red-500"></i> Peta Konsentrasi Gangguan
        </h3>
        <div class="flex items-center gap-2">
            <button id="btnMarker" onclick="setMapMode('marker')" class="map-mode-btn active">
                <i class="ph-bold ph-map-pin-line"></i> Marker
            </button>
            <button id="btnHeatmap" onclick="setMapMode('heatmap')" class="map-mode-btn">
                <i class="ph-bold ph-fire"></i> Heatmap
            </button>
            <span class="text-[10px] font-bold text-gray-400 ml-2" id="mapPointCount">0 titik</span>
        </div>
    </div>
    <div id="statsMap" class="w-full h-96 rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700"></div>
</div>

{{-- Export Section --}}
<div class="chart-card">
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="ph-fill ph-download-simple text-blue-500"></i> Export Data
        </h3>
        <div class="flex items-center gap-2">
            <a id="exportCsvLink" href="{{ route('statistics.export.csv') }}" class="export-btn bg-emerald-500 hover:bg-emerald-600">
                <i class="ph-bold ph-file-csv"></i> CSV
            </a>
            <a id="exportExcelLink" href="{{ route('statistics.export.excel') }}" class="export-btn bg-blue-500 hover:bg-blue-600">
                <i class="ph-bold ph-file-xls"></i> Excel
            </a>
            <button onclick="window.print()" class="export-btn bg-rose-500 hover:bg-rose-600">
                <i class="ph-bold ph-file-pdf"></i> PDF
            </button>
        </div>
    </div>
</div>
