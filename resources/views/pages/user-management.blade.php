@extends('layouts.nolana')
@section('content')
<div class="space-y-6">

  {{-- ═══════════════════════════════════════════════════
       HEADER
  ═══════════════════════════════════════════════════ --}}
  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
    <div>
      <div class="flex items-center gap-3 mb-1">
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-violet-500/20">
          <i class="ph-fill ph-users-three text-white text-lg"></i>
        </div>
        <div>
          <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kelola Pengguna</h1>
          <p class="text-sm text-slate-400 mt-0.5">Manajemen akun — <span class="font-semibold text-slate-500">Tambah, Edit, dan Reset Password</span></p>
        </div>
      </div>
    </div>
    <button onclick="openModal('createModal')" class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white text-sm font-bold rounded-2xl shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition-all hover:-translate-y-0.5 active:translate-y-0">
      <i class="ph-bold ph-user-plus text-base"></i> Tambah Pengguna
    </button>
  </div>

  {{-- ═══════════════════════════════════════════════════
       STATUS SUMMARY CARDS
  ═══════════════════════════════════════════════════ --}}
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 gap-y-6">
    {{-- Total --}}
    <div class="relative bg-white rounded-2xl border border-slate-100 p-5 hover:shadow-lg hover:shadow-slate-200/50 transition-all duration-300 cursor-default overflow-hidden">
      <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-indigo-50 to-transparent rounded-bl-full opacity-80"></div>
      <div class="relative">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-50 flex items-center justify-center">
            <i class="ph-fill ph-users text-indigo-500 text-lg"></i>
          </div>
        </div>
        <p class="text-3xl font-extrabold text-slate-800">{{ $counts['total'] }}</p>
        <p class="text-[11px] text-slate-400 mt-1 font-semibold uppercase tracking-wider">Total Pengguna</p>
      </div>
    </div>
    {{-- Superadmin --}}
    <a href="{{ route('users.index', ['role' => 'superadmin']) }}" class="relative bg-white rounded-2xl border border-slate-100 p-5 hover:shadow-lg hover:shadow-violet-100/50 hover:border-violet-200 transition-all duration-300 overflow-hidden {{ request('role') == 'superadmin' ? 'ring-2 ring-violet-400 shadow-lg shadow-violet-100/50' : '' }}">
      <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-violet-50 to-transparent rounded-bl-full opacity-80"></div>
      <div class="relative">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-100 to-violet-50 flex items-center justify-center">
            <i class="ph-fill ph-crown text-violet-500 text-lg"></i>
          </div>
        </div>
        <p class="text-3xl font-extrabold text-slate-800">{{ $counts['superadmin'] }}</p>
        <p class="text-[11px] text-violet-500 mt-1 font-bold uppercase tracking-wider">Superadmin</p>
      </div>
    </a>
    {{-- Admin --}}
    <a href="{{ route('users.index', ['role' => 'admin']) }}" class="relative bg-white rounded-2xl border border-slate-100 p-5 hover:shadow-lg hover:shadow-blue-100/50 hover:border-blue-200 transition-all duration-300 overflow-hidden {{ request('role') == 'admin' ? 'ring-2 ring-blue-400 shadow-lg shadow-blue-100/50' : '' }}">
      <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-blue-50 to-transparent rounded-bl-full opacity-80"></div>
      <div class="relative">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
            <i class="ph-fill ph-shield-star text-blue-500 text-lg"></i>
          </div>
        </div>
        <p class="text-3xl font-extrabold text-slate-800">{{ $counts['admin'] }}</p>
        <p class="text-[11px] text-blue-500 mt-1 font-bold uppercase tracking-wider">Admin</p>
      </div>
    </a>
    {{-- Direksi --}}
    <a href="{{ route('users.index', ['role' => 'direksi']) }}" class="relative bg-white rounded-2xl border border-slate-100 p-5 hover:shadow-lg hover:shadow-fuchsia-100/50 hover:border-fuchsia-200 transition-all duration-300 overflow-hidden {{ request('role') == 'direksi' ? 'ring-2 ring-fuchsia-400 shadow-lg shadow-fuchsia-100/50' : '' }}">
      <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-fuchsia-50 to-transparent rounded-bl-full opacity-80"></div>
      <div class="relative">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-fuchsia-100 to-fuchsia-50 flex items-center justify-center">
            <i class="ph-fill ph-briefcase text-fuchsia-500 text-lg"></i>
          </div>
        </div>
        <p class="text-3xl font-extrabold text-slate-800">{{ $counts['direksi'] }}</p>
        <p class="text-[11px] text-fuchsia-500 mt-1 font-bold uppercase tracking-wider">Direktur</p>
      </div>
    </a>
    {{-- Petugas --}}
    <a href="{{ route('users.index', ['role' => 'petugas']) }}" class="relative bg-white rounded-2xl border border-slate-100 p-5 hover:shadow-lg hover:shadow-emerald-100/50 hover:border-emerald-200 transition-all duration-300 overflow-hidden {{ request('role') == 'petugas' ? 'ring-2 ring-emerald-400 shadow-lg shadow-emerald-100/50' : '' }}">
      <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-emerald-50 to-transparent rounded-bl-full opacity-80"></div>
      <div class="relative">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-50 flex items-center justify-center">
            <i class="ph-fill ph-hard-hat text-emerald-500 text-lg"></i>
          </div>
        </div>
        <p class="text-3xl font-extrabold text-slate-800">{{ $counts['petugas'] }}</p>
        <p class="text-[11px] text-emerald-500 mt-1 font-bold uppercase tracking-wider">Petugas Lapang</p>
      </div>
    </a>
  </div>

  {{-- ═══════════════════════════════════════════════════
       FILTER BAR
  ═══════════════════════════════════════════════════ --}}
  <div class="bg-white rounded-2xl border border-slate-100 p-4">
    <form method="GET" action="{{ route('users.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
      <div class="relative flex-1">
        <i class="ph ph-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, username, atau email..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium placeholder-slate-400 focus:ring-2 focus:ring-violet-100 focus:border-violet-300 outline-none transition">
      </div>
      <select name="role" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 focus:ring-2 focus:ring-violet-100 outline-none appearance-none bg-white cursor-pointer">
        <option value="">Semua Role</option>
        <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>👑 Superadmin</option>
        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>🛡️ Admin</option>
        <option value="direksi" {{ request('role') == 'direksi' ? 'selected' : '' }}>💼 Direksi</option>
        <option value="petugas" {{ request('role') == 'petugas' ? 'selected' : '' }}>🔧 Petugas</option>
      </select>
      <button type="submit" class="px-5 py-2.5 bg-violet-600 text-white text-sm font-bold rounded-xl hover:bg-violet-700 transition-all shadow-sm shadow-violet-200 flex items-center gap-2">
        <i class="ph-bold ph-funnel-simple"></i> Filter
      </button>
      @if(request()->hasAny(['search', 'role']))
        <a href="{{ route('users.index') }}" class="px-4 py-2.5 text-sm font-semibold text-slate-500 hover:text-red-500 hover:bg-red-50 rounded-xl transition flex items-center gap-1.5">
          <i class="ph-bold ph-x"></i> Reset
        </a>
      @endif
    </form>
  </div>

  {{-- ═══════════════════════════════════════════════════
       ALERTS
  ═══════════════════════════════════════════════════ --}}
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
  @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-2xl text-sm font-bold">
      <div class="flex items-center gap-2 mb-1"><i class="ph-fill ph-warning-circle text-lg"></i> Validasi gagal:</div>
      <ul class="list-disc list-inside text-xs font-medium mt-1 space-y-0.5">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- ═══════════════════════════════════════════════════
       USER TABLE
  ═══════════════════════════════════════════════════ --}}
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    {{-- Table Header --}}
    <div class="hidden sm:grid sm:grid-cols-12 gap-4 px-6 py-3.5 bg-gradient-to-r from-slate-50 to-slate-50/80 border-b border-slate-100">
      <div class="col-span-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pengguna</div>
      <div class="col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Username</div>
      <div class="col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Role</div>
      <div class="col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Terdaftar</div>
      <div class="col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</div>
    </div>

    {{-- Table Body --}}
    @forelse($users as $user)
    <div class="sm:grid sm:grid-cols-12 gap-4 px-6 py-4 border-b border-slate-50 hover:bg-gradient-to-r hover:from-slate-50/60 hover:to-transparent transition-all items-center group">
      {{-- User Info --}}
      <div class="col-span-4 mb-3 sm:mb-0">
        <div class="flex items-center gap-3">
          <div class="relative">
            @if($user->photo)
              <img src="{{ asset('storage/' . $user->photo) }}" class="w-11 h-11 rounded-2xl object-cover ring-2 ring-white shadow-sm">
            @else
              <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white text-sm font-bold shadow-sm
                @if($user->role === 'superadmin') bg-gradient-to-br from-violet-500 to-purple-600
                @elseif($user->role === 'direksi') bg-gradient-to-br from-fuchsia-500 to-pink-600
                @elseif($user->role === 'admin') bg-gradient-to-br from-blue-500 to-indigo-600
                @else bg-gradient-to-br from-emerald-500 to-teal-600
                @endif
              ">
                {{ strtoupper(substr($user->name, 0, 2)) }}
              </div>
            @endif
            {{-- Online indicator for current user --}}
            @if($user->id === auth()->id())
              <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-400 rounded-full border-2 border-white"></div>
            @endif
          </div>
          <div class="min-w-0">
            <div class="flex items-center gap-1.5">
              <h4 class="text-sm font-bold text-slate-800 truncate">{{ $user->name }}</h4>
              @if($user->id === auth()->id())
                <span class="text-[9px] font-bold text-violet-600 bg-violet-50 px-1.5 py-0.5 rounded-md">ANDA</span>
              @endif
            </div>
            @if($user->email)
              <p class="text-[11px] text-slate-400 truncate mt-0.5">
                <i class="ph ph-envelope-simple text-[10px]"></i> {{ $user->email }}
              </p>
            @endif
            @if($user->phone)
              <p class="text-[10px] text-slate-400 truncate">
                <i class="ph ph-phone text-[9px]"></i> {{ $user->phone }}
              </p>
            @endif
            @if($user->telegram_chat_id)
              <p class="text-[10px] text-blue-400 truncate">
                <i class="ph-fill ph-telegram-logo text-[9px]"></i> {{ $user->telegram_chat_id }}
              </p>
            @endif
          </div>
        </div>
      </div>

      {{-- Username --}}
      <div class="col-span-2 mb-2 sm:mb-0">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-50 border border-slate-100 rounded-lg">
          <i class="ph ph-at text-[11px] text-slate-400"></i>
          <span class="text-xs font-bold text-slate-600 font-mono">{{ $user->username }}</span>
        </div>
      </div>

      {{-- Role Badge --}}
      <div class="col-span-2 mb-2 sm:mb-0">
        @if($user->role === 'superadmin')
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-gradient-to-r from-violet-50 to-purple-50 border border-violet-200 text-violet-700 text-[11px] font-bold">
            <i class="ph-fill ph-crown text-xs"></i> Superadmin
          </span>
        @elseif($user->role === 'admin')
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 text-blue-700 text-[11px] font-bold">
            <i class="ph-fill ph-shield-star text-xs"></i> Admin
          </span>
        @elseif($user->role === 'direksi')
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-gradient-to-r from-fuchsia-50 to-pink-50 border border-fuchsia-200 text-fuchsia-700 text-[11px] font-bold">
            <i class="ph-fill ph-briefcase text-xs"></i> Direktur
          </span>
        @else
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 text-emerald-700 text-[11px] font-bold">
            <i class="ph-fill ph-hard-hat text-xs"></i> Petugas
          </span>
        @endif
        
        @if($user->role === 'petugas' && $user->kecamatans->count() > 0)
          <div class="mt-2 flex flex-wrap gap-1">
            @foreach($user->kecamatans->take(2) as $k)
              <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                <i class="ph-fill ph-map-pin mr-0.5"></i> {{ $k->nama }}
              </span>
            @endforeach
            @if($user->kecamatans->count() > 2)
              <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                +{{ $user->kecamatans->count() - 2 }}
              </span>
            @endif
          </div>
        @endif
      </div>

      {{-- Registered --}}
      <div class="col-span-2 mb-2 sm:mb-0">
        <p class="text-[11px] text-slate-500 font-medium">
          <i class="ph ph-calendar-blank text-[10px] mr-0.5"></i>
          {{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}
        </p>
        <p class="text-[10px] text-slate-400 mt-0.5">
          {{ $user->created_at ? $user->created_at->diffForHumans() : '' }}
        </p>
      </div>

      {{-- Actions --}}
      <div class="col-span-2 flex items-center justify-end gap-1.5">
        <button onclick="openEditModal(this)" data-user="{{ base64_encode(json_encode($user)) }}" data-kecamatan="{{ $user->kecamatans ? base64_encode(json_encode($user->kecamatans->pluck('id'))) : base64_encode('[]') }}" class="inline-flex items-center gap-1 px-3 py-2 bg-slate-50 hover:bg-indigo-50 text-slate-500 hover:text-indigo-600 rounded-xl text-[11px] font-bold transition-all hover:-translate-y-0.5" title="Edit">
          <i class="ph-bold ph-pencil-simple text-sm"></i>
        </button>
        <button onclick="openResetModal({{ $user->id }}, '{{ addslashes($user->name) }}')" class="inline-flex items-center gap-1 px-3 py-2 bg-slate-50 hover:bg-amber-50 text-slate-500 hover:text-amber-600 rounded-xl text-[11px] font-bold transition-all hover:-translate-y-0.5" title="Reset Password">
          <i class="ph-bold ph-key text-sm"></i>
        </button>
        @if($user->id !== auth()->id())
        <form method="POST" action="{{ route('users.destroy', $user->id) }}" onsubmit="return confirm('Yakin hapus pengguna {{ addslashes($user->name) }}?')" class="inline">
          @csrf @method('DELETE')
          <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-slate-50 hover:bg-red-50 text-slate-500 hover:text-red-500 rounded-xl text-[11px] font-bold transition-all hover:-translate-y-0.5" title="Hapus">
            <i class="ph-bold ph-trash text-sm"></i>
          </button>
        </form>
        @endif
      </div>
    </div>
    @empty
    <div class="px-6 py-16 text-center">
      <div class="w-20 h-20 bg-gradient-to-br from-slate-50 to-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="ph-duotone ph-users-three text-4xl text-slate-300"></i>
      </div>
      <h3 class="text-slate-800 font-bold text-lg">Tidak ada pengguna</h3>
      <p class="text-slate-400 text-sm mt-1">Belum ada pengguna yang sesuai filter.</p>
      @if(request()->hasAny(['search', 'role']))
        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 text-sm font-bold text-violet-600 bg-violet-50 hover:bg-violet-100 rounded-xl transition">
          <i class="ph-bold ph-arrow-counter-clockwise"></i> Reset Filter
        </a>
      @endif
    </div>
    @endforelse
  </div>

  {{-- Pagination --}}
  @if($users->hasPages())
  <div class="flex justify-center">
    {{ $users->appends(request()->query())->links() }}
  </div>
  @endif

</div>

{{-- ══════════════════════════════════════════════════════════════
     CREATE USER MODAL
══════════════════════════════════════════════════════════════ --}}
<div id="createModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
  <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal('createModal')"></div>
  <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-3xl p-7 shadow-2xl" id="createModalPanel">

    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-violet-500/20">
          <i class="ph-fill ph-user-plus text-white text-lg"></i>
        </div>
        <h3 class="text-xl font-extrabold text-slate-900">Tambah Pengguna</h3>
      </div>
      <button onclick="closeModal('createModal')" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-red-50 text-slate-400 hover:text-red-500 flex items-center justify-center transition">
        <i class="ph-bold ph-x text-sm"></i>
      </button>
    </div>

    <form method="POST" action="{{ route('users.store') }}">
      @csrf
      <div class="space-y-4">
        {{-- Name --}}
        <div>
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
          <input type="text" name="name" required placeholder="Contoh: Ahmad Sudirman" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-violet-200 focus:border-violet-400 outline-none transition" value="{{ old('name') }}">
        </div>
        {{-- Username + Role row --}}
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Username <span class="text-red-400">*</span></label>
            <div class="relative">
              <i class="ph ph-at absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
              <input type="text" name="username" required placeholder="username" class="w-full pl-8 pr-4 bg-slate-50 border border-slate-200 rounded-xl py-3 text-sm font-mono font-medium text-slate-700 focus:ring-2 focus:ring-violet-200 focus:border-violet-400 outline-none transition" value="{{ old('username') }}">
            </div>
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Role <span class="text-red-400">*</span></label>
            <div class="relative">
              <select name="role" id="createRole" required class="w-full appearance-none bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-violet-200 focus:border-violet-400 outline-none transition cursor-pointer">
                <option value="" disabled selected>Pilih Role</option>
                <option value="petugas" {{ old('role') == 'petugas' ? 'selected' : '' }}>🔧 Petugas</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>🛡️ Admin</option>
                <option value="direksi" {{ old('role') == 'direksi' ? 'selected' : '' }}>💼 Direksi</option>
                <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>👑 Superadmin</option>
              </select>
              <i class="ph-bold ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            </div>
          </div>
        </div>
        {{-- Email + Phone row --}}
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Email <span class="text-slate-300">(opsional)</span></label>
            <input type="email" name="email" placeholder="email@pdam.co.id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-violet-200 focus:border-violet-400 outline-none transition" value="{{ old('email') }}">
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Telepon <span class="text-slate-300">(opsional)</span></label>
            <input type="text" name="phone" placeholder="0812xxxxxxxx" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-violet-200 focus:border-violet-400 outline-none transition" value="{{ old('phone') }}">
          </div>
        </div>
        {{-- Telegram Chat ID --}}
        <div>
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Telegram Chat ID <span class="text-slate-300">(opsional)</span></label>
          <div class="relative">
            <i class="ph-fill ph-telegram-logo absolute left-3 top-1/2 -translate-y-1/2 text-blue-400 text-sm"></i>
            <input type="text" name="telegram_chat_id" placeholder="Contoh: 1810412191" class="w-full pl-8 pr-4 bg-slate-50 border border-slate-200 rounded-xl py-3 text-sm font-mono font-medium text-slate-700 focus:ring-2 focus:ring-violet-200 focus:border-violet-400 outline-none transition" value="{{ old('telegram_chat_id') }}">
          </div>
          <p class="text-[10px] text-slate-400 mt-1">Kirim <code>/id</code> ke bot <b>@TiaraPengaduanBot</b> untuk mendapatkan Chat ID</p>
        </div>
        {{-- Kecamatan Selector (Only for Petugas) --}}
        <div id="createKecamatanWrapper" style="display: none;">
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Wilayah Tugas (Kecamatan) <span class="text-slate-300">(opsional)</span></label>
          <div class="relative">
            <select name="kecamatan_ids[]" multiple class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-violet-200 outline-none transition" size="4">
              @foreach($kecamatans as $kecamatan)
                <option value="{{ $kecamatan->id }}">{{ $kecamatan->nama }}</option>
              @endforeach
            </select>
            <p class="text-[10px] text-slate-400 mt-1">Tahan tombol Ctrl (Windows) / Cmd (Mac) untuk memilih lebih dari satu.</p>
          </div>
        </div>
        {{-- Password --}}
        <div>
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Password <span class="text-red-400">*</span></label>
          <div class="relative">
            <i class="ph ph-lock-simple absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full pl-8 pr-4 bg-slate-50 border border-slate-200 rounded-xl py-3 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-violet-200 focus:border-violet-400 outline-none transition">
          </div>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3 mt-6">
        <button type="button" onclick="closeModal('createModal')" class="px-5 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition">Batal</button>
        <button type="submit" class="px-5 py-3 bg-gradient-to-r from-violet-600 to-indigo-600 text-white rounded-xl font-bold text-sm hover:from-violet-700 hover:to-indigo-700 transition shadow-lg shadow-violet-500/25">
          <i class="ph-bold ph-user-plus mr-1"></i> Simpan
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     EDIT USER MODAL
══════════════════════════════════════════════════════════════ --}}
<div id="editModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
  <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal('editModal')"></div>
  <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-3xl p-7 shadow-2xl" id="editModalPanel">

    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
          <i class="ph-fill ph-pencil-simple text-white text-lg"></i>
        </div>
        <h3 class="text-xl font-extrabold text-slate-900">Edit Pengguna</h3>
      </div>
      <button onclick="closeModal('editModal')" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-red-50 text-slate-400 hover:text-red-500 flex items-center justify-center transition">
        <i class="ph-bold ph-x text-sm"></i>
      </button>
    </div>

    <form id="editForm" method="POST">
      @csrf @method('PUT')
      <div class="space-y-4">
        <div>
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
          <input type="text" name="name" id="editName" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition">
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Username <span class="text-red-400">*</span></label>
            <div class="relative">
              <i class="ph ph-at absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
              <input type="text" name="username" id="editUsername" required class="w-full pl-8 pr-4 bg-slate-50 border border-slate-200 rounded-xl py-3 text-sm font-mono font-medium text-slate-700 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition">
            </div>
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Role <span class="text-red-400">*</span></label>
            <div class="relative">
              <select name="role" id="editRole" required class="w-full appearance-none bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition cursor-pointer">
                <option value="petugas">🔧 Petugas</option>
                <option value="admin">🛡️ Admin</option>
                <option value="direksi">💼 Direksi</option>
                <option value="superadmin">👑 Superadmin</option>
              </select>
              <i class="ph-bold ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            </div>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Email</label>
            <input type="email" name="email" id="editEmail" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition">
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Telepon</label>
            <input type="text" name="phone" id="editPhone" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition">
          </div>
        </div>
        {{-- Telegram Chat ID --}}
        <div>
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Telegram Chat ID</label>
          <div class="relative">
            <i class="ph-fill ph-telegram-logo absolute left-3 top-1/2 -translate-y-1/2 text-blue-400 text-sm"></i>
            <input type="text" name="telegram_chat_id" id="editTelegramChatId" placeholder="Contoh: 1810412191" class="w-full pl-8 pr-4 bg-slate-50 border border-slate-200 rounded-xl py-3 text-sm font-mono font-medium text-slate-700 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition">
          </div>
          <p class="text-[10px] text-slate-400 mt-1">Kirim <code>/id</code> ke bot <b>@TiaraPengaduanBot</b> untuk mendapatkan Chat ID</p>
        </div>
        {{-- Kecamatan Selector (Only for Petugas) --}}
        <div id="editKecamatanWrapper" style="display: none;">
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Wilayah Tugas (Kecamatan) <span class="text-slate-300">(opsional)</span></label>
          <div class="relative">
            <select name="kecamatan_ids[]" id="editKecamatanIds" multiple class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-indigo-200 outline-none transition" size="4">
              @foreach($kecamatans as $kecamatan)
                <option value="{{ $kecamatan->id }}">{{ $kecamatan->nama }}</option>
              @endforeach
            </select>
            <p class="text-[10px] text-slate-400 mt-1">Tahan tombol Ctrl (Windows) / Cmd (Mac) untuk memilih lebih dari satu.</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3 mt-6">
        <button type="button" onclick="closeModal('editModal')" class="px-5 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition">Batal</button>
        <button type="submit" class="px-5 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-xl font-bold text-sm hover:from-indigo-700 hover:to-blue-700 transition shadow-lg shadow-indigo-500/25">
          <i class="ph-bold ph-floppy-disk mr-1"></i> Simpan
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     RESET PASSWORD MODAL
══════════════════════════════════════════════════════════════ --}}
<div id="resetModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
  <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal('resetModal')"></div>
  <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-3xl p-7 shadow-2xl" id="resetModalPanel">

    <div class="flex items-center justify-between mb-5">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-500/20">
          <i class="ph-fill ph-key text-white text-lg"></i>
        </div>
        <div>
          <h3 class="text-lg font-extrabold text-slate-900">Reset Password</h3>
          <p class="text-xs text-slate-400 mt-0.5">Untuk: <span id="resetUserName" class="font-bold text-slate-600"></span></p>
        </div>
      </div>
      <button onclick="closeModal('resetModal')" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-red-50 text-slate-400 hover:text-red-500 flex items-center justify-center transition">
        <i class="ph-bold ph-x text-sm"></i>
      </button>
    </div>

    <form id="resetForm" method="POST">
      @csrf
      <div class="mb-5">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Password Baru <span class="text-red-400">*</span></label>
        <div class="relative">
          <i class="ph ph-lock-simple absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
          <input type="password" name="new_password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full pl-8 pr-4 bg-slate-50 border border-slate-200 rounded-xl py-3 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-amber-200 focus:border-amber-400 outline-none transition">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <button type="button" onclick="closeModal('resetModal')" class="px-5 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition">Batal</button>
        <button type="submit" class="px-5 py-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-xl font-bold text-sm hover:from-amber-600 hover:to-orange-600 transition shadow-lg shadow-amber-500/25">
          <i class="ph-bold ph-key mr-1"></i> Reset
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal(id) {
  document.getElementById(id).classList.remove('hidden');
}
function closeModal(id) {
  document.getElementById(id).classList.add('hidden');
}

