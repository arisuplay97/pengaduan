@extends('layouts.nolana')

@section('content')
<div class="space-y-6">

    <!-- ===== TOP BAR: Welcome + Create Button ===== -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white">Dashboard</h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400 text-[11px] font-bold rounded-full border border-green-200 dark:border-green-500/20">
                    <span class="relative flex w-2 h-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full w-2 h-2 bg-green-500"></span>
                    </span>
                    Live
                </span>
            </div>
            <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400 font-medium">
                <div class="flex items-center gap-1.5">
                    <i class="ph ph-calendar-blank text-base"></i>
                    <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
                <div class="flex items-center gap-1.5" id="weather-widget">
                    <i class="ph-fill ph-spinner animate-spin text-base"></i>
                    <span>Memuat cuaca...</span>
                </div>
            </div>
        </div>
        
        <!-- Gradient Create Button & Notify Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            @if(auth()->user()->role !== 'petugas')
                <form method="POST" action="{{ route('dashboard.notify') }}" class="inline m-0">
                    @csrf
                    <input type="hidden" name="type" value="agenda">
                    <button type="submit" class="bg-indigo-50 border border-indigo-200 text-indigo-600 hover:bg-indigo-100 dark:bg-indigo-500/10 dark:border-indigo-500/20 dark:text-indigo-400 dark:hover:bg-indigo-500/20 px-4 py-3 rounded-2xl font-bold text-xs transition flex items-center gap-2" onclick="return confirm('Kirim WA Rekap Agenda hari ini ke seluruh Direksi?');">
                        <i class="ph-bold ph-whatsapp-logo text-sm"></i> WA Agenda
                    </button>
                </form>
                <form method="POST" action="{{ route('dashboard.notify') }}" class="inline m-0">
                    @csrf
                    <input type="hidden" name="type" value="stats">
                    <button type="submit" class="bg-teal-50 border border-teal-200 text-teal-600 hover:bg-teal-100 dark:bg-teal-500/10 dark:border-teal-500/20 dark:text-teal-400 dark:hover:bg-teal-500/20 px-4 py-3 rounded-2xl font-bold text-xs transition flex items-center gap-2" onclick="return confirm('Kirim WA Statistik Gangguan hari ini ke seluruh Direksi?');">
                        <i class="ph-bold ph-whatsapp-logo text-sm"></i> WA Statistik
                    </button>
                </form>
            @endif

            <button onclick="window.location.href='{{ route('agenda.index') }}'" class="group bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all duration-300 flex items-center gap-2.5 shadow-lg shadow-purple-500/25 hover:shadow-xl hover:shadow-purple-500/30 hover:-translate-y-0.5 active:translate-y-0 w-fit">
                <div class="w-7 h-7 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition">
                    <i class="ph-bold ph-plus text-sm"></i>
                </div>
                <span>Buat Agenda Baru</span>
            </button>
        </div>
    </div>

    <!-- ===== STATS CARDS ROW ===== -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        
        <!-- Total Agenda -->
        <div class="stat-card bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl p-3 border border-gray-200 dark:border-gray-700 shadow-sm cursor-pointer group hover:shadow-md transition-all">
            <div class="flex items-start justify-between">
                <div class="space-y-1">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">Total Agenda</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['agenda_count'] }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <i class="ph-fill ph-calendar text-gray-900 dark:text-white text-lg"></i>
                </div>
            </div>
            <div class="mt-2 text-[10px] font-bold text-gray-400 dark:text-gray-500">
                {{ date('M Y') }}
            </div>
        </div>

        <!-- Agenda Hari Ini -->
        <div class="stat-card bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl p-3 border border-gray-200 dark:border-gray-700 shadow-sm cursor-pointer group hover:shadow-md transition-all">
            <div class="flex items-start justify-between">
                <div class="space-y-1">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">Hari Ini</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['today_agenda_count'] }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <i class="ph-fill ph-calendar-check text-gray-900 dark:text-white text-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center gap-1 text-[10px] font-bold text-green-600 dark:text-green-400">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                Active
            </div>
        </div>

        <!-- Dokumen -->
        <div class="stat-card glass-effect p-3 cursor-pointer group hover:shadow-md transition-all">
            <div class="flex items-start justify-between">
                <div class="space-y-1">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">Dokumen</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ number_format($stats['document_count']) }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <i class="ph-fill ph-files text-gray-900 dark:text-white text-lg"></i>
                </div>
            </div>
            <div class="mt-2 text-[10px] font-bold text-gray-400 dark:text-gray-500">
                Arsip Digital
            </div>
        </div>

        <!-- NEW: LAPORAN GANGGUAN -->
        <div class="stat-card glass-effect p-3 cursor-pointer group hover:shadow-md transition-all">
            <div class="flex items-start justify-between">
                <div class="space-y-1">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider truncate">Gangguan</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ number_format($stats['disturbance_count'] ?? 0) }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <i class="ph-fill ph-warning-octagon text-gray-900 dark:text-white text-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center gap-1 text-[10px] font-bold text-red-600 dark:text-red-400">
                @if(($stats['disturbance_count'] ?? 0) > 0)
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                    Laporan Masuk
                @else
                     <span class="text-gray-400 dark:text-gray-500">Hari ini</span>
                @endif
            </div>
        </div>

        <!-- 🔴 LIVE STATUS DIREKSI (replaces Pending) -->
        <div class="stat-card glass-effect p-3 group">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-900 dark:text-white">Status Direksi</p>
                <div class="relative flex w-2 h-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full w-2 h-2 bg-red-500"></span>
                </div>
            </div>
            <div class="space-y-1.5">
                @foreach($direksiStatus as $direksi)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 w-8 flex-shrink-0">{{ $direksi['short'] }}</span>
                        @if($direksi['busy'])
                            <span class="text-[10px] font-bold text-red-500 truncate" title="{{ $direksi['agenda_title'] }}">{{ Str::limit($direksi['agenda_title'], 10) }}</span>
                        @else
                            <span class="text-[10px] font-bold text-green-500">Available</span>
                        @endif
                    </div>
                    <div class="relative flex-shrink-0">
                        @if($direksi['busy'])
                            <span class="block w-1.5 h-1.5 rounded-full bg-red-500"></span>
                        @else
                            <span class="block w-1.5 h-1.5 rounded-full bg-green-500"></span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- ===== MAIN CONTENT GRID ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        
        <!-- ===== LEFT COLUMN ===== -->
        <div class="lg:col-span-3 space-y-6">
            
            <!-- Today's Agenda -->
            <div class="card-hover glass-effect overflow-hidden">
                <div class="flex items-center justify-between p-4 pb-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-violet-600 rounded-lg flex items-center justify-center shadow-lg shadow-purple-500/20">
                            <i class="ph-fill ph-calendar-check text-white text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-gray-900 dark:text-white">Agenda Hari Ini</h2>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">{{ $todayAgendas->count() }} kegiatan dijadwalkan</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button onclick="shareToWa()" class="h-8 px-3 text-[11px] font-bold text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition flex items-center gap-1.5 rounded-lg hover:bg-green-50 dark:hover:bg-green-500/10 border border-transparent hover:border-green-200 dark:hover:border-green-500/20">
                            <i class="ph-bold ph-whatsapp-logo text-sm"></i>
                            <span class="hidden sm:inline">Share</span>
                        </button>
                        <a href="{{ route('agenda.index') }}" class="h-8 px-3 text-[11px] font-bold text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition flex items-center gap-1.5 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-500/10 border border-transparent hover:border-purple-200 dark:hover:border-purple-500/20">
                            Semua
                            <i class="ph-bold ph-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>

