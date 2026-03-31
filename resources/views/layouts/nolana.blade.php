<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tiara - Dashboard</title>

    <!-- CRITICAL: Dark Mode init BEFORE anything renders -->
    <script>
        (function() {
            if (localStorage.getItem('darkMode') === 'dark') {
                document.documentElement.classList.add('dark');
                document.documentElement.style.backgroundColor = '#0d1b1e';
            }
        })();
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .nolana-gradient {
            background: linear-gradient(135deg, #E0C3FC 0%, #8EC5FC 100%);
        }
        .sidebar-item-active {
            background-color: #111827;
            color: white;
        }
        .sidebar-item:hover:not(.sidebar-item-active) {
            background-color: #f3f4f6;
        }
    <style>
        /* Dark mode CSS - Teal/Cyan Theme */
        html.dark {
            color-scheme: dark;
            background-color: #0d1b1e !important;
        }
        
        html.dark body {
            background-color: #0d1b1e !important;
            color: #e2e8f0 !important;
            min-height: 100vh;
        }
        
        html.dark aside {
            background-color: #0a1416 !important;
            border-right-color: #1a3a3f !important;
        }
        
        html.dark main {
            background-color: #0d1b1e !important;
        }
        
        html.dark header {
            background-color: #0d1b1e !important;
        }
        
        /* Fix content wrapper and sections */
        html.dark .content-wrapper,
        html.dark section,
        html.dark article,
        html.dark div[class*="bg-[#f8f9fc]"],
        html.dark .bg-\[\#f8f9fc\] {
            background-color: #0d1b1e !important;
        }
        
        /* Fix all gray backgrounds */
        html.dark .bg-gray-50,
        html.dark .bg-gray-100 {
            background-color: #112428 !important;
        }
        
        html.dark .bg-white {
            background-color: #112428 !important;
        }
        
        html.dark .bg-gray-50 {
            background-color: #0a1416 !important;
        }
        
        html.dark .bg-gray-100 {
            background-color: #0f1e21 !important;
        }
        
        html.dark .text-gray-900 {
            color: #f1f5f9 !important;
        }
        
        html.dark .text-gray-800 {
            color: #e2e8f0 !important;
        }
        
        html.dark .text-gray-700 {
            color: #cbd5e1 !important;
        }
        
        html.dark .text-gray-600 {
            color: #94a3b8 !important;
        }
        
        html.dark .text-gray-500 {
            color: #64748b !important;
        }
        
        html.dark .text-gray-400 {
            color: #4a6670 !important;
        }
        
        html.dark .border-gray-100 {
            border-color: #1a3a3f !important;
        }
        
        html.dark .border-gray-200 {
            border-color: #1a3a3f !important;
        }
        
        html.dark .border-gray-300 {
            border-color: #234549 !important;
        }
        
        /* Input fields dark mode */
        html.dark input,
        html.dark select,
        html.dark textarea {
            background-color: #0a1416 !important;
            border-color: #1a3a3f !important;
            color: #e2e8f0 !important;
        }
        
        html.dark input::placeholder,
        html.dark textarea::placeholder {
            color: #4a6670 !important;
        }
        
        /* Cards and panels */
        html.dark .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.3) !important;
        }
        
        /* Accent colors for teal theme */
        html.dark .bg-purple-50,
        html.dark .bg-purple-100 {
            background-color: rgba(6, 182, 212, 0.15) !important;
        }
        
        html.dark .text-purple-600 {
            color: #22d3ee !important;
        }
        
        html.dark .bg-blue-50,
        html.dark .bg-blue-100 {
            background-color: rgba(6, 182, 212, 0.12) !important;
        }
        
        html.dark .bg-green-50,
        html.dark .bg-green-100 {
            background-color: rgba(20, 184, 166, 0.15) !important;
        }
        
        html.dark .bg-orange-50,
        html.dark .bg-orange-100 {
            background-color: rgba(251, 146, 60, 0.15) !important;
        }
        
        html.dark .bg-red-50,
        html.dark .bg-red-100 {
            background-color: rgba(239, 68, 68, 0.15) !important;
        }
        
        html.dark .bg-yellow-50,
        html.dark .bg-yellow-100 {
            background-color: rgba(234, 179, 8, 0.15) !important;
        }
        
        html.dark .bg-indigo-50,
        html.dark .bg-indigo-100 {
            background-color: rgba(99, 102, 241, 0.15) !important;
        }
        
        /* Sidebar active item dark mode */
        html.dark .sidebar-item-active {
            background: linear-gradient(to right, #134e4a, #0f3b38) !important;
            color: #5eead4 !important;
            border-left: 3px solid #14b8a6 !important;
        }
        
        html.dark .sidebar-item:hover:not(.sidebar-item-active) {
            background-color: #0f2225 !important;
        }
        
        /* Table row hover */
        html.dark tr:hover {
            background-color: #0f2225 !important;
        }
        
        /* Calendar specific dark mode */
        html.dark .rounded-2xl,
        html.dark .rounded-xl,
        html.dark .rounded-lg {
            border-color: #1a3a3f !important;
        }
        
        /* Fix for any white backgrounds */
        html.dark [class*="bg-white"],
        html.dark .glass-panel {
            background-color: #112428 !important;
        }
        
        /* Card borders in dark mode */
        html.dark [class*="border-gray"] {
            border-color: #1a3a3f !important;
        }
        
        /* Activity panel, agenda items */
        html.dark .divide-gray-100 > * {
            border-color: #1a3a3f !important;
        }
        
        /* Agenda time badge */
        html.dark .bg-purple-600 {
            background-color: #0d9488 !important;
        }
        
        /* Hover states */
        html.dark .hover\:bg-gray-50:hover {
            background-color: #0f2225 !important;
        }
        
        html.dark .hover\:bg-purple-50:hover {
            background-color: rgba(20, 184, 166, 0.15) !important;
        }
        
        /* Akses Cepat cards */
        html.dark .group:hover {
            background-color: #0f2225 !important;
        }
        
        /* Modal dark mode */
        html.dark [class*="bg-gray-900/50"],
        html.dark [class*="bg-black/50"] {
            background-color: rgba(10, 20, 22, 0.85) !important;
        }
        
        /* Text colors for better contrast */
        html.dark p,
        html.dark span,
        html.dark h1,
        html.dark h2,
        html.dark h3,
        html.dark h4,
        html.dark h5,
        html.dark h6 {
            color: inherit;
        }
        
        /* Fix stat card icon backgrounds */
        html.dark .bg-blue-100 i,
        html.dark .bg-purple-100 i,
        html.dark .bg-green-100 i,
        html.dark .bg-orange-100 i {
            color: inherit;
        }
        
        /* Reminder badges */
        html.dark .bg-red-500 {
            background-color: #dc2626 !important;
        }
        
        html.dark .bg-yellow-500 {
            background-color: #eab308 !important;
        }
        
        html.dark .bg-green-500 {
            background-color: #22c55e !important;
        }
        
        /* Scrollbar dark mode */
        html.dark ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        html.dark ::-webkit-scrollbar-track {
            background: #0a1416;
        }
        
        html.dark ::-webkit-scrollbar-thumb {
            background: #1a3a3f;
            border-radius: 4px;
        }
        
        html.dark ::-webkit-scrollbar-thumb:hover {
            background: #234549;
        }
        
        /* Fix remaining white text */
        html.dark .text-white {
            color: #ffffff !important;
        }
        
        /* Fix calendar day cells */
        html.dark td,
        html.dark th {
            background-color: transparent !important;
            color: #e2e8f0 !important;
        }
        
        /* Fix any remaining boxed elements */
        html.dark div[class*="shadow"] {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3), 0 1px 2px 0 rgba(0, 0, 0, 0.2) !important;
        }
    </style>
