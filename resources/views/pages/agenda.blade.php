@extends('layouts.nolana')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Agenda Direksi</h1>
            <p class="text-gray-500">Kelola dan monitor semua agenda rapat & kegiatan direksi</p>
        </div>
        <button class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:from-purple-700 hover:to-indigo-700 transition flex items-center gap-2 shadow-lg shadow-purple-500/30">
            <i class="ph-bold ph-plus"></i>
            <span>Tambah Agenda</span>
        </button>
    </div>

    <!-- Filter & View Toggle -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex flex-wrap items-center gap-4">
            <!-- View Toggle -->
            <div class="flex items-center gap-2 bg-gray-100 p-1 rounded-lg">
                <button id="calendar-view-btn" class="px-4 py-2 rounded-lg bg-white shadow-sm font-bold text-gray-900 flex items-center gap-2">
                    <i class="ph-fill ph-calendar"></i>
                    <span>Calendar</span>
                </button>
                <button id="list-view-btn" class="px-4 py-2 rounded-lg font-bold text-gray-600 hover:text-gray-900 flex items-center gap-2">
                    <i class="ph ph-list"></i>
                    <span>List</span>
                </button>
            </div>

            <!-- Filters -->
            <div class="flex-1 flex items-center gap-3">
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <i class="ph ph-magnifying-glass text-gray-400"></i>
                    </div>
                    <input 
                        type="text" 
                        placeholder="Cari agenda..." 
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                </div>

                <select class="px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 font-medium text-gray-700">
                    <option>Semua Status</option>
                    <option>Upcoming</option>
                    <option>Completed</option>
                    <option>Cancelled</option>
                </select>

                <select class="px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 font-medium text-gray-700">
                    <option>Bulan Ini</option>
                    <option>7 Hari Terakhir</option>
                    <option>30 Hari Terakhir</option>
                    <option>Custom Range</option>
                </select>
            </div>
        </div>
    </div>

    <!-- List View -->
    <div id="list-view" class="space-y-4 hidden">
        @forelse($agendas as $agenda)
            @php
                $color = 'purple';
                if($agenda->type == 'UMUM') $color = 'orange';
                if($agenda->type == 'OPERASIONAL') $color = 'green';
            @endphp
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 border-l-4 border-l-{{ $color }}-500 hover:shadow-md transition group">
                <div class="flex gap-6">
                    <!-- Date Badge -->
                    <div class="flex flex-col items-center gap-1 px-4 py-3 bg-{{ $color }}-50 rounded-xl border border-{{ $color }}-100 h-fit">
                        <span class="text-xs font-bold text-{{ $color }}-600 uppercase">{{ $agenda->start_at->format('M') }}</span>
                        <span class="text-3xl font-black text-{{ $color }}-900">{{ $agenda->start_at->format('d') }}</span>
                        <span class="text-xs font-bold text-{{ $color }}-600">{{ $agenda->start_at->format('Y') }}</span>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-1 group-hover:text-{{ $color }}-600 transition">{{ $agenda->title }}</h3>
                                <p class="text-sm text-gray-500 mb-3">{{ $agenda->description ?? 'Tidak ada deskripsi' }}</p>
                            </div>
                            <span class="px-3 py-1 bg-{{ $agenda->status == 'APPROVED' ? 'green' : 'yellow' }}-100 text-{{ $agenda->status == 'APPROVED' ? 'green' : 'yellow' }}-700 text-xs font-bold rounded-full whitespace-nowrap">
                                {{ $agenda->status }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="ph ph-clock text-purple-600"></i>
                                <span class="font-medium">{{ $agenda->start_at->format('H:i') }}{{ $agenda->end_at ? ' - ' . $agenda->end_at->format('H:i') : '' }} WITA</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="ph ph-map-pin text-purple-600"></i>
                                <span class="font-medium">{{ $agenda->location ?? 'Online/TBA' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="ph ph-tag text-purple-600"></i>
                                <span class="font-medium">{{ $agenda->type }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                             <div class="flex items-center gap-2">
                                <button onclick="editAgenda({{ $agenda->id }}, '{{ addslashes($agenda->title) }}', '{{ $agenda->type }}', '{{ addslashes($agenda->location ?? '') }}', '{{ addslashes($agenda->description ?? '') }}', '{{ $agenda->start_at ? $agenda->start_at->format('Y-m-d\TH:i') : '' }}', '{{ $agenda->end_at ? $agenda->end_at->format('Y-m-d\TH:i') : '' }}', '{{ $agenda->bidang ?? '' }}')" class="px-4 py-2 bg-purple-600 text-white rounded-lg font-bold hover:bg-purple-700 transition text-sm flex items-center gap-2">
                                    <i class="ph ph-pencil-simple"></i>
                                    <span>Edit</span>
                                </button>
                                <button onclick="deleteAgenda({{ $agenda->id }})" class="px-4 py-2 border border-red-200 text-red-600 rounded-lg font-bold hover:bg-red-50 transition text-sm flex items-center gap-2">
                                    <i class="ph ph-trash"></i>
                                    <span>Delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ph-duotone ph-calendar-slash text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Belum ada agenda</h3>
                <p class="text-gray-500">Silakan tambahkan agenda baru untuk direksi.</p>
            </div>
        @endforelse
    </div>

    <!-- Calendar View -->
    <div id="calendar-view" class="">
        <div class="flex gap-6">
            
            <!-- Left Sidebar -->
            <div class="w-64 flex-shrink-0 space-y-4">
                
                <!-- Mini Calendar -->
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <button onclick="prevMiniMonth()" class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-lg transition">
                            <i class="ph-bold ph-caret-left text-gray-600"></i>
                        </button>
                        <span id="mini-calendar-month" class="font-bold text-gray-900"></span>
                        <button onclick="nextMiniMonth()" class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-lg transition">
                            <i class="ph-bold ph-caret-right text-gray-600"></i>
                        </button>
                    </div>
                    
                    <!-- Mini Calendar Grid -->
                    <div class="grid grid-cols-7 gap-1 text-center mb-2">
                        <div class="text-xs font-bold text-gray-400 py-1">M</div>
                        <div class="text-xs font-bold text-gray-400 py-1">S</div>
                        <div class="text-xs font-bold text-gray-400 py-1">S</div>
                        <div class="text-xs font-bold text-gray-400 py-1">R</div>
                        <div class="text-xs font-bold text-gray-400 py-1">K</div>
                        <div class="text-xs font-bold text-gray-400 py-1">J</div>
                        <div class="text-xs font-bold text-gray-400 py-1">S</div>
                    </div>
                    <div id="mini-calendar-grid" class="grid grid-cols-7 gap-1 text-center"></div>
                </div>
                
                <!-- Search -->
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                            <i class="ph ph-magnifying-glass text-gray-400"></i>
                        </div>
                        <input type="text" placeholder="Cari event..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                
                <!-- My Calendars -->
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-3">Kalender Direksi</h3>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" checked class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <span class="w-3 h-3 bg-purple-500 rounded"></span>
                            <span class="text-sm text-gray-700">Direktur Utama</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" checked class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                            <span class="w-3 h-3 bg-orange-500 rounded"></span>
                            <span class="text-sm text-gray-700">Direktur Umum & Keuangan</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" checked class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <span class="w-3 h-3 bg-green-500 rounded"></span>
                            <span class="text-sm text-gray-700">Direktur Operasional</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Main Calendar Area -->
            <div class="flex-1 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                
                <!-- Calendar Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-4">
                        <h2 id="calendar-title" class="text-2xl font-bold text-gray-900"></h2>
                        <button onclick="goToToday()" class="px-4 py-2 border border-gray-200 rounded-lg font-bold text-gray-700 hover:bg-gray-50 transition text-sm">
                            Hari Ini
                        </button>
                        <div class="flex items-center gap-1">
                            <button onclick="prevMonth()" class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-lg transition">
                                <i class="ph-bold ph-caret-left text-gray-600"></i>
                            </button>
                            <button onclick="nextMonth()" class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-lg transition">
                                <i class="ph-bold ph-caret-right text-gray-600"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-100 p-1 rounded-lg">
                        <button onclick="setCalendarView('day')" id="day-view-btn" class="px-4 py-2 rounded-lg font-bold text-gray-600 hover:text-gray-900 text-sm transition">Hari</button>
                        <button onclick="setCalendarView('week')" id="week-view-btn" class="px-4 py-2 rounded-lg font-bold text-gray-600 hover:text-gray-900 text-sm transition">Minggu</button>
                        <button onclick="setCalendarView('month')" id="month-view-btn" class="px-4 py-2 rounded-lg bg-white shadow-sm font-bold text-gray-900 text-sm">Bulan</button>
                    </div>
                </div>
                
                <!-- Calendar Grid Header (Days of Week) -->
                <div class="grid grid-cols-7 border-b border-gray-100">
                    <div class="py-3 text-center text-sm font-bold text-gray-500 border-r border-gray-100">Min</div>
                    <div class="py-3 text-center text-sm font-bold text-gray-500 border-r border-gray-100">Sen</div>
                    <div class="py-3 text-center text-sm font-bold text-gray-500 border-r border-gray-100">Sel</div>
                    <div class="py-3 text-center text-sm font-bold text-gray-500 border-r border-gray-100">Rab</div>
                    <div class="py-3 text-center text-sm font-bold text-gray-500 border-r border-gray-100">Kam</div>
                    <div class="py-3 text-center text-sm font-bold text-gray-500 border-r border-gray-100">Jum</div>
                    <div class="py-3 text-center text-sm font-bold text-gray-500">Sab</div>
                </div>
                
                <!-- Calendar Grid Body -->
                <div id="calendar-grid" class="grid grid-cols-7"></div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div id="pagination" class="mt-6">
        {{ $agendas->links() }}
    </div>

</div>

<!-- Event Detail Modal -->
<div id="eventDetailModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="detail-modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeDetailModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                <div class="bg-white px-6 py-5">
                    <!-- Header with color indicator -->
                    <div class="flex items-start gap-4 mb-4">
                        <div id="detailColorBadge" class="w-4 h-4 rounded-full mt-1 flex-shrink-0"></div>
                        <div class="flex-1">
                            <h3 id="detailTitle" class="text-xl font-bold text-gray-900"></h3>
                            <span id="detailTypeBadge" class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-bold"></span>
                        </div>
                        <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 transition">
                            <i class="ph ph-x text-xl"></i>
                        </button>
                    </div>
                    
                    <!-- Details -->
                    <div class="space-y-3 text-sm">
                        <!-- Date & Time -->
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <i class="ph ph-calendar text-lg text-purple-600"></i>
                            <div>
                                <p class="font-bold text-gray-700">Tanggal</p>
                                <p id="detailDate" class="text-gray-600"></p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <i class="ph ph-clock text-lg text-purple-600"></i>
                            <div>
                                <p class="font-bold text-gray-700">Waktu</p>
                                <p id="detailTime" class="text-gray-600"></p>
                            </div>
                        </div>
                        
                        <!-- Location -->
                        <div id="detailLocationRow" class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <i class="ph ph-map-pin text-lg text-purple-600"></i>
                            <div>
                                <p class="font-bold text-gray-700">Lokasi</p>
                                <p id="detailLocation" class="text-gray-600"></p>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div id="detailDescriptionRow" class="p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="ph ph-note text-lg text-purple-600"></i>
                                <p class="font-bold text-gray-700">Deskripsi</p>
                            </div>
                            <p id="detailDescription" class="text-gray-600 pl-7"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Actions -->
                <div class="bg-gray-50 px-6 py-4 flex gap-3">
                    <button id="btnEditDetail" onclick="editFromDetail()" type="button" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-xl font-bold hover:bg-purple-700 transition flex items-center justify-center gap-2">
                        <i class="ph ph-pencil-simple"></i>
                        Edit
                    </button>
                    <button id="btnDeleteDetail" onclick="deleteFromDetail()" type="button" class="px-4 py-2 border border-red-200 text-red-600 rounded-xl font-bold hover:bg-red-50 transition flex items-center justify-center gap-2">
                        <i class="ph ph-trash"></i>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="agendaModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6" id="modalTitle">Tambah Agenda Baru</h3>
                    
                    <form id="agendaForm" class="space-y-4">
                        @csrf
                        <input type="hidden" id="agendaId" name="id">
                        
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Judul Agenda</label>
                            <input type="text" id="title" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-purple-500 focus:border-purple-500" placeholder="Contoh: Rapat Evaluasi Bulanan">
                        </div>

                        <!-- Type -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Agenda</label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="DIRUT" class="peer sr-only" required>
                                    <div class="px-3 py-2 border-2 border-gray-200 rounded-xl text-center peer-checked:border-purple-500 peer-checked:bg-purple-50 transition">
                                        <span class="block text-sm font-bold text-gray-700 peer-checked:text-purple-700">DIRUT</span>
                                        <span class="block text-xs text-gray-500">Ungu</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="UMUM" class="peer sr-only">
                                    <div class="px-3 py-2 border-2 border-gray-200 rounded-xl text-center peer-checked:border-orange-500 peer-checked:bg-orange-50 transition">
                                        <span class="block text-sm font-bold text-gray-700 peer-checked:text-orange-700">DIRUM</span>
                                        <span class="block text-xs text-gray-500">Orange</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="OPERASIONAL" class="peer sr-only">
                                    <div class="px-3 py-2 border-2 border-gray-200 rounded-xl text-center peer-checked:border-green-500 peer-checked:bg-green-50 transition">
                                        <span class="block text-sm font-bold text-gray-700 peer-checked:text-green-700">DIROP</span>
                                        <span class="block text-xs text-gray-500">Hijau</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Bidang -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Bidang</label>
                            <select id="bidang" name="bidang" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-purple-500 focus:border-purple-500">
                                <option value="">-- Pilih Bidang (Opsional) --</option>
                                <option value="Sekper">Bidang Sekper</option>
                                <option value="Keuangan">Bidang Keuangan</option>
                                <option value="Hublang">Bidang Hublang</option>
                                <option value="SPI">Bidang SPI</option>
                                <option value="Umum">Bidang Umum</option>
                                <option value="Perawatan">Bidang Perawatan</option>
                                <option value="Produksi">Bidang Produksi</option>
                                <option value="Transdit">Bidang Transdit</option>
                                <option value="Perencana">Bidang Perencana</option>
                                <option value="Cabang">Cabang</option>
                            </select>
                        </div>

                        <!-- Date Time -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Mulai <span class="text-red-500">*</span></label>
                                <input type="datetime-local" id="start_at" name="start_at" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Selesai <span class="text-gray-400 text-xs font-normal">(Opsional)</span></label>
                                <input type="datetime-local" id="end_at" name="end_at" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-purple-500 focus:border-purple-500">
                            </div>
                        </div>

                        <!-- Location -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Lokasi</label>
                            <input type="text" id="location" name="location" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-purple-500 focus:border-purple-500" placeholder="Contoh: Ruang Rapat Lt. 2">
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi</label>
                            <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-purple-500 focus:border-purple-500" placeholder="Tambahkan detail agenda..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" onclick="saveAgenda()" class="inline-flex w-full justify-center rounded-xl bg-purple-600 px-3 py-2 text-sm font-bold text-white shadow-sm hover:bg-purple-500 sm:ml-3 sm:w-auto">Simpan</button>
                    <button type="button" onclick="closeModal()" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-3 py-2 text-sm font-bold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Elements
    const modal = document.getElementById('agendaModal');
    const form = document.getElementById('agendaForm');
    const modalTitle = document.getElementById('modalTitle');
    const detailModal = document.getElementById('eventDetailModal');
    
    // Current event data for detail modal
    let currentEventData = null;
    
    // User Info
    const currentUserRole = "{{ auth()->user()->username }}";
    const currentUserId = {{ auth()->id() }};
    
    // Helper Functions
    function openModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        form.reset();
        document.getElementById('agendaId').value = '';
        modalTitle.innerText = 'Tambah Agenda Baru';
    }
    
    // Detail Modal Functions
    function openDetailModal(event) {
        currentEventData = event;
        
        // Permission Check
        const btnEdit = document.getElementById('btnEditDetail');
        const btnDelete = document.getElementById('btnDeleteDetail');
        
        let canEdit = false;
        
        // Admin or Owner
        if (currentUserRole === 'admin' || event.userId === currentUserId) {
            canEdit = true;
        } 
        // Logic Per Role (Backward Compatibility for Legacy Data)
        else if (currentUserRole === 'dirut' && event.type === 'DIRUT') {
            canEdit = true;
        } else if (currentUserRole === 'dirum' && event.type === 'UMUM') {
            canEdit = true;
        } else if (currentUserRole === 'dirop' && event.type === 'OPERASIONAL') {
            canEdit = true;
        }
        
        if (btnEdit && btnDelete) {
            if (canEdit) {
                btnEdit.style.display = 'flex';
                btnDelete.style.display = 'flex';
            } else {
                btnEdit.style.display = 'none';
                btnDelete.style.display = 'none';
            }
        }
        
        // Set color badge
        const colorBadge = document.getElementById('detailColorBadge');
        const typeBadge = document.getElementById('detailTypeBadge');
        
        let bgColor = 'bg-purple-500';
        let typeLabel = 'DIRUT';
        let badgeBg = 'bg-purple-100 text-purple-700';
        
        if (event.type === 'UMUM') {
            bgColor = 'bg-orange-500';
            typeLabel = 'DIRUM';
            badgeBg = 'bg-orange-100 text-orange-700';
        } else if (event.type === 'OPERASIONAL') {
            bgColor = 'bg-green-500';
            typeLabel = 'DIROP';
            badgeBg = 'bg-green-100 text-green-700';
        }
        
        colorBadge.className = `w-4 h-4 rounded-full mt-1 flex-shrink-0 ${bgColor}`;
        typeBadge.className = `inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-bold ${badgeBg}`;
        typeBadge.textContent = typeLabel;
        
        // Set title
        document.getElementById('detailTitle').textContent = event.title;
        
        // Set date
        const date = new Date(event.start);
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('detailDate').textContent = date.toLocaleDateString('id-ID', options);
        
        // Set time
        let timeText = event.startTime + ' WITA';
        if (event.endTime) {
            timeText = event.startTime + ' - ' + event.endTime + ' WITA';
        }
        document.getElementById('detailTime').textContent = timeText;
        
        // Set location
        const locationRow = document.getElementById('detailLocationRow');
        if (event.location) {
            document.getElementById('detailLocation').textContent = event.location;
            locationRow.classList.remove('hidden');
        } else {
            locationRow.classList.add('hidden');
        }
        
        // Set description
        const descriptionRow = document.getElementById('detailDescriptionRow');
        if (event.description) {
            document.getElementById('detailDescription').textContent = event.description;
            descriptionRow.classList.remove('hidden');
        } else {
            descriptionRow.classList.add('hidden');
        }
        
        // Show modal
        detailModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeDetailModal() {
        detailModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        currentEventData = null;
    }
    
    // Close Detail Modal
    function closeDetailModal() {
        if (detailModal) detailModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        currentEventData = null;
    }

    function editFromDetail() {
        if (currentEventData) {
            // Clone data before closing modal because closeDetailModal clears currentEventData
            const data = { ...currentEventData };
            closeDetailModal();
            
            editAgenda(
                data.id,
                data.title,
                data.type,
                data.location || '',
                data.description || '',
                data.startRaw || '',
                data.endRaw || '',
                data.bidang || ''
            );
        } else {
             alert('Error: No event data loaded');
        }
    }
    
    function deleteFromDetail() {
        if (currentEventData) {
            const id = currentEventData.id; // Capture ID before closing
            closeDetailModal();
            deleteAgenda(id);
        } else {
             alert('Error: No event data loaded');
        }
    }

    // Trigger Open Modal from "Tambah Agenda" Button (Ensure button has onclick="openModal()")
    const addBtn = document.querySelector('button.bg-gradient-to-r');
    if (addBtn) addBtn.setAttribute('onclick', 'openModal()');

    // Edit Agenda
    function editAgenda(id, title, type, location, description, start, end, bidang) {
        document.getElementById('agendaId').value = id;
        document.getElementById('title').value = title;
        document.getElementById('location').value = location;
        document.getElementById('description').value = description;
        document.getElementById('start_at').value = start;
        document.getElementById('end_at').value = end;
        document.getElementById('bidang').value = bidang || '';
        
        // Select Radio Button
        const radio = document.querySelector(`input[name="type"][value="${type}"]`);
        if (radio) radio.checked = true;
        
        modalTitle.innerText = 'Edit Agenda';
        openModal();
    }

    // Save Agenda (Create/Update)
    async function saveAgenda() {
        const id = document.getElementById('agendaId').value;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Convert dates to proper format if needed, but standard input value is usually fine
        // API expects JSON
        
        const url = id ? `/agenda/${id}` : '/agenda';
        const method = id ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                closeModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Agenda telah disimpan.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => window.location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: result.message || 'Terjadi kesalahan validasi.',
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
        }
    }

    // Delete Agenda
    async function deleteAgenda(id) {
        // Confirm first
        const result = await Swal.fire({
            title: 'Hapus Agenda?',
            text: "Yakin hapus data ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!'
        });

        if (!result.isConfirmed) {
            return;
        }

        try {
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const token = tokenMeta ? tokenMeta.getAttribute('content') : null;
            
            if (!token) {
                console.error('CSRF Token Missing');
                Swal.fire('Error', 'Token keamanan hilang. Muat ulang halaman.', 'error');
                return;
            }

            const response = await fetch(`/agenda/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json().catch(e => {
                return { message: 'Invalid JSON response' };
            });

            if (response.ok) {
                await Swal.fire('Berhasil', 'Data dihapus', 'success');
                window.location.reload();
            } else {
                Swal.fire('Gagal', data.message || 'Gagal menghapus', 'error');
            }
        } catch (error) {
            console.error('System Error: ' + error.message);
            Swal.fire('Error', error.message, 'error');
        }
    }

    // View Toggle Logic
    const listViewBtn = document.getElementById('list-view-btn');
    const calendarViewBtn = document.getElementById('calendar-view-btn');
    const listView = document.getElementById('list-view');
    const calendarView = document.getElementById('calendar-view');
    const pagination = document.getElementById('pagination');

    listViewBtn.addEventListener('click', () => {
        listView.classList.remove('hidden');
        calendarView.classList.add('hidden');
        pagination.classList.remove('hidden');
        
        listViewBtn.classList.add('bg-white', 'shadow-sm', 'text-gray-900');
        listViewBtn.classList.remove('text-gray-600');
        calendarViewBtn.classList.remove('bg-white', 'shadow-sm', 'text-gray-900');
        calendarViewBtn.classList.add('text-gray-600');
    });

    calendarViewBtn.addEventListener('click', () => {
        listView.classList.add('hidden');
        calendarView.classList.remove('hidden');
        pagination.classList.add('hidden');
        
        calendarViewBtn.classList.add('bg-white', 'shadow-sm', 'text-gray-900');
        calendarViewBtn.classList.remove('text-gray-600');
        listViewBtn.classList.remove('bg-white', 'shadow-sm', 'text-gray-900');
        listViewBtn.classList.add('text-gray-600');
        
        // Initialize calendar when switching to calendar view
        initCalendar();
    });

    // ========== CALENDAR FUNCTIONALITY ==========
    
    // Agenda data from server
    const agendaEvents = [
        @foreach($agendas as $agenda)
        {
            id: {{ $agenda->id }},
            userId: {{ $agenda->user_id ?? 'null' }},
            title: {!! json_encode($agenda->title) !!},
            type: "{{ $agenda->type }}",
            start: "{{ $agenda->start_at->format('Y-m-d') }}",
            startTime: "{{ $agenda->start_at->format('H:i') }}",
            endTime: "{{ $agenda->end_at ? $agenda->end_at->format('H:i') : '' }}",
            startRaw: "{{ $agenda->start_at->format('Y-m-d\TH:i') }}",
            endRaw: "{{ $agenda->end_at ? $agenda->end_at->format('Y-m-d\TH:i') : '' }}",
            location: {!! json_encode($agenda->location ?? '') !!},
            description: {!! json_encode($agenda->description ?? '') !!},
            bidang: {!! json_encode($agenda->bidang ?? '') !!}
        },
        @endforeach
    ];
    
    // Calendar State
    let currentDate = new Date();
    let miniCalendarDate = new Date();
    let currentCalendarView = 'month';
    
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const monthNamesShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 
                             'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
    
    // Initialize Calendar
    function initCalendar() {
        renderCalendarTitle();
        renderCalendarGrid();
        renderMiniCalendar();
    }
    
    // Render Calendar Title
    function renderCalendarTitle() {
        const title = document.getElementById('calendar-title');
        if (title) {
            title.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
        }
    }
    
    // Render Main Calendar Grid
    function renderCalendarGrid() {
        const grid = document.getElementById('calendar-grid');
        if (!grid) return;
        
        grid.innerHTML = '';
        
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startDay = firstDay.getDay(); // 0 = Sunday
        const daysInMonth = lastDay.getDate();
        
        // Previous month days
        const prevMonthLastDay = new Date(year, month, 0).getDate();
        for (let i = startDay - 1; i >= 0; i--) {
            const dayNum = prevMonthLastDay - i;
            grid.appendChild(createCalendarCell(dayNum, true, null, year, month - 1));
        }
        
        // Current month days
        const today = new Date();
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const isToday = today.getFullYear() === year && today.getMonth() === month && today.getDate() === day;
            const dayEvents = agendaEvents.filter(e => e.start === dateStr);
            grid.appendChild(createCalendarCell(day, false, dayEvents, year, month, isToday));
        }
        
        // Next month days
        const totalCells = startDay + daysInMonth;
        const remainingCells = totalCells <= 35 ? 35 - totalCells : 42 - totalCells;
        for (let day = 1; day <= remainingCells; day++) {
            grid.appendChild(createCalendarCell(day, true, null, year, month + 1));
        }
    }
    
    // Create Calendar Cell
    function createCalendarCell(day, isOtherMonth, events, year, month, isToday = false) {
        const cell = document.createElement('div');
        cell.className = `min-h-[100px] p-2 border-b border-r border-gray-100 ${isOtherMonth ? 'bg-gray-50' : 'bg-white hover:bg-gray-50'} transition cursor-pointer`;
        
        // Day number
        const dayDiv = document.createElement('div');
        dayDiv.className = `text-sm font-bold mb-2 ${isOtherMonth ? 'text-gray-300' : (isToday ? '' : 'text-gray-700')}`;
        
        if (isToday) {
            dayDiv.innerHTML = `<span class="w-7 h-7 bg-blue-600 text-white rounded-full inline-flex items-center justify-center">${day}</span>`;
        } else {
            dayDiv.textContent = day;
        }
        cell.appendChild(dayDiv);
        
        // Events
        if (events && events.length > 0) {
            const eventsContainer = document.createElement('div');
            eventsContainer.className = 'space-y-1';
            
            events.slice(0, 3).forEach(event => {
                const eventDiv = document.createElement('div');
                let bgColor = 'bg-purple-500';
                if (event.type === 'UMUM') bgColor = 'bg-orange-500';
                if (event.type === 'OPERASIONAL') bgColor = 'bg-green-500';
                
                eventDiv.className = `${bgColor} text-white text-xs px-2 py-1 rounded truncate font-medium cursor-pointer hover:opacity-80 transition`;
                eventDiv.textContent = event.title;
                eventDiv.title = `${event.title} (${event.startTime}${event.endTime ? ' - ' + event.endTime : ''})`;
                eventDiv.onclick = (e) => {
                    e.stopPropagation();
                    openDetailModal(event);
                };
                eventsContainer.appendChild(eventDiv);
            });
            
            if (events.length > 3) {
                const moreDiv = document.createElement('div');
                moreDiv.className = 'text-xs text-gray-500 font-medium pl-2';
                moreDiv.textContent = `+${events.length - 3} lainnya`;
                eventsContainer.appendChild(moreDiv);
            }
            
            cell.appendChild(eventsContainer);
        }
        
        return cell;
    }
    
    // Render Mini Calendar
    function renderMiniCalendar() {
        const monthLabel = document.getElementById('mini-calendar-month');
        const grid = document.getElementById('mini-calendar-grid');
        
        if (!monthLabel || !grid) return;
        
        monthLabel.textContent = `${monthNamesShort[miniCalendarDate.getMonth()]} ${miniCalendarDate.getFullYear()}`;
        grid.innerHTML = '';
        
        const year = miniCalendarDate.getFullYear();
        const month = miniCalendarDate.getMonth();
        
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startDay = firstDay.getDay();
        const daysInMonth = lastDay.getDate();
        
        // Previous month
        const prevMonthLastDay = new Date(year, month, 0).getDate();
        for (let i = startDay - 1; i >= 0; i--) {
            const dayNum = prevMonthLastDay - i;
            const dayEl = document.createElement('div');
            dayEl.className = 'text-xs text-gray-300 py-1';
            dayEl.textContent = dayNum;
            grid.appendChild(dayEl);
        }
        
        // Current month
        const today = new Date();
        for (let day = 1; day <= daysInMonth; day++) {
            const dayEl = document.createElement('div');
            const isToday = today.getFullYear() === year && today.getMonth() === month && today.getDate() === day;
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const hasEvent = agendaEvents.some(e => e.start === dateStr);
            
            if (isToday) {
                dayEl.className = 'text-xs font-bold text-white bg-blue-600 rounded-full w-6 h-6 flex items-center justify-center mx-auto cursor-pointer';
            } else if (hasEvent) {
                dayEl.className = 'text-xs font-medium text-blue-600 py-1 cursor-pointer hover:bg-blue-50 rounded';
            } else {
                dayEl.className = 'text-xs font-medium text-gray-700 py-1 cursor-pointer hover:bg-gray-100 rounded';
            }
            
            dayEl.textContent = day;
            dayEl.onclick = () => selectDate(year, month, day);
            grid.appendChild(dayEl);
        }
        
        // Next month
        const totalCells = startDay + daysInMonth;
        const remainingCells = totalCells <= 35 ? 35 - totalCells : 42 - totalCells;
        for (let day = 1; day <= remainingCells; day++) {
            const dayEl = document.createElement('div');
            dayEl.className = 'text-xs text-gray-300 py-1';
            dayEl.textContent = day;
            grid.appendChild(dayEl);
        }
    }
    
    // Navigation Functions
    function prevMonth() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        miniCalendarDate = new Date(currentDate);
        initCalendar();
    }
    
    function nextMonth() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        miniCalendarDate = new Date(currentDate);
        initCalendar();
    }
    
    function prevMiniMonth() {
        miniCalendarDate.setMonth(miniCalendarDate.getMonth() - 1);
        renderMiniCalendar();
    }
    
    function nextMiniMonth() {
        miniCalendarDate.setMonth(miniCalendarDate.getMonth() + 1);
        renderMiniCalendar();
    }
    
    function goToToday() {
        currentDate = new Date();
        miniCalendarDate = new Date();
        initCalendar();
    }
    
    function selectDate(year, month, day) {
        currentDate = new Date(year, month, day);
        miniCalendarDate = new Date(year, month, day);
        initCalendar();
    }
    
    // View Buttons
    function setCalendarView(view) {
        currentCalendarView = view;
        
        const dayBtn = document.getElementById('day-view-btn');
        const weekBtn = document.getElementById('week-view-btn');
        const monthBtn = document.getElementById('month-view-btn');
        
        [dayBtn, weekBtn, monthBtn].forEach(btn => {
            if (btn) {
                btn.classList.remove('bg-white', 'shadow-sm', 'text-gray-900');
                btn.classList.add('text-gray-600');
            }
        });
        
        const activeBtn = document.getElementById(`${view}-view-btn`);
        if (activeBtn) {
            activeBtn.classList.add('bg-white', 'shadow-sm', 'text-gray-900');
            activeBtn.classList.remove('text-gray-600');
        }
        
        // For now, only month view is fully implemented
        renderCalendarGrid();
    }
    
    // Make functions global
    window.prevMonth = prevMonth;
    window.nextMonth = nextMonth;
    window.prevMiniMonth = prevMiniMonth;
    window.nextMiniMonth = nextMiniMonth;
    window.goToToday = goToToday;
    window.setCalendarView = setCalendarView;
    window.editFromDetail = editFromDetail;
    window.deleteFromDetail = deleteFromDetail;
    window.deleteAgenda = deleteAgenda;
    window.editAgenda = editAgenda;
    
    // Initialize calendar on page load (Calendar is default view)
    initCalendar();
</script>

@endsection
