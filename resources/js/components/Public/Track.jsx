import React, { useState } from 'react';

const Track = ({ initialTicket, initialCode }) => {
    const [ticketCode, setTicketCode] = useState(initialCode || '');
    // For rendering we'll use a standard form submit so Laravel handles the actual fetching and re-rendering for simplicity, 
    // but the UI will be fully React.
    const ticket = initialTicket;

    return (
        <div className="min-h-screen bg-slate-50 font-jakarta text-slate-800">
            {/* Header */}
            <nav className="bg-white border-b border-slate-100 px-6 py-4 sticky top-0 z-40">
                <div className="max-w-2xl mx-auto flex items-center justify-between">
                    <a href="/" className="flex items-center gap-2 text-slate-500 hover:text-sky-600 font-bold transition">
                        <i className="ph-bold ph-arrow-left text-lg"></i> Kembali
                    </a>
                    <div className="flex items-center gap-2">
                        <div className="w-8 h-8 bg-sky-100 rounded-lg flex items-center justify-center">
                            <i className="ph-fill ph-drop text-sky-600"></i>
                        </div>
                        <span className="font-extrabold tracking-tight">Cek Laporan</span>
                    </div>
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
                        </div>

                        <div className="p-8">

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
                                {/* Base line (static) */}
                                <div className="absolute left-10 top-2 bottom-2 w-[2px] bg-slate-100/80 rounded-full"></div>
                                
                                {/* Active progress line (dynamic) */}
                                <div 
                                    className="absolute left-10 top-2 w-[2px] bg-emerald-500 rounded-full transition-all duration-1000"
                                    style={{ 
                                        height: ticket.finished_at ? '98%' : (ticket.started_at ? '66%' : (ticket.status !== 'pending' ? '40%' : '14%')) 
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

                                {/* Step 2: Mencari Petugas */}
                                <div className="relative flex items-start gap-5">
                                    <div className={`w-8 h-8 rounded-full border-4 border-white shadow-sm flex items-center justify-center shrink-0 relative z-10 ${ticket.status !== 'pending' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-500'}`}>
                                        {ticket.status !== 'pending' ? <i className="ph-bold ph-check text-sm"></i> : <span className="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>}
                                    </div>
                                    <div className="pt-1">
                                        <h4 className="text-sm font-bold text-slate-800">Mencari Petugas</h4>
                                        <p className="text-xs text-slate-400 mt-1 font-medium">
                                            {ticket.status === 'pending' ? 'Sistem sedang memanggil teknisi terdekat di wilayah pendaftaran.' : 'Teknisi lapangan telah dikonfirmasi.'}
                                        </p>
                                    </div>
                                </div>

                                {/* Step 3: Petugas Menuju Lokasi */}
                                <div className="relative flex items-start gap-5">
                                    <div className={`w-8 h-8 rounded-full border-4 border-white shadow-sm flex items-center justify-center shrink-0 relative z-10 ${ticket.started_at ? 'bg-emerald-100 text-emerald-600' : (ticket.status !== 'pending' ? 'bg-amber-100 text-amber-500' : 'bg-slate-100 text-slate-300')}`}>
                                        {ticket.started_at ? <i className="ph-bold ph-check text-sm"></i> : (ticket.status !== 'pending' ? <span className="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span> : <div className="w-1.5 h-1.5 rounded-full bg-slate-300"></div>)}
                                    </div>
                                    <div className="pt-1">
                                        <h4 className={`text-sm font-bold ${ticket.started_at || ticket.status !== 'pending' ? 'text-slate-800' : 'text-slate-400'}`}>Petugas Menuju Lokasi</h4>
                                        <p className="text-xs mt-1 font-medium text-slate-400">
                                            {ticket.started_at 
                                                ? (ticket.user ? `Teknisi lap. ${ticket.user.name} bergerak ke titik lokasi.` : 'Teknisi dalam perjalanan ke lokasi.') 
                                                : (ticket.status === 'pending' ? 'Menunggu ketersediaan teknisi...' : 'Teknisi sedang bersiap menuju target.')}
                                        </p>
                                        {ticket.started_at && <p className="text-xs text-slate-400 font-bold mt-1">{new Date(ticket.started_at).toLocaleString('id-ID', { timeStyle: 'short' })} WITA</p>}
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
                            </div>
                        </div>

                        {/* Photo After if exists */}
                        {ticket.photo_after && (
                            <div className="p-8 bg-slate-50 border-t border-slate-100">
                                <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Bukti Penyelesaian</p>
                                <img src={`/${ticket.photo_after}`} alt="Bukti Selesai" className="w-full h-48 object-cover rounded-2xl shadow-sm border border-slate-200" />
                            </div>
                        )}

                    </div>
                )}
            </div>

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
