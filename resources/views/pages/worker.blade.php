<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Petugas · Tiara Smart Assistant</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#a855f7',
                        glass: 'rgba(255, 255, 255, 0.7)',
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f3f4f6; }
        
        /* Glassmorphism Profile Panel */
        .glass-panel {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.5);
        }

        /* Scrollbar Hide */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="flex h-screen overflow-hidden text-slate-800">

    <!-- 1. SIDEBAR NAVIGATION (Leftmost) -->
    <aside class="w-20 bg-white flex flex-col items-center py-6 border-r border-slate-200 z-30 hidden sm:flex">
        <div class="mb-8">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-500/30">
                T
            </div>
        </div>
        
        <nav class="flex-1 flex flex-col gap-6 w-full px-4">
            <a href="{{ route('worker.dashboard') }}" class="w-12 h-12 rounded-xl {{ request()->routeIs('worker.dashboard') ? 'bg-indigo-50 text-indigo-600 shadow-sm' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' }} flex items-center justify-center transition-all" title="Dashboard">
                <i class="ph-fill ph-squares-four text-2xl"></i>
            </a>
            @if(auth()->user()->username === 'adminlapang')
            <a href="{{ route('worker.dispatch') }}" class="w-12 h-12 rounded-xl {{ request()->routeIs('worker.dispatch') ? 'bg-orange-50 text-orange-600 shadow-sm' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' }} flex items-center justify-center transition-all" title="Perintah Kerja">
                <i class="ph-bold ph-clipboard-text text-2xl"></i>
            </a>
            @else
            <a href="#" class="w-12 h-12 rounded-xl text-slate-400 hover:bg-slate-50 hover:text-slate-600 flex items-center justify-center transition-all">
                <i class="ph-bold ph-clipboard-text text-2xl"></i>
            </a>
            @endif
            <a href="#" class="w-12 h-12 rounded-xl text-slate-400 hover:bg-slate-50 hover:text-slate-600 flex items-center justify-center transition-all">
                <i class="ph-bold ph-folder text-2xl"></i>
            </a>
            <button onclick="openSettings()" class="w-12 h-12 rounded-xl text-slate-400 hover:bg-slate-50 hover:text-slate-600 flex items-center justify-center transition-all">
                <i class="ph-bold ph-gear text-2xl"></i>
            </button>
        </nav>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-12 h-12 rounded-xl text-red-400 hover:bg-red-50 hover:text-red-600 flex items-center justify-center transition-all">
                <i class="ph-bold ph-sign-out text-2xl"></i>
            </button>
        </form>
    </aside>

    <!-- 2. PROFILE PANEL (Middle-Left) -->
    <aside class="hidden lg:flex w-[340px] glass-panel flex-col p-6 overflow-y-auto no-scrollbar relative z-20">
        <!-- Header Profile -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <div class="relative cursor-pointer" onclick="openSettings()">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 p-[2px]">
                         @if(auth()->user()->photo)
                            <img src="{{ asset('storage/' . auth()->user()->photo) }}" class="w-full h-full rounded-full border-2 border-white object-cover profile-img-target">
                         @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random" class="w-full h-full rounded-full border-2 border-white object-cover profile-img-target">
                         @endif
                    </div>
                    <div class="absolute bottom-0 right-0 w-4 h-4 bg-white rounded-full flex items-center justify-center shadow-sm">
                        <i class="ph-bold ph-gear text-[10px] text-slate-500"></i>
                    </div>
                </div>
                <div>
                    <h2 class="font-bold text-slate-900 leading-tight profile-name-target">{{ explode(' ', auth()->user()->name)[0] }}</h2>
                    <div class="flex items-center gap-1 mt-0.5">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Online</span>
                    </div>
                </div>
            </div>
            <button class="w-8 h-8 rounded-full border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-white transition">
                <i class="ph-bold ph-caret-down"></i>
            </button>
        </div>

        <!-- Big Profile Card -->
        <div class="relative bg-white/50 rounded-3xl p-6 mb-6 border border-white/50 shadow-sm text-center group">
             <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-tr from-blue-400 to-indigo-500 p-1 mb-4 shadow-lg shadow-indigo-500/20 cursor-pointer relative" onclick="openSettings()">
                 @if(auth()->user()->photo)
                    <img src="{{ asset('storage/' . auth()->user()->photo) }}" class="w-full h-full rounded-full border-4 border-white/80 object-cover profile-img-target">
                 @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=1e293b&color=fff&size=200" class="w-full h-full rounded-full border-4 border-white/80 object-cover profile-img-target">
                 @endif
                 
                 <div class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition backdrop-blur-[2px]">
                    <i class="ph-bold ph-camera text-2xl"></i>
                 </div>
             </div>
             <h1 class="text-xl font-extrabold text-slate-900 profile-name-full-target">{{ auth()->user()->name }}</h1>
             <p class="text-sm font-semibold text-indigo-500 mt-1">@ {{ auth()->user()->username }}</p>
             
             <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-full text-xs font-bold mt-3 border border-indigo-100">
                 <i class="ph-fill ph-check-circle"></i> Petugas Lapangan
             </div>

             <!-- Action Buttons -->
             <div class="grid grid-cols-2 gap-3 mt-6">
                 <button onclick="alert('Fitur pesan belum aktif')" class="py-2.5 px-4 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-orange-500/20 transition flex items-center justify-center gap-2">
                     <i class="ph-bold ph-chat-circle-text"></i> Message
                 </button>
                 <button onclick="openSettings()" class="py-2.5 px-4 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl font-bold text-sm transition flex items-center justify-center gap-2">
                     <i class="ph-bold ph-gear"></i> Settings
                 </button>
             </div>
        </div>

        <!-- Notes -->
        <div class="mb-6">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Today's Notes</h3>
            <div class="bg-yellow-50/80 p-4 rounded-2xl border border-yellow-100 text-sm text-yellow-800 font-medium leading-relaxed">
                "Fokus pada perbaikan pipa bocor di sektor utara hari ini. Pastikan foto before/after jelas."
            </div>
        </div>

        <!-- Address / Area -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Wilayah Kerja</h3>
            </div>
            <div class="bg-white p-1 rounded-2xl border border-slate-100 shadow-sm relative group cursor-pointer overflow-hidden">
                <div class="absolute inset-0 bg-slate-900/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300 z-10">
                    <span class="px-3 py-1 bg-white rounded-lg text-xs font-bold shadow-md">Buka Peta</span>
                </div>
                <!-- Mock Map -->
                <div class="h-28 bg-blue-100 rounded-xl relative overflow-hidden">
                    <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(#3b82f6 1.5px, transparent 1.5px); background-size: 12px 12px;"></div>
                    <i class="ph-fill ph-map-pin text-red-500 text-2xl absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 drop-shadow-md"></i>
                </div>
                <div class="p-3">
                    <p class="text-xs font-bold text-slate-700">Lombok Tengah</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Praya &amp; Sekitarnya</p>
                </div>
            </div>
        </div>

        <!-- Attachments -->
        <div class="mt-auto">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Attachments</h3>
                <button class="text-[10px] font-bold py-1 px-2 border border-slate-200 rounded-lg hover:bg-white text-slate-500 transition">+ Add File</button>
            </div>
            <div class="space-y-2">
                <div class="flex items-center gap-3 p-3 bg-white border border-slate-100 rounded-xl hover:shadow-sm transition cursor-pointer">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center"><i class="ph-fill ph-file-pdf"></i></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-700 truncate">SOP_Perbaikan.pdf</p>
                        <p class="text-[10px] text-slate-400">1.2 MB</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-white border border-slate-100 rounded-xl hover:shadow-sm transition cursor-pointer">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center"><i class="ph-fill ph-file-doc"></i></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-700 truncate">Form_Laporan.docx</p>
                        <p class="text-[10px] text-slate-400">500 KB</p>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- 3. MAIN CONTENT (Right) -->
    <main class="flex-1 flex flex-col items-center bg-gray-50/50 relative overflow-hidden">
        
        <!-- Top Bar -->
        <div class="w-full max-w-5xl px-6 pt-6 pb-2">
             <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                 <!-- Search -->
                 <div class="relative w-full sm:w-96">
                     <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                     <input type="text" placeholder="Cari nomor pelapor atau alamat..." class="w-full pl-11 pr-4 py-3 rounded-2xl border-none bg-white shadow-sm text-sm font-semibold focus:ring-2 focus:ring-indigo-100 outline-none">
                 </div>
                 
                 <!-- Filters & Action -->
                 <div class="flex items-center gap-2">
                     <button class="bg-white px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 shadow-sm border border-transparent hover:border-slate-200 transition">
                         Any Status <i class="ph-bold ph-caret-down ml-1"></i>
                     </button>
                     <button class="bg-white px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 shadow-sm border border-transparent hover:border-slate-200 transition">
                         This Month <i class="ph-bold ph-caret-down ml-1"></i>
                     </button>
                 </div>
                 
                 <!-- Create Button (Mobile/Desktop) -->
                 <button onclick="openReport()" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-5 py-3 rounded-xl font-bold text-sm shadow-lg shadow-blue-500/30 hover:shadow-blue-500/40 hover:-translate-y-0.5 transition flex items-center gap-2">
                     <i class="ph-bold ph-plus"></i> <span class="hidden sm:inline">Buat Laporan</span>
                 </button>
                 <!-- Mobile Logout Button (only visible on mobile) -->
                 <form action="{{ route('logout') }}" method="POST" class="lg:hidden">
                     @csrf
                     <button type="submit" class="bg-white border border-red-200 text-red-500 hover:bg-red-50 px-3 py-3 rounded-xl font-bold text-sm transition flex items-center gap-1.5 shadow-sm">
                         <i class="ph-bold ph-sign-out text-lg"></i> <span class="hidden sm:inline">Keluar</span>
                     </button>
                 </form>
             </div>
        </div>

        <!-- Scrollable Content -->
        <div class="flex-1 w-full max-w-5xl px-4 sm:px-6 py-4 overflow-y-auto no-scrollbar space-y-6 pb-24">
            
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Total -->
                <div class="bg-slate-200/50 p-5 rounded-3xl border border-slate-300/50">
                    <p class="text-xs font-bold text-slate-500 uppercase">Total Laporan</p>
                    <div class="flex items-end justify-between mt-1">
                        <h2 class="text-3xl font-black text-slate-700">{{ $jobs->count() }}</h2>
                        <i class="ph-duotone ph-files text-3xl text-slate-400"></i>
                    </div>
                </div>
                <!-- Working -->
                <div class="bg-blue-100/50 p-5 rounded-3xl border border-blue-200/50">
                    <p class="text-xs font-bold text-blue-600 uppercase">Sedang Dikerjakan</p>
                    <div class="flex items-end justify-between mt-1">
                        <h2 class="text-3xl font-black text-blue-700">{{ $workingCount }}</h2>
                        <i class="ph-duotone ph-wrench text-3xl text-blue-400"></i>
                    </div>
                </div>
                <!-- Pending/Done -->
                <div class="bg-yellow-100/50 p-5 rounded-3xl border border-yellow-200/50">
                    <p class="text-xs font-bold text-yellow-600 uppercase">Selesai Hari Ini</p>
                    <div class="flex items-end justify-between mt-1">
                        <h2 class="text-3xl font-black text-yellow-700">{{ $doneCount }}</h2>
                        <i class="ph-duotone ph-check-circle text-3xl text-yellow-400"></i>
                    </div>
                </div>
            </div>

            <!-- Header List -->
            <div class="flex items-center justify-between px-2">
                <h3 class="text-lg font-bold text-slate-800">Daftar Laporan</h3>
                <span class="text-xs font-bold text-slate-400">{{ now()->translatedFormat('d F Y') }}</span>
            </div>

            <!-- List / Table -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="min-w-full">
                    @forelse($jobs as $job)
                    <!-- Item Row -->
                    <div class="group flex items-center justify-between p-3 sm:p-4 border-b border-slate-50 hover:bg-slate-50/80 transition gap-2 sm:gap-4">
                        
                         <!-- Icon & Info (clickable for detail) -->
                         <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0 cursor-pointer" onclick='openDetail(@json($job))'>
                             <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl flex items-center justify-center shrink-0 
                                 {{ Str::contains($job->title, 'Pipa') ? 'bg-blue-50 text-blue-600' : (Str::contains($job->title, 'Meteran') ? 'bg-slate-100 text-slate-600' : 'bg-orange-50 text-orange-600') }}">
                                 @if(Str::contains($job->title, 'Pipa')) <i class="ph-fill ph-drop text-lg sm:text-xl"></i>
                                 @elseif(Str::contains($job->title, 'Meteran')) <i class="ph-fill ph-gauge text-lg sm:text-xl"></i>
                                 @else <i class="ph-fill ph-warning-circle text-lg sm:text-xl"></i>
                                 @endif
                             </div>
                             <div class="flex-1 min-w-0">
                                 <div class="flex items-center gap-2 flex-wrap">
                                     <h4 class="font-bold text-slate-800 text-[13px] sm:text-base truncate">{{ $job->title }} #{{ $job->id }}</h4>
                                     @if($job->source == 'public')
                                         <div class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full bg-cyan-50 border border-cyan-200 text-cyan-700 text-[9px] sm:text-[10px] font-bold shrink-0">
                                             <i class="ph-bold ph-globe-simple"></i> Publik
                                         </div>
                                     @endif
                                     @if($job->status == 'pending')
                                        <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-slate-100 border border-slate-200 text-slate-600 text-[9px] sm:text-[10px] font-bold shrink-0">
                                            <i class="ph-bold ph-clock"></i> Pending
                                        </div>
                                    @elseif($job->status == 'on_progress')
                                        <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-yellow-50 border border-yellow-200 text-yellow-700 text-[9px] sm:text-[10px] font-bold shrink-0">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span> Working
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-green-50 border border-green-200 text-green-700 text-[9px] sm:text-[10px] font-bold shrink-0">
                                            <i class="ph-bold ph-check-circle"></i> Done
                                        </div>
                                    @endif
                                 </div>
                                 <div class="flex items-center gap-1.5 mt-0.5 sm:mt-1">
                                     <span class="text-[10px] sm:text-xs font-medium text-slate-400 shrink-0">{{ $job->created_at->format('H:i') }}</span>
                                     <span class="w-1 h-1 rounded-full bg-slate-300 shrink-0"></span>
                                     <span class="text-[10px] sm:text-xs font-medium text-slate-400 truncate">{{ $job->address }}</span>
                                 </div>
                                 @if($job->reporter_name)
                                 <div class="flex items-center gap-1.5 mt-0.5">
                                     <i class="ph ph-user text-[10px] text-slate-300"></i>
                                     <span class="text-[10px] sm:text-xs font-medium text-slate-400 truncate">{{ $job->reporter_name }} · {{ $job->reporter_phone }}</span>
                                 </div>
                                 @endif
                             </div>
                         </div>

                         <!-- Action Buttons -->
                         <div class="flex items-center gap-1 sm:gap-2 shrink-0">
                             @if($job->latitude && $job->longitude)
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $job->latitude }},{{ $job->longitude }}" target="_blank" rel="noopener" class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition border border-emerald-100" title="Navigasi ke lokasi">
                                    <i class="ph-bold ph-navigation-arrow text-sm sm:text-base"></i>
                                </a>
                             @endif
                             @if($job->status == 'pending')
                                <button onclick="openStart({{ $job->id }})" class="px-2.5 sm:px-3 py-1.5 sm:py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[10px] sm:text-xs font-bold transition shadow-sm">
                                    <i class="ph-bold ph-play"></i> Kerjakan
                                </button>
                                <button onclick="cancelJob({{ $job->id }})" class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl text-red-500 hover:bg-red-50 transition">
                                   <i class="ph-bold ph-trash"></i>
                                </button>
                            @elseif($job->status == 'on_progress')
                                <button onclick="openFinish({{ $job->id }})" class="px-2.5 sm:px-3 py-1.5 sm:py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-xl text-[10px] sm:text-xs font-bold transition">
                                     Selesaikan
                                 </button>
                                 <button onclick="cancelJob({{ $job->id }})" class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl text-red-500 hover:bg-red-50 transition">
                                    <i class="ph-bold ph-trash"></i>
                                 </button>
                             @else
                                 <span class="text-[10px] sm:text-sm font-bold text-slate-900 mr-2">{{ $job->finished_at ? $job->finished_at->format('d M') : '-' }}</span>
                                 
                                 <button onclick="openEditJobModal({{ $job }})" class="w-7 h-7 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-indigo-600 transition">
                                    <i class="ph-bold ph-pencil-simple text-sm sm:text-base"></i>
                                 </button>
                                 <button onclick="cancelJob({{ $job->id }})" class="w-7 h-7 sm:w-9 sm:h-9 flex items-center justify-center rounded-xl text-slate-400 hover:bg-red-50 hover:text-red-500 transition border border-transparent">
                                    <i class="ph-bold ph-trash text-sm sm:text-base"></i>
                                 </button>
                             @endif
                         </div>

                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                            <i class="ph-duotone ph-clipboard-text text-3xl"></i>
                        </div>
                        <h3 class="text-slate-900 font-bold">Tidak ada laporan</h3>
                        <p class="text-slate-400 text-sm">Pekerjaan Anda hari ini sudah selesai!</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- ═══════════════════════════════════════════
                 RIWAYAT PEKERJAAN (History)
            ═══════════════════════════════════════════ --}}
            @if(isset($history) && $history->count() > 0)
            <div class="mt-6">
                <button onclick="toggleHistory()" class="flex items-center justify-between w-full px-2 mb-3">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="ph-bold ph-clock-counter-clockwise text-slate-400"></i>
                        Riwayat Pekerjaan
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">{{ $history->count() }} laporan</span>
                        <i id="historyChevron" class="ph-bold ph-caret-down text-slate-400 transition-transform duration-300"></i>
                    </div>
                </button>

                <div id="historyList" class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="min-w-full">
                        @php $lastDate = null; @endphp
                        @foreach($history as $hJob)
                            @php $jobDate = $hJob->created_at->format('Y-m-d'); @endphp
                            @if($lastDate !== $jobDate)
                                @php $lastDate = $jobDate; @endphp
                                <div class="px-4 py-2 bg-slate-50 border-b border-slate-100">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        {{ $hJob->created_at->translatedFormat('l, d F Y') }}
                                    </span>
                                </div>
                            @endif
                            <div class="group flex items-center justify-between p-3 sm:p-4 border-b border-slate-50 hover:bg-slate-50/80 transition gap-2 sm:gap-4">
                                {{-- Icon & Info --}}
                                <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl flex items-center justify-center shrink-0 
                                        {{ Str::contains($hJob->title, 'Pipa') ? 'bg-blue-50 text-blue-600' : (Str::contains($hJob->title, 'Meteran') ? 'bg-slate-100 text-slate-600' : 'bg-orange-50 text-orange-600') }}">
                                        @if(Str::contains($hJob->title, 'Pipa')) <i class="ph-fill ph-drop text-lg sm:text-xl"></i>
                                        @elseif(Str::contains($hJob->title, 'Meteran')) <i class="ph-fill ph-gauge text-lg sm:text-xl"></i>
                                        @else <i class="ph-fill ph-warning-circle text-lg sm:text-xl"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-bold text-slate-800 text-[13px] sm:text-base truncate">{{ $hJob->title }} #{{ $hJob->id }}</h4>
                                            @if($hJob->status == 'on_progress')
                                                <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-yellow-50 border border-yellow-200 text-yellow-700 text-[9px] sm:text-[10px] font-bold shrink-0">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span> Working
                                                </div>
                                            @else
                                                <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-green-50 border border-green-200 text-green-700 text-[9px] sm:text-[10px] font-bold shrink-0">
                                                    <i class="ph-bold ph-check-circle"></i> Done
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1.5 mt-0.5 sm:mt-1">
                                            <span class="text-[10px] sm:text-xs font-medium text-slate-400 shrink-0">{{ $hJob->created_at->format('H:i') }}</span>
                                            <span class="w-1 h-1 rounded-full bg-slate-300 shrink-0"></span>
                                            <span class="text-[10px] sm:text-xs font-medium text-slate-400 truncate">{{ $hJob->address }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Date badge --}}
                                <div class="flex items-center gap-1 sm:gap-2 shrink-0">
                                    <span class="text-[10px] sm:text-xs font-bold text-slate-400">{{ $hJob->created_at->format('d M') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>
    </main>

    <!-- ===== DETAIL MODAL ===== -->
    <div id="detailModal" class="fixed inset-0 z-[60] hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeModal('detailModal')"></div>
        <div class="absolute bottom-0 sm:bottom-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 w-full sm:w-[520px] bg-white sm:rounded-3xl rounded-t-3xl shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-4 sm:hidden"></div>
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="ph-duotone ph-clipboard-text text-indigo-500"></i> Detail Laporan
                    </h3>
                    <button onclick="closeModal('detailModal')" class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-slate-200 transition">
                        <i class="ph-bold ph-x"></i>
                    </button>
                </div>

                <!-- Photo -->
                <div id="detailPhotoWrap" class="mb-5 hidden">
                    <img id="detailPhoto" src="" alt="Foto Gangguan" class="w-full max-h-64 object-cover rounded-2xl border border-slate-100 bg-slate-50">
                </div>

                <!-- Info Grid -->
                <div class="space-y-3">
                    <!-- Title + Status -->
                    <div class="flex items-center justify-between">
                        <h4 id="detailTitle" class="text-base font-bold text-slate-900"></h4>
                        <span id="detailStatus" class="text-[10px] font-bold px-2 py-0.5 rounded-full"></span>
                    </div>

                    <!-- Source Badge -->
                    <div id="detailSourceWrap" class="hidden">
                        <span id="detailSource" class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-1 rounded-lg"></span>
                    </div>

                    <!-- Ticket Code -->
                    <div id="detailTicketWrap" class="hidden">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nomor Tiket</label>
                        <p id="detailTicket" class="text-sm font-black text-indigo-600 mt-0.5 tracking-wider font-mono"></p>
                    </div>

                    <!-- Reporter Info -->
                    <div id="detailReporterWrap" class="hidden bg-cyan-50/50 rounded-xl p-3 border border-cyan-100">
                        <label class="text-[10px] font-bold text-cyan-600 uppercase tracking-widest">Pelapor</label>
                        <p id="detailReporterName" class="text-sm font-bold text-slate-800 mt-0.5"></p>
                        <p id="detailReporterPhone" class="text-xs text-slate-500 flex items-center gap-1 mt-0.5"><i class="ph ph-phone"></i> <span id="detailPhoneText"></span></p>
                    </div>

                    <!-- Address -->
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Alamat</label>
                        <p id="detailAddress" class="text-sm font-semibold text-slate-700 mt-0.5"></p>
                    </div>

                    <!-- Kecamatan -->
                    <div id="detailKecWrap" class="hidden">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kecamatan</label>
                        <p id="detailKec" class="text-sm font-semibold text-slate-700 mt-0.5"></p>
                    </div>

                    <!-- Description -->
                    <div id="detailDescWrap" class="hidden">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Deskripsi</label>
                        <p id="detailDesc" class="text-sm text-slate-600 mt-0.5 leading-relaxed bg-slate-50 rounded-xl p-3 border border-slate-100"></p>
                    </div>

                    <!-- GPS -->
                    <div id="detailGpsWrap" class="hidden">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Koordinat GPS</label>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span id="detailGps" class="text-xs font-mono text-slate-500"></span>
                            <a id="detailGpsLink" href="#" target="_blank" rel="noopener" class="text-xs font-bold text-emerald-600 hover:underline flex items-center gap-1">
                                <i class="ph-bold ph-navigation-arrow"></i> Buka Maps
                            </a>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Dibuat</label>
                            <p id="detailCreated" class="text-xs font-semibold text-slate-600 mt-0.5"></p>
                        </div>
                        <div id="detailFinishedWrap" class="hidden">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Selesai</label>
                            <p id="detailFinished" class="text-xs font-semibold text-emerald-600 mt-0.5"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== MOBILE BOTTOM NAV (Visible only < sm) ===== -->
    <div class="sm:hidden fixed bottom-6 left-6 right-6 bg-white/90 backdrop-blur-xl border border-white/50 shadow-2xl rounded-2xl p-2 z-50 flex justify-around items-center">
        <a href="#" class="p-3 text-indigo-600 bg-indigo-50 rounded-xl"><i class="ph-fill ph-squares-four text-xl"></i></a>
        <a href="#" class="p-3 text-slate-400"><i class="ph-bold ph-clipboard-text text-xl"></i></a>
        <button onclick="openReport()" class="p-3 -mt-12 bg-indigo-600 text-white rounded-2xl shadow-lg shadow-indigo-500/40"><i class="ph-bold ph-plus text-2xl"></i></button>
        <a href="#" class="p-3 text-slate-400"><i class="ph-bold ph-folder text-xl"></i></a>
        <button onclick="openSettings()" class="p-3 text-slate-400"><i class="ph-bold ph-gear text-xl"></i></button>
    </div>

    <!-- ===== REPORT MODAL ===== -->
    <div id="reportModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeModal('reportModal')"></div>
        <div class="absolute bottom-0 sm:bottom-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 w-full sm:w-[480px] bg-white sm:rounded-3xl rounded-t-3xl p-6 shadow-2xl transition-transform transform translate-y-full sm:translate-y-0" id="reportModalPanel">
            
            <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-6 sm:hidden"></div>
            
            <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                <i class="ph-duotone ph-warning-octagon text-red-500"></i> Lapor Kerusakan
            </h3>

            <form id="reportForm" action="{{ route('worker.jobs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="latitude" id="rLat">
                <input type="hidden" name="longitude" id="rLng">

                <!-- Photo Input -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Buckti Foto</label>
                    <div class="relative w-full h-40 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center text-slate-400 hover:border-indigo-400 hover:bg-indigo-50/50 transition cursor-pointer overflow-hidden" id="rBox">
                        <input type="file" name="photo_before" accept="image/*" capture="camera" required onchange="previewImage(this, 'rPrev', 'rBox')" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                        <i class="ph-duotone ph-camera text-3xl mb-2"></i>
                        <span class="text-xs font-bold">Ambil Foto</span>
                        <img id="rPrev" class="absolute inset-0 w-full h-full object-contain bg-black hidden">
                    </div>
                </div>

                <!-- Type Select -->
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Jenis Masalah</label>
                    <div class="relative">
                        <select name="title" class="w-full appearance-none bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition" required>
                            <option value="">Pilih Kategori...</option>
                            <option value="Pipa Bocor">🔴 Pipa Bocor</option>
                            <option value="Meteran Mati">⚫ Meteran Mati</option>
                            <option value="Air Keruh">🟤 Air Keruh</option>
                            <option value="Sambungan Lepas">🟠 Sambungan Lepas</option>
                            <option value="Meteran Tersumbat">🔵 Meteran Tersumbat</option>
                            <option value="Lainnya">⚪ Lainnya</option>
                        </select>
                        <i class="ph-bold ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Kecamatan Select -->
                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Kecamatan</label>
                    <div class="relative">
                        <select name="kecamatan_id" class="w-full appearance-none bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition" required>
                            <option value="">Pilih Kecamatan...</option>
                            @foreach($kecamatans as $kec)
                                <option value="{{ $kec->id }}">{{ $kec->nama }}</option>
                            @endforeach
                        </select>
                        <i class="ph-bold ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="closeModal('reportModal')" class="px-5 py-3.5 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" id="rBtn" class="px-5 py-3.5 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/25">
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== EDIT JOB MODAL ===== -->
    <div id="editJobModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeModal('editJobModal')"></div>
        <div class="absolute bottom-0 sm:bottom-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 w-full sm:w-[480px] bg-white sm:rounded-3xl rounded-t-3xl p-6 shadow-2xl transition-transform transform translate-y-full sm:translate-y-0" id="editJobModalPanel">
            
            <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-6 sm:hidden"></div>
            
            <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                <i class="ph-duotone ph-pencil-simple text-indigo-500"></i> Edit Laporan
            </h3>

            <form id="editJobForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Photo Input (Optional) -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Ubah Bukti Foto (Opsional)</label>
                    <div class="relative w-full h-40 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center text-slate-400 hover:border-indigo-400 hover:bg-indigo-50/50 transition cursor-pointer overflow-hidden" id="eBox">
                        <input type="file" name="photo_before" accept="image/*" capture="camera" onchange="previewImage(this, 'ePrev', 'eBox')" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                        <span class="text-xs font-bold z-0 text-slate-400 group-hover:hidden" id="eBoxText">Klik untuk ubah foto</span>
                        <img id="ePrev" class="absolute inset-0 w-full h-full object-contain bg-black hidden">
                    </div>
                </div>

                <!-- Type Select -->
                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Jenis Masalah</label>
                    <div class="relative">
                        <select name="title" id="editJobTitle" class="w-full appearance-none bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition" required>
                            <option value="">Pilih Kategori...</option>
                            <option value="Pipa Bocor">🔴 Pipa Bocor</option>
                            <option value="Meteran Mati">⚫ Meteran Mati</option>
                            <option value="Air Keruh">🟤 Air Keruh</option>
                            <option value="Sambungan Lepas">🟠 Sambungan Lepas</option>
                            <option value="Meteran Tersumbat">🔵 Meteran Tersumbat</option>
                            <option value="Lainnya">⚪ Lainnya</option>
                        </select>
                        <i class="ph-bold ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="closeModal('editJobModal')" class="px-5 py-3.5 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-5 py-3.5 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/25">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== FINISH MODAL ===== -->
    <div id="finishModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeModal('finishModal')"></div>
        <div class="absolute bottom-0 sm:bottom-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 w-full sm:w-[480px] bg-white sm:rounded-3xl rounded-t-3xl p-6 shadow-2xl transition-transform transform translate-y-full sm:translate-y-0" id="finishModalPanel">
            
            <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-6 sm:hidden"></div>
            
            <h3 class="text-xl font-bold text-slate-900 mb-2 flex items-center gap-2">
                <i class="ph-duotone ph-check-circle text-green-500"></i> Selesaikan Tugas
            </h3>
            <p class="text-sm text-slate-500 mb-6">Upload foto bukti perbaikan untuk menyelesaikan tugas.</p>

            <input type="hidden" id="fJobId">

            <div class="mb-6">
                <div class="relative w-full h-48 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center text-slate-400 hover:border-green-400 hover:bg-green-50/50 transition cursor-pointer overflow-hidden" id="fBox">
                    <input type="file" id="fFile" accept="image/*" capture="camera" required onchange="previewImage(this, 'fPrev', 'fBox')" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                    <i class="ph-duotone ph-camera text-4xl mb-2 text-green-500"></i>
                    <span class="text-xs font-bold text-green-600">Ambil Foto Hasil</span>
                    <img id="fPrev" class="absolute inset-0 w-full h-full object-contain bg-black hidden">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button onclick="closeModal('finishModal')" class="px-5 py-3.5 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition">Batal</button>
                <button onclick="submitFinish()" id="fBtn" class="px-5 py-3.5 bg-green-600 text-white rounded-xl font-bold text-sm hover:bg-green-700 transition shadow-lg shadow-green-500/25">
                    Selesai & Kirim
                </button>
            </div>
        </div>
    </div>

    <!-- ===== START JOB MODAL (Photo Before) ===== -->
    <div id="startModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeModal('startModal')"></div>
        <div class="absolute bottom-0 sm:bottom-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 w-full sm:w-[480px] bg-white sm:rounded-3xl rounded-t-3xl p-6 shadow-2xl transition-transform transform translate-y-full sm:translate-y-0" id="startModalPanel">
            
            <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-6 sm:hidden"></div>
            
            <h3 class="text-xl font-bold text-slate-900 mb-2 flex items-center gap-2">
                <i class="ph-duotone ph-play-circle text-indigo-500"></i> Mulai Kerjakan
            </h3>
            <p class="text-sm text-slate-500 mb-6">Ambil foto kondisi awal sebelum mengerjakan.</p>

            <input type="hidden" id="sJobId">

            <div class="mb-6">
                <div class="relative w-full h-48 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center text-slate-400 hover:border-indigo-400 hover:bg-indigo-50/50 transition cursor-pointer overflow-hidden" id="stBox">
                    <input type="file" id="stFile" accept="image/*" capture="camera" required onchange="previewImage(this, 'stPrev', 'stBox')" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                    <i class="ph-duotone ph-camera text-4xl mb-2 text-indigo-500"></i>
                    <span class="text-xs font-bold text-indigo-600">Ambil Foto Sebelum</span>
                    <img id="stPrev" class="absolute inset-0 w-full h-full object-contain bg-black hidden">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button onclick="closeModal('startModal')" class="px-5 py-3.5 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition">Batal</button>
                <button onclick="submitStart()" id="stBtn" class="px-5 py-3.5 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/25">
                    Mulai Kerjakan
                </button>
            </div>
        </div>
    </div>

    <!-- ===== SETTINGS MODAL (New) ===== -->
    <div id="settingsModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeModal('settingsModal')"></div>
        <div class="absolute bottom-0 sm:bottom-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 w-full sm:w-[480px] bg-white sm:rounded-3xl rounded-t-3xl p-6 shadow-2xl transition-transform transform translate-y-full sm:translate-y-0" id="settingsModalPanel">
            
            <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-6 sm:hidden"></div>
            
            <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                <i class="ph-duotone ph-gear text-indigo-500"></i> Pengaturan Profil
            </h3>

            <!-- Alert Placeholder -->
            <div id="settingsAlert" class="hidden mb-4 p-3 rounded-xl text-sm font-bold flex items-center gap-2"></div>

            <form id="settingsForm">
                <!-- Photo Input -->
                <div class="flex justify-center mb-6">
                    <div class="relative w-24 h-24 rounded-full bg-slate-100 border-2 border-dashed border-slate-300 flex items-center justify-center overflow-hidden cursor-pointer hover:border-indigo-400 group" id="sBox">
                         @if(auth()->user()->photo)
                            <img src="{{ asset('storage/' . auth()->user()->photo) }}" id="sPrev" class="w-full h-full object-cover">
                         @else
                            <img id="sPrev" class="w-full h-full object-cover hidden">
                            <span class="text-2xl font-bold text-slate-400 group-hover:hidden">{{ substr(auth()->user()->name, 0, 2) }}</span>
                         @endif
                         
                         <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                             <i class="ph-bold ph-camera text-white text-xl"></i>
                         </div>
                         <input type="file" name="photo" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" onchange="previewSettingsImage(this)">
                    </div>
                </div>

                <!-- Name Input -->
                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ auth()->user()->name }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition" required>
                </div>

                <!-- Buttons -->
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="closeModal('settingsModal')" class="px-5 py-3.5 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" id="sBtn" class="px-5 py-3.5 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/25">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
        // === UI Interactions ===
        function openReport() {
            const m = document.getElementById('reportModal');
            m.classList.remove('hidden');
            setTimeout(() => {
                const p = document.getElementById('reportModalPanel');
                if(window.innerWidth < 640) { p.style.transform = 'translateY(0)'; }
            }, 10);
        }
        
        function openFinish(id) {
            document.getElementById('fJobId').value = id;
            document.getElementById('fFile').value = '';
            document.getElementById('fPrev').classList.add('hidden');
            
            const m = document.getElementById('finishModal');
            m.classList.remove('hidden');
            setTimeout(() => {
                const p = document.getElementById('finishModalPanel');
                if(window.innerWidth < 640) { p.style.transform = 'translateY(0)'; }
            }, 10);
        }

        function openStart(id) {
            document.getElementById('sJobId').value = id;
            document.getElementById('stFile').value = '';
            document.getElementById('stPrev').classList.add('hidden');
            const m = document.getElementById('startModal');
            m.classList.remove('hidden');
            setTimeout(() => {
                const p = document.getElementById('startModalPanel');
                if(window.innerWidth < 640) { p.style.transform = 'translateY(0)'; }
            }, 10);
        }

        function openSettings() {
            const m = document.getElementById('settingsModal');
            document.getElementById('settingsAlert').classList.add('hidden');
            m.classList.remove('hidden');
             setTimeout(() => {
                const p = document.getElementById('settingsModalPanel');
                if(window.innerWidth < 640) { p.style.transform = 'translateY(0)'; }
            }, 10);
        }

        function openEditJobModal(job) {
            const form = document.getElementById('editJobForm');
            form.action = `/worker/jobs/${job.id}`;
            document.getElementById('editJobTitle').value = job.title;
            
            // Clear photo
            document.getElementById('ePrev').classList.add('hidden');
            document.getElementById('ePrev').src = '';
            document.getElementById('eBoxText').classList.remove('hidden');

            const m = document.getElementById('editJobModal');
            m.classList.remove('hidden');
             setTimeout(() => {
                const p = document.getElementById('editJobModalPanel');
                if(window.innerWidth < 640) { p.style.transform = 'translateY(0)'; }
            }, 10);
        }

        function openDetail(job) {
            // Title + Status
            document.getElementById('detailTitle').textContent = job.title + ' #' + job.id;
            const st = document.getElementById('detailStatus');
            if (job.status === 'pending') {
                st.textContent = 'Pending'; st.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600';
            } else if (job.status === 'on_progress') {
                st.textContent = 'Working'; st.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-yellow-50 text-yellow-700';
            } else {
                st.textContent = 'Done'; st.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-green-50 text-green-700';
            }

            // Source badge
            const srcW = document.getElementById('detailSourceWrap');
            const srcE = document.getElementById('detailSource');
            if (job.source === 'public') {
                srcE.innerHTML = '<i class="ph-bold ph-globe-simple"></i> Laporan Publik';
                srcE.className = 'inline-flex items-center gap-1 text-[10px] font-bold px-2 py-1 rounded-lg bg-cyan-50 text-cyan-700 border border-cyan-200';
                srcW.classList.remove('hidden');
            } else if (job.source === 'dispatch') {
                srcE.innerHTML = '<i class="ph-bold ph-megaphone"></i> Perintah Kerja';
                srcE.className = 'inline-flex items-center gap-1 text-[10px] font-bold px-2 py-1 rounded-lg bg-orange-50 text-orange-700 border border-orange-200';
                srcW.classList.remove('hidden');
            } else {
                srcW.classList.add('hidden');
            }

            // Ticket code
            const tkW = document.getElementById('detailTicketWrap');
            if (job.ticket_code) {
                document.getElementById('detailTicket').textContent = job.ticket_code;
                tkW.classList.remove('hidden');
            } else { tkW.classList.add('hidden'); }

            // Reporter info
            const rpW = document.getElementById('detailReporterWrap');
            if (job.reporter_name) {
                document.getElementById('detailReporterName').textContent = job.reporter_name;
                document.getElementById('detailPhoneText').textContent = job.reporter_phone || '-';
                rpW.classList.remove('hidden');
            } else { rpW.classList.add('hidden'); }

            // Photo
            const phW = document.getElementById('detailPhotoWrap');
            if (job.photo_before) {
                document.getElementById('detailPhoto').src = '/' + job.photo_before;
                phW.classList.remove('hidden');
            } else { phW.classList.add('hidden'); }

            // Address
            document.getElementById('detailAddress').textContent = job.address || '-';

            // Kecamatan
            const kcW = document.getElementById('detailKecWrap');
            if (job.kecamatan && job.kecamatan.nama) {
                document.getElementById('detailKec').textContent = job.kecamatan.nama;
                kcW.classList.remove('hidden');
            } else { kcW.classList.add('hidden'); }

            // Description
            const dsW = document.getElementById('detailDescWrap');
            if (job.description) {
                document.getElementById('detailDesc').textContent = job.description;
                dsW.classList.remove('hidden');
            } else { dsW.classList.add('hidden'); }

            // GPS
            const gpW = document.getElementById('detailGpsWrap');
            if (job.latitude && job.longitude) {
                document.getElementById('detailGps').textContent = job.latitude + ', ' + job.longitude;
                document.getElementById('detailGpsLink').href = 'https://www.google.com/maps/dir/?api=1&destination=' + job.latitude + ',' + job.longitude;
                gpW.classList.remove('hidden');
            } else { gpW.classList.add('hidden'); }

            // Timestamps
            if (job.created_at) {
                const d = new Date(job.created_at);
                document.getElementById('detailCreated').textContent = d.toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric'}) + ' ' + d.toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit'});
            }
            const fnW = document.getElementById('detailFinishedWrap');
            if (job.finished_at) {
                const f = new Date(job.finished_at);
                document.getElementById('detailFinished').textContent = f.toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric'}) + ' ' + f.toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit'});
                fnW.classList.remove('hidden');
            } else { fnW.classList.add('hidden'); }

            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeModal(id) {
            const m = document.getElementById(id);
            const p = m.querySelector('div[id$="Panel"]');
            if(p && window.innerWidth < 640) { p.style.transform = 'translateY(100%)'; }
            
            setTimeout(() => m.classList.add('hidden'), 200);
        }

        function previewImage(input, imgId, boxId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById(imgId);
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewSettingsImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('sPrev');
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function compressImage(file, maxSize, callback) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function(event) {
                const img = new Image();
                img.src = event.target.result;
                img.onload = function() {
                    let width = img.width;
                    let height = img.height;
                    if (width > maxSize || height > maxSize) {
                        if (width > height) {
                            height = Math.round((height *= maxSize / width));
                            width = maxSize;
                        } else {
                            width = Math.round((width *= maxSize / height));
                            height = maxSize;
                        }
                    }
                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    canvas.toBlob(function(blob) {
                        callback(new File([blob], file.name, { type: 'image/jpeg', lastModified: Date.now() }));
                    }, 'image/jpeg', 0.8);
                };
            };
        }

        // === Logic (Preserved) ===
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        document.getElementById('reportForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('rBtn');
            const originalText = btn.innerText;
            const form = this;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="ph-bold ph-spinner animate-spin"></i> GPS...';

            if (!navigator.geolocation) {
                alert("Browser tidak mendukung GPS.");
                btn.disabled = false; btn.innerText = originalText;
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    document.getElementById('rLat').value = position.coords.latitude;
                    document.getElementById('rLng').value = position.coords.longitude;
                    
                    btn.innerHTML = '<i class="ph-bold ph-spinner animate-spin"></i> Memproses foto...';
                    const fileInput = form.querySelector('input[type="file"]');
                    if (fileInput && fileInput.files.length > 0) {
                        compressImage(fileInput.files[0], 1200, function(compressedFile) {
                            const dt = new DataTransfer();
                            dt.items.add(compressedFile);
                            fileInput.files = dt.files;
                            btn.innerHTML = '<i class="ph-bold ph-spinner animate-spin"></i> Mengirim...';
                            form.submit();
                        });
                    } else {
                        btn.innerHTML = '<i class="ph-bold ph-spinner animate-spin"></i> Mengirim...';
                        form.submit();
                    }
                },
                function(error) {
                    alert("Gagal mengambil lokasi: " + error.message);
                    btn.disabled = false; btn.innerText = originalText;
                }, 
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });

        document.getElementById('editJobForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.innerText;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="ph-bold ph-spinner animate-spin"></i> Memproses...';
            
            const fileInput = form.querySelector('input[type="file"]');
            if (fileInput && fileInput.files.length > 0) {
                compressImage(fileInput.files[0], 1200, function(compressedFile) {
                    const dt = new DataTransfer();
                    dt.items.add(compressedFile);
                    fileInput.files = dt.files;
                    btn.innerHTML = '<i class="ph-bold ph-spinner animate-spin"></i> Mengirim...';
                    form.submit();
                });
            } else {
                form.submit();
            }
        });

        function submitFinish() {
            const jobId = document.getElementById('fJobId').value;
            const fileInput = document.getElementById('fFile');
            const btn = document.getElementById('fBtn');
            
            if (fileInput.files.length === 0) {
                alert("Harap lampirkan foto bukti perbaikan.");
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="ph-bold ph-spinner animate-spin"></i> Uploading...';

            compressImage(fileInput.files[0], 1200, function(compressedFile) {
                const formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('photo_after', compressedFile);

                fetch(`/worker/jobs/${jobId}/finish`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Gagal menyimpan data.');
                        btn.disabled = false; btn.innerText = 'Selesai & Kirim';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan jaringan.');
                    btn.disabled = false; btn.innerText = 'Selesai & Kirim';
                });
            });
        }

        function submitStart() {
            const jobId = document.getElementById('sJobId').value;
            const fileInput = document.getElementById('stFile');
            const btn = document.getElementById('stBtn');

            if (!fileInput.files || fileInput.files.length === 0) {
                alert("Harap lampirkan foto kondisi sebelum mengerjakan.");
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="ph-bold ph-spinner animate-spin"></i> Uploading...';

            compressImage(fileInput.files[0], 1200, function(compressedFile) {
                const formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('photo_before', compressedFile);

                fetch(`/worker/jobs/${jobId}/start`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Gagal memulai pekerjaan.');
                        btn.disabled = false; btn.innerText = 'Mulai Kerjakan';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan jaringan.');
                    btn.disabled = false; btn.innerText = 'Mulai Kerjakan';
                });
            });
        }

        // === Settings Update ===
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('sBtn');
            const alertBox = document.getElementById('settingsAlert');
            const formData = new FormData(this);
            formData.append('_token', csrfToken);

            btn.disabled = true;
            btn.innerHTML = '<i class="ph-bold ph-spinner animate-spin"></i> Menyimpan...';

            fetch("{{ route('worker.profile.update') }}", {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerText = 'Simpan Perubahan';

                if(data.success) {
                    alertBox.className = 'mb-4 p-3 rounded-xl text-sm font-bold flex items-center gap-2 bg-green-50 text-green-700';
                    alertBox.innerHTML = '<i class="ph-fill ph-check-circle"></i> ' + data.message;
                    
                    // Update UI instantly
                    document.querySelectorAll('.profile-name-target').forEach(el => el.innerText = formData.get('name').split(' ')[0]);
                    document.querySelectorAll('.profile-name-full-target').forEach(el => el.innerText = formData.get('name'));
                    if(data.photo_url) {
                        document.querySelectorAll('.profile-img-target').forEach(el => el.src = data.photo_url);
                        document.getElementById('sPrev').src = data.photo_url;
                    }
                    setTimeout(() => closeModal('settingsModal'), 1500);
                } else {
                    throw new Error(data.message || 'Gagal update.');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerText = 'Simpan Perubahan';
                alertBox.className = 'mb-4 p-3 rounded-xl text-sm font-bold flex items-center gap-2 bg-red-50 text-red-700';
                alertBox.innerHTML = '<i class="ph-fill ph-warning-circle"></i> ' + err.message;
            });
        });

        function toggleHistory() {
            const list = document.getElementById('historyList');
            const chevron = document.getElementById('historyChevron');
            if (!list) return;
            if (list.style.display === 'none') {
                list.style.display = '';
                if (chevron) chevron.style.transform = '';
            } else {
                list.style.display = 'none';
                if (chevron) chevron.style.transform = 'rotate(-90deg)';
            }
        }

        function cancelJob(id) {
            if(!confirm("Hapus laporan ini?")) return;
            
            fetch(`/worker/jobs/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
               if(data.success) window.location.reload();
            });
        }
    </script>
</body>
</html>