<script>
function shareToWa() {
    let dateObj = new Date();
    let options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
    let date = dateObj.toLocaleDateString('id-ID', options);
    let text = `Hallo Pak Dirut, berikut jadwal kegiatan Anda hari ini:\n\n`;
    text += `*Agenda Direksi – ${date}*\n\n`;
    @foreach($todayAgendas as $agenda)
        text += `\uD83D\uDD67 ${"{{ $agenda->start_at->format('H:i') }}"}{{ $agenda->end_at ? " – " . $agenda->end_at->format('H:i') : '' }}\n`;
        text += `\uD83D\uDCCC ${"{{ addslashes($agenda->title) }}"}\n`;
        text += `\uD83D\uDCCD ${"{{ addslashes($agenda->location ?? 'Tidak ada lokasi') }}"}\n\n`;
    @endforeach
    if ({{ $todayAgendas->count() }} === 0) {
        text += "Tidak ada agenda kegiatan untuk hari ini.\n\n";
    }
    text += `_Tiara Smart Assistant_`;
    let url = `https://wa.me/?text=${encodeURIComponent(text)}`;
    window.open(url, '_blank');
}
</script>

                <div class="p-4 pt-4">
                    <div class="space-y-1">
                        @forelse($todayAgendas as $agenda)
                            <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-all duration-200 cursor-pointer group border border-transparent hover:border-gray-100 dark:hover:border-gray-700/60">
                                <!-- Time Block -->
                                <div class="flex flex-col items-center min-w-[52px] py-1 px-2 bg-purple-50 dark:bg-purple-500/10 rounded-lg">
                                    <span class="text-sm font-extrabold text-purple-600 dark:text-purple-400 leading-tight">{{ $agenda->start_at->format('H:i') }}</span>
                                    @if($agenda->end_at)
                                    <span class="text-[9px] text-purple-400 dark:text-purple-500 font-semibold">{{ $agenda->end_at->format('H:i') }}</span>
                                    @endif
                                </div>
                                
                                <!-- Vertical Line -->
                                <div class="w-0.5 h-10 bg-gradient-to-b from-purple-400 to-purple-200 dark:from-purple-500 dark:to-purple-800 rounded-full flex-shrink-0"></div>
                                
                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white truncate group-hover:text-purple-600 dark:group-hover:text-purple-400 transition">{{ $agenda->title }}</h3>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-[11px] text-gray-400 dark:text-gray-500 flex items-center gap-1 truncate font-medium">
                                            <i class="ph-fill ph-map-pin text-[10px] text-red-400"></i>
                                            {{ $agenda->location ?? 'Belum ada lokasi' }}
                                        </span>
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">{{ $agenda->type }}</span>
                                    </div>
                                </div>
                                
                                <!-- Arrow -->
                                <i class="ph-bold ph-caret-right text-gray-300 dark:text-gray-600 text-xs opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all"></i>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="w-16 h-16 bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-500/10 dark:to-violet-500/10 rounded-2xl flex items-center justify-center mb-4">
                                    <i class="ph-duotone ph-calendar-blank text-3xl text-purple-300 dark:text-purple-600"></i>
                                </div>
                                <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400">Tidak ada agenda hari ini</h3>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Waktu santai! Nikmati harimu 🎉</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Mini Calendar -->
            <div class="card-hover glass-effect p-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center shadow-lg shadow-blue-500/20">
                            <i class="ph-fill ph-calendar text-white text-lg"></i>
                        </div>
                        <h2 class="text-base font-extrabold text-gray-900 dark:text-white" id="calendar-month-title">Februari 2026</h2>
                    </div>
                    <div class="flex items-center gap-1">
                        <button class="w-8 h-8 hover:bg-gray-100 dark:hover:bg-gray-700/40 rounded-lg flex items-center justify-center transition">
                            <i class="ph-bold ph-caret-left text-gray-400 text-xs"></i>
                        </button>
                        <button class="w-8 h-8 hover:bg-gray-100 dark:hover:bg-gray-700/40 rounded-lg flex items-center justify-center transition">
                            <i class="ph-bold ph-caret-right text-gray-400 text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Days Header -->
                <div class="grid grid-cols-7 gap-1 text-center mb-2">
                    @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $day)
                    <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider py-2">{{ $day }}</div>
                    @endforeach
                </div>
                
                <!-- Calendar Grid -->
                <div class="grid grid-cols-7 gap-1 text-center" id="calendar-grid">
                    @php
                        $firstDay = now()->startOfMonth();
                        $startDow = $firstDay->dayOfWeek; // 0=Sun
                        $daysInMonth = now()->daysInMonth;
                        $today = now()->day;
                        $prevMonth = now()->subMonth();
                        $prevDays = $prevMonth->daysInMonth;
                    @endphp
                    
                    {{-- Previous month padding --}}
                    @for($i = $startDow - 1; $i >= 0; $i--)
                    <div class="text-xs text-gray-300 dark:text-gray-600 py-2 rounded-lg">{{ $prevDays - $i }}</div>
                    @endfor
                    
                    {{-- Current month days --}}
                    @for($d = 1; $d <= $daysInMonth; $d++)
                        @if($d === $today)
                        <div class="text-xs font-bold text-white py-2 bg-gradient-to-br from-purple-500 to-violet-600 rounded-lg shadow-md shadow-purple-500/25 cursor-pointer">{{ $d }}</div>
                        @else
                        <div class="text-xs font-semibold text-gray-600 dark:text-gray-300 py-2 hover:bg-gray-100 dark:hover:bg-gray-700/40 rounded-lg cursor-pointer transition">{{ $d }}</div>
                        @endif
                    @endfor
                    
                    {{-- Next month padding --}}
                    @php $remainder = (7 - ($startDow + $daysInMonth) % 7) % 7; @endphp
                    @for($i = 1; $i <= $remainder; $i++)
                    <div class="text-xs text-gray-300 dark:text-gray-600 py-2 rounded-lg">{{ $i }}</div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- ===== RIGHT COLUMN ===== -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Reminder Widget -->
            <div class="card-hover glass-effect overflow-hidden">
                <div class="flex items-center justify-between p-4 pb-2 border-b border-gray-100 dark:border-gray-700/50">
                    <div class="flex items-baseline gap-2">
                        <h2 class="text-base font-extrabold text-gray-900 dark:text-white leading-tight">Reminder</h2>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400">{{ $reminders->count() }} aktif</span>
                    </div>
                    <button onclick="openCreateReminderModal()" class="w-7 h-7 rounded-lg bg-purple-600 hover:bg-purple-700 text-white flex items-center justify-center transition shadow-lg shadow-purple-600/20">
                        <i class="ph-bold ph-plus text-xs"></i>
                    </button>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @foreach($reminders as $reminder)
                        @php
                            $daysLeft = (int) now()->startOfDay()->diffInDays($reminder->deadline->startOfDay(), false);
                            $dotColor = 'bg-green-500'; $badgeBg = 'bg-green-50 dark:bg-green-500/10'; $badgeText = 'text-green-600 dark:text-green-400';
                            $daysText = $daysLeft . ' hari lagi';
                            if ($daysLeft < 0) { $dotColor = 'bg-red-500'; $badgeBg = 'bg-red-50 dark:bg-red-500/10'; $badgeText = 'text-red-600 dark:text-red-400'; $daysText = 'Lewat ' . abs($daysLeft) . ' hari'; }
                            elseif ($daysLeft == 0) { $dotColor = 'bg-red-500'; $badgeBg = 'bg-red-50 dark:bg-red-500/10'; $badgeText = 'text-red-600 dark:text-red-400'; $daysText = 'Hari ini!'; }
                            elseif ($daysLeft <= 2) { $dotColor = 'bg-orange-500'; $badgeBg = 'bg-orange-50 dark:bg-orange-500/10'; $badgeText = 'text-orange-600 dark:text-orange-400'; $daysText = $daysLeft . ' hari lagi'; }
                            elseif ($daysLeft <= 10) { $dotColor = 'bg-blue-500'; $badgeBg = 'bg-blue-50 dark:bg-blue-500/10'; $badgeText = 'text-blue-600 dark:text-blue-400'; $daysText = $daysLeft . ' hari lagi'; }
                        @endphp
                        
                        <div class="group p-4 hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-all cursor-default">
                            <div class="flex items-start gap-3">
                                <div class="w-1.5 h-1.5 {{ $dotColor }} rounded-full mt-2 flex-shrink-0"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white truncate pr-2">{{ $reminder->title }}</h3>
                                        <span class="text-[10px] font-bold {{ $badgeText }} {{ $badgeBg }} px-2 py-0.5 rounded-md whitespace-nowrap flex-shrink-0">{{ $daysText }}</span>
                                    </div>
                                    <div class="flex items-center gap-3 mt-1 text-[11px] text-gray-400 dark:text-gray-500 font-medium">
                                        <span class="flex items-center gap-1"><i class="ph-fill ph-calendar-blank"></i> {{ $reminder->deadline->format('d M') }}</span>
                                        <span class="flex items-center gap-1"><i class="ph-fill ph-user"></i> {{ $reminder->pic }}</span>
                                    </div>
                                    
                                    <!-- Action Buttons (Visible on Hover) -->
                                    <div class="flex items-center gap-2 mt-2 opacity-0 group-hover:opacity-100 transition-all translate-y-1 group-hover:translate-y-0">
                                        <button onclick="openEditReminderModal({{ $reminder }})" class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-[10px] font-bold rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition flex items-center gap-1">
                                            <i class="ph-bold ph-pencil-simple"></i> Edit
                                        </button>
                                        <form action="{{ route('reminder.destroy', $reminder->id) }}" method="POST" onsubmit="return confirm('Tandai selesai?');" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-2 py-1 bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400 text-[10px] font-bold rounded hover:bg-green-100 transition flex items-center gap-1">
                                                <i class="ph-bold ph-check"></i> Selesai
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($reminders->count() == 0)
                        <div class="p-8 text-center">
                            <div class="w-12 h-12 mx-auto bg-gray-50 dark:bg-gray-700/50 rounded-full flex items-center justify-center mb-3">
                                <i class="ph-duotone ph-bell-slash text-xl text-gray-300 dark:text-gray-500"></i>
                            </div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">Tidak ada reminder aktif</p>
                            <button onclick="openCreateReminderModal()" class="mt-2 text-[10px] font-bold text-purple-600 hover:text-purple-700 hover:underline">Tambah Baru</button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card-hover glass-effect p-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg shadow-indigo-500/20">
                        <i class="ph-fill ph-clock-counter-clockwise text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-extrabold text-gray-900 dark:text-white leading-tight">Aktivitas</h2>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Update terbaru</p>
                    </div>
                </div>
                <div class="space-y-3">
                    @if(isset($recentActivities) && $recentActivities->count() > 0)
                        @foreach($recentActivities as $act)
                        <div class="flex items-start gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                            <div class="w-6 h-6 bg-{{ $act['color'] }}-50 dark:bg-{{ $act['color'] }}-500/10 rounded-md flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="ph-fill {{ $act['icon'] }} text-{{ $act['color'] }}-500 text-[10px]"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[11px] font-bold text-gray-700 dark:text-gray-300 leading-relaxed">{{ $act['text'] }}</p>
                                <p class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5 font-medium">{{ $act['time']->diffForHumans() }} · {{ $act['user'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="py-6 text-center">
                            <i class="ph ph-clock-counter-clockwise text-xl text-gray-300 dark:text-gray-600"></i>
                            <p class="text-[10px] text-gray-400 mt-1">Belum ada aktivitas</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ===== CUSTOM STYLES ===== -->
