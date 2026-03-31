@extends('layouts.nolana')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Calendar</h1>
            <p class="text-gray-500">Kelola dan lihat jadwal semua direksi</p>
        </div>
        <button class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-2.5 rounded-xl font-bold hover:from-purple-700 hover:to-indigo-700 transition flex items-center gap-2 shadow-lg shadow-purple-500/30">
            <i class="ph-bold ph-plus"></i>
            <span>New Event</span>
        </button>
    </div>

    <!-- Main Calendar Card (Dark Theme) -->
    <div class="bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 rounded-3xl shadow-2xl overflow-hidden border border-slate-700">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-0">
            
            <!-- Left: Calendar Grid -->
            <div class="lg:col-span-2 p-8">
                
                <!-- Calendar Header -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <h2 class="text-2xl font-bold text-white">Februari 2026</h2>
                        <button class="w-8 h-8 bg-slate-700 hover:bg-slate-600 rounded-lg flex items-center justify-center text-white transition">
                            <i class="ph-bold ph-caret-left"></i>
                        </button>
                        <button class="w-8 h-8 bg-slate-700 hover:bg-slate-600 rounded-lg flex items-center justify-center text-white transition">
                            <i class="ph-bold ph-caret-right"></i>
                        </button>
                    </div>
                    
                    <!-- Director Filter -->
                    <div class="flex items-center gap-2">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold shadow-lg hover:bg-blue-700 transition">
                            Direktur Utama
                        </button>
                        <button class="px-4 py-2 bg-slate-700 text-gray-300 rounded-lg text-sm font-medium hover:bg-slate-600 transition">
                            Dir. Umum
                        </button>
                        <button class="px-4 py-2 bg-slate-700 text-gray-300 rounded-lg text-sm font-medium hover:bg-slate-600 transition">
                            Dir. Operasional
                        </button>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="grid grid-cols-7 gap-1">
                    <!-- Day Headers -->
                    <div class="text-center py-3 text-xs font-bold text-gray-400 uppercase">Sun</div>
                    <div class="text-center py-3 text-xs font-bold text-gray-400 uppercase">Mon</div>
                    <div class="text-center py-3 text-xs font-bold text-gray-400 uppercase">Tue</div>
                    <div class="text-center py-3 text-xs font-bold text-gray-400 uppercase">Wed</div>
                    <div class="text-center py-3 text-xs font-bold text-gray-400 uppercase">Thu</div>
                    <div class="text-center py-3 text-xs font-bold text-gray-400 uppercase">Fri</div>
                    <div class="text-center py-3 text-xs font-bold text-gray-400 uppercase">Sat</div>

                    <!-- Previous Month Days -->
                    <div class="aspect-square bg-slate-800/50 rounded-lg p-2 text-gray-600 text-sm font-medium">30</div>
                    
                    <!-- Current Month Days -->
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition group">
                        <div class="text-white text-sm font-medium mb-1">1</div>
                    </div>
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition group">
                        <div class="text-white text-sm font-medium mb-1">2</div>
                    </div>
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition group">
                        <div class="text-white text-sm font-medium mb-1">3</div>
                    </div>
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition group">
                        <div class="text-white text-sm font-medium mb-1">4</div>
                    </div>
                    
                    <!-- Day 5 - Today with events -->
                    <div class="aspect-square bg-blue-600 rounded-lg p-2 cursor-pointer transition group relative overflow-hidden">
                        <div class="text-white text-sm font-bold mb-1">5</div>
                        <div class="space-y-0.5">
                            <div class="text-[8px] text-white/90 bg-blue-700 rounded px-1 py-0.5 truncate">Finance Meeting</div>
                            <div class="text-[8px] text-white/90 bg-blue-700 rounded px-1 py-0.5 truncate">Weekly Stand-up</div>
                        </div>
                    </div>
                    
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition group">
                        <div class="text-white text-sm font-medium mb-1">6</div>
                    </div>

                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition group">
                        <div class="text-white text-sm font-medium mb-1">7</div>
                    </div>
                    
                    <!-- Day 8 with events -->
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition group">
                        <div class="text-white text-sm font-medium mb-1">8</div>
                        <div class="space-y-0.5">
                            <div class="text-[8px] text-gray-300 bg-slate-600 rounded px-1 py-0.5 truncate">English Lesson</div>
                            <div class="text-[8px] text-gray-300 bg-slate-600 rounded px-1 py-0.5 truncate">Job Interview</div>
                        </div>
                    </div>

                    <!-- Day 9 with event -->
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition group">
                        <div class="text-white text-sm font-medium mb-1">9</div>
                        <div class="space-y-0.5">
                            <div class="text-[8px] text-gray-300 bg-slate-600 rounded px-1 py-0.5 truncate">Brainstorm Session</div>
                        </div>
                    </div>

                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition">
                        <div class="text-white text-sm font-medium mb-1">10</div>
                    </div>
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition">
                        <div class="text-white text-sm font-medium mb-1">11</div>
                    </div>
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition">
                        <div class="text-white text-sm font-medium mb-1">12</div>
                        <div class="text-[8px] text-gray-300 bg-slate-600 rounded px-1 py-0.5 truncate">Weekly Stand-up</div>
                    </div>
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition">
                        <div class="text-white text-sm font-medium mb-1">13</div>
                    </div>

                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition">
                        <div class="text-white text-sm font-medium mb-1">14</div>
                    </div>
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition">
                        <div class="text-white text-sm font-medium mb-1">15</div>
                        <div class="text-[8px] text-gray-300 bg-slate-600 rounded px-1 py-0.5 truncate">Marketing Review</div>
                    </div>
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition">
                        <div class="text-white text-sm font-medium mb-1">16</div>
                    </div>
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition">
                        <div class="text-white text-sm font-medium mb-1">17</div>
                    </div>
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition">
                        <div class="text-white text-sm font-medium mb-1">18</div>
                    </div>
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition">
                        <div class="text-white text-sm font-medium mb-1">19</div>
                        <div class="text-[8px] text-gray-300 bg-slate-600 rounded px-1 py-0.5 truncate">Marketing Review</div>
                    </div>
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition">
                        <div class="text-white text-sm font-medium mb-1">20</div>
                    </div>

                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition">
                        <div class="text-white text-sm font-medium mb-1">21</div>
                    </div>
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition">
                        <div class="text-white text-sm font-medium mb-1">22</div>
                    </div>
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition">
                        <div class="text-white text-sm font-medium mb-1">23</div>
                    </div>
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition">
                        <div class="text-white text-sm font-medium mb-1">24</div>
                    </div>
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition">
                        <div class="text-white text-sm font-medium mb-1">25</div>
                    </div>
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition">
                        <div class="text-white text-sm font-medium mb-1">26</div>
                    </div>
                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition">
                        <div class="text-white text-sm font-medium mb-1">27</div>
                    </div>

                    <div class="aspect-square bg-slate-700/30 rounded-lg p-2 hover:bg-slate-700/50 cursor-pointer transition">
                        <div class="text-white text-sm font-medium mb-1">28</div>
                    </div>
                </div>

            </div>

            <!-- Right: Event Details Panel -->
            <div class="lg:col-span-1 bg-slate-900/50 border-l border-slate-700 p-6">
                
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Scheduled</h3>
                    <span class="text-sm text-gray-400">5 February, 2026</span>
                </div>

                <!-- Event List -->
                <div class="space-y-4 overflow-y-auto max-h-[600px]">
                    
                    <!-- Event 1 -->
                    <div class="bg-slate-800 rounded-xl p-4 border-l-4 border-orange-500 hover:bg-slate-700 transition cursor-pointer">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-gray-400">09:00</span>
                            <span class="text-xs font-bold text-orange-400">45 min</span>
                        </div>
                        <h4 class="text-white font-bold mb-1">English Lesson</h4>
                        <p class="text-xs text-gray-400 mb-3">Online class with tutor</p>
                        <div class="flex -space-x-2">
                            <img src="https://ui-avatars.com/api/?name=Alex+W&background=667eea&color=fff" class="w-6 h-6 rounded-full border-2 border-slate-800" alt="">
                            <img src="https://ui-avatars.com/api/?name=Ivan+M&background=f56565&color=fff" class="w-6 h-6 rounded-full border-2 border-slate-800" alt="">
                        </div>
                    </div>

                    <!-- Event 2 -->
                    <div class="bg-slate-800 rounded-xl p-4 border-l-4 border-yellow-500 hover:bg-slate-700 transition cursor-pointer">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-gray-400">10:00</span>
                            <span class="text-xs font-bold text-yellow-400">1 hour</span>
                        </div>
                        <h4 class="text-white font-bold mb-1">Job Interview</h4>
                        <p class="text-xs text-gray-400 mb-3">Frontend Developer position</p>
                        <div class="flex items-center gap-2 text-xs text-gray-400">
                            <i class="ph-fill ph-video-camera text-green-400"></i>
                            <span>Meet Link</span>
                        </div>
                    </div>

                    <!-- Event 3 -->
                    <div class="bg-slate-800 rounded-xl p-4 border-l-4 border-blue-500 hover:bg-slate-700 transition cursor-pointer">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-gray-400">13:00</span>
                            <span class="text-xs font-bold text-blue-400">2 hours</span>
                        </div>
                        <h4 class="text-white font-bold mb-1">Team Sync Call</h4>
                        <p class="text-xs text-gray-400 mb-3">Weekly updates</p>
                        <div class="flex -space-x-2 mb-2">
                            <img src="https://ui-avatars.com/api/?name=Julia+K&background=10b981&color=fff" class="w-6 h-6 rounded-full border-2 border-slate-800" alt="">
                            <img src="https://ui-avatars.com/api/?name=Ivan+M&background=3b82f6&color=fff" class="w-6 h-6 rounded-full border-2 border-slate-800" alt="">
                            <div class="w-6 h-6 rounded-full border-2 border-slate-800 bg-slate-600 flex items-center justify-center text-[9px] text-white font-bold">+5</div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
@endsection
