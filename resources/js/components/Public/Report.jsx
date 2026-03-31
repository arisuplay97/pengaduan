import React, { useState, useEffect, useRef } from 'react';

const Report = ({ kecamatans }) => {
    const [step, setStep] = useState(1);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const problemDropdownRef = useRef(null);

    // Form data
    const [formData, setFormData] = useState({
        reporter_name: '',
        customer_id: '',
        reporter_phone: '',
        title: '',
        kecamatan_id: '',
        address: '',
        description: '',
        latitude: '',
        longitude: ''
    });

    const [isProblemDropdownOpen, setIsProblemDropdownOpen] = useState(false);
    const [gpsStatus, setGpsStatus] = useState('Tekan tombol untuk mendeteksi...');
    const [mapInitialized, setMapInitialized] = useState(false);

    useEffect(() => {
        // Load leaflet conditionally when step 2 is active and coordinates exist
        if (step === 2 && formData.latitude && formData.longitude && !mapInitialized) {
            if (window.L) {
                // Lombok island approx bounds
                const lombokBounds = window.L.latLngBounds(
                    [-9.0, 115.7], // South West
                    [-8.1, 116.9]  // North East
                );

                const map = window.L.map('map', {
                    maxBounds: lombokBounds,
                    maxBoundsViscosity: 1.0,
                    minZoom: 10
                }).setView([formData.latitude, formData.longitude], 16);

                window.L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                }).addTo(map);

                map.on('moveend', () => {
                    const center = map.getCenter().wrap();
                    setFormData(prev => ({ ...prev, latitude: center.lat, longitude: center.lng }));
                    setGpsStatus(`${center.lat.toFixed(6)}, ${center.lng.toFixed(6)} ✅ (Diset manual)`);
                });
                setMapInitialized(true);
            }
        }
    }, [step, formData.latitude, formData.longitude, mapInitialized]);

    // Close dropdown when clicking outside
    useEffect(() => {
        const handleClickOutside = (event) => {
            if (problemDropdownRef.current && !problemDropdownRef.current.contains(event.target)) {
                setIsProblemDropdownOpen(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSelectProblem = (id) => {
        setFormData({ ...formData, title: id });
        setIsProblemDropdownOpen(false);
    };

    const detectGPS = () => {
        setGpsStatus('Mendeteksi lokasi...');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    setFormData(prev => ({ ...prev, latitude: lat, longitude: lng }));
                    setGpsStatus(`${lat.toFixed(6)}, ${lng.toFixed(6)} ✅`);
                },
                () => setGpsStatus('Gagal mendeteksi. Pastikan GPS aktif/diizinkan.')
            );
        } else {
            setGpsStatus('Browser tidak mendukung GPS.');
        }
    };

    const handleNext = () => {
        if (step === 1) {
            if (!formData.reporter_name) return alert('Mohon isi nama Anda.');
            if (!formData.customer_id) return alert('Mohon isi No. Pelanggan Anda.');

            // Validation for 08 prefix
            if (!formData.reporter_phone) {
                return alert('Mohon isi nomor WhatsApp.');
            }
            if (!formData.reporter_phone.startsWith('08')) {
                return alert('Nomor WhatsApp harus diawali dengan "08".');
            }
            if (formData.reporter_phone.length < 10) {
                return alert('Nomor WhatsApp tidak valid (terlalu pendek).');
            }

            if (!formData.title) return alert('Mohon pilih jenis gangguan.');
            setStep(2);
        }
    };

    const handleSubmit = async () => {
        if (!formData.kecamatan_id) return alert('Mohon pilih kecamatan.');
        if (!formData.address) return alert('Mohon isi alamat lengkap.');

        setIsSubmitting(true);
        try {
            const payload = {
                ...formData,
                photo: 'NO_PHOTO'
            };

            const token = document.querySelector('meta[name="csrf-token"]')?.content;

            const resp = await fetch('/lapor', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(payload)
            });

            const data = await resp.json();
            if (resp.ok && data.success) {
                window.location.href = `/lacak?kode=${data.ticket_code}`;
            } else {
                alert(data.errors ? Object.values(data.errors).flat().join('\n') : data.message);
            }
        } catch (e) {
            alert('Terjadi kesalahan server saat mengirim laporan.');
        } finally {
            setIsSubmitting(false);
        }
    };

    // Problem Types based on image 4 exactly
    const problemTypes = [
        { id: 'Pipa Bocor', color: 'bg-[#DE3E53]' }, // Red
        { id: 'Meteran Mati', color: 'bg-[#292D3E]' }, // Dark Gray
        { id: 'Air Keruh', color: 'bg-[#7E5750]' }, // Brown
        { id: 'Sambungan Lepas', color: 'bg-[#FF8A4C]' }, // Orange
        { id: 'Meteran Tersumbat', color: 'bg-[#3A75CA]' }, // Blue
        { id: 'Lainnya', color: 'bg-[#E3E0EE]' }, // Light Purple/Gray
    ];

    const selectedProblem = problemTypes.find(p => p.id === formData.title);

    return (
        <div className="min-h-screen bg-slate-50 font-jakarta text-slate-800 pb-20">
            {/* Header */}
            <nav className="bg-white border-b border-slate-100 px-6 py-4 sticky top-0 z-40">
                <div className="max-w-3xl mx-auto flex items-center justify-between">
                    <a href="/" className="flex items-center gap-2 text-slate-500 hover:text-sky-600 font-bold transition">
                        <i className="ph-bold ph-arrow-left text-lg"></i> Kembali
                    </a>
                    <div className="flex items-center gap-2">
                        <div className="w-8 h-8 bg-sky-100 rounded-lg flex items-center justify-center">
                            <i className="ph-fill ph-drop text-sky-600"></i>
                        </div>
                        <span className="font-extrabold tracking-tight">Lapor Gangguan</span>
                    </div>
                </div>
            </nav>

            <div className="max-w-2xl mx-auto px-6 pt-10">
                <div className="text-center mb-10">
                    <h1 className="text-3xl font-black text-slate-900 mb-2">Formulir Pengaduan</h1>
                    <p className="text-slate-500 font-medium tracking-wide">Lengkapi detail untuk mempercepat penanganan teknisi</p>
                </div>

                {/* Progress Tabs */}
                <div className="flex items-center justify-center mb-8">
                    <div className={`flex items-center px-6 py-2.5 rounded-full text-sm font-bold transition-all duration-300 border ${step === 1 ? 'bg-sky-50 text-sky-700 border-sky-200 shadow-sm' : 'bg-white text-slate-500 border-slate-200'}`}>
                        <span className={`w-5 h-5 rounded-full flex items-center justify-center text-[10px] mr-2.5 ${step === 1 ? 'bg-sky-600 text-white' : 'bg-slate-200 text-slate-500'}`}>1</span>
                        Data Diri
                    </div>
                    <div className={`w-10 h-px mx-2 ${step > 1 ? 'bg-sky-300' : 'bg-slate-200'}`}></div>
                    <div className={`flex items-center px-6 py-2.5 rounded-full text-sm font-bold transition-all duration-300 border ${step === 2 ? 'bg-sky-50 text-sky-700 border-sky-200 shadow-sm' : 'bg-white text-slate-500 border-slate-200'}`}>
                        <span className={`w-5 h-5 rounded-full flex items-center justify-center text-[10px] mr-2.5 ${step === 2 ? 'bg-sky-600 text-white' : 'bg-slate-200 text-slate-500'}`}>2</span>
                        Lokasi & Geotagging
                    </div>
                </div>

                {/* Card Wrapper Minimalist */}
                <div className="bg-white rounded-[24px] shadow-sm border border-slate-200 p-6 sm:p-10 transition-all">

                    {/* STEP 1 */}
                    {step === 1 && (
                        <div className="space-y-6 animate-fade-in-up">

                            <div>
                                <label className="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Lengkap</label>
                                <input type="text" name="reporter_name" value={formData.reporter_name} onChange={handleChange} className="w-full bg-white border border-slate-300 rounded-xl px-4 py-3.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none transition placeholder-slate-400" />
                            </div>

                            <div>
                                <label className="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">No. Pelanggan</label>
                                <input type="text" name="customer_id" value={formData.customer_id} onChange={handleChange} className="w-full bg-white border border-slate-300 rounded-xl px-4 py-3.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none transition placeholder-slate-400" />
                            </div>

                            <div>
                                <label className="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">No WhatsApp</label>
                                <div className="relative">
                                    <div className="absolute left-4 top-1/2 -translate-y-1/2 flex items-center gap-2">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Flag_of_Indonesia.svg/20px-Flag_of_Indonesia.svg.png" alt="ID" className="w-5 h-4 object-cover rounded-[2px]" />
                                    </div>
                                    <input type="tel" name="reporter_phone" value={formData.reporter_phone} onChange={handleChange} placeholder="08xxxxxxxxxxx" className="w-full bg-white border border-slate-300 rounded-xl pl-12 pr-4 py-3.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none transition placeholder-slate-400" />
                                </div>
                                <p className="text-[11px] text-slate-500 mt-2 font-medium bg-slate-50 inline-block px-2 py-1 rounded">No WA wajib diawali dengan 08</p>
                            </div>

                            {/* Custom Dropdown exactly mirroring image 4 */}
                            <div className="relative" ref={problemDropdownRef}>
                                <label className="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Jenis Gangguan</label>

                                <div
                                    onClick={() => setIsProblemDropdownOpen(!isProblemDropdownOpen)}
                                    className={`w-full bg-white border ${isProblemDropdownOpen ? 'border-sky-500 ring-2 ring-sky-500/20' : 'border-slate-300'} rounded-xl px-4 py-3.5 text-sm font-medium flex items-center justify-between cursor-pointer transition select-none ${selectedProblem ? 'text-slate-800' : 'text-slate-400'}`}
                                >
                                    <div className="flex items-center gap-3">
                                        {selectedProblem && (
                                            <div className={`w-4 h-4 rounded-full ${selectedProblem.color}`}></div>
                                        )}
                                        {selectedProblem ? selectedProblem.id : 'Pilih Jenis Gangguan...'}
                                    </div>
                                    <i className={`ph-bold ph-caret-down transition-transform ${isProblemDropdownOpen ? 'rotate-180 text-sky-500' : 'text-slate-400'}`}></i>
                                </div>

                                {isProblemDropdownOpen && (
                                    <div className="absolute z-10 w-full mt-2 bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden animate-fade-in-up" style={{ animationDuration: '0.2s' }}>
                                        {/* The header item from image 1 but applied to generic custom select */}
                                        <div className="px-4 py-3 bg-slate-500 text-white text-[13px] font-medium border-b border-slate-600 hidden">
                                            Pilih Jenis Gangguan...
                                        </div>

                                        <div className="max-h-[250px] overflow-y-auto">
                                            {problemTypes.map((pt) => (
                                                <div
                                                    key={pt.id}
                                                    onClick={() => handleSelectProblem(pt.id)}
                                                    className={`w-full px-4 py-3 flex items-center gap-3 cursor-pointer hover:bg-slate-50 transition-colors ${formData.title === pt.id ? 'bg-sky-50/50' : ''}`}
                                                >
                                                    <div className={`w-[14px] h-[14px] rounded-full ${pt.color}`}></div>
                                                    <span className="text-[15px] text-[#294B73] font-medium tracking-wide">{pt.id}</span>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                )}
                            </div>

                            <button onClick={handleNext} className="w-full mt-8 bg-[#0092FF] hover:bg-[#0080FF] text-white py-4 rounded-xl font-bold transition shadow-[0_4px_14px_0_rgba(0,118,255,0.39)] hover:shadow-[0_6px_20px_rgba(0,118,255,0.23)] hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                Lanjutkan <i className="ph-bold ph-arrow-right"></i>
                            </button>
                        </div>
                    )}

                    {/* STEP 2 */}
                    {step === 2 && (
                        <div className="space-y-6 animate-fade-in-up">

                            {/* Matching Dropdown style for Kecamatan as well for consistency */}
                            <div>
                                <label className="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Kecamatan</label>
                                <div className="relative">
                                    <select name="kecamatan_id" value={formData.kecamatan_id} onChange={handleChange} className="w-full bg-white border border-slate-300 rounded-xl px-4 py-3.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none transition appearance-none cursor-pointer">
                                        <option value="" className="bg-slate-500 text-white py-2">Pilih Kecamatan...</option>
                                        {kecamatans?.map(kec => (
                                            <option key={kec.id} value={kec.id} className="text-slate-700 py-1 border-b border-slate-100">{kec.nama}</option>
                                        ))}
                                    </select>
                                    <i className="ph-bold ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                </div>
                            </div>

                            <div>
                                <label className="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Alamat Lengkap</label>
                                <textarea name="address" value={formData.address} onChange={handleChange} rows="3" placeholder="Sebutkan ciri-ciri bangunan terdekat..." className="w-full bg-white border border-slate-300 rounded-xl px-4 py-3.5 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none transition resize-none placeholder-slate-400"></textarea>
                            </div>

                            <div className="bg-sky-50/50 border border-sky-100 rounded-2xl p-4">
                                <div className="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 className="text-[13px] font-bold text-slate-800 flex items-center gap-1.5">
                                            <i className="ph-fill ph-map-pin text-sky-500"></i> Geotagging
                                        </h4>
                                        <p className="text-[10px] text-slate-500 mt-0.5">{gpsStatus}</p>
                                    </div>
                                    <button onClick={detectGPS} className="bg-white border border-slate-200 text-slate-700 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-slate-50 transition shadow-sm flex items-center gap-1">
                                        <i className="ph-bold ph-crosshair"></i> Deteksi
                                    </button>
                                </div>
                                <div className="h-48 rounded-xl overflow-hidden bg-slate-200 relative border border-slate-200 shadow-inner">
                                    <div id="map" className="w-full h-full"></div>
                                    {formData.latitude && formData.longitude && (
                                        <div className="absolute inset-0 z-[1000] pointer-events-none flex items-center justify-center pb-8">
                                            <i className="ph-fill ph-map-pin text-4xl text-rose-500 drop-shadow-md"></i>
                                        </div>
                                    )}
                                </div>
                            </div>

                            <div className="flex gap-4 mt-8 pt-4 border-t border-slate-100">
                                <button onClick={() => setStep(1)} className="px-6 py-4 bg-white border border-slate-300 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition flex items-center justify-center">
                                    Kembali
                                </button>
                                <button onClick={handleSubmit} disabled={isSubmitting} className="flex-1 bg-[#0092FF] hover:bg-[#0080FF] text-white py-4 rounded-xl font-bold transition shadow-[0_4px_14px_0_rgba(0,118,255,0.39)] hover:shadow-[0_6px_20px_rgba(0,118,255,0.23)] hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                    {isSubmitting ? <span className="animate-pulse">Loading...</span> : <>Kirim Laporan <i className="ph-bold ph-paper-plane-tilt text-lg"></i></>}
                                </button>
                            </div>
                        </div>
                    )}

                </div>
            </div>

            <style dangerouslySetInnerHTML={{
                __html: `
                @keyframes fade-in-up { 0% { opacity: 0; transform: translateY(10px); } 100% { opacity: 1; transform: translateY(0); } }
                .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
                
                /* Hide scrollbar for dropdown */
                .overflow-y-auto::-webkit-scrollbar { width: 6px; }
                .overflow-y-auto::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 8px; }
                .overflow-y-auto::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
            `}} />
        </div>
    );
};

export default Report;