<style>
    /* Custom Glass Effect */
    .glass-effect {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(25px) saturate(180%);
        -webkit-backdrop-filter: blur(25px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 20px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
    }
    .dark .glass-effect {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
    }

    /* Card hover effect - lift & scale */
    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 20px 40px -12px rgba(0,0,0,0.1);
        border-color: rgba(139, 92, 246, 0.3);
    }
    .dark .stat-card:hover {
        box-shadow: 0 20px 40px -12px rgba(0,0,0,0.4);
        border-color: rgba(139, 92, 246, 0.2);
    }
    
    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px -8px rgba(0,0,0,0.08);
    }
    .dark .card-hover:hover {
        box-shadow: 0 12px 30px -8px rgba(0,0,0,0.3);
    }
    
    .quick-action-btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .quick-action-btn:hover {
        transform: translateY(-3px) scale(1.03);
        box-shadow: 0 8px 25px -5px rgba(0,0,0,0.1);
        border-color: rgba(139, 92, 246, 0.25);
    }

    @keyframes pulse-once {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.02); }
    }
    .animate-pulse-once {
        animation: pulse-once 0.5s ease-in-out 2;
    }
    @keyframes ring {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(-15deg); }
        75% { transform: rotate(15deg); }
    }
    .animate-ring {
        animation: ring 0.5s ease-in-out infinite;
    }
