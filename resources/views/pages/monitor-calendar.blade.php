@extends('layouts.app')

@section('content')
<div class="h-screen w-screen bg-slate-900 overflow-hidden flex flex-col p-4 relative">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6 px-4">
        <div>
            <h1 class="text-4xl font-extrabold text-white mb-2">Monitor Agenda Direksi</h1>
            <p class="text-slate-400 text-lg" id="clock">...</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                <span class="text-slate-300">Umum</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                <span class="text-slate-300">Dirut</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                <span class="text-slate-300">Operasional</span>
            </div>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="flex-1 bg-slate-800/50 rounded-3xl border border-slate-700/50 p-6 overflow-hidden flex flex-col shadow-2xl">
        <!-- Calendar Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="ph-fill ph-calendar-blank text-indigo-500"></i>
                <span id="calendar-title">...</span>
            </h2>
            <div class="flex gap-2">
                <button onclick="prevMonth()" class="p-3 hover:bg-slate-700 rounded-xl text-white transition"><i class="ph-bold ph-caret-left text-xl"></i></button>
                <button onclick="nextMonth()" class="p-3 hover:bg-slate-700 rounded-xl text-white transition"><i class="ph-bold ph-caret-right text-xl"></i></button>
            </div>
        </div>

        <!-- Grid Header -->
        <div class="grid grid-cols-7 gap-px mb-2 text-center">
            <div class="text-slate-400 font-bold uppercase tracking-wider py-2">Minggu</div>
            <div class="text-slate-400 font-bold uppercase tracking-wider py-2">Senin</div>
            <div class="text-slate-400 font-bold uppercase tracking-wider py-2">Selasa</div>
            <div class="text-slate-400 font-bold uppercase tracking-wider py-2">Rabu</div>
            <div class="text-slate-400 font-bold uppercase tracking-wider py-2">Kamis</div>
            <div class="text-slate-400 font-bold uppercase tracking-wider py-2">Jumat</div>
            <div class="text-slate-400 font-bold uppercase tracking-wider py-2">Sabtu</div>
        </div>

        <!-- Grid Body -->
        <div id="calendar-grid" class="grid grid-cols-7 gap-px bg-slate-700 flex-1 border border-slate-700 rounded-xl overflow-hidden">
            <!-- Dynamic Content -->
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    // Clock
    function updateClock() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
        document.getElementById('clock').textContent = now.toLocaleDateString('id-ID', options);
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Data
    const agendaEvents = [
        @foreach($agendas as $agenda)
        {
            id: {{ $agenda->id }},
            title: {!! json_encode($agenda->title) !!},
            type: "{{ $agenda->type }}",
            start: "{{ $agenda->start_at->format('Y-m-d') }}",
            startTime: "{{ $agenda->start_at->format('H:i') }}",
            startRaw: "{{ $agenda->start_at->format('Y-m-d\TH:i') }}",
            bidang: {!! json_encode($agenda->bidang ?? '') !!}
        },
        @endforeach
    ];

    let currentDate = new Date();
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    function initCalendar() {
        renderTitle();
        renderGrid();
    }

    function renderTitle() {
        document.getElementById('calendar-title').textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    }

    function renderGrid() {
        const grid = document.getElementById('calendar-grid');
        grid.innerHTML = '';
        
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startDay = firstDay.getDay(); 
        const daysInMonth = lastDay.getDate();
        
        // Pushing previous month empty slots
        for (let i = 0; i < startDay; i++) {
            const cell = document.createElement('div');
            cell.className = 'bg-slate-800/50 p-2 min-h-[100px] border-b border-r border-slate-700/50';
            grid.appendChild(cell);
        }

        // Days
        const today = new Date();
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const isToday = today.getFullYear() === year && today.getMonth() === month && today.getDate() === day;
            const dayEvents = agendaEvents.filter(e => e.start === dateStr);
            
            const cell = document.createElement('div');
            cell.className = `bg-slate-800 p-2 min-h-[100px] border-b border-r border-slate-700/50 relative hover:bg-slate-700/50 transition cursor-default`;
            
            // Date Number
            const dateNum = document.createElement('div');
            dateNum.className = `text-lg font-bold mb-2 ${isToday ? 'w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white shadow-lg shadow-indigo-500/50' : 'text-slate-300'}`;
            dateNum.textContent = day;
            cell.appendChild(dateNum);

            // Events
            const eventsContainer = document.createElement('div');
            eventsContainer.className = 'space-y-1.5 overflow-hidden';
            
            dayEvents.slice(0, 4).forEach(event => {
                const eventDiv = document.createElement('div');
                let bgColor = 'bg-purple-600'; // Default DIRUT
                if (event.type === 'UMUM') bgColor = 'bg-orange-500';
                if (event.type === 'OPERASIONAL') bgColor = 'bg-green-600';
                
                eventDiv.className = `${bgColor} text-white text-xs px-2 py-1.5 rounded shadow-sm font-medium truncate flex items-center gap-1.5`;
                eventDiv.innerHTML = `<span class="opacity-75 text-[10px]">${event.startTime}</span> <span>${event.title}</span>`;
                eventsContainer.appendChild(eventDiv);
            });
            
            if (dayEvents.length > 4) {
                 const more = document.createElement('div');
                 more.className = 'text-xs text-slate-400 font-medium pl-1';
                 more.textContent = `+ ${dayEvents.length - 4} lainnya`;
                 eventsContainer.appendChild(more);
            }

            cell.appendChild(eventsContainer);
            grid.appendChild(cell);
        }
        
        // Fill remaining slots
        const total = startDay + daysInMonth;
        const remaining = (7 - total % 7) % 7;
        for (let i = 0; i < remaining; i++) {
             const cell = document.createElement('div');
             cell.className = 'bg-slate-800/50 p-2 min-h-[100px] border-b border-r border-slate-700/50';
             grid.appendChild(cell);
        }
    }

    function prevMonth() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        initCalendar();
    }

    function nextMonth() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        initCalendar();
    }

    // Start
    initCalendar();
    
    // Auto refresh every 5 minutes to fetch new data
    setTimeout(() => window.location.reload(), 300000);