// Toggle Kecamatan display based on role
document.getElementById('createRole').addEventListener('change', function() {
    document.getElementById('createKecamatanWrapper').style.display = (this.value === 'petugas') ? 'block' : 'none';
});
document.getElementById('editRole').addEventListener('change', function() {
    document.getElementById('editKecamatanWrapper').style.display = (this.value === 'petugas') ? 'block' : 'none';
});

function decodeBase64Json(b64) {
    try {
        // decodeURIComponent(escape(atob())) safely decodes UTF-8 base64 strings in JS
        return JSON.parse(decodeURIComponent(escape(atob(b64))));
    } catch(e) {
        return null;
    }
}

function openEditModal(btn) {
  const user = decodeBase64Json(btn.getAttribute('data-user')) || {};
  const kecamatanIds = decodeBase64Json(btn.getAttribute('data-kecamatan')) || [];

  document.getElementById('editForm').action = `/kelola-pengguna/${user.id}`;
  document.getElementById('editName').value = user.name || '';
  document.getElementById('editUsername').value = user.username || '';
  document.getElementById('editEmail').value = user.email || '';
  document.getElementById('editPhone').value = user.phone || '';
  document.getElementById('editRole').value = user.role || 'petugas';
  document.getElementById('editTelegramChatId').value = user.telegram_chat_id || '';
  
  // Show/hide kecamatan based on role
  document.getElementById('editKecamatanWrapper').style.display = (user.role === 'petugas') ? 'block' : 'none';
  
  // Preselect kecamatans
  const select = document.getElementById('editKecamatanIds');
  Array.from(select.options).forEach(opt => {
      opt.selected = kecamatanIds.includes(parseInt(opt.value));
  });

  openModal('editModal');
}

function openResetModal(userId, userName) {
  document.getElementById('resetForm').action = `/kelola-pengguna/${userId}/reset-password`;
  document.getElementById('resetUserName').textContent = userName;
  openModal('resetModal');
}
</script>
@endsection