</style>

<!-- ===== JAVASCRIPT ===== -->
<script>
    // Weather Widget
    async function fetchWeather() {
        const widget = document.getElementById('weather-widget');
        try {
            const response = await fetch('https://api.open-meteo.com/v1/forecast?latitude=-6.2088&longitude=106.8456&current=temperature_2m,weather_code&timezone=auto');
            const data = await response.json();
            const temp = Math.round(data.current.temperature_2m);
            const wc = data.current.weather_code;
            let icon = 'ph-sun', text = 'Cerah';
            if (wc > 3) { icon = 'ph-cloud'; text = 'Berawan'; }
            if (wc > 45) { icon = 'ph-cloud-fog'; text = 'Kabut'; }
            if (wc > 50) { icon = 'ph-drop'; text = 'Gerimis'; }
            if (wc > 60) { icon = 'ph-cloud-rain'; text = 'Hujan'; }
            if (wc > 80) { icon = 'ph-cloud-lightning'; text = 'Badai'; }
            widget.innerHTML = `<i class="ph-fill ${icon} text-base"></i><span>${temp}°C ${text}</span>`;
        } catch (e) {
            widget.innerHTML = `<i class="ph-fill ph-warning-circle"></i><span>Offline</span>`;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        fetchWeather();
        startAgendaNotificationChecker();
    });

    // ====== AGENDA NOTIFICATION SYSTEM ======
    let notifiedAgendas30 = new Set();
    let notifiedAgendas10 = new Set();

    function startAgendaNotificationChecker() {
        checkUpcomingAgendas();
        setInterval(checkUpcomingAgendas, 60000);
    }

    function checkUpcomingAgendas() {
        fetch('{{ route("api.upcoming") }}')
            .then(res => res.json())
            .then(agendas => {
                const now = new Date();
                agendas.forEach(agenda => {
                    const startTime = new Date(agenda.start_at);
                    const diffMinutes = (startTime - now) / (1000 * 60);
                    if (diffMinutes > 10 && diffMinutes <= 30.5 && !notifiedAgendas30.has(agenda.id)) {
                        notifiedAgendas30.add(agenda.id);
                        showAgendaNotification({
                            ...agenda,
                            start_time: startTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}),
                            end_time: agenda.end_at ? new Date(agenda.end_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : ''
                        }, Math.round(diffMinutes), '30 menit');
                    }
                    else if (diffMinutes > 0 && diffMinutes <= 10.5 && !notifiedAgendas10.has(agenda.id)) {
                        notifiedAgendas10.add(agenda.id);
                        showAgendaNotification({
                            ...agenda,
                            start_time: startTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}),
                            end_time: agenda.end_at ? new Date(agenda.end_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : ''
                        }, Math.round(diffMinutes), '10 menit');
                    }
                });
            })
            .catch(err => console.error('Notification Poll Error:', err));
    }

    // ====== AUDIO SYSTEM ======
    let globalAudioContext = null;
    let audioUnlocked = false;
    let pendingSound = false;

    try { globalAudioContext = new (window.AudioContext || window.webkitAudioContext)(); } catch (e) {}

    function unlockAudioOnClick() {
        if (globalAudioContext && globalAudioContext.state === 'suspended') {
            globalAudioContext.resume().then(() => {
                audioUnlocked = true;
                if (pendingSound) { pendingSound = false; actuallyPlaySound(); }
            });
        } else { audioUnlocked = true; }
    }
    document.addEventListener('click', unlockAudioOnClick, { once: true });
    document.addEventListener('touchstart', unlockAudioOnClick, { once: true });

    function playNotificationSound() {
        if (globalAudioContext && globalAudioContext.state === 'suspended' && !audioUnlocked) {
            pendingSound = true; globalAudioContext.resume(); return;
        }
        actuallyPlaySound();
    }

    function actuallyPlaySound() {
        if (!globalAudioContext) globalAudioContext = new (window.AudioContext || window.webkitAudioContext)();
        if (globalAudioContext.state === 'suspended') globalAudioContext.resume();
        function playTone(f, s, d) {
            const o = globalAudioContext.createOscillator(), g = globalAudioContext.createGain();
            o.connect(g); g.connect(globalAudioContext.destination);
            o.frequency.value = f; o.type = 'sine';
            g.gain.setValueAtTime(0.3, s); g.gain.exponentialRampToValueAtTime(0.01, s + d);
            o.start(s); o.stop(s + d);
        }
        const n = globalAudioContext.currentTime;
        playTone(880, n, 0.15); playTone(880, n+0.2, 0.15); playTone(1100, n+0.4, 0.25);
        playTone(880, n+1.2, 0.15); playTone(880, n+1.4, 0.15); playTone(1100, n+1.6, 0.25);
        playTone(880, n+2.4, 0.15); playTone(880, n+2.6, 0.15); playTone(1100, n+2.8, 0.25);
        setTimeout(() => {
            const audio = new Audio('{{ asset("audio/notification.mp3") }}');
            audio.play().catch(e => {});
        }, 3500);
    }

    function showAgendaNotification(agenda, minutesLeft, alertType) {
        playNotificationSound();
        if ('Notification' in window && Notification.permission === 'default') Notification.requestPermission();
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('📅 Agenda Segera Dimulai!', {
                body: `${agenda.title} akan dimulai dalam ${minutesLeft} menit`,
                icon: '/pdam-logo.png', tag: 'agenda-' + agenda.id + '-' + alertType
            });
        }
        const modal = document.getElementById('agendaNotificationModal');
        document.getElementById('notif-title').textContent = agenda.title;
        document.getElementById('notif-time').textContent = `${agenda.start_time} - ${agenda.end_time || 'Selesai'}`;
        document.getElementById('notif-location').textContent = agenda.location || 'Tidak ada lokasi';
        document.getElementById('notif-countdown').textContent = `${minutesLeft} menit lagi`;
        document.getElementById('notif-alert-type').textContent = alertType === '10 menit' ? '⚠️ SEGERA!' : '🔔 Pengingat';
        modal.classList.remove('hidden');
        const mc = modal.querySelector('.modal-content');
        mc.classList.add('animate-pulse-once');
        setTimeout(() => mc.classList.remove('animate-pulse-once'), 1000);
    }

    function dismissNotification() {
        document.getElementById('agendaNotificationModal').classList.add('hidden');
    }
