@extends('layouts.nolana')

@section('content')
<style>
    /* Fullscreen Support */
    .leaflet-container:fullscreen { width: 100% !important; height: 100% !important; z-index: 99999; }
    
    /* Fullscreen & Dark Mode Base */
    .command-center-wrap { margin: -1.5rem; position: relative; height: calc(100vh - 64px); background: #000; overflow: hidden; }
    
    /* Map Container */
    #ccMap { width: 100%; height: 100%; z-index: 1; background: #111; }
    
    /* STATS PANEL */
    .cc-stats-panel {
        position: absolute; top: 20px; left: 20px; z-index: 10000;
        display: flex; gap: 15px;
        background: rgba(10, 10, 10, 0.75); backdrop-filter: blur(10px);
        padding: 12px 20px; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    .stats-box { text-align: center; min-width: 80px; }
    .stats-count { font-size: 1.8rem; font-weight: 900; line-height: 1; margin-bottom: 2px; }
    .stats-label { font-size: 0.7rem; font-weight: 700; color: #888; letter-spacing: 1px; }

    /* Sidebar */
    .cc-sidebar {
        position: absolute; top: 20px; right: 20px; bottom: 20px; width: 350px;
        background: rgba(10, 10, 10, 0.85); backdrop-filter: blur(15px);
        border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.1);
        z-index: 1000; display: flex; flex-direction: column; overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.5); transform: translateX(120%); transition: transform 0.3s ease;
    }
    .cc-sidebar.active { transform: translateX(0); }
    
    .cc-header { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .cc-title { font-size: 1.1rem; font-weight: 800; color: #fff; letter-spacing: 0.5px; display: flex; align-items: center; gap: 10px; }
    .cc-subtitle { font-size: 0.75rem; color: #888; margin-top: 4px; font-weight: 500; }
    
    /* History List */
    .cc-list-header { padding: 10px 20px; font-size: 0.75rem; color: #aaa; text-transform: uppercase; font-weight: 700; background: rgba(255,255,255,0.02); }
    .cc-list { flex: 1; overflow-y: auto; padding: 0; }
    .job-item { 
        padding: 15px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); cursor: pointer; transition: background 0.2s; 
    }
    .job-item:hover { background: rgba(255,255,255,0.05); }
    .job-item.active { background: rgba(220, 38, 38, 0.1); border-left: 3px solid #dc2626; }
    .job-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px; }
    .job-title { font-size: 0.9rem; font-weight: 700; color: #eee; }
    .job-time { font-size: 0.7rem; color: #aaa; font-family: monospace; }
    .job-loc { font-size: 0.8rem; color: #888; display: flex; gap: 6px; align-items: flex-start; line-height: 1.4; }
    .job-status { font-size: 0.65rem; padding: 2px 8px; border-radius: 10px; font-weight: 700; text-transform: uppercase; display: inline-block; margin-top: 8px; }
    /* Marker Styling */
    /* CRITICAL: Do NOT animate transform on the leaflet-marker-icon itself (outer div) 
       because Leaflet uses transform for positioning! Animate the INNER div instead. */
    .marker-inner {
        width: 100%; height: 100%;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        border: 2px solid #fff; color: white;
        box-shadow: 0 0 10px #ef4444;
        background: #ef4444; /* Default Red */
        animation: pulse-red 1.5s infinite;
    }

    .marker-hq-inner {
        width: 100%; height: 100%;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        border: 3px solid #fff; color: white;
        background: #3b82f6;
        box-shadow: 0 0 20px #2563eb;
        font-size: 1.2rem;
    }

    .marker-branch-inner {
        width: 100%; height: 100%;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        border: 2px solid #fff; color: white;
        background: #0ea5e9; /* Sky Blue for Branches */
        box-shadow: 0 0 15px #0284c7;
        font-size: 1rem;
    }

    /* Yellow Variant */
    .marker-working-yellow .marker-inner {
        background: #eab308 !important; /* Yellow-500 */
        box-shadow: 0 0 10px #eab308 !important;
        animation: pulse-yellow 1.5s infinite !important;
    }

    /* Polyline Glow Effects */
    .ant-path {
        stroke-dasharray: 8, 12;
        animation: ant-march 20s linear infinite;
        transition: stroke 0.3s;
    }
    
    .glow-red { filter: drop-shadow(0 0 5px #ff0000); }
    .glow-yellow { filter: drop-shadow(0 0 5px #ffff00); }

    @keyframes pulse-red {
        0% { transform: scale(1); filter: drop-shadow(0 0 5px #ff0000); }
        50% { transform: scale(1.15); filter: drop-shadow(0 0 20px #ff0000); }
        100% { transform: scale(1); filter: drop-shadow(0 0 5px #ff0000); }
    }
    @keyframes pulse-yellow {
        0% { transform: scale(1); filter: drop-shadow(0 0 5px #ffff00); }
        50% { transform: scale(1.15); filter: drop-shadow(0 0 20px #ffff00); }
        100% { transform: scale(1); filter: drop-shadow(0 0 5px #ffff00); }
    }
    @keyframes ant-march {
        from { stroke-dashoffset: 200; }
        to { stroke-dashoffset: 0; }
    }

    /* Popup Modal */
    #jobDetailModal { z-index: 99999; }
    .glass-dark { background: rgba(15, 15, 15, 0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1); }
    
    /* Custom Buttons (Hamburger & Fullscreen) */
    .cc-btn-group {
        position: absolute; top: 20px; right: 20px; z-index: 9999;
        display: flex; gap: 10px;
    }
    .cc-btn {
        width: 44px; height: 44px;
        background: rgba(220, 38, 38, 0.9); color: white; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; box-shadow: 0 4px 15px rgba(220,38,38,0.4); border: 1px solid rgba(255,255,255,0.1);
        transition: transform 0.2s, background 0.2s; font-size: 1.2rem;
    }
    .cc-btn:hover { transform: scale(1.05); background: #ef4444; }
    .cc-btn.btn-dark { background: rgba(30, 41, 59, 0.9); box-shadow: 0 4px 15px rgba(0,0,0,0.3); }

    /* Move Leaflet Controls down */
    .leaflet-control-layers { 
        background: rgba(10,10,10,0.8) !important; color: #eee !important; border: 1px solid rgba(255,255,255,0.1) !important;
    }
    .leaflet-top.leaflet-right { top: 80px; }

    /* Custom Legend */
    .cc-legend {
        position: absolute; bottom: 35px; left: 25px; 
        background: rgba(10,10,10,0.85); backdrop-filter: blur(10px);
        padding: 12px 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);
        z-index: 1000 !important; display: flex; gap: 15px;
    }
    .command-center-wrap:fullscreen .cc-legend,
    .command-center-wrap:fullscreen .cc-stats-panel,
    .command-center-wrap:fullscreen .cc-btn-group { display: flex !important; }

    .legend-item { display: flex; align-items: center; gap: 8px; font-size: 0.8rem; color: #ccc; font-weight: 600; }
    .legend-icon-working {
        width: 24px; height: 24px; background: #ef4444; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 10px; border: 1px solid white; box-shadow: 0 0 5px red;
    }
    
    /* Glossy Green Done Icon */
    .marker-done-inner {
        width: 100%; height: 100%;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        border: 2px solid #fff; 
        color: white;
        background: radial-gradient(circle at 30% 30%, #4ade80, #16a34a); /* Glossy Green */
        box-shadow: 0 0 15px #22c55e, inset 0 2px 5px rgba(255,255,255,0.4);
        animation: pulse-green 2s infinite;
    }
    .legend-icon-done {
        width: 24px; height: 24px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 10px; border: 1px solid white;
        background: radial-gradient(circle at 30% 30%, #4ade80, #16a34a);
        box-shadow: 0 0 10px #22c55e;
    }

    @keyframes pulse-green {
        0% { transform: scale(1); box-shadow: 0 0 10px #22c55e; }
        50% { transform: scale(1.1); box-shadow: 0 0 25px #22c55e; }
        100% { transform: scale(1); box-shadow: 0 0 10px #22c55e; }
    }

    /* Tooltip Styling */
    .leaflet-tooltip.custom-tooltip {
        background: rgba(10, 10, 10, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 12px;
        color: #fff;
        padding: 0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        font-family: 'Plus Jakarta Sans', sans-serif;
        overflow: hidden;
        min-width: 280px;
    }
    .leaflet-tooltip-top:before, .leaflet-tooltip-bottom:before, .leaflet-tooltip-left:before, .leaflet-tooltip-right:before {
        display: none; /* Hide default arrow */
    }
    .tooltip-header {
        padding: 10px 15px;
        background: rgba(255,255,255,0.05);
        border-bottom: 1px solid rgba(255,255,255,0.1);
        display: flex; justify-content: space-between; align-items: center;
    }
    .tooltip-body { padding: 10px 15px; }
    .tooltip-images { display: flex; gap: 8px; margin-top: 5px; }
    .tooltip-img-box {
        flex: 1; height: 80px; background: #000; border-radius: 6px; overflow: hidden; position: relative; border: 1px solid rgba(255,255,255,0.1);
    }
    .tooltip-img-box img { width: 100%; height: 100%; object-fit: cover; }
    .tooltip-placeholder {
        width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
        text-align: center; color: #666; font-size: 0.6rem; padding: 5px;
    }
    .tooltip-label { font-size: 0.65rem; color: #888; text-transform: uppercase; margin-bottom: 4px; display: block; }

    /* === HIGH-END EXECUTIVE ALERT UI === */
    
    /* 1. Custom Popup Container */
    div:where(.swal2-container) div:where(.swal2-popup) {
        background: #0f1115 !important; /* Deep Charcoal */
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(220, 38, 38, 0.3); /* Subtle Red Border */
        border-radius: 24px;
        box-shadow: 
            0 20px 60px rgba(0,0,0,0.8), /* Deep Drop Shadow */
            inset 0 0 40px rgba(220, 38, 38, 0.05); /* Internal Red Ambient */
        padding: 0;
        overflow: hidden;
        width: 450px !important;
    }

    /* 2. Warning Icon Area */
    .alert-icon-wrapper {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 40px auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Radar/Ripple Effect Behind Icon */
    .alert-radar-pulse {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(220, 38, 38, 0.2) 0%, rgba(220, 38, 38, 0) 70%);
        animation: radar-ping 2s infinite cubic-bezier(0, 0, 0.2, 1);
        z-index: 0;
    }

    /* The Main Triangle Icon */
    .alert-main-icon {
        font-size: 5rem;
        color: #ef4444; /* Red-500 */
        filter: drop-shadow(0 0 15px rgba(239, 68, 68, 0.6));
        z-index: 1;
        animation: icon-breathe 1.5s ease-in-out infinite;
    }

    /* 3. Typography */
    .alert-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.5rem !important;
        font-weight: 900 !important;
        color: #fff !important;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-align: center;
        margin-bottom: 5px !important;
        text-shadow: 0 4px 10px rgba(220, 38, 38, 0.5);
    }

    .alert-subtitle {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1rem;
        font-weight: 500;
        color: #94a3b8; /* Slate-400 */
        text-align: center;
        margin-bottom: 30px;
        letter-spacing: 0.5px;
    }

    /* 4. Buttons */
    /* Primary (Red Gradient + Glow) */
    div:where(.swal2-confirm) {
        background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%) !important;
        border: 1px solid rgba(255, 100, 100, 0.2) !important;
        box-shadow: 0 0 20px rgba(220, 38, 38, 0.3);
        color: white !important;
        font-weight: 800 !important;
        letter-spacing: 1px !important;
        padding: 14px 28px !important;
        border-radius: 12px !important;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease !important;
    }
    
    div:where(.swal2-confirm):hover {
        transform: scale(1.05);
        box-shadow: 0 0 30px rgba(220, 38, 38, 0.6);
        border-color: rgba(255, 100, 100, 0.5) !important;
    }

    /* Light Sweep Animation */
    div:where(.swal2-confirm)::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 50%;
        height: 100%;
        background: linear-gradient(to right, transparent, rgba(255,255,255,0.4), transparent);
        transform: skewX(-25deg);
        animation: btn-sweep 4s infinite ease-in-out;
    }

    /* Secondary (Muted Gray) */
    div:where(.swal2-cancel) {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #94a3b8 !important;
        font-weight: 600 !important;
        padding: 14px 24px !important;
        border-radius: 12px !important;
    }
    div:where(.swal2-cancel):hover {
        background: rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
    }

    /* 5. Animations */
    @keyframes radar-ping {
        0% { transform: scale(0.8); opacity: 0.6; }
        100% { transform: scale(2); opacity: 0; }
    }

    @keyframes icon-breathe {
        0%, 100% { transform: scale(1); filter: drop-shadow(0 0 15px rgba(239, 68, 68, 0.6)); }
        50% { transform: scale(1.08); filter: drop-shadow(0 0 25px rgba(239, 68, 68, 0.9)); }
    }

    @keyframes btn-sweep {
        0% { left: -100%; }
        20% { left: 200%; } /* Fast sweep */
        100% { left: 200%; } /* Wait */
    }
    
    /* Remove default success/error icons if they appear */
    .swal2-icon { display: none !important; }
</style>

<div class="command-center-wrap" id="fullscreenContainer">
    <div id="ccMap"></div>

    <div class="cc-stats-panel">
        <div class="stats-box">
            <div class="stats-count text-red-500" id="panelWorking">-</div>
            <div class="stats-label">GANGGUAN</div>
        </div>
        <div class="stats-box" style="border-left: 1px solid rgba(255,255,255,0.1); padding-left: 15px;">
            <div class="stats-count text-emerald-500" id="panelDone">-</div>
            <div class="stats-label">SELESAI</div>
        </div>
    </div>

    <div class="cc-btn-group">
        <button class="cc-btn btn-dark" onclick="toggleFullScreen()" title="Fullscreen"><i class="fa-solid fa-expand"></i></button>
        <button class="cc-btn" onclick="toggleSidebar()" id="btnOpenSidebar" title="Menu"><i class="ph-bold ph-list"></i></button>
    </div>

    <div class="cc-sidebar" id="ccSidebar">
        <!-- Sidebar content same as before -->
        <div class="cc-header">
            <div class="flex justify-between items-center">
                <div>
                    <div class="cc-title"><i class="ph-fill ph-broadcast text-red-500"></i> LAYANAN GANGGUAN</div>
                    <div class="cc-subtitle">Monitoring Real-time Petugas Lapangan</div>
                </div>
                <div class="flex gap-2">
                    <button onclick="testAudio()" class="text-xs bg-red-900/50 text-red-200 px-2 py-1 rounded hover:bg-red-900 border border-red-800 transition" title="Test Audio">
                        <i class="fa-solid fa-volume-high"></i> Test
                    </button>
                    <button onclick="toggleSidebar()" class="text-gray-400 hover:text-white p-2 rounded-lg hover:bg-white/10 transition">
                        <i class="ph-bold ph-x"></i>
                    </button>
                </div>
            </div>
            <form id="filterForm" class="mt-4 flex gap-2">
                <input type="date" name="date" id="filterDate" class="flex-1 bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-red-500 transition" value="{{ request('date', date('Y-m-d')) }}">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-lg shadow-red-900/40">FILTER</button>
            </form>
        </div>
        <div class="cc-list-header"><i class="ph-bold ph-clock-counter-clockwise"></i> RIWAYAT GANGGUAN TERBARU</div>
        <div class="cc-list" id="jobList">
            <div class="p-8 text-center text-gray-500 text-sm">Memuat data...</div>
        </div>
    </div>

    <div class="cc-legend">
        <div class="legend-item"><div class="legend-icon-working"><i class="fa-solid fa-person-digging"></i></div> Sedang Dikerjakan</div>
        <div class="legend-item"><i class="fa-solid fa-check-double text-emerald-400"></i> Selesai Hari Ini</div>
        <div class="legend-item"><i class="ph-fill ph-buildings text-blue-500"></i> Kantor Pusat</div>
        <div class="legend-item"><i class="ph-fill ph-house-line text-sky-500"></i> Kantor Cabang</div>
    </div>
</div>

<!-- Modal -->
<div id="jobDetailModal" class="fixed inset-0 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeJobModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-2xl glass-dark text-left shadow-2xl transition-all sm:w-full sm:max-w-md border border-gray-700">
                <div class="px-6 py-5 border-b border-gray-800 flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-bold text-white mb-1" id="modalJobTitle">Deteksi Gangguan</h3>
                        <span id="modalJobStatus" class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">STATUS</span>
                    </div>
                     <button onclick="closeJobModal()" class="text-gray-400 hover:text-white transition bg-white/5 rounded-lg p-1">
                        <i class="ph-bold ph-x text-xl"></i>
                    </button>
                </div>
                <div class="px-6 py-6 space-y-4">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center flex-shrink-0 text-blue-400"><i class="ph-fill ph-map-pin text-xl"></i></div>
                        <div><div class="text-xs font-bold text-gray-500 uppercase mb-1">Lokasi</div><div class="text-sm font-medium text-gray-200 leading-relaxed" id="modalJobAddress">-</div></div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center flex-shrink-0 text-purple-400"><i class="ph-fill ph-clock text-xl"></i></div>
                        <div><div class="text-xs font-bold text-gray-500 uppercase mb-1">Waktu Laporan</div><div class="text-sm font-medium text-gray-200" id="modalJobTime">-</div></div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-orange-500/20 flex items-center justify-center flex-shrink-0 text-orange-400"><i class="ph-fill ph-hourglass text-xl"></i></div>
                        <div><div class="text-xs font-bold text-gray-500 uppercase mb-1">Durasi Pengerjaan</div><div class="text-sm font-medium text-gray-200" id="modalJobDuration">-</div></div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-white/5 border-t border-gray-800 flex justify-end">
                    <button type="button" onclick="closeJobModal()" class="px-4 py-2 rounded-lg bg-gray-700 hover:bg-gray-600 text-white font-bold text-sm transition">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const HQ_COORDS = [-8.7027, 116.2717];
    // Force reload audio by appending timestamp
    const AUDIO_ALARM = new Audio('{{ asset("sounds/beep.mp3") }}?v=' + new Date().getTime());
    AUDIO_ALARM.load(); // Preload

    let currentPolylineColor = '#ff0000';
    let currentGlowClass = 'glow-red';

    const map = L.map('ccMap', { zoomControl: false }).setView(HQ_COORDS, 13);
    L.control.zoom({ position: 'bottomright' }).addTo(map);
    
    // Layers
    const tacticalDark = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', { attribution: '&copy; CARTO', subdomains: 'abcd', maxZoom: 20 }).addTo(map);
    const satelliteHybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', { maxZoom: 20, subdomains: ['mt0','mt1','mt2','mt3'] });
    const standardLight = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OSM' });
    L.control.layers({ "Tactical Dark": tacticalDark, "Satellite Hybrid": satelliteHybrid, "Standard Light": standardLight }, null, { position: 'topright' }).addTo(map);

    map.on('baselayerchange', function(e) {
        if (e.name === 'Satellite Hybrid') {
            currentPolylineColor = '#ffff00'; currentGlowClass = 'glow-yellow';
            document.querySelectorAll('.marker-working').forEach(el => el.classList.add('marker-working-yellow'));
        } else {
            currentPolylineColor = '#ff0000'; currentGlowClass = 'glow-red';
            document.querySelectorAll('.marker-working').forEach(el => el.classList.remove('marker-working-yellow'));
        }
        lineLayer.eachLayer(layer => {
            if (layer instanceof L.Polyline) layer.setStyle({ color: currentPolylineColor, className: 'ant-path ' + currentGlowClass });
        });
    });

    // MARKER ICON
    // Using Center Anchor [18, 18] to match exact coordinate.
    // Animation is applied to .marker-inner to avoid conflict with Leaflet's transform.
    const iconWorking = L.divIcon({
        html: '<div class="marker-inner"><i class="fa-solid fa-person-digging text-lg"></i></div>',
        className: 'marker-working', 
        iconSize: [36, 36], 
        iconAnchor: [18, 18], // CENTER
        popupAnchor: [0, -20]
    });

    const iconDone = L.divIcon({
        html: '<div class="marker-done-inner"><i class="fa-solid fa-check text-lg"></i></div>',
        className: 'marker-done', 
        iconSize: [30, 30], 
        iconAnchor: [15, 15], // CENTER
        popupAnchor: [0, -15]
    });

    const iconHQ = L.divIcon({
        html: '<div class="marker-hq-inner"><i class="ph-fill ph-buildings"></i></div>',
        className: 'marker-hq', 
        iconSize: [40, 40], 
        iconAnchor: [20, 20] // CENTER
    });
    L.marker(HQ_COORDS, { icon: iconHQ }).bindTooltip("Kantor Pusat", { offset: [0, -15], direction: 'top' }).addTo(map);

    // BRANCH OFFICES
    const iconBranch = L.divIcon({
        html: '<div class="marker-branch-inner"><i class="ph-fill ph-house-line"></i></div>',
        className: 'marker-branch', 
        iconSize: [32, 32], 
        iconAnchor: [16, 16] // CENTER
    });

    const branches = [
        { name: "Cabang Praya Barat", lat: -8.7418725, lng: 116.242106 },
        { name: "Cabang Batukliang", lat: -8.6252367, lng: 116.3074722 },
        { name: "Cabang Batukliang Utara", lat: -8.5880157, lng: 116.3344747 },
        { name: "Cabang Pujut", lat: -8.7977049, lng: 116.2952782 },
        { name: "Cabang Praya Tengah", lat: -8.7144168, lng: 116.2867062 },
        { name: "Cabang Praya Barat Daya", lat: -8.7383657, lng: 116.2092708 },
        { name: "Cabang Kopang", lat: -8.6352987, lng: 116.3444956 },
        { name: "Cabang Janapria", lat: -8.695646, lng: 116.4024166 },
        { name: "Cabang Praya Timur", lat: -8.7623697, lng: 116.3592237 },
        { name: "Cabang Pos Kuta", lat: -8.8661849, lng: 116.2818779 },
        { name: "Cabang Pringgarata", lat: -8.6187931, lng: 116.2541116 },
        { name: "Cabang Praya", lat: -8.7125768, lng: 116.2655702 },
        { name: "Cabang Pos Bodak", lat: -8.6550009, lng: 116.2903405 }
    ];

    branches.forEach(b => {
        if (b.lat && b.lng) { // simple validation
            L.marker([b.lat, b.lng], { icon: iconBranch })
             .bindTooltip(b.name, { offset: [0, -10], direction: 'top', className: 'font-bold text-sky-500' })
             .addTo(map);
        }
    });

    let markers = L.markerClusterGroup({ disableClusteringAtZoom: 15, spiderfyOnMaxZoom: false });
    map.addLayer(markers);
    let lineLayer = L.layerGroup().addTo(map);
    let knownJobIds = new Set();
    let firstLoad = true;

    async function fetchData() {
        const date = document.getElementById('filterDate').value;
        try {
            const res = await fetch(`{{ route('api.map-data') }}?date=${date}`);
            const data = await res.json();
            updateMap(data);
        } catch (e) { console.error("Fetch error:", e); }
    }

    function updateMap(data) {
        markers.clearLayers();
        lineLayer.clearLayers();
        const listEl = document.getElementById('jobList');
        listEl.innerHTML = '';
        let countW = 0, countD = 0;
        let newDetected = false;
        let latestJob = null;

        data.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        const displayData = data.slice(0, 10);

        data.forEach(job => {
            if (job.status === 'on_progress') countW++; else countD++;
            if (job.status === 'on_progress' && !knownJobIds.has(job.id)) {
                if (!firstLoad) { newDetected = true; latestJob = job; }
                knownJobIds.add(job.id);
            }

            if (job.latitude && job.longitude) {
                const isWorking = job.status === 'on_progress';
                const latLng = [ parseFloat(job.latitude), parseFloat(job.longitude) ];
                
                if (isWorking) {
                    L.polyline([HQ_COORDS, latLng], {
                        color: currentPolylineColor, weight: 2, dashArray: '8, 12', className: 'ant-path ' + currentGlowClass, opacity: 0.9
                    }).addTo(lineLayer);
                }

                const marker = L.marker(latLng, { 
                    icon: isWorking ? iconWorking : iconDone,
                    zIndexOffset: isWorking ? 20000 : 0 
                });

                // HOVER TOOLTIP
                const imgBefore = job.photo_before || 'https://via.placeholder.com/150?text=No+Image';
                const imgAfterStr = job.photo_after ? `<img src="${job.photo_after}">` : '<div class="tooltip-placeholder"><i class="ph-bold ph-hourglass text-xl mb-1"></i><br>Sedang<br>diproses...</div>';

                const tooltipContent = `
                    <div class="tooltip-header">
                        <div>
                            <div style="font-size:0.8rem; font-weight:800; color:#fff;">${job.title}</div>
                            <div style="font-size:0.7rem; color:#aaa;">Petugas: ${job.user_name}</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:0.65rem; color:#888; text-transform:uppercase; font-weight:700;">Durasi</div>
                            <div style="font-size:0.8rem; font-weight:700; color:${isWorking ? '#ef4444' : '#10b981'};">${job.duration}</div>
                        </div>
                    </div>
                    <div class="tooltip-body">
                        <div class="tooltip-images">
                            <div class="tooltip-img-box">
                                <img src="${imgBefore}">
                                <div style="position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,0.6); color:white; font-size:9px; padding:2px 5px; text-align:center;">SEBELUM</div>
                            </div>
                            <div class="tooltip-img-box">
                                ${imgAfterStr}
                                <div style="position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,0.6); color:white; font-size:9px; padding:2px 5px; text-align:center;">SESUDAH</div>
                            </div>
                        </div>
                    </div>
                `;

                marker.bindTooltip(tooltipContent, {
                    permanent: false,
                    direction: 'top',
                    className: 'custom-tooltip',
                    offset: [0, -25],
                    opacity: 1
                });
                
                marker.on('click', () => { window.openJobModal(job); map.flyTo(latLng, 18, { duration: 1 }); });
                markers.addLayer(marker);
            }
        });
        
        displayData.forEach(job => {
             const isWorking = job.status === 'on_progress';
             const item = document.createElement('div');
             item.className = 'job-item';
             item.innerHTML = `<div class="job-row"><div class="job-title">${job.title}</div><div class="job-time">${job.created_at.split(' ')[1] || '-'}</div></div><div class="job-loc"><i class="ph-bold ph-map-pin"></i> ${job.address}</div><div class="job-status ${isWorking ? 'status-working' : 'status-done'}">${isWorking ? 'Sedang Dikerjakan' : 'Selesai'}</div>`;
            item.onclick = () => { if (job.latitude && job.longitude) { const latLng = [job.latitude, job.longitude]; map.flyTo(latLng, 18, { duration: 1.5 }); window.openJobModal(job); } };
            listEl.appendChild(item);
        });

        document.getElementById('panelWorking').innerText = countW;
        document.getElementById('panelDone').innerText = countD;

        if (newDetected && latestJob) playAlert(latestJob);
        firstLoad = false;
    }

    // Modal & Alert Logic
    // Modal & Alert Logic
    window.openJobModal = function(job) {
        document.getElementById('modalJobTitle').innerText = job.title;
        
        // Show loading state first, then fetch address
        const addrEl = document.getElementById('modalJobAddress');
        addrEl.innerHTML = '<span class="animate-pulse text-gray-400">Mendeteksi lokasi...</span>';
        
        if (job.latitude && job.longitude) {
            getAlamat(job.latitude, job.longitude).then(txt => {
                // If the modal is still open and showing this job, update it
                // Note: getAlamat returns HTML with icon, so use innerHTML
                addrEl.innerHTML = txt;
            });
        } else {
            addrEl.innerText = job.address || '-';
        }

        document.getElementById('modalJobTime').innerText = job.created_at;
        document.getElementById('modalJobDuration').innerText = job.duration || '-';
        const statusEl = document.getElementById('modalJobStatus');
        if (job.status === 'on_progress') { statusEl.className = 'inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-red-500/20 text-red-500 border border-red-500/30'; statusEl.innerText = 'SEDANG DIKERJAKAN'; } 
        else { statusEl.className = 'inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-500 border border-emerald-500/30'; statusEl.innerText = 'SELESAI'; }
        document.getElementById('jobDetailModal').classList.remove('hidden');
    }
    window.closeJobModal = function() { document.getElementById('jobDetailModal').classList.add('hidden'); }
    window.toggleFullScreen = function() { 
        const elem = document.getElementById('fullscreenContainer');
        if (!document.fullscreenElement && !document.webkitFullscreenElement) { 
            if (elem.requestFullscreen) { elem.requestFullscreen(); }
            else if (elem.webkitRequestFullscreen) { elem.webkitRequestFullscreen(); } // Safari/Chrome
            else if (elem.msRequestFullscreen) { elem.msRequestFullscreen(); } // IE11
        } else { 
            if (document.exitFullscreen) { document.exitFullscreen(); }
            else if (document.webkitExitFullscreen) { document.webkitExitFullscreen(); }
            else if (document.msExitFullscreen) { document.msExitFullscreen(); }
        } 
    }
    window.toggleSidebar = function() { document.getElementById('ccSidebar').classList.toggle('active'); }

    // Test Audio Function
    window.testAudio = function() {
        AUDIO_ALARM.currentTime = 0;
        AUDIO_ALARM.play().then(() => {
            Swal.fire({
                toast: true, position: 'top-end', icon: 'success', 
                title: 'Audio Berhasil!', showConfirmButton: false, timer: 1500
            });
        }).catch(e => {
            Swal.fire({
                icon: 'error', title: 'Audio Gagal', 
                text: e.message + '. Pastikan file public/sounds/beep.mp3 ada.',
                footer: '<a href="#">Isu Autoplay Policy Browser?</a>'
            });
        });
    }

    // ALERT: CENTER MODAL (No Toast)
    const addressCache = {};

    async function getAlamat(lat, lng) {
        const key = `${lat},${lng}`;
        if (addressCache[key]) return addressCache[key];

        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 3000);

        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, { 
                headers: { 'User-Agent': 'PDAM-Agenda-App' },
                signal: controller.signal 
            });
            clearTimeout(timeoutId);
            if (!res.ok) throw new Error('Network error');
            const data = await res.json();
            
            const addr = data.address;
            const parts = [];
            if (addr.village || addr.suburb || addr.neighbourhood) parts.push(addr.village || addr.suburb || addr.neighbourhood);
            if (addr.city || addr.town || addr.municipality) parts.push(addr.city || addr.town || addr.municipality);
            if (addr.county || addr.regency || addr.district) parts.push(addr.county || addr.regency || addr.district);

            const pinHtml = `<div class="inline-flex flex-col items-center justify-center relative translate-y-1"><i class="fa-solid fa-location-dot text-red-600 text-xl z-10 drop-shadow-md"></i><div class="w-3 h-1 bg-red-600/50 rounded-full blur-[1px] mt-[1px]"></div></div>`;
            
            const result = parts.length > 0 ? `${pinHtml} <span class="ml-2">${parts.join(', ')}</span>` : `${pinHtml} <span class="ml-2">Lokasi tidak tersedia</span>`;
            addressCache[key] = result;
            return result;
        } catch (error) {
            return `<i class="fa-solid fa-circle-exclamation text-red-500"></i> <span class="ml-2">Gagal memuat alamat</span>`; 
        }
    }

    function playAlert(job) {
        AUDIO_ALARM.currentTime = 0;
        AUDIO_ALARM.play().catch(e => console.log("Audio blocked", e));
        
        Swal.fire({
            html: `
                <div class="alert-icon-wrapper">
                    <div class="alert-radar-pulse"></div>
                    <i class="fa-solid fa-triangle-exclamation alert-main-icon"></i>
                </div>
                <h2 class="alert-title">TERDETEKSI GANGGUAN BARU</h2>
                <div class="alert-subtitle">${job.title || 'Kerusakan Pipa'}</div>
                
                <div id="lokasi-teks" class="text-md font-semibold text-white/90 mb-4 flex items-center justify-center gap-2 min-h-[30px]">
                    <div class="inline-flex flex-col items-center justify-center relative translate-y-1 animate-bounce">
                        <i class="fa-solid fa-location-dot text-red-600 text-xl z-10 drop-shadow-md"></i>
                        <div class="w-3 h-1 bg-red-600/50 rounded-full blur-[1px] mt-[1px]"></div>
                    </div>
                    <span class="animate-pulse ml-2">Mendeteksi alamat...</span>
                </div>

                <div class="flex items-center justify-center gap-2 mb-4">
                     <span class="px-2 py-1 rounded bg-white/5 border border-white/10 text-xs text-gray-400 font-mono"><i class="ph-fill ph-map-pin text-red-500"></i> GPS: ${job.latitude}, ${job.longitude}</span>
                </div>
            `,
            didOpen: () => {
                const el = Swal.getHtmlContainer().querySelector('#lokasi-teks');
                if (job.latitude && job.longitude) {
                    getAlamat(job.latitude, job.longitude).then(txt => {
                        if (el) {
                            el.innerHTML = txt;
                            el.classList.remove('animate-pulse');
                        }
                    });
                } else {
                    if (el) el.innerHTML = `<span class="text-gray-400">Lokasi tidak tersedia</span>`;
                }
            },
            showConfirmButton: true,
            showCancelButton: true,
            confirmButtonText: 'CEK LOKASI',
            cancelButtonText: 'TUTUP',
            background: 'transparent',
            backdrop: `rgba(0,0,0,0.7) center center no-repeat`,
            timer: 15000,
            timerProgressBar: false,
            buttonsStyling: true, // Use SweetAlert styling but with our custom CSS overrides
            showClass: { popup: 'animate__animated animate__fadeInUp animate__faster' },
            hideClass: { popup: 'animate__animated animate__fadeOutDown animate__faster' }
        }).then((result) => {
            if (result.isConfirmed) {
                if (job.latitude && job.longitude) {
                     map.flyTo([job.latitude, job.longitude], 18, { duration: 1.5 });
                     setTimeout(() => window.openJobModal(job), 1500);
                }
            }
        });
    }

    fetchData();
    setInterval(fetchData, 5000);
    document.getElementById('filterForm').addEventListener('submit', function(e) { e.preventDefault(); fetchData(); });
});
</script>
@endsection
