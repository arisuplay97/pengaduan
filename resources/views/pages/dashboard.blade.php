@extends('layouts.app')

@section('title', 'Dashboard - Tiara')

@section('content')
<style>
    /* ===== Dashboard Layout ===== */
    .dash {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 28px;
        align-items: start;
    }
    .dash-col { display:flex; flex-direction:column; gap: 28px; }

    @media (max-width: 1024px){
        .dash { grid-template-columns: 1fr; }
    }

    /* ===== Helpers ===== */
    .card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
    }
    .card-pad { padding: 1.75rem; }

    .section-head{
        display:flex; align-items:center; justify-content:space-between;
        gap: 12px;
        margin-bottom: 14px;
    }
    .section-title{
        font-size: 1.15rem;
        font-weight: 900;
        letter-spacing: -0.2px;
    }
    .muted{ color: var(--text-muted); }
    .small{ font-size: .85rem; }

    .btn-primary{
        background: var(--primary);
        color: #fff;
        border: 1px solid rgba(255,255,255,.0);
        padding: 10px 16px;
        border-radius: 999px;
        font-weight: 900;
        font-size: .85rem;
        cursor: pointer;
        transition: transform .18s ease, box-shadow .18s ease;
        box-shadow: 0 12px 28px -14px var(--primary-glow);
    }
    .btn-primary:hover{ transform: translateY(-2px); box-shadow: var(--shadow-md); }

    .btn-ghost{
        background: transparent;
        color: var(--primary);
        border: 1px solid var(--border-color);
        padding: 10px 14px;
        border-radius: 999px;
        font-weight: 900;
        font-size: .85rem;
        cursor: pointer;
        transition: .18s ease;
    }
    .btn-ghost:hover{ transform: translateY(-2px); box-shadow: var(--shadow-sm); }

    /* ===== Hero Summary ===== */
    .hero {
        border-radius: 32px;
        padding: 2.2rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        background: radial-gradient(1200px 600px at 10% 20%, rgba(255,255,255,.14), rgba(255,255,255,0)),
                    linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
        box-shadow: 0 22px 50px -18px rgba(99, 102, 241, .55);
        border: 1px solid rgba(255,255,255,.18);
    }
    .hero::after{
        content:'';
        position:absolute;
        top:-70px; right:-70px;
        width: 260px; height: 260px;
        background: rgba(255,255,255,.10);
        border-radius: 50%;
        filter: blur(.2px);
    }
    .hero-top{
        display:flex; align-items:flex-start; justify-content:space-between; gap: 18px;
        position: relative; z-index: 1;
    }
    .hero-title{
        font-size: 1.05rem;
        font-weight: 900;
        letter-spacing: .2px;
        margin-bottom: 6px;
    }
    .hero-sub{
        font-size: .9rem;
        color: rgba(255,255,255,.82);
        font-weight: 650;
        line-height: 1.35;
        max-width: 560px;
    }

    .stats{
        display:grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-top: 18px;
        position: relative;
        z-index: 1;
    }
    @media (max-width: 640px){
        .stats{ grid-template-columns: repeat(2, 1fr); }
    }

    .stat{
        background: rgba(255,255,255,.92);
        color: var(--text-main);
        border-radius: 18px;
        padding: 14px 14px 12px;
        border: 1px solid rgba(255,255,255,.35);
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .stat:hover{ transform: translateY(-2px); box-shadow: 0 18px 45px rgba(2,6,23,.10); }
    .stat-kicker{
        display:flex; align-items:center; justify-content:space-between;
        font-size: .72rem;
        font-weight: 900;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 8px;
    }
    .stat-val{
        font-size: 2.0rem;
        font-weight: 950;
        letter-spacing: -1px;
        line-height: 1;
    }
    .pill{
        display:inline-flex;
        align-items:center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 900;
        border: 1px solid rgba(255,255,255,.25);
        background: rgba(255,255,255,.12);
        color: rgba(255,255,255,.92);
        user-select:none;
    }
    .pill i{ font-size: 1rem; }

    /* ===== AI Bar ===== */
    .ai {
        margin-top: 18px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.20);
        border-radius: 24px;
        padding: 14px;
        backdrop-filter: blur(10px);
        position: relative;
        z-index: 1;
    }
    .chips{
        display:flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }
    .chip{
        background: rgba(255,255,255,.16);
        border: 1px solid rgba(255,255,255,.20);
        color: #fff;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 850;
        cursor: pointer;
        transition: .18s ease;
    }
    .chip:hover{ background: rgba(255,255,255,.95); color: #4F46E5; }

    .ai-input{
        display:flex; align-items:center; gap: 10px;
        background: rgba(255,255,255,.96);
        border-radius: 999px;
        padding: 8px 8px 8px 14px;
        border: 1px solid rgba(2,6,23,.06);
    }
    .ai-input i{ color: var(--primary); font-size: 1.25rem; }
    .ai-input input{
        border: none;
        background: transparent;
        flex: 1;
        font-weight: 650;
        color: var(--text-main);
        font-size: .95rem;
        padding: 6px 0;
    }
    .ai-send{
        width: 40px; height: 40px;
        border-radius: 999px;
        border: none;
        background: var(--text-main);
        color: #fff;
        cursor: pointer;
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .ai-send:hover{ transform: translateY(-1px); box-shadow: 0 16px 32px rgba(2,6,23,.20); }

    /* ===== Tasks ===== */
    .task-list{ display:flex; flex-direction:column; gap: 14px; }

    .task{
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 14px 14px 14px 16px;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap: 14px;
        position: relative;
        overflow:hidden;
        transition: transform .18s ease, box-shadow .18s ease;
        box-shadow: var(--shadow-sm);
    }
    .task:hover{ transform: translateY(-2px); box-shadow: var(--shadow-md); }

    .task::before{
        content:'';
        position:absolute; left:0; top:0; bottom:0;
        width: 6px;
        background: var(--border-color);
    }
    .task.urgent::before{ background: var(--danger); }
    .task.warn::before{ background: var(--warning); }
    .task.ok::before{ background: var(--success); }

    .task-left{ display:flex; align-items:center; gap: 14px; min-width: 0; }
    .task-icon{
        width: 46px; height: 46px;
        border-radius: 14px;
        display:grid; place-items:center;
        border: 1px solid var(--border-color);
        background: var(--bg-elev);
        color: var(--text-muted);
        flex: 0 0 auto;
        font-size: 1.35rem;
    }
    .task.urgent .task-icon{ color: var(--danger); }
    .task.warn .task-icon{ color: var(--warning); }
    .task.ok .task-icon{ color: var(--success); }

    .task-title{
        font-weight: 950;
        letter-spacing: -0.2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .task-meta{
        margin-top: 4px;
        font-size: .85rem;
        font-weight: 750;
        color: var(--text-muted);
        display:flex;
        align-items:center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .badge{
        display:inline-flex;
        align-items:center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 900;
        border: 1px solid var(--border-color);
        background: var(--bg-elev);
        color: var(--text-muted);
    }
    .badge.danger{ color: var(--danger); border-color: rgba(239,68,68,.25); }
    .badge.warn{ color: var(--warning); border-color: rgba(245,158,11,.28); }
    .badge.ok{ color: var(--success); border-color: rgba(16,185,129,.25); }

    .task-right{
        display:flex; align-items:center; gap: 10px;
        flex: 0 0 auto;
    }
    .avatars{ display:flex; align-items:center; }
    .avatar{
        width: 30px; height: 30px;
        border-radius: 999px;
        border: 2px solid var(--bg-card);
        margin-left: -8px;
        object-fit: cover;
        box-shadow: 0 8px 18px rgba(2,6,23,.10);
    }
    .task-actions{
        display:flex; gap: 8px;
    }
    .icon-mini{
        width: 40px; height: 40px;
        border-radius: 14px;
        border: 1px solid var(--border-color);
        background: var(--bg-elev);
        color: var(--text-muted);
        display:grid; place-items:center;
        cursor:pointer;
        transition: .18s ease;
    }
    .icon-mini:hover{ transform: translateY(-1px); box-shadow: var(--shadow-sm); color: var(--primary); }

    /* ===== Calendar ===== */
    .cal-head{
        display:flex; align-items:center; justify-content:space-between; gap: 12px;
        margin-bottom: 14px;
    }
    .cal-month{
        font-weight: 950;
        color: var(--primary);
        cursor:pointer;
        display:inline-flex;
        align-items:center;
        gap: 8px;
        user-select:none;
    }
    .date-strip{
        display:flex;
        gap: 10px;
        justify-content: space-between;
        margin-top: 14px;
    }
    .date-box{
        flex: 1 1 auto;
        text-align:center;
        padding: 10px 8px;
        border-radius: 16px;
        border: 1px solid var(--border-color);
        background: var(--bg-elev);
        cursor:pointer;
        transition: .18s ease;
        min-width: 54px;
    }
    .date-box:hover{ transform: translateY(-1px); box-shadow: var(--shadow-sm); }
    .date-box .dow{
        font-size: .72rem;
        font-weight: 900;
        color: var(--text-muted);
        letter-spacing: 1px;
    }
    .date-box .dom{
        display:block;
        margin-top: 6px;
        font-size: 1.1rem;
        font-weight: 950;
    }
    .date-box.active{
        background: var(--primary);
        border-color: rgba(255,255,255,.0);
        color: #fff;
        box-shadow: 0 14px 32px -16px var(--primary-glow);
    }
    .date-box.active .dow{ color: rgba(255,255,255,.85); }

    .cal-subhead{
        display:flex; align-items:center; justify-content:space-between;
        margin-top: 18px;
    }
    .kicker{
        font-size: .72rem;
        font-weight: 950;
        color: var(--text-muted);
        letter-spacing: 1.2px;
    }

    .timeline{
        margin-top: 14px;
        display:flex;
        flex-direction:column;
        gap: 12px;
    }
    .event{
        display:flex;
        gap: 12px;
        align-items:flex-start;
    }
    .time{
        width: 54px;
        font-size: .85rem;
        font-weight: 900;
        color: var(--text-muted);
        padding-top: 10px;
        text-align:left;
    }
    .event-card{
        flex:1;
        border-radius: 18px;
        background: var(--bg-elev);
        border: 1px solid var(--border-color);
        padding: 12px 12px 12px 14px;
        position: relative;
        overflow:hidden;
    }
    .event-card::before{
        content:'';
        position:absolute; left:0; top:0; bottom:0;
        width: 4px;
        background: var(--primary);
        opacity: .9;
    }
    .event-title{
        font-weight: 950;
        margin-bottom: 6px;
    }
    .event-meta{
        font-size: .78rem;
        font-weight: 800;
        color: var(--text-muted);
        display:flex;
        align-items:center;
        gap: 8px;
        margin-bottom: 10px;
    }
    .event-actions{ display:flex; gap: 10px; flex-wrap: wrap; }
    .btn-join{
        background: var(--bg-card);
        border: 1.5px solid rgba(99,102,241,.55);
        color: var(--primary);
        padding: 8px 12px;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 950;
        cursor:pointer;
        transition: .18s ease;
    }
    .btn-join:hover{ transform: translateY(-1px); box-shadow: var(--shadow-sm); }

</style>

<div class="dash">
    <!-- LEFT -->
    <div class="dash-col">
        <!-- HERO SUMMARY -->
        <div class="hero">
            <div class="hero-top">
                <div>
                    <div class="hero-title">Ringkasan Hari Ini</div>
                    <div class="hero-sub">Fokus ke tugas prioritas & agenda terdekat. Tiara bisa bantu buat ringkasan, draft email, atau susun agenda rapat.</div>
                </div>
                <div class="pill" title="Data ringkasan hari ini">
                    <i class="ph ph-shield-check"></i> Updated
                </div>
            </div>

            <div class="stats">
                <div class="stat" title="Total seluruh tugas yang sedang kamu kelola">
                    <div class="stat-kicker">Total <span class="muted">Tasks</span></div>
                    <div class="stat-val">23</div>
                </div>

                <div class="stat" title="Tugas yang sudah selesai">
                    <div class="stat-kicker">Selesai</div>
                    <div class="stat-val">19</div>
                </div>

                <div class="stat" title="Tugas yang sedang dikerjakan">
                    <div class="stat-kicker">Proses</div>
                    <div class="stat-val">4</div>
                </div>

                <div class="stat" title="Terhambat: tugas yang tertahan karena kendala (bisa overlap dengan Proses)">
                    <div class="stat-kicker">Terhambat <span class="muted">?</span></div>
                    <div class="stat-val" style="color: var(--danger);">11</div>
                </div>
            </div>

            <div class="ai">
                <div class="chips">
                    <button class="chip">✨ Ringkas prioritas hari ini</button>
                    <button class="chip">📧 Draft email tindak lanjut</button>
                    <button class="chip">🧭 Susun agenda rapat</button>
                </div>

                <div class="ai-input">
                    <i class="ph-fill ph-sparkle" aria-hidden="true"></i>
                    <input type="text" placeholder="Tanya Tiara AI (contoh: buatkan ringkasan 3 task urgent)" aria-label="Tanya Tiara AI">
                    <button class="ai-send" aria-label="Kirim"><i class="ph-bold ph-arrow-right"></i></button>
                </div>
            </div>
        </div>

        <!-- TASKS -->
        <div class="card card-pad">
            <div class="section-head">
                <div>
                    <div class="section-title">My Tasks</div>
                    <div class="muted small">Prioritaskan yang mendekati deadline atau status blocked.</div>
                </div>
                <button class="btn-primary" onclick="openNewTaskModal()">+ New Task</button>
            </div>

            <div class="task-list">
                <div class="task urgent">
                    <div class="task-left">
                        <div class="task-icon" title="Urgent"><i class="ph-fill ph-warning"></i></div>
                        <div style="min-width:0;">
                            <div class="task-title">Review Laporan Keuangan PDAM</div>
                            <div class="task-meta">
                                <span>Divisi Keuangan</span>
                                <span>•</span>
                                <span class="badge danger"><i class="ph ph-clock"></i> Hari H 17:00</span>
                                <span class="badge warn"><i class="ph ph-lock-key"></i> Blocked</span>
                            </div>
                        </div>
                    </div>

                    <div class="task-right">
                        <div class="avatars" title="PIC">
                            <img src="https://ui-avatars.com/api/?name=Budi" class="avatar" alt="Budi">
                            <img src="https://ui-avatars.com/api/?name=Siti" class="avatar" alt="Siti">
                        </div>
                        <div class="task-actions">
                            <button class="icon-mini" title="Tandai selesai"><i class="ph ph-check"></i></button>
                            <button class="icon-mini" title="Detail"><i class="ph ph-eye"></i></button>
                        </div>
                    </div>
                </div>

                <div class="task warn">
                    <div class="task-left">
                        <div class="task-icon" title="Soon"><i class="ph-fill ph-clock"></i></div>
                        <div style="min-width:0;">
                            <div class="task-title">Persiapan Rapat Tahunan</div>
                            <div class="task-meta">
                                <span>Divisi SDM</span>
                                <span>•</span>
                                <span class="badge warn"><i class="ph ph-calendar"></i> 3 Hari Lagi</span>
                            </div>
                        </div>
                    </div>

                    <div class="task-right">
                        <div class="avatars" title="PIC">
                            <img src="https://ui-avatars.com/api/?name=Andi" class="avatar" alt="Andi">
                        </div>
                        <div class="task-actions">
                            <button class="icon-mini" title="Tandai selesai"><i class="ph ph-check"></i></button>
                            <button class="icon-mini" title="Detail"><i class="ph ph-eye"></i></button>
                        </div>
                    </div>
                </div>

                {{-- Kalau kosong, ini enak buat empty state --}}
                {{-- <div class="muted small" style="padding: 10px 6px;">Tidak ada task. Mantap 👏</div> --}}
            </div>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="dash-col">
        <div class="card card-pad">
            <div class="cal-head">
                <div class="section-title">Calendar</div>
                <div class="cal-month">July 2025 <i class="ph-bold ph-caret-right"></i></div>
            </div>

            <div class="date-strip">
                <div class="date-box"><div class="dow">THU</div><span class="dom">5</span></div>
                <div class="date-box active"><div class="dow">FRI</div><span class="dom">6</span></div>
                <div class="date-box"><div class="dow">SAT</div><span class="dom">7</span></div>
                <div class="date-box"><div class="dow">SUN</div><span class="dom">8</span></div>
                <div class="date-box"><div class="dow">MON</div><span class="dom">9</span></div>
            </div>

            <div class="cal-subhead">
                <div class="kicker">TODAY’S SCHEDULE</div>
                <button class="btn-ghost" onclick="openAddScheduleModal()">+ Add Event</button>
            </div>

            <div class="timeline">
                <div class="event">
                    <div class="time">10:00</div>
                    <div class="event-card">
                        <div class="event-title">Meeting with VP</div>
                        <div class="event-meta">
                            <span><i class="ph ph-clock"></i> 10:00 - 11:00</span>
                            <span>•</span>
                            <span><i class="ph ph-map-pin"></i> Ruang Direksi</span>
                        </div>
                        <div class="event-actions">
                            <button class="btn-join">Join Meet</button>
                            <button class="btn-ghost" style="padding:8px 12px;">Detail</button>
                        </div>
                    </div>
                </div>

                {{-- Tambahin event kedua biar timeline terasa hidup --}}
                <div class="event">
                    <div class="time">14:00</div>
                    <div class="event-card" style="--primary: var(--warning);">
                        <div class="event-title">Review Proyek & Risiko</div>
                        <div class="event-meta">
                            <span><i class="ph ph-clock"></i> 14:00 - 14:45</span>
                            <span>•</span>
                            <span><i class="ph ph-users"></i> Tim Inti</span>
                        </div>
                        <div class="event-actions">
                            <button class="btn-join">Open Notes</button>
                            <button class="btn-ghost" style="padding:8px 12px;">Detail</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kamu bisa tambah card "Notifications" di sini kalau mau --}}
    </div>
</div>

<script>
    async function openAddScheduleModal() {
        const { value: formValues } = await Swal.fire({
            title: 'Buat Agenda Baru',
            html: `
                <span class="swal-label">Judul Kegiatan</span>
                <input id="sched-title" class="swal2-input" placeholder="Contoh: Rapat Koordinasi Pipa">

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                    <div>
                        <span class="swal-label">Tanggal</span>
                        <input id="sched-date" type="date" class="swal2-input">
                    </div>
                    <div>
                        <span class="swal-label">Waktu</span>
                        <input id="sched-time" type="time" class="swal2-input">
                    </div>
                </div>

                <span class="swal-label">Penanggung Jawab (PIC)</span>
                <select id="sched-pic" class="swal2-select">
                    <option value="Arief">Pak Arief (Saya)</option>
                    <option value="Budi">Budi Santoso (Teknik)</option>
                    <option value="Siti">Siti Aminah (Keuangan)</option>
                </select>

                <span class="swal-label">Prioritas</span>
                <select id="sched-prio" class="swal2-select">
                    <option value="normal">Normal</option>
                    <option value="high">🔥 Tinggi (Urgent)</option>
                </select>
            `,
            confirmButtonText: 'Simpan Agenda',
            confirmButtonColor: '#6366F1',
            showCancelButton: true,
            preConfirm: () => {
                return [
                    document.getElementById('sched-title').value,
                    document.getElementById('sched-pic').value,
                    document.getElementById('sched-date').value
                ]
            }
        });
        if (formValues) Swal.fire('Berhasil', 'Agenda telah dijadwalkan.', 'success');
    }

    async function openNewTaskModal() {
        const { value: formValues } = await Swal.fire({
            title: 'Tambah Tugas',
            html: `
                <span class="swal-label">Nama Tugas</span>
                <input id="task-title" class="swal2-input" placeholder="Apa yang perlu diselesaikan?">
                <span class="swal-label">Keterangan</span>
                <input id="task-desc" class="swal2-input" placeholder="Detail singkat...">
                <span class="swal-label">Deadline</span>
                <select id="task-dl" class="swal2-select">
                    <option value="today">Hari H</option>
                    <option value="soon">3 Hari Lagi</option>
                    <option value="safe">Minggu Depan</option>
                </select>
            `,
            confirmButtonText: 'Buat Tugas',
            confirmButtonColor: '#6366F1',
            showCancelButton: true
        });
    }
</script>
@endsection