</script>

<!-- ===== MODALS ===== -->

<!-- Agenda Notification Modal -->
<div id="agendaNotificationModal" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="modal-content relative transform overflow-hidden rounded-3xl bg-white dark:bg-gray-800 text-left shadow-2xl sm:w-full sm:max-w-md">
                <div class="bg-gradient-to-r from-orange-500 to-red-500 px-6 py-5 text-white">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                            <i class="ph-fill ph-bell-ringing text-3xl animate-ring"></i>
                        </div>
                        <div>
                            <p class="text-orange-200 text-xs font-bold uppercase tracking-wide" id="notif-alert-type">🔔 Pengingat</p>
                            <h3 class="text-xl font-black">Agenda Mendekat!</h3>
                            <p class="text-orange-100 text-sm font-medium" id="notif-countdown">30 menit lagi</p>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-5">
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Kegiatan</p>
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white" id="notif-title">Nama Agenda</h4>
                        </div>
                        <div class="flex gap-6">
                            <div>
                                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Waktu</p>
                                <p class="text-gray-900 dark:text-white font-semibold flex items-center gap-1.5" id="notif-time"><i class="ph-fill ph-clock text-blue-500"></i> 09:00 - 10:00</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Lokasi</p>
                                <p class="text-gray-900 dark:text-white font-semibold flex items-center gap-1.5" id="notif-location"><i class="ph-fill ph-map-pin text-red-500"></i> Ruang Rapat</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 pb-6 flex gap-3">
                    <button onclick="dismissNotification()" class="flex-1 px-4 py-3 bg-gray-100 dark:bg-gray-700 font-bold text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 transition flex items-center justify-center gap-2"><i class="ph-bold ph-x"></i> Tutup</button>
                    <button onclick="dismissNotification()" class="flex-1 px-4 py-3 bg-gradient-to-r from-orange-500 to-red-500 font-bold text-white rounded-xl hover:from-orange-600 hover:to-red-600 transition flex items-center justify-center gap-2 shadow-lg shadow-orange-500/30"><i class="ph-bold ph-check"></i> Siap!</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Reminder Modal -->