</head>
<body class="flex text-gray-800 dark:text-gray-100 antialiased min-h-screen bg-[#f8f9fc] dark:bg-gray-900 transition-colors duration-200 relative">

    <!-- Sidebar -->
    <aside id="main-sidebar" class="w-64 bg-white dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700 flex-shrink-0 flex flex-col h-screen py-6 pr-2 pl-4 hidden md:flex fixed left-0 top-0 overflow-y-auto transition-colors duration-200 z-50">
        <!-- Logo -->
        <div class="flex items-center gap-3 px-2 mb-8">
            <img src="{{ asset('pdam-logo.png') }}" alt="Logo PDAM" class="w-14 h-14 object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="w-14 h-14 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl items-center justify-center text-white font-bold text-2xl shadow-lg shadow-purple-200 hidden">
                <i class="ph-fill ph-sparkle"></i>
            </div>
            <div>
                <div class="font-bold text-lg tracking-tight leading-tight text-gray-900 dark:text-white">Tiara Smart Assistant</div>
                <div class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mt-0.5">Smart Secretary v2.0</div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex-1 overflow-y-auto space-y-1 px-2">
            
            <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'sidebar-item-active' : '' }} w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition group relative overflow-hidden {{ request()->routeIs('dashboard') ? 'shadow-sm shadow-purple-900/10' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                 <!-- gradient overlay for active -->
                 @if(request()->routeIs('dashboard'))
                 <div class="absolute inset-0 bg-gradient-to-r from-gray-800 to-gray-900 pointer-events-none"></div>
                 @endif
                <i class="ph-fill ph-drop text-lg relative z-10 {{ request()->routeIs('dashboard') ? 'text-blue-400' : 'text-gray-400 group-hover:text-blue-600' }} transition"></i>
                <span class="relative z-10">Dashboard</span>
            </a>

            <a href="{{ route('command-center') }}" class="sidebar-item {{ request()->routeIs('command-center') ? 'sidebar-item-active' : '' }} w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition group relative overflow-hidden {{ request()->routeIs('command-center') ? 'shadow-sm shadow-purple-900/10' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                @if(request()->routeIs('command-center'))
                <div class="absolute inset-0 bg-gradient-to-r from-gray-800 to-gray-900 pointer-events-none"></div>
                @endif
                <i class="ph-bold ph-broadcast text-lg relative z-10 {{ request()->routeIs('command-center') ? 'text-red-400' : 'text-gray-400 group-hover:text-red-600' }} transition"></i>
                <span class="relative z-10">Layanan Gangguan</span>
            </a>

            <a href="{{ route('statistics') }}" class="sidebar-item {{ request()->routeIs('statistics') ? 'sidebar-item-active' : '' }} w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition group relative overflow-hidden {{ request()->routeIs('statistics') ? 'shadow-sm shadow-purple-900/10' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                @if(request()->routeIs('statistics'))
                <div class="absolute inset-0 bg-gradient-to-r from-gray-800 to-gray-900 pointer-events-none"></div>
                @endif
                <i class="ph-bold ph-chart-bar text-lg relative z-10 {{ request()->routeIs('statistics') ? 'text-cyan-400' : 'text-gray-400 group-hover:text-cyan-600' }} transition"></i>
                <span class="relative z-10">Statistik & Analisis</span>
            </a>

            <a href="{{ route('assignment.index') }}" class="sidebar-item {{ request()->routeIs('assignment.*') ? 'sidebar-item-active' : '' }} w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition group relative overflow-hidden {{ request()->routeIs('assignment.*') ? 'shadow-sm shadow-purple-900/10' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                @if(request()->routeIs('assignment.*'))
                <div class="absolute inset-0 bg-gradient-to-r from-gray-800 to-gray-900 pointer-events-none"></div>
                @endif
                <i class="ph-bold ph-clipboard-text text-lg relative z-10 {{ request()->routeIs('assignment.*') ? 'text-orange-400' : 'text-gray-400 group-hover:text-orange-600' }} transition"></i>
                <span class="relative z-10">Manajemen Tugas</span>
            </a>
            <a href="{{ route('agenda.index') }}" class="sidebar-item {{ request()->routeIs('agenda.*') ? 'sidebar-item-active' : '' }} w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition group relative overflow-hidden {{ request()->routeIs('agenda.*') ? 'shadow-sm shadow-purple-900/10' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                @if(request()->routeIs('agenda.*'))
                <div class="absolute inset-0 bg-gradient-to-r from-gray-800 to-gray-900 pointer-events-none"></div>
                @endif
                <i class="ph-bold ph-calendar-check text-lg relative z-10 {{ request()->routeIs('agenda.*') ? 'text-purple-400' : 'text-gray-400 group-hover:text-purple-600' }} transition"></i>
                <span class="relative z-10">Agenda Direksi</span>
            </a>
            
            <a href="{{ route('dokumen.index') }}" class="sidebar-item {{ request()->routeIs('dokumen.*') ? 'sidebar-item-active' : '' }} w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition group relative overflow-hidden {{ request()->routeIs('dokumen.*') ? 'shadow-sm shadow-purple-900/10' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                @if(request()->routeIs('dokumen.*'))
                <div class="absolute inset-0 bg-gradient-to-r from-gray-800 to-gray-900 pointer-events-none"></div>
                @endif
                <i class="ph-bold ph-file-text text-lg relative z-10 {{ request()->routeIs('dokumen.*') ? 'text-purple-400' : 'text-gray-400 group-hover:text-purple-600' }} transition"></i>
                <span class="relative z-10">Dokumen</span>
            </a>
            
            <a href="{{ route('notulen.index') }}" class="sidebar-item {{ request()->routeIs('notulen.*') ? 'sidebar-item-active' : '' }} w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition group relative overflow-hidden {{ request()->routeIs('notulen.*') ? 'shadow-sm shadow-purple-900/10' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                @if(request()->routeIs('notulen.*'))
                <div class="absolute inset-0 bg-gradient-to-r from-gray-800 to-gray-900 pointer-events-none"></div>
                @endif
                <i class="ph-bold ph-notebook text-lg relative z-10 {{ request()->routeIs('notulen.*') ? 'text-purple-400' : 'text-gray-400 group-hover:text-purple-600' }} transition"></i>
                <span class="relative z-10">Notulen</span>
            </a>

            <a href="{{ route('settings') }}" class="sidebar-item {{ request()->routeIs('settings') ? 'sidebar-item-active' : '' }} w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition group relative overflow-hidden {{ request()->routeIs('settings') ? 'shadow-sm shadow-purple-900/10' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                @if(request()->routeIs('settings'))
                <div class="absolute inset-0 bg-gradient-to-r from-gray-800 to-gray-900 pointer-events-none"></div>
                @endif
                <i class="ph-bold ph-gear text-lg relative z-10 {{ request()->routeIs('settings') ? 'text-purple-400' : 'text-gray-400 group-hover:text-purple-600' }} transition"></i>
                <span class="relative z-10">Setting</span>
            </a>

            @if(auth()->check() && auth()->user()->role === 'superadmin')
            <a href="{{ route('users.index') }}" class="sidebar-item {{ request()->routeIs('users.*') ? 'sidebar-item-active' : '' }} w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition group relative overflow-hidden {{ request()->routeIs('users.*') ? 'shadow-sm shadow-purple-900/10' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                @if(request()->routeIs('users.*'))
                <div class="absolute inset-0 bg-gradient-to-r from-gray-800 to-gray-900 pointer-events-none"></div>
                @endif
                <i class="ph-bold ph-users-three text-lg relative z-10 {{ request()->routeIs('users.*') ? 'text-violet-400' : 'text-gray-400 group-hover:text-violet-600' }} transition"></i>
                <span class="relative z-10">Kelola Pengguna</span>
            </a>
            @endif

        </div>


        <!-- Footer User -->
        <div class="mt-auto px-2 pb-2 space-y-2">
            <!-- Current User Info -->
            @auth
            <div class="flex items-center gap-3 px-3 py-2">
                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center text-white text-xs font-bold uppercase">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->username }}</p>
                </div>
            </div>
            
            <!-- Logout Button -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-500 rounded-xl hover:bg-red-50 hover:text-red-600 transition border border-transparent hover:border-red-200 group">
                    <i class="ph ph-sign-out text-lg text-gray-400 group-hover:text-red-500 transition"></i>
                    <span class="text-xs font-semibold">Keluar</span>
                </button>
            </form>
            @endauth
        </div>

    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0 bg-[#f8f9fc] dark:bg-gray-900 relative transition-colors duration-200 md:ml-64 min-h-screen">
        
        <!-- Header -->
        <header class="h-20 flex items-center justify-between px-8 py-4 bg-[#f8f9fc] z-20 sticky top-0">
            <!-- Search -->
            <div class="hidden md:block relative w-96 group">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-purple-600 transition text-lg"></i>
                <input type="text" placeholder="Search tasks, projects, or users..." 
                    class="w-full bg-white pl-11 pr-4 h-11 rounded-full text-sm font-medium border border-transparent shadow-[0_2px_10px_rgba(0,0,0,0.03)] focus:shadow-[0_4px_20px_rgba(124,58,237,0.1)] focus:border-purple-200 outline-none transition-all placeholder-gray-400"
                >
            </div>
            
            <!-- Mobile Toggle -->
            <button id="mobile-sidebar-btn" class="md:hidden p-2 rounded-lg bg-white border border-gray-100 shadow-sm">
                <i class="ph ph-list text-xl"></i>
            </button>

            <!-- Right Actions -->
            <div class="flex items-center gap-3 sm:gap-5">
                <!-- Theme Toggle -->
                <div class="flex items-center bg-white rounded-full p-1 shadow-[0_2px_8px_rgba(0,0,0,0.04)] border border-gray-100/50">
                    <button id="dark-mode-btn" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-purple-600 transition">
                         <i class="ph ph-moon text-lg"></i>
                    </button>
                    <button id="light-mode-btn" class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-50 to-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm border border-indigo-100">
                        <i class="ph-fill ph-sun text-lg"></i>
                    </button>
                </div>

                <!-- Project Selector -->
                <div class="hidden sm:flex items-center gap-2 bg-white pl-1 pr-3 py-1 rounded-full border border-gray-100 shadow-sm cursor-pointer hover:border-gray-200 transition group">
                   <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 group-hover:bg-gray-200 transition">
                        <i class="ph-fill ph-users"></i>
                   </div>
                    <span class="text-sm font-bold text-gray-700">Tiara Team</span>
                    <i class="ph ph-caret-down text-xs text-gray-400 ml-1"></i>
                </div>
                
                <!-- Notification Bell + Dropdown -->
                <div class="relative" id="notifWrapper">
                    <button onclick="document.getElementById('notifDropdown').classList.toggle('hidden'); event.stopPropagation();" class="relative w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer">
                        <i class="ph ph-bell text-xl text-gray-600 dark:text-gray-300"></i>
                        @if(isset($notifCount) && $notifCount > 0)
                        <span class="absolute top-1 right-1 min-w-[18px] h-[18px] px-1 flex items-center justify-center text-[10px] font-black text-white bg-red-500 rounded-full border-2 border-[#f8f9fc] dark:border-gray-800 shadow-sm animate-pulse">{{ $notifCount > 9 ? '9+' : $notifCount }}</span>
                        @else
                        <span class="absolute top-2 right-2 w-2 h-2 bg-gray-300 rounded-full border-2 border-[#f8f9fc] dark:border-gray-800"></span>
                        @endif
                    </button>

                    <!-- Dropdown Panel -->
                    <div id="notifDropdown" class="hidden absolute right-0 top-12 w-80 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden" onclick="event.stopPropagation()">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                            <div>
                                <h3 class="text-sm font-extrabold text-gray-900 dark:text-white">Notifikasi</h3>
                                <p class="text-[10px] text-gray-400 mt-0.5">Update aktivitas terbaru</p>
                            </div>
                            @if(isset($notifCount) && $notifCount > 0)
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400">{{ $notifCount }} baru</span>
                            @endif
                        </div>
                        <div class="max-h-72 overflow-y-auto divide-y divide-gray-50 dark:divide-gray-700/50">
                            @if(isset($recentActivities) && $recentActivities->count() > 0)
                                @foreach($recentActivities->take(5) as $act)
                                <div class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition cursor-pointer">
                                    <div class="w-7 h-7 bg-{{ $act['color'] }}-50 dark:bg-{{ $act['color'] }}-500/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="ph-fill {{ $act['icon'] }} text-{{ $act['color'] }}-500 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[11px] font-bold text-gray-700 dark:text-gray-300 leading-relaxed">{{ $act['text'] }}</p>
                                        <p class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5 font-medium">{{ $act['time']->diffForHumans() }} · {{ $act['user'] }}</p>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="px-4 py-8 text-center">
                                    <i class="ph ph-bell-slash text-2xl text-gray-300 dark:text-gray-600"></i>
                                    <p class="text-xs text-gray-400 mt-2">Belum ada notifikasi</p>
                                </div>
                            @endif
                        </div>
                        <div class="px-4 py-2.5 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                            <a href="{{ route('statistics') }}" class="block text-center text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 transition">Lihat semua aktivitas →</a>
                        </div>
                    </div>
                </div>

                <!-- Profile -->
                <div class="w-10 h-10 rounded-full p-0.5 border-2 border-purple-100 bg-white cursor-pointer relative shadow-sm hover:scale-105 transition">
                    <img src="https://ui-avatars.com/api/?name=Ty+Zamkow&background=random&color=fff" class="w-full h-full rounded-full object-cover">
                    <!-- Status dot -->
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="fixed top-24 right-8 z-50 space-y-2" id="flash-messages">
            @if(session('success'))
            <div class="bg-green-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 animate-slideIn max-w-md">
                <i class="ph-fill ph-check-circle text-2xl"></i>
                <div class="flex-1">
                    <p class="font-bold">Success!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="hover:bg-green-600 rounded-lg p-1 transition">
                    <i class="ph-bold ph-x"></i>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 animate-slideIn max-w-md">
                <i class="ph-fill ph-warning-circle text-2xl"></i>
                <div class="flex-1">
                    <p class="font-bold">Error!</p>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="hover:bg-red-600 rounded-lg p-1 transition">
                    <i class="ph-bold ph-x"></i>
                </button>
            </div>
            @endif

            @if(session('info'))
            <div class="bg-blue-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 animate-slideIn max-w-md">
                <i class="ph-fill ph-info text-2xl"></i>
                <div class="flex-1">
                    <p class="font-bold">Info</p>
                    <p class="text-sm">{{ session('info') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="hover:bg-blue-600 rounded-lg p-1 transition">
                    <i class="ph-bold ph-x"></i>
                </button>
            </div>
            @endif
        </div>

        <!-- Content Scrollable Area -->
        <div class="px-4 sm:px-8 pb-8 pt-2">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </div>

    </main>

    <!-- Mobile Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-gray-900/50 z-40 hidden md:hidden backdrop-blur-sm transition-opacity"></div>

    <!-- Scripts stack -->
    @stack('scripts')
    
    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarBtn = document.getElementById('mobile-sidebar-btn');
            const sidebar = document.getElementById('main-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            
            // Toggle Sidebar
            if(sidebarBtn) {
                sidebarBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('hidden');
                    sidebar.classList.toggle('flex'); // Ensure it becomes flex container
                    backdrop.classList.toggle('hidden');
                });
            }
            
            // Close on backdrop click
            if(backdrop) {
                backdrop.addEventListener('click', () => {
                    sidebar.classList.add('hidden');
                    sidebar.classList.remove('flex');
                    backdrop.classList.add('hidden');
                });
            }
        });

        // Dark Mode Logic
        const darkModeBtn = document.getElementById('dark-mode-btn');
        const lightModeBtn = document.getElementById('light-mode-btn');
        const htmlElement = document.documentElement;
        
        // ... (rest of dark mode logic is fine, let's keep it here or just append the sidebar logic before it)
    </script>

    <!-- Dark Mode Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
             // ... existing dark mode code ...
            const darkModeBtn = document.getElementById('dark-mode-btn');
            const lightModeBtn = document.getElementById('light-mode-btn');
            const htmlElement = document.documentElement;
            
            // Link existing logic...
            const currentTheme = localStorage.getItem('theme') || 'light';
            
            function updateTheme(theme) {
                if (theme === 'dark') {
                    htmlElement.classList.add('dark');
                    document.body.classList.add('dark:bg-gray-900', 'dark:text-white');
                    if(darkModeBtn) {
                        darkModeBtn.classList.add('bg-gradient-to-br', 'from-purple-50', 'to-indigo-50', 'text-indigo-600', 'shadow-sm', 'border', 'border-indigo-100');
                        darkModeBtn.classList.remove('text-gray-400');
                    }
                    if(lightModeBtn) {
                        lightModeBtn.classList.remove('bg-gradient-to-br', 'from-purple-50', 'to-indigo-50', 'text-indigo-600', 'shadow-sm', 'border', 'border-indigo-100');
                        lightModeBtn.classList.add('text-gray-400');
                    }
                } else {
                    htmlElement.classList.remove('dark');
                    document.body.classList.remove('dark:bg-gray-900', 'dark:text-white');
                    if(lightModeBtn) {
                        lightModeBtn.classList.add('bg-gradient-to-br', 'from-purple-50', 'to-indigo-50', 'text-indigo-600', 'shadow-sm', 'border', 'border-indigo-100');
                        lightModeBtn.classList.remove('text-gray-400');
                    }
                    if(darkModeBtn) {
                        darkModeBtn.classList.remove('bg-gradient-to-br', 'from-purple-50', 'to-indigo-50', 'text-indigo-600', 'shadow-sm', 'border', 'border-indigo-100');
                        darkModeBtn.classList.add('text-gray-400');
                    }
                }
            }
            
            updateTheme(currentTheme);
            
            if(darkModeBtn) darkModeBtn.addEventListener('click', () => {
                localStorage.setItem('theme', 'dark');
                updateTheme('dark');
            });
            
            if(lightModeBtn) lightModeBtn.addEventListener('click', () => {
                localStorage.setItem('theme', 'light');
                updateTheme('light');
            });
        });
    </script>
    <!-- TTS Global Notification (Cache Busting) -->
    <script>window.audioPath = "{{ asset('audio/notification.mp3') }}";</script>
    <script src="{{ asset('js/tts-notification.js') }}?v={{ time() }}"></script>
    <script>
        // Silent Audio Unlocker on first interaction
        document.addEventListener('click', function() {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                const ctx = new AudioContext();
                if (ctx.state === 'suspended') ctx.resume();
            }
        }, { once: true });

        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide overlay if already interacted? (Optional logic, sticking to mandatory for now)
            
            const notifKey = 'tiara_notified_session_' + new Date().toDateString();
            let notifiedSet = new Set(JSON.parse(sessionStorage.getItem(notifKey) || '[]'));
            
            function checkGlobalNotifications() {
                // console.log('Checking dashboard notifications...'); 
                fetch('{{ route("api.upcoming") }}')
                    .then(res => res.json())
                    .then(events => {
                        // console.log('API Response:', events);
                        if (!events || events.length === 0) return;
                        
                        events.forEach(event => {
                            if (!notifiedSet.has(event.id)) {
                                 console.log('Triggering notification for:', event.title);
                                 playTTSNotification("Hallo. Ini adalah pengingat dari Smart Agenda. Kegiatan Anda akan segera dimulai.");
                                 notifiedSet.add(event.id);
                            }
                        });
                        // Save processed IDs
                        sessionStorage.setItem(notifKey, JSON.stringify([...notifiedSet]));
                    })
                    .catch(err => console.error('Notif Check Error:', err));
            }
            
            // Initial check delay 3s
            setTimeout(checkGlobalNotifications, 3000);
            // Check every minute
            setInterval(checkGlobalNotifications, 60000);

            // Close notification dropdown on click outside
            document.addEventListener('click', function(e) {
                const dd = document.getElementById('notifDropdown');
                const wrap = document.getElementById('notifWrapper');
                if (dd && wrap && !wrap.contains(e.target)) {
                    dd.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>
