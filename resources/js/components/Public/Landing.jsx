import React, { useEffect, useState, useRef } from 'react';

const Landing = () => {
    const [scrolled, setScrolled] = useState(false);
    const counterRef = useRef(null);
    const [counter, setCounter] = useState(0);
    const [hasAnimatedCounter, setHasAnimatedCounter] = useState(false);

    const alurRef = useRef(null);
    const [isAlurVisible, setIsAlurVisible] = useState(false);

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

    // Alur Animation Observer
    useEffect(() => {
        const observer = new IntersectionObserver(
            (entries) => {
                if (entries[0].isIntersecting && !isAlurVisible) {
                    setIsAlurVisible(true);
                }
            },
            { threshold: 0.01 }
        );

        if (alurRef.current) {
            observer.observe(alurRef.current);
        }

        return () => observer.disconnect();
    }, [isAlurVisible]);

    const steps = [
        { icon: 'ph-note-pencil', title: 'Isi Formulir', desc: 'Masukkan data diri dan pilih jenis gangguan air.' },
        { icon: 'ph-map-pin', title: 'Titik Lokasi', desc: 'Sistem otomatis mendeteksi lokasi GPS Anda yang akurat.' },
        { icon: 'ph-paper-plane-tilt', title: 'Kirim Laporan', desc: 'Tiket otomatis masuk ke sistem dan Teknisi terdekat.' },
        { icon: 'ph-wrench', title: 'Perbaikan Selesai', desc: 'Tim teknis menuju lokasi dan menyelesaikan perbaikan.' },
    ];

    return (
        <div className="min-h-screen bg-gradient-to-br from-[#F4F7FA] via-[#E8F0F8] to-[#F4F7FA] font-jakarta text-slate-800 selection:bg-[#0095FF]/30 selection:text-[#0095FF]">

            {/* Premium Light Background with Subtle Glowing Orbs */}
            <div className="fixed inset-0 pointer-events-none z-0 overflow-hidden">
                {/* Clean, high-end mesh gradient look */}
                <div className="absolute -top-40 -right-40 w-[800px] h-[800px] bg-sky-200/40 rounded-full blur-[120px]"></div>
                <div className="absolute top-60 -left-20 w-[600px] h-[600px] bg-blue-100/50 rounded-full blur-[100px]"></div>
            </div>

            {/* Navigation */}
            <nav className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${scrolled ? 'bg-white/80 backdrop-blur-xl shadow-sm py-4 border-b border-slate-200/50' : 'bg-transparent py-8'}`}>
                <div className="max-w-7xl mx-auto px-6 sm:px-12 flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        {/* Logo */}
                        <img src="/pdam-logo.png" alt="Logo Tirta Ardhia Rinjani" className="h-20 sm:h-24 object-contain drop-shadow-sm" />
                        <div className="hidden sm:block">
                            <h2 className="text-sm font-extrabold leading-tight text-slate-900 font-jakarta tracking-wide">Perumdam Tirta Ardhia Rinjani</h2>
                            <p className="text-[9px] text-[#0095FF] font-bold tracking-[0.2em] uppercase mt-0.5">Kabupaten Lombok Tengah</p>
                        </div>
                    </div>

                    {/* Navigation Buttons Removed */}
                </div>
            </nav>

            {/* Hero Section */}
            <div className="relative min-h-[100dvh] flex items-center justify-center pt-40 md:pt-32 pb-20 z-10">
                <div className="max-w-[1440px] mx-auto px-6 sm:px-12 w-full grid grid-cols-1 lg:grid-cols-2 gap-20 lg:gap-12 items-center">

                    {/* Left Copy */}
                    <div className="text-center lg:text-left space-y-7 animate-fade-in-up">


                        <h1 className="text-6xl sm:text-7xl md:text-[84px] font-black tracking-tight leading-[1.05] text-slate-900">
                            Respons Cepat,<br />
                            <span className="text-[#0095FF]">Air Lancar.</span>
                        </h1>

                        <p className="text-lg md:text-xl text-slate-500 max-w-xl mx-auto lg:mx-0 leading-relaxed font-medium">
                            Portal resmi aduan gangguan layanan air <span className="whitespace-nowrap font-bold text-slate-700">Perumdam Tirta Ardhia Rinjani</span>. Laporkan masalah Anda dalam hitungan detik tanpa ribet.
                        </p>

                        <div className="flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start pt-4">
                            <a href="/lapor" className="w-full sm:w-auto bg-gradient-to-r from-[#0095FF] to-[#0070FF] text-white px-8 py-4 rounded-2xl text-base font-bold hover:shadow-xl hover:shadow-blue-500/30 hover:ring-4 hover:ring-blue-500/20 transition-all hover:-translate-y-1 flex items-center justify-center gap-3 group">
                                <i className="ph-bold ph-pencil-simple text-xl group-hover:rotate-12 transition-transform"></i> Buat Laporan
                            </a>
                            <a href="/lacak" className="w-full sm:w-auto bg-white text-slate-700 hover:text-slate-900 border border-slate-200 hover:border-slate-300 px-8 py-4 rounded-2xl text-base font-bold transition-all hover:bg-slate-50 hover:shadow-md flex items-center justify-center gap-3">
                                <i className="ph-bold ph-magnifying-glass text-xl"></i> Lacak Tiket
                            </a>
                        </div>
                    </div>

                    {/* Right Visuals - Typography Driven Statistics */}
                    <div className="relative flex justify-center lg:justify-end animate-fade-in-up lg:translate-x-8" style={{ animationDelay: '0.2s' }}>
                        <div className="relative flex flex-col items-center justify-center mt-8 lg:mt-0 p-12" ref={counterRef}>

                            {/* The Big Number with count animation */}
                            <div className="min-w-[300px] sm:min-w-[450px] flex justify-center">
                                <div className="text-[100px] sm:text-[130px] font-black tracking-tighter leading-none flex items-center tabular-nums bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-700 drop-shadow-sm">
                                    {counter.toLocaleString('id-ID')}<span className="text-[#0095FF] ml-1">+</span>
                                </div>
                            </div>

                            {/* Main Subtitle */}
                            <div className="text-sm sm:text-base font-bold text-slate-500 mt-4 tracking-[0.2em] uppercase">
                                Total Pelanggan
                            </div>

                            {/* Divider Line */}
                            <div className="w-24 h-1 bg-slate-200 rounded-full my-8"></div>

                            {/* Secondary Stats - Typographic Approach */}
                            <div className="flex items-center gap-8 sm:gap-16 w-full justify-center">
                                {/* Layanan */}
                                <div className="text-center group cursor-default">
                                    <div className="text-4xl sm:text-5xl font-black tracking-tighter text-transparent bg-clip-text bg-gradient-to-br from-emerald-400 to-emerald-600 mb-2 group-hover:scale-110 group-hover:rotate-2 transition-transform duration-300">
                                        24/7
                                    </div>
                                    <p className="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Siap Melayani</p>
                                </div>
                                {/* Thin Divider */}
                                <div className="w-px h-16 bg-slate-200/60 rounded-full"></div>
                                {/* Cakupan */}
                                <div className="text-center group cursor-default">
                                    <div className="text-4xl sm:text-5xl font-black tracking-tighter text-transparent bg-clip-text bg-gradient-to-br from-[#0095FF] to-blue-600 mb-2 group-hover:scale-110 group-hover:-rotate-2 transition-transform duration-300">
                                        12
                                    </div>
                                    <p className="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Kecamatan</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {/* How it Works Section - Ultra Premium Typographic List */}
            <div ref={alurRef} className={`pt-24 pb-32 relative max-w-5xl mx-auto px-6 sm:px-12 z-10 transition-all duration-1000 ${isAlurVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'}`}>
                <div className="mb-20 md:mb-28">
                    <h2 className="text-xs font-black tracking-[0.3em] text-[#0095FF] uppercase mb-4">Sistem Terintegrasi</h2>
                    <h3 className="text-4xl md:text-6xl font-black text-slate-900 tracking-tighter leading-tight">
                        Dirancang untuk<br />
                        <span className="text-slate-400">Efisiensi Maksimal.</span>
                    </h3>
                </div>

                <div className="flex flex-col gap-12 md:gap-20">
                    {steps.map((step, idx) => (
                        <div key={idx} className="flex flex-col md:flex-row items-start md:items-center gap-6 md:gap-16 group cursor-default">
                            {/* Giant Architectural Number - Now Bold and Vibrant */}
                            <div className="text-7xl md:text-[120px] font-black leading-none tracking-tighter text-transparent bg-clip-text bg-gradient-to-br from-[#0095FF] to-blue-800 opacity-20 group-hover:opacity-100 transition-opacity duration-500 w-32 md:w-48 drop-shadow-md">
                                0{idx + 1}
                            </div>
                            
                            {/* Vertical Accent Line */}
                            <div className="hidden md:block w-0.5 h-24 bg-slate-100 relative overflow-hidden">
                                <div className="absolute top-0 left-0 w-full h-full bg-[#0095FF] translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-out"></div>
                            </div>

                            {/* Typographic Content */}
                            <div className="flex-1 max-w-xl">
                                <h4 className="text-2xl md:text-3xl font-bold text-slate-900 mb-4 tracking-tight group-hover:text-[#0095FF] transition-colors duration-300">
                                    {step.title}
                                </h4>
                                <p className="text-slate-500 text-base md:text-lg leading-relaxed md:leading-loose">
                                    {step.desc}
                                </p>
                            </div>
                        </div>
                    ))}
                </div>
            </div>



            <style dangerouslySetInnerHTML={{
                __html: `
                @keyframes fade-in-up { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }
                .animate-fade-in-up { animation: fade-in-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
            `}} />


        </div>
    );
};

export default Landing;
