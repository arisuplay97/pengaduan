@extends('layouts.nolana')
@section('content')
<div class="space-y-6">

  {{-- ═══════════════════════════════════════════════════
       HEADER
  ═══════════════════════════════════════════════════ --}}
  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Penugasan</h1>
      <p class="text-sm text-slate-400 mt-1">Kelola status laporan gangguan — <span class="font-semibold text-slate-500">Pending → Proses → Selesai</span></p>
    </div>
    <div class="flex items-center gap-3">
      <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-600 text-[11px] font-bold">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live
      </span>
      <span class="text-xs text-slate-400 font-medium hidden sm:block">{{ now()->translatedFormat('d F Y, H:i') }}</span>
      <!-- Tombol Lapor Manual Admin -->
      <button onclick="document.getElementById('createModal').classList.remove('hidden'); setTimeout(() => map.invalidateSize(), 300)" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-200 hover:shadow-indigo-300 hover:-translate-y-0.5 transition-all flex items-center gap-2">
        <i class="ph-bold ph-plus"></i> <span class="hidden sm:inline">Buat Laporan</span>
      </button>
    </div>
  </div>

  {{-- ═══════════════════════════════════════════════════
       STATUS SUMMARY CARDS
  ═══════════════════════════════════════════════════ --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    {{-- Total --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5 hover:shadow-md transition-all duration-300 cursor-default">
      <div class="flex items-center justify-between mb-3">
        <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center">
          <i class="ph-fill ph-files text-indigo-500 text-lg"></i>
        </div>
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total</span>
      </div>
      <p class="text-2xl font-extrabold text-slate-800">{{ $counts['total'] }}</p>
      <p class="text-[11px] text-slate-400 mt-0.5">Seluruh laporan</p>
    </div>
    {{-- Pending --}}
    <a href="{{ route('assignment.index', ['status' => 'pending']) }}" class="bg-white rounded-2xl border border-slate-100 p-5 hover:shadow-md hover:border-slate-200 transition-all duration-300 group {{ request('status') == 'pending' ? 'ring-2 ring-slate-300' : '' }}">
      <div class="flex items-center justify-between mb-3">
        <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center">
          <i class="ph-fill ph-clock text-slate-500 text-lg"></i>
        </div>
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pending</span>
      </div>
      <p class="text-2xl font-extrabold text-slate-800">{{ $counts['pending'] }}</p>
      <p class="text-[11px] text-slate-400 mt-0.5">Menunggu dikerjakan</p>
    </a>
    {{-- Working --}}
    <a href="{{ route('assignment.index', ['status' => 'on_progress']) }}" class="bg-white rounded-2xl border border-slate-100 p-5 hover:shadow-md hover:border-amber-200 transition-all duration-300 group {{ request('status') == 'on_progress' ? 'ring-2 ring-amber-300' : '' }}">
      <div class="flex items-center justify-between mb-3">
        <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
          <i class="ph-fill ph-wrench text-amber-500 text-lg"></i>
        </div>
        <span class="text-[10px] font-bold text-amber-500 uppercase tracking-widest">Proses</span>
      </div>
      <p class="text-2xl font-extrabold text-slate-800">{{ $counts['on_progress'] }}</p>
      <p class="text-[11px] text-slate-400 mt-0.5">Sedang dikerjakan</p>
    </a>
    {{-- Done --}}
    <a href="{{ route('assignment.index', ['status' => 'selesai']) }}" class="bg-white rounded-2xl border border-slate-100 p-5 hover:shadow-md hover:border-emerald-200 transition-all duration-300 group {{ request('status') == 'selesai' ? 'ring-2 ring-emerald-300' : '' }}">
      <div class="flex items-center justify-between mb-3">
        <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
          <i class="ph-fill ph-check-circle text-emerald-500 text-lg"></i>
        </div>
        <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">Selesai</span>
      </div>
      <p class="text-2xl font-extrabold text-slate-800">{{ $counts['selesai'] }}</p>
      <p class="text-[11px] text-slate-400 mt-0.5">Telah diselesaikan</p>
    </a>
  </div>

  {{-- ═══════════════════════════════════════════════════
       FILTER BAR
  ═══════════════════════════════════════════════════ --}}
  <div class="bg-white rounded-2xl border border-slate-100 p-4">
    <form method="GET" action="{{ route('assignment.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
      {{-- Search --}}
      <div class="relative flex-1">
        <i class="ph ph-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, alamat, atau ID..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium placeholder-slate-400 focus:ring-2 focus:ring-indigo-100 focus:border-indigo-300 outline-none transition">
      </div>
      {{-- Status Select --}}
      <select name="status" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 focus:ring-2 focus:ring-indigo-100 outline-none appearance-none bg-white cursor-pointer">
        <option value="">Semua Status</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="working" {{ request('status') == 'on_progress' ? 'selected' : '' }}>Proses</option>
        <option value="done" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
      </select>
      {{-- Kecamatan Select --}}
      <select name="kecamatan_id" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 focus:ring-2 focus:ring-indigo-100 outline-none appearance-none bg-white cursor-pointer">
        <option value="">Semua Kecamatan</option>
        @foreach($kecamatans as $kec)
          <option value="{{ $kec->id }}" {{ request('kecamatan_id') == $kec->id ? 'selected' : '' }}>{{ $kec->nama }}</option>
        @endforeach
      </select>
      <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-sm shadow-indigo-200 flex items-center gap-2">
        <i class="ph-bold ph-funnel-simple"></i> Filter
      </button>
      @if(request()->hasAny(['search', 'status', 'kecamatan_id']))
        <a href="{{ route('assignment.index') }}" class="px-4 py-2.5 text-sm font-semibold text-slate-500 hover:text-red-500 hover:bg-red-50 rounded-xl transition flex items-center gap-1.5">
          <i class="ph-bold ph-x"></i> Reset
        </a>
      @endif
    </form>
  </div>

  {{-- ═══════════════════════════════════════════════════
       SUCCESS / ERROR ALERT
  ═══════════════════════════════════════════════════ --}}
  @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-2xl text-sm font-bold flex items-center gap-2 animate-pulse">
      <i class="ph-fill ph-check-circle text-lg"></i> {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-2xl text-sm font-bold flex items-center gap-2">
      <i class="ph-fill ph-warning-circle text-lg"></i> {{ session('error') }}
    </div>
  @endif

  {{-- ═══════════════════════════════════════════════════
       DATA TABLE
  ═══════════════════════════════════════════════════ --}}
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    {{-- Table Header --}}
    <div class="hidden sm:grid sm:grid-cols-12 gap-4 px-6 py-3.5 bg-slate-50 border-b border-slate-100">
      <div class="col-span-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">#</div>
      <div class="col-span-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Laporan</div>
      <div class="col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Petugas</div>
      <div class="col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Waktu</div>
      <div class="col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status</div>
      <div class="col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</div>
    </div>

    {{-- Table Body --}}
    @forelse($jobs as $job)
    <div class="sm:grid sm:grid-cols-12 gap-4 px-6 py-4 border-b border-slate-50 hover:bg-slate-50/60 transition-colors items-center group">
      {{-- ID --}}
      <div class="col-span-1 mb-2 sm:mb-0">
        <span class="text-xs font-bold text-slate-400">#{{ $job->id }}</span>
      </div>

      {{-- Laporan Info --}}
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
            <p class="text-[11px] text-slate-400 truncate mt-0.5">
              <i class="ph ph-map-pin text-[10px]"></i> {{ $job->address ?? 'Belum ada alamat' }}
            </p>
            @if($job->kecamatan)
              <span class="text-[10px] font-semibold text-indigo-500">{{ $job->kecamatan->nama }}</span>
            @endif
          </div>
        </div>
      </div>

      {{-- Petugas --}}
      <div class="col-span-2 mb-2 sm:mb-0">
        @if($job->user)
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-[10px] font-bold shrink-0">
              {{ strtoupper(substr($job->user->name, 0, 2)) }}
            </div>
            <div class="min-w-0">
              <p class="text-xs font-bold text-slate-700 truncate">{{ $job->user->name }}</p>
              <p class="text-[10px] text-slate-400">Petugas</p>
            </div>
          </div>
        @else
          <span class="text-xs text-slate-400 italic">Laporan Publik</span>
        @endif
      </div>

      {{-- Waktu --}}
      <div class="col-span-2 mb-2 sm:mb-0">
        <div class="space-y-0.5">
          <p class="text-[11px] text-slate-500">
            <i class="ph ph-calendar-blank text-[10px] mr-0.5"></i>
            {{ $job->created_at->translatedFormat('d M Y, H:i') }}
          </p>
          @if($job->started_at)
            <p class="text-[10px] text-amber-500 font-medium">
              <i class="ph-fill ph-play text-[9px]"></i> Mulai: {{ $job->started_at->format('H:i') }}
            </p>
          @endif
          @if($job->finished_at)
            <p class="text-[10px] text-emerald-500 font-medium">
              <i class="ph-fill ph-check text-[9px]"></i> Selesai: {{ $job->finished_at->format('H:i') }}
            </p>
          @endif
        </div>
      </div>

      {{-- Status Badge --}}
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

      {{-- Action Buttons --}}
      <div class="col-span-2 flex items-center justify-end gap-1.5">
        @if(auth()->user()->role === 'superadmin')
          <form method="POST" action="{{ route('assignment.destroy', $job->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus laporan ini beserta fotonya secara permanen?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-white border border-rose-200 text-rose-500 hover:bg-rose-50 hover:text-rose-600 rounded-xl shadow-sm transition-all" title="Hapus Laporan">
              <i class="ph-bold ph-trash text-sm"></i>
            </button>
          </form>
        @endif

        <a href="{{ route('assignment.export.pdf', $job->id) }}" class="inline-flex items-center justify-center w-8 h-8 bg-white border border-red-200 text-red-500 hover:bg-red-50 hover:text-red-600 rounded-xl shadow-sm transition-all" title="Download PDF">
          <i class="ph-bold ph-file-pdf text-sm"></i>
        </a>
        <a href="{{ route('assignment.export.excel', $job->id) }}" class="inline-flex items-center justify-center w-8 h-8 bg-white border border-emerald-200 text-emerald-500 hover:bg-emerald-50 hover:text-emerald-600 rounded-xl shadow-sm transition-all" title="Download Excel">
          <i class="ph-bold ph-file-xls text-sm"></i>
        </a>

        @if($job->status === 'pending')
          <form method="POST" action="{{ route('assignment.update-status', $job->id) }}">
            @csrf
            <input type="hidden" name="action" value="start">
            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-bold rounded-xl shadow-sm shadow-indigo-200 hover:shadow-indigo-300 transition-all hover:-translate-y-0.5">
              <i class="ph-bold ph-play text-xs"></i> Mulai
            </button>
          </form>
        @elseif($job->status === 'on_progress')
          <form method="POST" action="{{ route('assignment.update-status', $job->id) }}">
            @csrf
            <input type="hidden" name="action" value="finish">
            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold rounded-xl shadow-sm shadow-emerald-200 hover:shadow-emerald-300 transition-all hover:-translate-y-0.5" onclick="return confirm('Tandai laporan ini telah selesai?');">
              <i class="ph-bold ph-check-circle text-xs"></i> Selesai
            </button>
          </form>
        @else
          <span class="text-[10px] font-semibold text-emerald-500 flex items-center gap-1 px-2 py-1.5">
            <i class="ph-fill ph-seal-check text-sm"></i> Selesai
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
      <p class="text-slate-400 text-sm mt-1">Belum ada laporan gangguan yang sesuai filter.</p>
      @if(request()->hasAny(['search', 'status', 'kecamatan_id']))
        <a href="{{ route('assignment.index') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 text-sm font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition">
          <i class="ph-bold ph-arrow-counter-clockwise"></i> Reset Filter
        </a>
      @endif
    </div>
    @endforelse
  </div>

  {{-- ═══════════════════════════════════════════════════
       PAGINATION
  ═══════════════════════════════════════════════════ --}}
  @if($jobs->hasPages())
  <div class="flex justify-center">
    {{ $jobs->appends(request()->query())->links() }}
  </div>
  @endif

</div>

{{-- ===== CREATE ORDER MODAL (Manual Input by Admin) ===== --}}
<div id="createModal" class="fixed inset-0 z-50 hidden" role="dialog">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="document.getElementById('createModal').classList.add('hidden')"></div>
    <div class="absolute bottom-0 sm:bottom-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 w-full sm:w-[600px] bg-white sm:rounded-3xl rounded-t-3xl shadow-2xl z-10 max-h-[90vh] overflow-y-auto no-scrollbar">
        <div class="p-6">
            <div class="flex justify-between items-center bg-slate-50 -mx-6 -mt-6 px-6 py-4 mb-5 border-b border-slate-100 rounded-t-3xl">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="ph-duotone ph-megaphone text-indigo-500 text-xl"></i> Lapor Kerusakan Manual
                </h3>
                <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="w-8 h-8 flex items-center justify-center rounded-xl bg-white text-slate-400 hover:text-rose-500 shadow-sm transition">
                    <i class="ph-bold ph-x text-sm"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('worker.dispatch.store') }}">
                @csrf
                <input type="hidden" name="latitude" id="rLat">
                <input type="hidden" name="longitude" id="rLng">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    {{-- Nama Pelapor --}}
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nama Pelapor *</label>
                        <input type="text" name="reporter_name" required placeholder="H. Muhammad..." class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium placeholder-slate-400 outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-300">
                    </div>
                    {{-- No. WhatsApp --}}
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">No. WhatsApp *</label>
                        <input type="tel" name="reporter_phone" required placeholder="08..." pattern="^(08|628|\+628)[0-9]{7,11}$" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium placeholder-slate-400 outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-300">
                    </div>
                </div>

                {{-- ID Pelanggan --}}
                <div class="mb-4">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">No. Pelanggan *</label>
                    <input type="text" name="customer_id" required placeholder="010xxxxxxx" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium placeholder-slate-400 outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-300 font-mono">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    {{-- Type --}}
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Jenis Masalah *</label>
                        <div class="relative">
                            <select name="title" required class="w-full appearance-none px-4 py-3 rounded-xl border border-slate-200 text-sm font-bold text-slate-700 bg-slate-50 outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-300">
                                <option value="">Pilih Kategori...</option>
                                <option value="Pipa Bocor">🔴 Pipa Bocor</option>
                                <option value="Meteran Mati">⚫ Meteran Mati</option>
                                <option value="Air Keruh">🟤 Air Keruh</option>
                                <option value="Sambungan Lepas">🟠 Sambungan Lepas</option>
                                <option value="Meteran Tersumbat">🔵 Meteran Tersumbat</option>
                                <option value="Lainnya">⚪ Lainnya</option>
                            </select>
                            <i class="ph-bold ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>
                    {{-- Kecamatan --}}
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Kecamatan *</label>
                        <div class="relative">
                            <select name="kecamatan_id" required class="w-full appearance-none px-4 py-3 rounded-xl border border-slate-200 text-sm font-bold text-slate-700 bg-slate-50 outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-300">
                                <option value="">Pilih Kecamatan...</option>
                                @foreach($kecamatans as $kec)
                                    <option value="{{ $kec->id }}">{{ $kec->nama }}</option>
                                @endforeach
                            </select>
                            <i class="ph-bold ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>
                </div>

                {{-- Minimap --}}
                <div class="mb-4">
                    <div class="flex justify-between items-end mb-1.5">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pilih Lokasi di Peta *</label>
                        <button type="button" id="btn-locate" class="text-[10px] font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded-lg transition-colors flex items-center gap-1">
                            <i class="ph-bold ph-crosshair"></i> Deteksi Lokasi
                        </button>
                    </div>
                    <div class="relative rounded-xl border-2 border-slate-200 overflow-hidden">
                        <div id="map" class="h-48 w-full z-0"></div>
                    </div>
                </div>

                {{-- Alamat --}}
                <div class="mb-4">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Detail Alamat *</label>
                    <textarea id="rAddress" name="address" required rows="2" placeholder="Detail alamat lokasi..." class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium placeholder-slate-400 outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-300 resize-none"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    {{-- Tugaskan Ke --}}
                    <div>
                        <label class="block text-[10px] font-bold text-amber-500 uppercase tracking-widest mb-1.5">Tugaskan Ke *</label>
                        <div class="relative">
                            <select name="user_id" required class="w-full appearance-none px-4 py-3 rounded-xl border border-amber-200 text-sm font-bold text-slate-700 bg-amber-50 outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400">
                                <option value="BROADCAST">📣 Broadcast (Semua Petugas)</option>
                                <optgroup label="Pilih Spesifik">
                                    @php
                                        // Fetch petugas list for this form
                                        $petugasListAdmin = \App\Models\User::where('role', 'petugas')->orderBy('name')->get();
                                    @endphp
                                    @foreach($petugasListAdmin as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->username }})</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            <i class="ph-bold ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-amber-500 pointer-events-none"></i>
                        </div>
                    </div>
                    {{-- Deskripsi (Optional) --}}
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Catatan Untuk Petugas</label>
                        <textarea name="description" rows="1" placeholder="Cepat ke lokasi..." class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-medium placeholder-slate-400 outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-300 resize-none h-11"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-[1] py-3.5 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="flex-[2] py-3.5 bg-indigo-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                        <i class="ph-bold ph-paper-plane-tilt"></i> Kirim Laporan & Notif Tele
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Init Map
        var map = L.map('map').setView([-8.72, 116.28], 11); // Center to Lombok Tengah
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var marker;

                // Try getting user's IP-based/GPS location first
        map.locate({setView: true, maxZoom: 16});

        // Set up the locate button
        document.getElementById('btn-locate').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Add loading state
            const icon = this.querySelector('i');
            icon.classList.remove('ph-crosshair');
            icon.classList.add('ph-spinner', 'animate-spin');
            
            map.locate({setView: true, maxZoom: 16, enableHighAccuracy: true});
        });

        // Handle auto-detect location success
        map.on('locationfound', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;

            if (marker) {
                map.removeLayer(marker);
            }
            marker = L.marker([lat, lng]).addTo(map);

            document.getElementById('rLat').value = lat;
            document.getElementById('rLng').value = lng;
            document.getElementById('rAddress').placeholder = "Memuat alamat...";

            // Reset icon
            const locateIcon = document.querySelector('#btn-locate i');
            if(locateIcon) {
                locateIcon.classList.remove('ph-spinner', 'animate-spin');
                locateIcon.classList.add('ph-crosshair');
            }

            // Reverse Geocode
            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    if(data && data.display_name) {
                        let address = data.display_name;
                        let parts = address.split(',');
                        if(parts.length > 5) {
                            address = parts.slice(0, 5).join(',');
                        }
                        document.getElementById('rAddress').value = address.trim();
                    } else {
                        document.getElementById('rAddress').value = "GPS: " + lat.toFixed(5) + ", " + lng.toFixed(5);
                    }
                })
                .catch(() => {
                    document.getElementById('rAddress').value = "GPS: " + lat.toFixed(5) + ", " + lng.toFixed(5);
                });
        });

        // Handle location error
        map.on('locationerror', function(e) {
            const locateIcon = document.querySelector('#btn-locate i');
            if(locateIcon) {
                locateIcon.classList.remove('ph-spinner', 'animate-spin');
                locateIcon.classList.add('ph-crosshair');
            }
            alert("Akses lokasi ditolak atau tidak tersedia. Silakan ketuk peta secara manual.");
        });

        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;

            if (marker) {
                map.removeLayer(marker);
            }

            marker = L.marker([lat, lng]).addTo(map);

            document.getElementById('rLat').value = lat;
            document.getElementById('rLng').value = lng;
            document.getElementById('rAddress').placeholder = "Memuat alamat otomatis...";

            // Reverse Geocoding (Nominatim)
            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    if(data && data.display_name) {
                        let address = data.display_name;
                        // simplify address if too long
                        let parts = address.split(',');
                        if(parts.length > 5) {
                            address = parts.slice(0, 5).join(',');
                        }
                        document.getElementById('rAddress').value = address.trim();
                    } else {
                        document.getElementById('rAddress').value = "GPS: " + lat.toFixed(5) + ", " + lng.toFixed(5);
                    }
                })
                .catch(() => {
                    document.getElementById('rAddress').value = "GPS: " + lat.toFixed(5) + ", " + lng.toFixed(5);
                });
        });

        // Ensure map renders correctly when modal opens
        window.map = map;
    });
</script>

@endsection
