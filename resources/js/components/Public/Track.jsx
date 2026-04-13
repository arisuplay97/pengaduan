import React, { useState, useEffect, useRef } from 'react';

const Track = ({ initialTicket, initialCode }) => {
    const [ticketCode, setTicketCode] = useState(initialCode || '');
    const [ticket, setTicket] = useState(initialTicket);
    const [copied, setCopied] = useState(false);
    const [lastRefresh, setLastRefresh] = useState(null);

    // Rating state
    const [hoverStar, setHoverStar] = useState(0);
    const [selectedStar, setSelectedStar] = useState(0);
    const [ratingFeedback, setRatingFeedback] = useState('');
    const [ratingSubmitted, setRatingSubmitted] = useState(false);
    const [ratingLoading, setRatingLoading] = useState(false);
    const [ratingError, setRatingError] = useState('');

    // Cancel state
    const [showCancelModal, setShowCancelModal] = useState(false);
    const [cancelPhone, setCancelPhone] = useState('');
    const [cancelLoading, setCancelLoading] = useState(false);
    const [cancelError, setCancelError] = useState('');

    // Rating modal phone verification
    const [showRatingPhone, setShowRatingPhone] = useState(false);
    const [ratingPhone, setRatingPhone] = useState('');

    // CSRF token helper
    const getCsrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ── Auto-Refresh polling (30s) ──
    useEffect(() => {
        if (!ticket || ['selesai', 'dibatalkan', 'ditutup'].includes(ticket.status)) return;

        const interval = setInterval(async () => {
            try {
                const res = await fetch(`/api/lacak?kode=${ticket.ticket_code}`);
                if (res.ok) {
                    const data = await res.json();
                    setTicket(data);
                    setLastRefresh(new Date());
                }
            } catch (e) { /* silent */ }
        }, 30000);

        return () => clearInterval(interval);
    }, [ticket?.ticket_code, ticket?.status]);

    // Check if already rated
    useEffect(() => {
        if (ticket?.rating) setRatingSubmitted(true);
    }, [ticket?.rating]);

    // ── Copy to clipboard ──
    const handleCopy = () => {
        const url = `${window.location.origin}/lacak?kode=${ticket.ticket_code}`;
        navigator.clipboard.writeText(url).then(() => {
            setCopied(true);
            setTimeout(() => setCopied(false), 2500);
        });
    };

    // ── Submit CSAT Rating ──
    const handleSubmitRating = async () => {
        if (!selectedStar) return;
        setRatingLoading(true);
        setRatingError('');
        try {
            const res = await fetch('/api/lacak/rate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
                body: JSON.stringify({
                    ticket_code: ticket.ticket_code,
                    reporter_phone: ratingPhone,
                    rating: selectedStar,
                    feedback: ratingFeedback,
                }),
            });
            const data = await res.json();
            if (data.success) {
                setRatingSubmitted(true);
                setShowRatingPhone(false);
            } else {
                setRatingError(data.message || 'Gagal mengirim rating.');
            }
        } catch (e) {
            setRatingError('Terjadi kesalahan jaringan.');
        } finally {
            setRatingLoading(false);
        }
    };

    // ── Cancel Ticket ──
    const handleCancel = async () => {
        setCancelLoading(true);
        setCancelError('');
        try {
            const res = await fetch('/api/lacak/cancel', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
                body: JSON.stringify({
                    ticket_code: ticket.ticket_code,
                    reporter_phone: cancelPhone,
                }),
            });
            const data = await res.json();
            if (data.success) {
                setTicket({ ...ticket, status: 'dibatalkan', cancelled_at: new Date().toISOString() });
                setShowCancelModal(false);
            } else {
                setCancelError(data.message || 'Gagal membatalkan.');
            }
        } catch (e) {
            setCancelError('Terjadi kesalahan jaringan.');
        } finally {
            setCancelLoading(false);
        }
    };

    return (
        <div className="min-h-screen bg-slate-50 font-jakarta text-slate-800">
            {/* Header */}
            <nav className="bg-white border-b border-slate-100 px-6 py-4 sticky top-0 z-40">
                <div className="max-w-2xl mx-auto flex items-center justify-between">
                    <a href="/" className="flex items-center gap-2 text-slate-500 hover:text-sky-600 font-bold transition">
                        <i className="ph-bold ph-arrow-left text-lg"></i> Kembali
                    </a>
                </div>
            </nav>

            <div className="max-w-2xl mx-auto px-6 py-12 animate-fade-in-up">

                <div className="text-center mb-10">
                    <div className="inline-flex items-center gap-2 px-4 py-1.5 bg-sky-100 text-sky-700 rounded-full text-xs font-bold mb-4 shadow-sm">
                        <i className="ph-bold ph-magnifying-glass"></i> Tracking System
                    </div>
                    <h1 className="text-3xl font-black text-slate-900 mb-2">Lacak Tiket Anda</h1>
                    <p className="text-slate-500 font-medium text-sm">Masukkan nomor tiket untuk memantau progress perbaikan secara real-time</p>
                </div>

                {/* Search Box */}
                <div className="bg-white rounded-3xl p-4 sm:p-6 shadow-xl shadow-slate-200/50 border border-slate-100 mb-8">
                    <form method="GET" action="/lacak" className="flex flex-col sm:flex-row gap-4">
                        <div className="relative flex-1">
                            <i className="ph-bold ph-ticket absolute left-5 top-1/2 -translate-y-1/2 text-sky-500 text-lg"></i>
                            <input
                                type="text"
                                name="kode"
                                value={ticketCode}
                                onChange={(e) => setTicketCode(e.target.value)}
                                placeholder="Contoh: TKT-XYZ-1234"
                                required
                                className="w-full pl-14 pr-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-black text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-sky-400 focus:bg-white transition uppercase tracking-widest font-mono"
                            />
                        </div>
                        <button type="submit" className="px-8 py-4 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-2xl shadow-[0_0_20px_rgba(14,165,233,0.3)] hover:shadow-[0_0_30px_rgba(14,165,233,0.5)] transition hover:-translate-y-1 flex items-center justify-center gap-2 shrink-0">
                            Cari Tiket <i className="ph-bold ph-arrow-right"></i>
                        </button>
                    </form>
                </div>

                {/* Not Found */}
                {initialCode && !ticket && (
                    <div className="bg-white rounded-3xl p-10 text-center shadow-lg border border-red-50 animate-fade-in-up">
                        <div className="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-5">
                            <i className="ph-fill ph-warning-circle text-red-500 text-4xl"></i>
                        </div>
                        <h3 className="text-xl font-black text-slate-900 mb-2">Tiket Tidak Ditemukan</h3>
                        <p className="text-slate-500 text-sm">Nomor <span className="font-mono font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded">{initialCode}</span> tidak terdaftar di sistem kami.</p>
                    </div>
                )}

                {/* Result */}
                {ticket && (
                    <div className="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden animate-fade-in-up">

                        {/* Status Header */}
                        <div className="bg-gradient-to-br from-slate-900 to-slate-800 p-8 text-white relative overflow-hidden">
                            <i className="ph-fill ph-ticket absolute -right-6 -bottom-6 text-9xl text-white/5 rotate-[-15deg]"></i>

                            <div className="flex items-center justify-between relative z-10 mb-4">
                                <span className="text-xs font-bold text-slate-400 uppercase tracking-widest">Detail Laporan</span>
                                {ticket.status === 'selesai' ? (
                                    <span className="bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 px-4 py-1.5 rounded-full text-xs font-bold flex items-center gap-1.5 backdrop-blur-md">
                                        <i className="ph-fill ph-check-circle"></i> Selesai
                                    </span>
                                ) : ticket.status === 'dibatalkan' ? (
                                    <span className="bg-red-500/20 border border-red-500/30 text-red-400 px-4 py-1.5 rounded-full text-xs font-bold flex items-center gap-1.5 backdrop-blur-md">
                                        <i className="ph-fill ph-x-circle"></i> Dibatalkan
                                    </span>
                                ) : (ticket.status === 'on_progress' || ticket.status === 'assigned') ? (
                                    <span className="bg-amber-500/20 border border-amber-500/30 text-amber-400 px-4 py-1.5 rounded-full text-xs font-bold flex items-center gap-1.5 backdrop-blur-md">
                                        <span className="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span> Dalam Pengerjaan
                                    </span>
                                ) : (
                                    <span className="bg-sky-500/20 border border-sky-500/30 text-sky-400 px-4 py-1.5 rounded-full text-xs font-bold flex items-center gap-1.5 backdrop-blur-md">
                                        <i className="ph-fill ph-clock"></i> Menunggu Petugas
                                    </span>
                                )}
                            </div>

                            <h2 className="text-3xl sm:text-4xl font-black font-mono tracking-widest relative z-10">{ticket.ticket_code}</h2>

                            {/* Copy Link Button */}
                            <button
                                onClick={handleCopy}
                                className="mt-4 relative z-10 inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-white bg-white/10 hover:bg-white/20 px-3.5 py-2 rounded-xl transition-all"
                            >
                                <i className={`ph-bold ${copied ? 'ph-check' : 'ph-copy'} text-sm`}></i>
                                {copied ? 'Tersalin ✓' : 'Salin Link Pelacakan'}
                            </button>
                        </div>

                        <div className="p-8">

                            {/* Auto-refresh badge */}
                            {ticket && !['selesai', 'dibatalkan', 'ditutup'].includes(ticket.status) && (
                                <div className="flex items-center gap-2 mb-6 text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                                    <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    Update otomatis setiap 30 detik
                                    {lastRefresh && <span className="text-slate-300 normal-case"> · terakhir {lastRefresh.toLocaleTimeString('id-ID', { timeStyle: 'short' })}</span>}
                                </div>
                            )}

                            {/* Grid Info */}
                            <div className="grid grid-cols-2 gap-4 mb-10">
                                <div className="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                    <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Gangguan</p>
                                    <p className="text-sm font-bold text-slate-800">{ticket.title}</p>
                                </div>
                                <div className="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                    <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Dilaporkan</p>
                                    <p className="text-sm font-bold text-slate-800">{new Date(ticket.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })}</p>
                                </div>

                                <div className="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                    <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Pelapor</p>
                                    <p className="text-sm font-bold text-slate-800">{ticket.reporter_name}</p>
                                </div>
                                <div className="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                    <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">No. HP Pelapor</p>
                                    <p className="text-sm font-bold text-slate-800">{ticket.reporter_phone}</p>
                                </div>

                                <div className="col-span-2 bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                    <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Alamat</p>
                                    <p className="text-sm font-bold text-slate-800 leading-relaxed">{ticket.address}</p>
                                </div>
                            </div>

                            {/* Timeline */}
                            <h3 className="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                                <i className="ph-bold ph-git-commit"></i> Riwayat Penanganan
                            </h3>
                            
                            <div className="relative pl-6 space-y-8">
                                {/* Base line */}
                                <div className="absolute left-10 top-2 bottom-2 w-[2px] bg-slate-100/80 rounded-full"></div>
                                
                                {/* Active progress line */}
                                <div 
                                    className={`absolute left-10 top-2 w-[2px] rounded-full transition-all duration-1000 ${ticket.status === 'dibatalkan' ? 'bg-red-400' : 'bg-emerald-500'}`}
                                    style={{ 
                                        height: ticket.status === 'dibatalkan' ? '14%' : (ticket.finished_at ? '98%' : (ticket.started_at ? '66%' : (ticket.status !== 'pending' ? '40%' : '14%')))
                                    }}
                                ></div>

                                {/* Step 1: Laporan Terdaftar */}
                                <div className="relative flex items-start gap-5">
                                    <div className="w-8 h-8 rounded-full bg-emerald-100 border-4 border-white shadow-sm flex items-center justify-center shrink-0 relative z-10">
                                        <i className="ph-bold ph-check text-emerald-600 text-sm"></i>
                                    </div>
                                    <div className="pt-1">
                                        <h4 className="text-sm font-bold text-slate-800">Laporan Terdaftar</h4>
                                        <p className="text-xs text-slate-400 mt-1 font-medium">Laporan masuk ke sistem dan sedang ditinjau administrasi.</p>
                                        <p className="text-xs text-slate-400 font-bold mt-1">{new Date(ticket.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })}</p>
                                    </div>
                                </div>

                                {/* Step 2: Petugas Sedang Mempersiapkan */}
                                <div className="relative flex items-start gap-5">
                                    <div className={`w-8 h-8 rounded-full border-4 border-white shadow-sm flex items-center justify-center shrink-0 relative z-10 ${ticket.status === 'dibatalkan' ? 'bg-red-100 text-red-500' : ticket.status !== 'pending' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-500'}`}>
                                        {ticket.status === 'dibatalkan' ? <i className="ph-bold ph-x text-sm"></i> : ticket.status !== 'pending' ? <i className="ph-bold ph-check text-sm"></i> : <span className="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>}
                                    </div>
                                    <div className="pt-1">
                                        <h4 className="text-sm font-bold text-slate-800">
                                            {ticket.status === 'dibatalkan' ? 'Laporan Dibatalkan' : 'Petugas Sedang Mempersiapkan'}
                                        </h4>
                                        <p className="text-xs text-slate-400 mt-1 font-medium">
                                            {ticket.status === 'dibatalkan'
                                                ? 'Laporan dibatalkan oleh pelapor.'
                                                : ticket.status === 'pending' ? 'Sistem sedang memanggil teknisi terdekat di wilayah pendaftaran.' : 'Teknisi lapangan telah dikonfirmasi.'}
                                        </p>
                                        {ticket.cancelled_at && (
                                            <p className="text-xs text-red-400 font-bold mt-1">{new Date(ticket.cancelled_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })}</p>
                                        )}
                                    </div>
                                </div>

                                {/* Cancelled — stop timeline here */}
                                {ticket.status !== 'dibatalkan' && (
                                    <>
                                        {/* Step 3: Petugas Menuju Lokasi */}
                                        <div className="relative flex items-start gap-5">
                                            <div className={`w-8 h-8 rounded-full border-4 border-white shadow-sm flex items-center justify-center shrink-0 relative z-10 ${ticket.started_at ? 'bg-emerald-100 text-emerald-600' : (ticket.status !== 'pending' ? 'bg-amber-100 text-amber-500' : 'bg-slate-100 text-slate-300')}`}>
                                                {ticket.started_at ? <i className="ph-bold ph-check text-sm"></i> : (ticket.status !== 'pending' ? <span className="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span> : <div className="w-1.5 h-1.5 rounded-full bg-slate-300"></div>)}
                                            </div>
                                            <div className="pt-1">
                                                <h4 className={`text-sm font-bold ${ticket.started_at || ticket.status !== 'pending' ? 'text-slate-800' : 'text-slate-400'}`}>Petugas Menuju Lokasi</h4>
                                                <p className="text-xs mt-1 font-medium text-slate-400">
                                                    {ticket.started_at 
                                                        ? (ticket.user ? `Teknisi lapangan bergerak ke titik lokasi.` : 'Teknisi dalam perjalanan ke lokasi.') 
                                                        : (ticket.status === 'pending' ? 'Menunggu ketersediaan teknisi...' : 'Teknisi sedang bersiap menuju target.')}
                                                </p>

                                                {ticket.status !== 'pending' && ticket.user && (
                                                    <div className="mt-3.5 flex items-center gap-3">
                                                        <div className="w-10 h-10 rounded-full bg-slate-100 shrink-0 flex items-center justify-center overflow-hidden">
                                                            {ticket.user.photo ? (
                                                                <img src={ticket.user.photo.startsWith('http') ? ticket.user.photo : `/storage/${ticket.user.photo}`} alt={ticket.user.name} className="w-full h-full object-cover" />
                                                            ) : (
                                                                <i className="ph-fill ph-user text-xl text-sky-600"></i>
                                                            )}
                                                        </div>
                                                        <div>
                                                            <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Petugas Teknis</p>
                                                            <p className="text-sm font-bold text-slate-800">{ticket.user.name}</p>
                                                            {ticket.user.phone && (
                                                                <a href={`https://wa.me/${ticket.user.phone.replace(/^0/, '62')}`} target="_blank" rel="noreferrer" className="text-xs text-green-600 font-bold flex items-center gap-1 mt-0.5 hover:text-green-700 transition">
                                                                    <i className="ph-fill ph-whatsapp-logo text-green-500 text-sm"></i> {ticket.user.phone}
                                                                </a>
                                                            )}
                                                        </div>
                                                    </div>
                                                )}

                                                {ticket.started_at && <p className="text-xs text-slate-400 font-bold mt-2">{new Date(ticket.started_at).toLocaleString('id-ID', { timeStyle: 'short' })} WITA</p>}
                                            </div>
                                        </div>

                                        {/* Step 4: Proses Perbaikan */}
                                        <div className="relative flex items-start gap-5">
                                            <div className={`w-8 h-8 rounded-full border-4 border-white shadow-md flex items-center justify-center shrink-0 relative z-10 ${ticket.finished_at ? 'bg-emerald-100 text-emerald-600' : (ticket.started_at ? 'bg-red-50 text-red-500 animate-pulse-border' : 'bg-slate-100 text-slate-300')}`}>
                                                {ticket.finished_at ? <i className="ph-bold ph-check text-sm"></i> : (ticket.started_at ? <span className="w-3 h-3 rounded-full bg-red-600 border-2 border-white shadow-sm glow-red"></span> : <div className="w-1.5 h-1.5 rounded-full bg-slate-300"></div>)}
                                            </div>
                                            <div className="pt-1">
                                                <h4 className={`text-sm font-bold ${ticket.finished_at || ticket.started_at ? 'text-slate-800' : 'text-slate-400'}`}>Proses Perbaikan</h4>
                                                <p className="text-xs mt-1 font-medium text-slate-400">
                                                    {ticket.finished_at ? 'Perbaikan di lapangan telah berhasil.' : (ticket.started_at ? 'Teknisi sedang melakukan tindakan penanganan dan perbaikan langsung di titik lokasi.' : 'Menunggu kedatangan teknisi.')}
                                                </p>
                                                {ticket.started_at && !ticket.finished_at && (
                                                    <div className="mt-3 flex flex-col gap-2">
                                                        <div className="inline-flex items-center gap-1.5 bg-red-50 text-red-600 px-3 py-1 rounded-full border border-red-100 w-fit">
                                                            <i className="ph-duotone ph-wrench text-sm animate-bounce"></i>
                                                            <span className="text-[10px] uppercase font-bold tracking-wider">Sedang Dikerjakan</span>
                                                        </div>
                                                        {ticket.estimated_time && (
                                                            <div className="bg-slate-50 rounded-xl p-3 border border-slate-100 w-full max-w-[200px]">
                                                                <p className="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Estimasi Lama Perbaikan</p>
                                                                <div className="text-xs font-black text-slate-800 flex items-center gap-1.5">
                                                                    <i className="ph-bold ph-hourglass-high text-red-500"></i>
                                                                    {ticket.estimated_time}
                                                                </div>
                                                            </div>
                                                        )}
                                                    </div>
                                                )}
                                            </div>
                                        </div>

                                        {/* Step 5: Selesai */}
                                        <div className="relative flex items-start gap-5">
                                            <div className={`w-8 h-8 rounded-full border-4 border-white shadow-sm flex items-center justify-center shrink-0 relative z-10 ${ticket.finished_at ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-300'}`}>
                                                {ticket.finished_at ? <i className="ph-bold ph-seal-check text-lg"></i> : <div className="w-1.5 h-1.5 rounded-full bg-slate-300"></div>}
                                            </div>
                                            <div className="pt-1">
                                                <h4 className={`text-sm font-bold ${ticket.finished_at ? 'text-emerald-600' : 'text-slate-400'}`}>Perbaikan Selesai</h4>
                                                <p className="text-xs mt-1 font-medium text-slate-400">
                                                    {ticket.finished_at ? 'Terima kasih atas laporan Anda. Dokumen foto teknisi telah diverifikasi.' : 'Menunggu validasi penyelesaian.'}
                                                </p>
                                                {ticket.finished_at && (
                                                    <p className="text-xs text-emerald-500 mt-1 font-bold">
                                                        {new Date(ticket.finished_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })}
                                                    </p>
                                                )}
                                            </div>
                                        </div>
                                    </>
                                )}
                            </div>

                            {/* ── Cancel Button (only when pending) ── */}
                            {ticket.status === 'pending' && (
                                <div className="mt-8 pt-6 border-t border-slate-100">
                                    <button
                                        onClick={() => setShowCancelModal(true)}
                                        className="w-full py-3 text-sm font-bold text-red-500 hover:text-red-600 bg-red-50 hover:bg-red-100 rounded-xl border border-red-100 transition flex items-center justify-center gap-2"
                                    >
                                        <i className="ph-bold ph-x-circle"></i> Batalkan Laporan
                                    </button>
                                    <p className="text-[10px] text-slate-400 text-center mt-2">Laporan hanya bisa dibatalkan jika belum diproses oleh petugas.</p>
                                </div>
                            )}
                        </div>

                        {/* Photo After if exists */}
                        {ticket.photo_after && (
                            <div className="p-8 bg-slate-50 border-t border-slate-100">
                                <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Bukti Penyelesaian</p>
                                <img src={`/${ticket.photo_after}`} alt="Bukti Selesai" className="w-full h-48 object-cover rounded-2xl shadow-sm border border-slate-200" />
                            </div>
                        )}

                        {/* ── CSAT Rating (only when selesai & not yet rated) ── */}
                        {ticket.status === 'selesai' && (
                            <div className="p-8 border-t border-slate-100">
                                {ratingSubmitted || ticket.rating ? (
                                    <div className="text-center py-4">
                                        <div className="flex justify-center gap-1 mb-3">
                                            {[1,2,3,4,5].map(s => (
                                                <i key={s} className={`ph-fill ph-star text-2xl ${s <= (ticket.rating || selectedStar) ? 'text-amber-400' : 'text-slate-200'}`}></i>
                                            ))}
                                        </div>
                                        <p className="text-sm font-bold text-slate-700">Terima kasih atas penilaian Anda!</p>
                                        <p className="text-xs text-slate-400 mt-1">Masukan Anda sangat berharga untuk peningkatan layanan kami.</p>
                                    </div>
                                ) : !showRatingPhone ? (
                                    <div className="text-center">
                                        <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Bagaimana pelayanan kami?</p>
                                        <div className="flex justify-center gap-2 mb-4">
                                            {[1,2,3,4,5].map(s => (
                                                <button
                                                    key={s}
                                                    onMouseEnter={() => setHoverStar(s)}
                                                    onMouseLeave={() => setHoverStar(0)}
                                                    onClick={() => setSelectedStar(s)}
                                                    className="transition-transform hover:scale-125"
                                                >
                                                    <i className={`ph-fill ph-star text-3xl transition-colors ${s <= (hoverStar || selectedStar) ? 'text-amber-400' : 'text-slate-200'}`}></i>
                                                </button>
                                            ))}
                                        </div>
                                        {selectedStar > 0 && (
                                            <div className="space-y-3 animate-fade-in-up">
                                                <textarea
                                                    value={ratingFeedback}
                                                    onChange={(e) => setRatingFeedback(e.target.value)}
                                                    placeholder="Tulis komentar (opsional)..."
                                                    maxLength={500}
                                                    className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm resize-none h-20 focus:ring-2 focus:ring-sky-400 focus:bg-white transition"
                                                />
                                                <button
                                                    onClick={() => setShowRatingPhone(true)}
                                                    className="px-6 py-2.5 bg-amber-500 hover:bg-amber-400 text-white font-bold rounded-xl text-sm transition hover:-translate-y-0.5 shadow-sm"
                                                >
                                                    Kirim Penilaian
                                                </button>
                                            </div>
                                        )}
                                    </div>
                                ) : (
                                    <div className="text-center space-y-3 animate-fade-in-up">
                                        <p className="text-sm font-bold text-slate-700">Verifikasi Nomor HP Pelapor</p>
                                        <p className="text-xs text-slate-400">Masukkan nomor HP yang Anda gunakan saat membuat laporan ini.</p>
                                        <input
                                            type="tel"
                                            value={ratingPhone}
                                            onChange={(e) => setRatingPhone(e.target.value)}
                                            placeholder="Contoh: 081234567890"
                                            className="w-full max-w-xs mx-auto p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-center font-mono focus:ring-2 focus:ring-sky-400 transition"
                                        />
                                        {ratingError && <p className="text-xs text-red-500 font-bold">{ratingError}</p>}
                                        <div className="flex gap-2 justify-center">
                                            <button onClick={() => setShowRatingPhone(false)} className="px-4 py-2 text-sm font-bold text-slate-500 bg-slate-100 rounded-xl hover:bg-slate-200 transition">Kembali</button>
                                            <button
                                                onClick={handleSubmitRating}
                                                disabled={ratingLoading || !ratingPhone}
                                                className="px-6 py-2 bg-amber-500 hover:bg-amber-400 text-white font-bold rounded-xl text-sm transition disabled:opacity-50"
                                            >
                                                {ratingLoading ? 'Mengirim...' : 'Konfirmasi'}
                                            </button>
                                        </div>
                                    </div>
                                )}
                            </div>
                        )}

                    </div>
                )}
            </div>

            {/* ── Cancel Modal ── */}
            {showCancelModal && (
                <div className="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-6" onClick={() => setShowCancelModal(false)}>
                    <div className="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl animate-fade-in-up" onClick={e => e.stopPropagation()}>
                        <div className="text-center mb-6">
                            <div className="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i className="ph-fill ph-warning text-red-500 text-3xl"></i>
                            </div>
                            <h3 className="text-xl font-black text-slate-900 mb-2">Batalkan Laporan?</h3>
                            <p className="text-sm text-slate-500">Tindakan ini tidak bisa dibatalkan. Mohon masukkan nomor HP yang Anda gunakan saat melapor untuk verifikasi.</p>
                        </div>
                        <input
                            type="tel"
                            value={cancelPhone}
                            onChange={(e) => setCancelPhone(e.target.value)}
                            placeholder="Masukkan No. HP Pelapor"
                            className="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-mono text-center mb-4 focus:ring-2 focus:ring-red-400 transition"
                        />
                        {cancelError && <p className="text-xs text-red-500 font-bold text-center mb-3">{cancelError}</p>}
                        <div className="flex gap-3">
                            <button onClick={() => setShowCancelModal(false)} className="flex-1 py-3 text-sm font-bold text-slate-600 bg-slate-100 rounded-2xl hover:bg-slate-200 transition">
                                Tidak Jadi
                            </button>
                            <button
                                onClick={handleCancel}
                                disabled={cancelLoading || !cancelPhone}
                                className="flex-1 py-3 text-sm font-bold text-white bg-red-500 hover:bg-red-600 rounded-2xl transition disabled:opacity-50"
                            >
                                {cancelLoading ? 'Memproses...' : 'Ya, Batalkan'}
                            </button>
                        </div>
                    </div>
                </div>
            )}

            <style dangerouslySetInnerHTML={{
                __html: `
                @keyframes fade-in-up { 0% { opacity: 0; transform: translateY(15px); } 100% { opacity: 1; transform: translateY(0); } }
                .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
                .glow-red { box-shadow: 0 0 10px rgba(239, 68, 68, 0.8), 0 0 20px rgba(239, 68, 68, 0.4); }
                @keyframes pulse-border { 0% { border-color: rgba(239, 68, 68, 0.2); } 50% { border-color: rgba(239, 68, 68, 0.6); } 100% { border-color: rgba(239, 68, 68, 0.2); } }
                .animate-pulse-border { animation: pulse-border 2s infinite; }
            `}} />
        </div>
    );
};

export default Track;
