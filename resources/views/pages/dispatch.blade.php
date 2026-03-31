<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Perintah Kerja — Admin Lapangan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden text-slate-800 bg-slate-50">

    <!-- 1. SIDEBAR (same as worker) -->
    <aside class="w-20 bg-white flex flex-col items-center py-6 border-r border-slate-200 z-30 hidden sm:flex">
        <div class="mb-8">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-500/30">
                T
            </div>
        </div>
        
        <nav class="flex-1 flex flex-col gap-6 w-full px-4">
            <a href="{{ route('worker.dashboard') }}" class="w-12 h-12 rounded-xl text-slate-400 hover:bg-slate-50 hover:text-slate-600 flex items-center justify-center transition-all" title="Dashboard">
                <i class="ph-fill ph-squares-four text-2xl"></i>
            </a>
            <a href="{{ route('worker.dispatch') }}" class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 shadow-sm flex items-center justify-center transition-all" title="Perintah Kerja">
                <i class="ph-bold ph-clipboard-text text-2xl"></i>
            </a>
            <a href="#" class="w-12 h-12 rounded-xl text-slate-400 hover:bg-slate-50 hover:text-slate-600 flex items-center justify-center transition-all">
                <i class="ph-bold ph-folder text-2xl"></i>
            </a>
        </nav>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-12 h-12 rounded-xl text-red-400 hover:bg-red-50 hover:text-red-600 flex items-center justify-center transition-all">
                <i class="ph-bold ph-sign-out text-2xl"></i>
            </button>
        </form>
    </aside>

    <!-- 2. MAIN CONTENT -->
    <main class="flex-1 overflow-y-auto">
        <div class="max-w-6xl mx-auto px-6 py-8 space-y-6">

            {{-- HEADER --}}
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Perintah Kerja</h1>
                    <p class="text-sm text-slate-400 mt-1">Buat dan kelola perintah untuk petugas lapangan</p>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Mobile logout -->
                    <form action="{{ route('logout') }}" method="POST" class="sm:hidden">
                        @csrf
                        <button type="submit" class="bg-white border border-red-200 text-red-500 hover:bg-red-50 px-3 py-2; rounded-xl font-bold text-sm transition flex items-center gap-1.5 shadow-sm">
                            <i class="ph-bold ph-sign-out text-lg"></i>
                        </button>
                    </form>
                    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-5 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white text-sm font-bold rounded-xl shadow-lg shadow-orange-200 hover:shadow-orange-300 hover:-translate-y-0.5 transition-all flex items-center gap-2">
                        <i class="ph-bold ph-plus"></i> Buat Perintah
                    </button>
                </div>
            </div>

            {{-- STATUS CARDS --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-slate-100 p-4 hover:shadow-md transition-all">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center"><i class="ph-fill ph-files text-indigo-500"></i></div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total</span>
                    </div>
                    <p class="text-2xl font-extrabold text-slate-800">{{ $counts['total'] }}</p>
                </div>
                <a href="{{ route('worker.dispatch', ['status'=>'pending']) }}" class="bg-white rounded-2xl border border-slate-100 p-4 hover:shadow-md hover:border-slate-200 transition-all {{ request('status')=='pending' ? 'ring-2 ring-slate-300' : '' }}">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center"><i class="ph-fill ph-clock text-slate-500"></i></div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pending</span>
                    </div>
                    <p class="text-2xl font-extrabold text-slate-800">{{ $counts['pending'] }}</p>
                </a>
                <a href="{{ route('worker.dispatch', ['status'=>'on_progress']) }}" class="bg-white rounded-2xl border border-slate-100 p-4 hover:shadow-md hover:border-amber-200 transition-all {{ request('status')=='on_progress' ? 'ring-2 ring-amber-300' : '' }}">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center"><i class="ph-fill ph-wrench text-amber-500"></i></div>
                        <span class="text-[10px] font-bold text-amber-500 uppercase tracking-widest">Proses</span>
                    </div>
                    <p class="text-2xl font-extrabold text-slate-800">{{ $counts['on_progress'] }}</p>
                </a>
                <a href="{{ route('worker.dispatch', ['status'=>'selesai']) }}" class="bg-white rounded-2xl border border-slate-100 p-4 hover:shadow-md hover:border-emerald-200 transition-all {{ request('status')=='selesai' ? 'ring-2 ring-emerald-300' : '' }}">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center"><i class="ph-fill ph-check-circle text-emerald-500"></i></div>
                        <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">Selesai</span>
                    </div>
                    <p class="text-2xl font-extrabold text-slate-800">{{ $counts['selesai'] }}</p>
                </a>
            </div>

            {{-- FILTER --}}
            <div class="bg-white rounded-2xl border border-slate-100 p-4">
                <form method="GET" action="{{ route('worker.dispatch') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <select name="status" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 outline-none bg-white cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                        <option value="working" {{ request('status')=='on_progress' ? 'selected' : '' }}>Proses</option>
                        <option value="done" {{ request('status')=='selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    <select name="user_id" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 outline-none bg-white cursor-pointer">
                        <option value="">Semua Petugas</option>
                        @foreach($petugasList as $p)
                            <option value="{{ $p->id }}" {{ request('user_id')==$p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                        <i class="ph-bold ph-funnel-simple"></i> Filter
                    </button>
                    @if(request()->hasAny(['status','user_id']))
                        <a href="{{ route('worker.dispatch') }}" class="px-4 py-2.5 text-sm font-semibold text-slate-500 hover:text-red-500 hover:bg-red-50 rounded-xl transition flex items-center gap-1.5">
                            <i class="ph-bold ph-x"></i> Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- ALERTS --}}
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-2xl text-sm font-bold flex items-center gap-2">
                    <i class="ph-fill ph-check-circle text-lg"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-2xl text-sm font-bold flex items-center gap-2">
                    <i class="ph-fill ph-warning-circle text-lg"></i> {{ session('error') }}
                </div>
            @endif

            {{-- JOB LIST --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                {{-- Table Header (Desktop) --}}
                <div class="hidden sm:grid sm:grid-cols-12 gap-4 px-6 py-3.5 bg-slate-50 border-b border-slate-100">
                    <div class="col-span-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">#</div>
                    <div class="col-span-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Laporan</div>
                    <div class="col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ditugaskan</div>
                    <div class="col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Waktu</div>
                    <div class="col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status</div>
                    <div class="col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</div>
                </div>

                @forelse($jobs as $job)
                <div class="sm:grid sm:grid-cols-12 gap-4 px-6 py-4 border-b border-slate-50 hover:bg-slate-50/60 transition-colors items-center">
                    <div class="col-span-1 mb-2 sm:mb-0">
                        <span class="text-xs font-bold text-slate-400">#{{ $job->id }}</span>
                    </div>

                    <div class="col-span-3 mb-2 sm:mb-0">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 
                                {{ Str::contains($job->title, 'Pipa') ? 'bg-blue-50 text-blue-600' : (Str::contains($job->title, 'Meteran') ? 'bg-slate-100 text-slate-600' : 'bg-orange-50 text-orange-600') }}">
                                @if(Str::contains($job->title, 'Pipa')) <i class="ph-fill ph-drop text-lg"></i>
                                @elseif(Str::contains($job->title, 'Meteran')) <i class="ph-fill ph-gauge text-lg"></i>
                                @else <i class="ph-fill ph-warning-circle text-lg"></i>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-sm font-bold text-slate-800 truncate">{{ $job->title }}</h4>
                                <p class="text-[11px] text-slate-400 truncate mt-0.5"><i class="ph ph-map-pin text-[10px]"></i> {{ $job->address ?? '-' }}</p>
                                @if($job->kecamatan)
                                    <span class="text-[10px] font-semibold text-indigo-500">{{ $job->kecamatan->nama }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 mb-2 sm:mb-0">
                        @if($job->user)
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-[10px] font-bold shrink-0">
                                    {{ strtoupper(substr($job->user->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-700 truncate">{{ $job->user->name }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $job->user->username }}</p>
                                </div>
                            </div>
                        @else
                            <span class="text-xs text-slate-400 italic">-</span>
                        @endif
                    </div>

                    <div class="col-span-2 mb-2 sm:mb-0">
                        <div class="space-y-0.5">
                            <p class="text-[11px] text-slate-500">{{ $job->created_at->translatedFormat('d M Y, H:i') }}</p>
                            @if($job->started_at)
                                <p class="text-[10px] text-amber-500 font-medium"><i class="ph-fill ph-play text-[9px]"></i> {{ $job->started_at->format('H:i') }}</p>
                            @endif
                            @if($job->finished_at)
                                <p class="text-[10px] text-emerald-500 font-medium"><i class="ph-fill ph-check text-[9px]"></i> {{ $job->finished_at->format('H:i') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="col-span-2 mb-2 sm:mb-0">
                        @if($job->status === 'pending')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-100 text-slate-600 text-[11px] font-bold">
                                <i class="ph-fill ph-clock text-xs"></i> Pending
                            </span>
                        @elseif($job->status === 'on_progress')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-[11px] font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Proses
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-[11px] font-bold">
                                <i class="ph-fill ph-check-circle text-xs"></i> Selesai
                            </span>
                        @endif
                    </div>

                    <div class="col-span-2 flex items-center justify-end gap-2">
                        @if($job->status === 'pending')
                            <form method="POST" action="{{ route('worker.dispatch.update-status', $job->id) }}">
                                @csrf
                                <input type="hidden" name="action" value="start">
                                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all hover:-translate-y-0.5">
                                    <i class="ph-bold ph-play text-sm"></i> Mulai
                                </button>
                            </form>
                        @elseif($job->status === 'on_progress')
                            <form method="POST" action="{{ route('worker.dispatch.update-status', $job->id) }}">
                                @csrf
                                <input type="hidden" name="action" value="finish">
                                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all hover:-translate-y-0.5">
                                    <i class="ph-bold ph-check-circle text-sm"></i> Selesai
                                </button>
                            </form>
                        @else
                            <span class="text-xs font-semibold text-emerald-500 flex items-center gap-1.5">
                                <i class="ph-fill ph-seal-check text-base"></i> Selesai
                            </span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="px-6 py-16 text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ph-duotone ph-clipboard-text text-3xl text-slate-300"></i>
                    </div>
                    <h3 class="text-slate-800 font-bold text-lg">Tidak ada laporan</h3>
                    <p class="text-slate-400 text-sm mt-1">Buat perintah kerja dengan tombol di atas.</p>
                </div>
                @endforelse
            </div>

            @if($jobs->hasPages())
            <div class="flex justify-center">
                {{ $jobs->appends(request()->query())->links() }}
            </div>
            @endif

        </div>
    </main>

    {{-- ===== CREATE ORDER MODAL ===== --}}
    <div id="createModal" class="fixed inset-0 z-50 hidden" role="dialog">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="document.getElementById('createModal').classList.add('hidden')"></div>
        <div class="absolute bottom-0 sm:bottom-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 w-full sm:w-[520px] bg-white sm:rounded-3xl rounded-t-3xl p-6 shadow-2xl z-10">
            <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-6 sm:hidden"></div>
            <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                <i class="ph-duotone ph-megaphone text-orange-500"></i> Buat Perintah Kerja
            </h3>

            <form method="POST" action="{{ route('worker.dispatch.store') }}">
                @csrf
                {{-- Petugas Select --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Tugaskan ke Petugas *</label>
                    <select name="user_id" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium text-slate-700 bg-white outline-none focus:ring-2 focus:ring-orange-100 focus:border-orange-300">
                        <option value="">-- Pilih Petugas --</option>
                        @foreach($petugasList as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->username }})</option>
                        @endforeach
                    </select>
                </div>
                {{-- Title --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Jenis Gangguan *</label>
                    <select name="title" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium text-slate-700 bg-white outline-none focus:ring-2 focus:ring-orange-100 focus:border-orange-300">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="Pipa Bocor">Pipa Bocor</option>
                        <option value="Air Keruh">Air Keruh</option>
                        <option value="Air Mati">Air Mati</option>
                        <option value="Meteran Rusak">Meteran Rusak</option>
                        <option value="Sambungan Baru">Sambungan Baru</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                {{-- Problem Type --}}
                <input type="hidden" name="problem_type" value="pipe_leak">
                {{-- Address --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Alamat Lokasi *</label>
                    <input type="text" name="address" required placeholder="Jl. Raya ..." class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium placeholder-slate-400 outline-none focus:ring-2 focus:ring-orange-100 focus:border-orange-300">
                </div>
                {{-- Kecamatan --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Kecamatan</label>
                    <select name="kecamatan_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium text-slate-700 bg-white outline-none focus:ring-2 focus:ring-orange-100 focus:border-orange-300">
                        <option value="">-- Pilih Kecamatan --</option>
                        @foreach($kecamatans as $kec)
                            <option value="{{ $kec->id }}">{{ $kec->nama }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Description --}}
                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Catatan</label>
                    <textarea name="description" rows="2" placeholder="Catatan tambahan..." class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium placeholder-slate-400 outline-none focus:ring-2 focus:ring-orange-100 focus:border-orange-300 resize-none"></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 py-3 px-4 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="flex-1 py-3 px-4 bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-xl font-bold text-sm shadow-lg shadow-orange-200 hover:shadow-orange-300 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                        <i class="ph-bold ph-paper-plane-tilt"></i> Kirim Perintah
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
