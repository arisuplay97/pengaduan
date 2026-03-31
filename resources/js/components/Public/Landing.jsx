import React, { useEffect, useState, useRef } from 'react';

const Landing = () => {
    const [scrolled, setScrolled] = useState(false);
    const counterRef = useRef(null);
    const [counter, setCounter] = useState(0);
    const [hasAnimatedCounter, setHasAnimatedCounter] = useState(false);

    useEffect(() => {
        const handleScroll = () => {
            setScrolled(window.scrollY > 50);
        };
        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    // Counter Animation
    useEffect(() => {
        const observer = new IntersectionObserver(
            (entries) => {
                if (entries[0].isIntersecting && !hasAnimatedCounter) {
                    setHasAnimatedCounter(true);
                    const end = 50000;
                    const duration = 2500; // 2.5 seconds
                    const startTime = performance.now();

                    const step = (currentTime) => {
                        const elapsed = currentTime - startTime;
                        const progress = Math.min(elapsed / duration, 1);
                        // Ease out exponential for that premium slow down at the end
                        const easeProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                        setCounter(Math.floor(easeProgress * end));

                        if (progress < 1) {
                            requestAnimationFrame(step);
                        }
                    };
                    requestAnimationFrame(step);
                }
            },
            { threshold: 0.5 }
        );

        if (counterRef.current) {
            observer.observe(counterRef.current);
        }

        return () => observer.disconnect();
    }, [hasAnimatedCounter]);

    const steps = [
        { icon: 'ph-note-pencil', title: 'Isi Formulir', desc: 'Masukkan data diri dan pilih jenis gangguan air.' },
        { icon: 'ph-map-pin', title: 'Titik Lokasi', desc: 'Sistem otomatis mendeteksi lokasi GPS Anda yang akurat.' },
        { icon: 'ph-paper-plane-tilt', title: 'Kirim Laporan', desc: 'Tiket otomatis masuk ke sistem dan Teknisi terdekat.' },
        { icon: 'ph-wrench', title: 'Perbaikan Selesai', desc: 'Tim teknis menuju lokasi dan menyelesaikan perbaikan.' },
    ];

    return (
        <div className="min-h-screen bg-[#F8FAFC] font-jakarta text-slate-800 selection:bg-[#0095FF]/30 selection:text-[#0095FF]">

            {/* Premium Light Background with Subtle Glowing Orbs */}
            <div className="fixed inset-0 pointer-events-none z-0 overflow-hidden">
                {/* Clean, high-end mesh gradient look */}
                <div className="absolute -top-40 -right-40 w-[800px] h-[800px] bg-sky-200/40 rounded-full blur-[120px]"></div>
                <div className="absolute top-60 -left-20 w-[600px] h-[600px] bg-blue-100/50 rounded-full blur-[100px]"></div>

                {/* Ultra-subtle grid for texture */}
                <div className="absolute inset-0 opacity-[0.03]" style={{
                    backgroundImage: 'linear-gradient(#0f172a 1px, transparent 1px), linear-gradient(90deg, #0f172a 1px, transparent 1px)',
                    backgroundSize: '64px 64px'
                }}></div>
            </div>

            {/* Navigation */}
            <nav className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${scrolled ? 'bg-white/80 backdrop-blur-xl shadow-sm py-4 border-b border-slate-200/50' : 'bg-transparent py-8'}`}>
                <div className="max-w-7xl mx-auto px-6 sm:px-12 flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        {/* Logo */}
                        <img src="/pdam-logo.png" alt="Logo Tirta Ardhia Rinjani" className="h-12 sm:h-16 object-contain drop-shadow-sm" />
                        <div className="hidden sm:block">
                            <h2 className="text-sm font-extrabold leading-tight text-slate-900 font-jakarta tracking-wide">Perumdam Tirta Ardhia Rinjani</h2>
                            <p className="text-[9px] text-[#0095FF] font-bold tracking-[0.2em] uppercase mt-0.5">Kabupaten Lombok Tengah</p>
                        </div>
                    </div>

                    <div className="flex items-center gap-6">
                        <a href="/lacak" className="text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors duration-200">
                            Lacak Tiket
                        </a>
                        <a href="/lapor" className="bg-[#0095FF] hover:bg-[#0080FF] text-white px-6 py-2.5 rounded-full text-sm font-bold transition-all shadow-[0_4px_14px_0_rgba(0,149,255,0.39)] hover:shadow-[0_6px_20px_rgba(0,149,255,0.23)] hover:-translate-y-0.5 flex items-center gap-2">
                            Lapor Sekarang <i className="ph-bold ph-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </nav>

            {/* Hero Section */}
            <div className="relative pt-44 pb-20 justify-center z-10">
                <div className="max-w-7xl mx-auto px-6 sm:px-12 w-full grid grid-cols-1 lg:grid-cols-2 gap-20 lg:gap-8 items-center">

                    {/* Left Copy */}
                    <div className="text-center lg:text-left space-y-7 animate-fade-in-up">
                        <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-[#0095FF] text-[10px] sm:text-xs font-bold tracking-wide">
                            <span className="w-1.5 h-1.5 rounded-full bg-[#0095FF] shadow-[0_0_8px_rgba(0,149,255,0.8)]"></span>
                            Layanan Pengaduan 24/7
                        </div>

                        <h1 className="text-5xl sm:text-6xl md:text-7xl font-black tracking-tight leading-[1.1] text-slate-900">
                            Respons Cepat,<br />
                            <span className="text-[#0095FF]">Air Lancar.</span>
                        </h1>

                        <p className="text-lg text-slate-500 max-w-lg mx-auto lg:mx-0 leading-relaxed font-medium">
                            Portal resmi aduan gangguan layanan air Perumdam Tirta Ardhia Rinjani. Laporkan masalah Anda dalam hitungan detik tanpa ribet.
                        </p>

                        <div className="flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start pt-4">
                            <a href="/lapor" className="w-full sm:w-auto bg-[#0095FF] text-white px-8 py-4 rounded-full text-base font-bold hover:bg-[#0080FF] transition-all shadow-[0_4px_14px_0_rgba(0,149,255,0.39)] hover:shadow-[0_6px_20px_rgba(0,149,255,0.23)] hover:-translate-y-0.5 flex items-center justify-center gap-3">
                                <i className="ph-bold ph-pencil-simple text-xl"></i> Buat Laporan
                            </a>
                            <a href="/lacak" className="w-full sm:w-auto bg-white text-slate-700 hover:text-slate-900 border border-slate-200 hover:border-slate-300 px-8 py-4 rounded-full text-base font-bold transition-all shadow-sm hover:shadow-md flex items-center justify-center gap-3">
                                <i className="ph-bold ph-magnifying-glass text-xl"></i> Lacak Tiket
                            </a>
                        </div>
                    </div>

                    {/* Right Visuals - Typography Driven Statistics */}
                    <div className="relative flex justify-center lg:justify-end animate-fade-in-up md:pr-10" style={{ animationDelay: '0.2s' }}>
                        <div className="relative flex flex-col items-center justify-center mt-8 lg:mt-0 p-12" ref={counterRef}>

                            {/* The Big Number with count animation */}
                            <div className="text-[80px] sm:text-[100px] font-black tracking-tighter leading-none flex items-center bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-700 drop-shadow-sm">
                                {counter.toLocaleString('id-ID')}<span className="text-[#0095FF] ml-1">+</span>
                            </div>

                            {/* Main Subtitle */}
                            <div className="text-sm sm:text-base font-bold text-slate-500 mt-4 tracking-[0.2em] uppercase">
                                Total Pelanggan
                            </div>

                            {/* Divider Line */}
                            <div className="w-24 h-1 bg-slate-200 rounded-full my-8"></div>

                            {/* Secondary Stats (Replacing the floating badges) */}
                            <div className="flex items-center gap-12 w-full justify-center">
                                {/* Layanan */}
                                <div className="text-center group cursor-default">
                                    <div className="w-12 h-12 rounded-[16px] bg-emerald-50 text-emerald-500 flex items-center justify-center mx-auto mb-3 group-hover:-translate-y-1 transition-transform">
                                        <i className="ph-fill ph-check-circle text-2xl"></i>
                                    </div>
                                    <p className="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Layanan</p>
                                    <p className="text-lg font-black text-slate-800">24 Jam</p>
                                </div>

                                {/* Cakupan */}
                                <div className="text-center group cursor-default">
                                    <div className="w-12 h-12 rounded-[16px] bg-blue-50 text-[#0095FF] flex items-center justify-center mx-auto mb-3 group-hover:-translate-y-1 transition-transform">
                                        <i className="ph-fill ph-map-pin text-2xl"></i>
                                    </div>
                                    <p className="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Cakupan</p>
                                    <p className="text-lg font-black text-slate-800">12 Kecamatan</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {/* How it Works Section - Light Premium Cards */}
            <div className="pt-24 pb-32 relative max-w-7xl mx-auto px-6 sm:px-12 z-10">
                <div className="text-center mb-16">
                    <h2 className="text-sm font-bold tracking-widest text-[#0095FF] uppercase mb-3">Sistem Terintegrasi</h2>
                    <h3 className="text-3xl sm:text-4xl font-black text-slate-900">Alur Penyelesaian Gangguan</h3>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    {steps.map((step, idx) => (
                        <div key={idx} className="bg-white border border-slate-100 rounded-[28px] p-8 hover:border-blue-100 hover:shadow-[0_20px_40px_-15px_rgba(0,149,255,0.15)] transition-all duration-300 flex flex-col items-start shadow-sm group">
                            <div className="w-14 h-14 bg-slate-50 border border-slate-100 group-hover:bg-[#0095FF] rounded-[18px] flex items-center justify-center mb-6 transition-colors duration-300">
                                <i className={`ph-bold ${step.icon} text-2xl text-slate-600 group-hover:text-white transition-colors duration-300`}></i>
                            </div>
                            <h4 className="text-[18px] font-bold text-slate-900 mb-2.5 flex items-center gap-2 tracking-tight">
                                <span className="text-[#0095FF] text-[13px] bg-blue-50 px-2 py-0.5 rounded-md">0{idx + 1}</span> {step.title}
                            </h4>
                            <p className="text-slate-500 text-[14px] leading-relaxed font-medium">
                                {step.desc}
                            </p>
                        </div>
                    ))}
                </div>
            </div>

            {/* Beautiful Light CTA */}
            <div className="relative py-32 z-10 bg-white border-t border-slate-100">
                <div className="max-w-4xl mx-auto text-center px-6">
                    <div className="w-20 h-20 bg-blue-50 rounded-[24px] flex items-center justify-center mx-auto mb-8">
                        <i className="ph-fill ph-rocket-launch text-4xl text-[#0095FF]"></i>
                    </div>
                    <h2 className="text-[40px] md:text-[48px] font-black mb-6 text-slate-900 tracking-tight">Siap Lapor Gangguan?</h2>
                    <p className="text-slate-500 text-[16px] md:text-[18px] mb-12 max-w-[600px] mx-auto font-medium leading-relaxed">
                        Jangan biarkan air mampet atau keruh mengganggu aktivitas Anda. Tim kami siap meluncur ke lokasi segera setelah laporan Anda masuk.
                    </p>
                    <a href="/lapor" className="inline-flex items-center gap-3 bg-[#0095FF] hover:bg-[#0080FF] text-white px-10 py-4 rounded-full text-[16px] font-bold transition-all shadow-[0_4px_14px_0_rgba(0,149,255,0.39)] hover:shadow-[0_6px_20px_rgba(0,149,255,0.23)] hover:-translate-y-1">
                        Buat Laporan Sekarang <i className="ph-bold ph-arrow-right text-xl"></i>
                    </a>
                </div>
            </div>

            <style dangerouslySetInnerHTML={{
                __html: `
                @keyframes fade-in-up { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }
                .animate-fade-in-up { animation: fade-in-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
            `}} />

            {/* Floating WhatsApp Call Center Button */}
            <a
                href="https://wa.me/6282114005005"
                target="_blank"
                rel="noopener noreferrer"
                className="fixed bottom-6 right-6 z-50 flex items-center justify-center w-14 h-14 bg-[#25D366] text-white rounded-full shadow-[0_4px_14px_0_rgba(37,211,102,0.39)] hover:shadow-[0_6px_20px_rgba(37,211,102,0.23)] hover:-translate-y-1 transition-all duration-300 group"
                title="Hubungi Call Center WhatsApp"
            >
                <i className="ph-fill ph-whatsapp-logo text-3xl"></i>

                {/* Tooltip on hover */}
                <span className="absolute right-16 px-3 py-1.5 bg-slate-800 text-white text-[13px] font-bold rounded-lg opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap shadow-xl">
                    Call Center PDAM
                </span>
            </a>
        </div>
    );
};

export default Landing;