</script>

<!-- TTS Notification Script (Cache Busting) -->
<script>window.audioPath = "{{ asset('audio/notification.mp3') }}";</script>
<script src="{{ asset('js/tts-notification.js') }}?v={{ time() }}"></script>
<script>
    // Silent Audio Unlocker
    document.addEventListener('click', function() {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (AudioContext) {
            const ctx = new AudioContext();
            if (ctx.state === 'suspended') ctx.resume();
        }
    }, { once: true });

    // Notification Logic
    // Use sessionStorage so each tab notifies independently (Dashboard + Monitor both play)
    const notifKey = 'tiara_notified_session_' + new Date().toDateString(); // Reset daily
    let notifiedEvents = new Set(JSON.parse(sessionStorage.getItem(notifKey) || '[]'));
    
    function checkUpcomingEvents() {
        // console.log('Checking upcoming events...');
        const now = new Date();
        const leadTimeMins = 30; // Notify 30 mins before
        
        agendaEvents.forEach(event => {
            const start = new Date(event.startRaw);
            const diffMs = start - now;
            const diffMins = diffMs / 60000;
            
            // Trigger if within 15 mins AND not yet notified (and not passed by more than 1 min)
            if (diffMins > -1 && diffMins <= leadTimeMins && !notifiedEvents.has(event.id)) {
                 playTTSNotification("Hallo. Ini adalah pengingat dari Smart Agenda. Kegiatan Anda akan segera dimulai.");
                 notifiedEvents.add(event.id);
            }
        });
        sessionStorage.setItem(notifKey, JSON.stringify([...notifiedEvents]));
    }
    
    // Check every 1 minute
    setInterval(checkUpcomingEvents, 60000);
    
    // Initial check (delay 2s to allow interaction)
    setTimeout(checkUpcomingEvents, 2000);
</script>
@endsection
