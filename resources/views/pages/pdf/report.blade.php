<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan - {{ $job->ticket_code }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; line-height: 1.4; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0284c7; padding-bottom: 15px; }
        .header h1 { margin: 0; color: #0284c7; font-size: 20px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; color: #64748b; font-size: 14px; }
        .grid { display: flex; flex-wrap: wrap; margin-bottom: 20px; border: 1px solid #e2e8f0; }
        .box { width: 50%; box-sizing: border-box; padding: 10px; border-bottom: 1px solid #e2e8f0; }
        .box-full { width: 100%; box-sizing: border-box; padding: 10px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
        .label { font-size: 10px; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .val { font-size: 13px; font-weight: bold; color: #0f172a; margin-top: 5px; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; }
        .bg-green { background: #dcfce7; color: #166534; }
        .bg-yellow { background: #fef9c3; color: #854d0e; }
        .bg-gray { background: #f1f5f9; color: #475569; }
        .photo-section { margin-top: 20px; page-break-inside: avoid; }
        .photo-title { font-size: 14px; font-weight: bold; border-bottom: 1px solid #cbd5e1; padding-bottom: 5px; margin-bottom: 15px; color: #334155; }
        .photo { width: 100%; max-height: 400px; object-fit: contain; border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px; }
        .footer { margin-top: 40px; font-size: 10px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Gangguan PDAM</h1>
        <p>TICKET: <strong>{{ $job->ticket_code }}</strong></p>
    </div>

    <table width="100%" style="border-collapse: collapse; margin-bottom: 20px;">
        <tr>
            <td style="padding: 10px; border: 1px solid #cbd5e1; width: 50%;">
                <div class="label">Status</div>
                <div class="val" style="margin-top: 5px;">
                    @if($job->status === 'selesai')
                        <span class="badge bg-green">Selesai</span>
                    @elseif($job->status === 'on_progress')
                        <span class="badge bg-yellow">Sedang Dikerjakan</span>
                    @else
                        <span class="badge bg-gray">Menunggu</span>
                    @endif
                </div>
            </td>
            <td style="padding: 10px; border: 1px solid #cbd5e1; width: 50%;">
                <div class="label">Jenis Gangguan</div>
                <div class="val">{{ $job->title }}</div>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #cbd5e1;">
                <div class="label">Dilaporkan Pada</div>
                <div class="val">{{ $job->created_at->format('d/m/Y H:i') }}</div>
            </td>
            <td style="padding: 10px; border: 1px solid #cbd5e1;">
                <div class="label">Selesai Pada</div>
                <div class="val">{{ $job->finished_at ? $job->finished_at->format('d/m/Y H:i') : '-' }}</div>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #cbd5e1;">
                <div class="label">Kecamatan</div>
                <div class="val">{{ $job->kecamatan ? $job->kecamatan->nama : '-' }}</div>
            </td>
            <td style="padding: 10px; border: 1px solid #cbd5e1;">
                <div class="label">Petugas Lapangan</div>
                <div class="val">{{ $job->user ? $job->user->name : '-' }}</div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 10px; border: 1px solid #cbd5e1; background: #f8fafc;">
                <div class="label">Alamat Lengkap</div>
                <div class="val">{{ $job->address }}</div>
            </td>
        </tr>
        <tr>
             <td style="padding: 10px; border: 1px solid #cbd5e1;">
                <div class="label">Nama Pelapor</div>
                <div class="val">{{ $job->reporter_name ?? '-' }}</div>
            </td>
            <td style="padding: 10px; border: 1px solid #cbd5e1;">
                <div class="label">No. Telepon / HP</div>
                <div class="val">{{ $job->reporter_phone ?? '-' }}</div>
            </td>
        </tr>
        <tr>
             <td colspan="2" style="padding: 10px; border: 1px solid #cbd5e1;">
                <div class="label">Keterangan / Deskripsi</div>
                <div class="val">{{ $job->description ?? '-' }}</div>
            </td>
        </tr>
    </table>

    <div class="photo-section">
        <div class="photo-title">Foto Sebelum Perbaikan (Before)</div>
        @if($job->photo_before && file_exists(public_path($job->photo_before)))
            <img src="{{ public_path($job->photo_before) }}" class="photo">
        @else
            <p style="color: #94a3b8; font-style: italic;">Tidak ada foto/dokumentasi tersedia.</p>
        @endif
    </div>

    <div style="page-break-before: always;"></div>

    <div class="photo-section">
        <div class="photo-title">Foto Hasil Perbaikan (After)</div>
        @if($job->photo_after && file_exists(public_path($job->photo_after)))
            <img src="{{ public_path($job->photo_after) }}" class="photo">
        @else
            <p style="color: #94a3b8; font-style: italic;">Tidak ada foto hasil perbaikan tersedia.</p>
        @endif
    </div>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} oleh Sistem Informasi Keluhan PDAM
    </div>

</body>
</html>
