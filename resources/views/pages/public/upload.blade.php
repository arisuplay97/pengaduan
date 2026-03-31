<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Upload Foto — {{ $job->ticket_code }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Plus Jakarta Sans',sans-serif;background:linear-gradient(135deg,#0c4a6e 0%,#0369a1 50%,#0284c7 100%);color:#fff;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .card{background:#fff;border-radius:28px;max-width:480px;width:100%;padding:28px;color:#0f172a;box-shadow:0 25px 50px rgba(0,0,0,.25)}
        .badge{display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:1px;padding:6px 12px;border-radius:8px;margin-bottom:12px}
        .badge-ticket{background:#e0f2fe;color:#0369a1}
        h1{font-size:20px;font-weight:900;margin-bottom:4px}
        .subtitle{font-size:12px;color:#64748b;margin-bottom:20px}
        .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:20px}
        .info-item{background:#f8fafc;border-radius:14px;padding:10px 12px;border:1px solid #e2e8f0}
        .info-label{font-size:9px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:1px}
        .info-value{font-size:12px;font-weight:700;color:#1e293b;margin-top:2px;word-break:break-word}
        .upload-area{border:2px dashed #cbd5e1;border-radius:18px;padding:20px;text-align:center;cursor:pointer;transition:all .3s;position:relative;overflow:hidden;margin-bottom:12px}
        .upload-area:hover{border-color:#0369a1;background:#f0f9ff}
        .upload-area.has-file{border-color:#22c55e;background:#f0fdf4}
        .upload-area input[type="file"]{position:absolute;inset:0;opacity:0;cursor:pointer;z-index:2}
        .upload-area i{font-size:32px;color:#94a3b8;margin-bottom:6px;display:block}
        .upload-area .label{font-size:12px;font-weight:700;color:#64748b}
        .upload-area .hint{font-size:10px;color:#94a3b8;margin-top:3px}
        .upload-area img{max-height:180px;width:100%;object-fit:cover;border-radius:12px;margin-top:8px}
        .btn{width:100%;padding:14px;border:none;border-radius:16px;font-size:14px;font-weight:800;cursor:pointer;transition:all .3s;display:flex;align-items:center;justify-content:center;gap:8px}
        .btn-primary{background:linear-gradient(135deg,#0369a1,#0284c7);color:#fff;box-shadow:0 8px 20px rgba(3,105,161,.3)}
        .btn-primary:hover{transform:translateY(-2px);box-shadow:0 12px 30px rgba(3,105,161,.4)}
        .btn-primary:disabled{opacity:.5;cursor:not-allowed;transform:none}
        .alert{padding:12px 16px;border-radius:14px;font-size:13px;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:8px}
        .alert-success{background:#dcfce7;color:#166534;border:1px solid #bbf7d0}
        .alert-error{background:#fee2e2;color:#991b1b;border:1px solid #fecaca}
        .status-badge{display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:800;padding:4px 10px;border-radius:20px}
        .status-selesai{background:#dcfce7;color:#166534}
        .status-assigned,.status-on_progress,.status-pending{background:#dbeafe;color:#1e40af}
        .divider{height:1px;background:#e2e8f0;margin:16px 0}
        .gps-status{font-size:11px;color:#64748b;display:flex;align-items:center;gap:4px;margin-bottom:12px}
        .done-card{text-align:center;padding:32px 16px}
        .done-card i{font-size:48px;color:#22c55e;margin-bottom:12px;display:block}
        .done-card h2{font-size:18px;font-weight:900;color:#166534;margin-bottom:4px}
        .done-card p{font-size:12px;color:#64748b}
        .photo-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:16px}
        .photo-grid img{width:100%;height:120px;object-fit:cover;border-radius:12px;border:2px solid #e2e8f0}
        .form-group{margin-bottom:16px;text-align:left}
        .form-label{display:block;font-size:11px;font-weight:800;color:#64748b;margin-bottom:6px;text-transform:uppercase;letter-spacing:1px}
        .form-select{width:100%;background:#f8fafc;border:2px solid #e2e8f0;border-radius:14px;padding:12px 14px;font-size:13px;font-weight:700;color:#1e293b;appearance:auto;outline:none;transition:all .3s;font-family:inherit}
        .form-select:focus{border-color:#0369a1;background:#f0f9ff;box-shadow:0 0 0 4px rgba(3,105,161,.1)}
        @keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}
    </style>
</head>
<body>
    <div class="card">
        @if(session('success'))
            <div class="alert alert-success"><i class="ph-fill ph-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-error"><i class="ph-fill ph-warning-circle"></i> {{ $errors->first() }}</div>
        @endif

        <div class="badge badge-ticket"><i class="ph-bold ph-ticket"></i> {{ $job->ticket_code }}</div>
        <h1>Upload Foto Lapangan</h1>
        <p class="subtitle">Ambil foto sebelum & sesudah perbaikan untuk dokumentasi.</p>

        <!-- Info -->
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Masalah</div>
                <div class="info-value">{{ $job->title }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="status-badge status-{{ $job->status }}">{{ strtoupper(str_replace('_', ' ', $job->status)) }}</span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Alamat</div>
                <div class="info-value">{{ Str::limit($job->address, 50) }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Petugas</div>
                <div class="info-value">{{ $job->user ? $job->user->name : '-' }}</div>
            </div>
        </div>

        @if($job->status === 'selesai' && $job->photo_before && $job->photo_after)
            {{-- Already done --}}
            <div class="done-card">
                <i class="ph-fill ph-check-circle"></i>
                <h2>Tugas Selesai! 🎉</h2>
                <p>Foto sudah diupload dan tiket ditandai selesai.</p>
                <div class="photo-grid">
                    <div>
                        <img src="{{ asset($job->photo_before) }}" alt="Before">
                        <div class="photo-label">BEFORE ✅</div>
                    </div>
                    <div>
                        <img src="{{ asset($job->photo_after) }}" alt="After">
                        <div class="photo-label">AFTER ✅</div>
                    </div>
                </div>
            </div>
        @else
            <div class="divider"></div>

            <!-- GPS Detection -->
            <div class="gps-status" id="gpsStatus">
                <i class="ph-bold ph-map-pin-line"></i>
                <span id="gpsText">Mendeteksi lokasi GPS...</span>
            </div>

            @if(!$job->photo_before)
                <div class="divider"></div>
                <div class="alert alert-success" style="background:#f0f9ff; color:#0369a1; border-color:#bae6fd;">
                    <i class="ph-bold ph-info"></i> Tahap 1: Upload Foto Kedatangan
                </div>

                <form action="{{ request()->fullUrl() }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">

                    <div class="upload-area" id="beforeBox">
                        <input type="file" name="photo_before" accept="image/*" capture="camera" required onchange="previewUpload(this, 'beforePreview', 'beforeBox')">
                        <i class="ph-duotone ph-camera"></i>
                        <div class="label">📷 Foto SEBELUM Perbaikan</div>
                        <div class="hint">Wajib diunggah di lokasi</div>
                        <img id="beforePreview" style="display:none">
                    </div>

                    <div class="form-group">
                        <label class="form-label">⏱️ Estimasi Lama Pengerjaan</label>
                        <select name="estimated_time" required class="form-select">
                            <option value="" disabled selected>-- Pilih Perkiraan Waktu --</option>
                            <option value="Kurang dari 30 Menit">Kurang dari 30 Menit</option>
                            <option value="30 Menit - 1 Jam">30 Menit - 1 Jam</option>
                            <option value="1 - 2 Jam">1 - 2 Jam</option>
                            <option value="2 - 3 Jam">2 - 3 Jam</option>
                            <option value="> 3 Jam">> 3 Jam</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="ph-bold ph-play-circle"></i> Mulai Pengerjaan (Step 1)
                    </button>
                </form>
            @else
                <div class="divider"></div>
                <div class="info-item" style="margin-bottom:16px; border-style:dashed;">
                    <div class="info-label">✅ Foto Kedatangan Terverifikasi</div>
                    <div style="display:flex; align-items:center; gap:10px; margin-top:8px;">
                        <img src="{{ asset($job->photo_before) }}" style="width:60px; height:60px; border-radius:8px; object-fit:cover;">
                        <div>
                            <div style="font-size:11px; font-weight:800; color:#1e293b;">Estimasi: {{ $job->estimated_time }}</div>
                            <div style="font-size:10px; color:#64748b;">Mulai: {{ $job->started_at->format('H:i') }} WITA</div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-success">
                    <i class="ph-bold ph-wrench"></i> Tahap 2: Upload Foto Penyelesaian
                </div>

                <form action="{{ request()->fullUrl() }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">

                    <div class="upload-area" id="afterBox">
                        <input type="file" name="photo_after" accept="image/*" capture="camera" required onchange="previewUpload(this, 'afterPreview', 'afterBox')">
                        <i class="ph-duotone ph-camera-rotate"></i>
                        <div class="label">📷 Foto SESUDAH Perbaikan</div>
                        <div class="hint">Wajib diunggah setelah selesai</div>
                        <img id="afterPreview" style="display:none">
                    </div>

                    <button type="submit" class="btn btn-primary" id="btnSubmit" style="background:linear-gradient(135deg,#166534,#22c55e); box-shadow:0 8px 20px rgba(22,101,52,.3);">
                        <i class="ph-bold ph-check-circle"></i> Selesaikan Tugas (Step 2)
                    </button>
                </form>
            @endif
        @endif
    </div>

    <script>
        // GPS
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    document.getElementById('latitude').value = pos.coords.latitude;
                    document.getElementById('longitude').value = pos.coords.longitude;
                    document.getElementById('gpsText').textContent = pos.coords.latitude.toFixed(6) + ', ' + pos.coords.longitude.toFixed(6) + ' ✅';
                },
                function() {
                    document.getElementById('gpsText').textContent = 'GPS tidak tersedia ⚠️';
                },
                { enableHighAccuracy: true }
            );
        }

        function previewUpload(input, previewId, boxId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById(previewId);
                    img.src = e.target.result;
                    img.style.display = 'block';
                    document.getElementById(boxId).classList.add('has-file');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.getElementById('uploadForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmit');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="ph-bold ph-spinner" style="animation:spin 1s linear infinite"></i> Mengupload & memproses...';
            }
        });
    </script>
</body>
</html>