<div id="reminderModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="document.getElementById('reminderModal').classList.add('hidden')"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-xl sm:my-8 sm:w-full sm:max-w-md">
                <div class="bg-white dark:bg-gray-800 px-6 pb-6 pt-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-5" id="reminderModalTitle">Buat Reminder Baru</h3>
                    <form id="reminderForm" action="{{ route('reminder.store') }}" method="POST">
                        @csrf
                        <div id="methodUtils"></div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Judul Laporan</label>
                                <input type="text" name="title" required class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition" placeholder="Contoh: Laporan Keuangan Q1">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">PIC (Penanggung Jawab)</label>
                                <input type="text" name="pic" required class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition" placeholder="Nama PIC">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Deadline</label>
                                <input type="date" name="deadline" required class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" onclick="document.getElementById('reminderModal').classList.add('hidden')" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 font-bold text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 transition">Batal</button>
                            <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 font-bold text-white rounded-xl hover:from-violet-700 hover:to-purple-700 transition shadow-lg shadow-purple-500/25">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openCreateReminderModal() {
        const modal = document.getElementById('reminderModal');
        const form = document.getElementById('reminderForm');
        const title = document.getElementById('reminderModalTitle');
        const methodUtils = document.getElementById('methodUtils');
        
        // Reset
        form.reset();
        form.action = "{{ route('reminder.store') }}";
        title.textContent = "Buat Reminder Baru";
        methodUtils.innerHTML = ''; // Remove PUT method if exists
        
        modal.classList.remove('hidden');
    }

    function openEditReminderModal(reminder) {
        const modal = document.getElementById('reminderModal');
        const form = document.getElementById('reminderForm');
        const title = document.getElementById('reminderModalTitle');
        const methodUtils = document.getElementById('methodUtils');
        
        // Populate
        form.querySelector('input[name="title"]').value = reminder.title;
        form.querySelector('input[name="pic"]').value = reminder.pic;
        // Handle Date yyyy-mm-dd
        let dateStr = reminder.deadline; 
        if(dateStr.includes('T')) dateStr = dateStr.split('T')[0];
        form.querySelector('input[name="deadline"]').value = dateStr;
        
        // Set Update Action
        form.action = "{{ url('reminder') }}/" + reminder.id;
        title.textContent = "Edit Reminder";
        
        // Add PUT method
        methodUtils.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        modal.classList.remove('hidden');
    }
</script>

@endsection
