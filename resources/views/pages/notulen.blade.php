@extends('layouts.nolana')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2">Notulen Rapat</h1>
            <p class="text-gray-500 dark:text-gray-400">Kelola semua dokumen dan notulen rapat</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-6 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl font-bold text-gray-700 dark:text-gray-300 hover:border-purple-600 hover:text-purple-600 transition flex items-center gap-2">
                <i class="ph-bold ph-funnel"></i>
                <span>Filter</span>
            </button>
            <button onclick="openCreateModal()" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:from-purple-700 hover:to-indigo-700 transition flex items-center gap-2 shadow-lg shadow-purple-500/30">
                <i class="ph-bold ph-plus"></i>
                <span>Tambah Notulen</span>
            </button>
        </div>
    </div>

    <!-- Tab Filters (Signpaper-style) -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="flex items-center px-6 py-4 border-b border-gray-100 dark:border-gray-700 overflow-x-auto gap-6">
            <button class="flex items-center gap-2 px-4 py-2 bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 rounded-lg font-bold text-sm whitespace-nowrap border-2 border-purple-200 dark:border-purple-800">
                <i class="ph-fill ph-files"></i>
                <span>All Documents</span>
                <span class="ml-1 px-2 py-0.5 bg-purple-600 text-white rounded-full text-xs">24</span>
            </button>
            <button class="flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg font-medium text-sm whitespace-nowrap transition">
                <i class="ph ph-file-dashed"></i>
                <span>Draft</span>
            </button>
            <button class="flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg font-medium text-sm whitespace-nowrap transition">
                <i class="ph ph-eye"></i>
                <span>Viewed</span>
            </button>
            <button class="flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg font-medium text-sm whitespace-nowrap transition">
                <i class="ph ph-check-circle"></i>
                <span>Completed</span>
            </button>
            <button class="flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg font-medium text-sm whitespace-nowrap transition">
                <i class="ph ph-hourglass"></i>
                <span>For Approval</span>
            </button>
        </div>

        <!-- Search & Sort -->
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-2 px-4 py-2 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg">
                <span class="text-sm text-purple-600 dark:text-purple-400 font-medium">Sort By: Last Updated</span>
                <button class="w-5 h-5 bg-purple-600 rounded flex items-center justify-center hover:bg-purple-700 transition">
                    <i class="ph-bold ph-x text-white text-xs"></i>
                </button>
            </div>
            <select class="px-4 py-2 border border-gray-200 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-200 rounded-lg font-medium text-sm">
                <option>Name</option>
                <option>Date</option>
                <option>Status</option>
            </select>
            <select class="px-4 py-2 border border-gray-200 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-200 rounded-lg font-medium text-sm">
                <option>Date</option>
                <option>This Week</option>
                <option>This Month</option>
            </select>
            <button class="px-4 py-2 border border-purple-600 text-purple-600 dark:text-purple-400 dark:border-purple-500 rounded-lg font-bold text-sm hover:bg-purple-50 dark:hover:bg-purple-900/20 transition flex items-center gap-2 ml-auto">
                <i class="ph-bold ph-funnel-simple"></i>
                <span>More Filters</span>
            </button>
        </div>
    </div>

    <!-- Notulen List -->
    <div class="grid grid-cols-1 gap-6">
        
        @forelse($notulens as $notulen)
        <!-- Notulen Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-lg transition group cursor-pointer">
            <div class="flex gap-6 p-6">
                
                <!-- Left: Preview Thumbnail -->
                <div class="flex-shrink-0">
                    <div class="w-48 h-32 bg-gradient-to-br from-purple-100 to-indigo-100 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-xl flex items-center justify-center overflow-hidden relative border border-purple-200 dark:border-purple-800">
                        <div class="absolute inset-0 bg-black/5"></div>
                        <div class="relative z-10 text-center">
                            <i class="ph-bold ph-clipboard-text text-4xl text-purple-600 dark:text-purple-400 mb-2"></i>
                            <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">Notulen</p>
                        </div>
                    </div>
                </div>

                <!-- Right: Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $notulen->title }}</h3>
                                @if($notulen->status == 'draft')
                                    <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs font-bold rounded-full">Draft</span>
                                @elseif($notulen->status == 'approved')
                                    <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 text-xs font-bold rounded-full">For Approval</span>
                                @else
                                    <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-bold rounded-full">Completed</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400 mb-3">
                                <span class="flex items-center gap-1">
                                    <i class="ph ph-calendar-blank"></i>
                                    {{ $notulen->meeting_date->translatedFormat('D, d M Y') }}
                                </span>
                                @if($notulen->duration)
                                <span class="flex items-center gap-1">
                                    <i class="ph ph-clock"></i>
                                    {{ $notulen->duration }} menit
                                </span>
                                @endif
                                @if($notulen->participants_count > 0)
                                <span class="flex items-center gap-1">
                                    <i class="ph ph-users"></i>
                                    {{ $notulen->participants_count }} Peserta
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <form action="{{ route('notulen.destroy', $notulen) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus notulen ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center justify-center transition">
                                    <i class="ph ph-trash text-lg text-red-500"></i>
                                </button>
                            </form>
                            <a href="{{ route('notulen.edit', $notulen) }}" class="w-9 h-9 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition">
                                <i class="ph ph-pencil-simple text-lg text-gray-600 dark:text-gray-400"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Summary Preview -->
                    @if($notulen->overview)
                    <div class="mb-4">
                        <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                            <i class="ph-bold ph-list-bullets text-purple-600 dark:text-purple-400"></i>
                            Overview
                        </h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed line-clamp-2">
                            {{ Str::limit($notulen->overview, 200) }}
                        </p>
                    </div>
                    @endif

                    <!-- Tags & Actions -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-2">
                            @if($notulen->summary)
                            <span class="px-3 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-xs font-bold rounded-full">
                                Summary
                            </span>
                            @endif
                            @if($notulen->transcript)
                            <span class="px-3 py-1 bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 text-xs font-bold rounded-full">
                                Transcript
                            </span>
                            @endif
                            @if($notulen->video_url)
                            <span class="px-3 py-1 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 text-xs font-bold rounded-full">
                                Video
                            </span>
                            @endif
                        </div>
                        <a href="{{ route('notulen.show', $notulen) }}" class="px-5 py-2 bg-purple-600 text-white rounded-lg font-bold hover:bg-purple-700 transition text-sm flex items-center gap-2">
                            <i class="ph-bold ph-eye"></i>
                            <span>View Details</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
            <div class="w-20 h-20 bg-purple-100 dark:bg-purple-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ph-bold ph-clipboard-text text-4xl text-purple-600 dark:text-purple-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Belum ada notulen</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Mulai buat notulen rapat pertama Anda</p>
            <button onclick="openCreateModal()" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:from-purple-700 hover:to-indigo-700 transition inline-flex items-center gap-2 shadow-lg shadow-purple-500/30">
                <i class="ph-bold ph-plus"></i>
                <span>Tambah Notulen</span>
            </button>
        </div>
        @endforelse

    </div>

    <!-- Pagination -->
    @if($notulens->hasPages())
    <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Menampilkan <span class="font-bold">{{ $notulens->firstItem() }}-{{ $notulens->lastItem() }}</span> dari <span class="font-bold">{{ $notulens->total() }}</span> notulen
        </p>
        <div class="flex items-center gap-2">
            {{ $notulens->links() }}
        </div>
    </div>
    @endif

</div>

<!-- Create Notulen Modal -->
<div id="createModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 animate-fadeIn">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto transform transition-all animate-slideUp">
        
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 px-8 py-6 flex items-center justify-between z-10">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Notulen Rapat</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Buat catatan rapat baru dengan summary dan transcript</p>
            </div>
            <button onclick="closeCreateModal()" class="w-10 h-10 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition">
                <i class="ph-bold ph-x text-xl text-gray-600 dark:text-gray-400"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form action="{{ route('notulen.store') }}" method="POST" class="p-8 space-y-6">
            @csrf

            <!-- Meeting Title -->
            <div>
                <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                    Meeting Title <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    required 
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition"
                    placeholder="e.g., Weekly Development Sync"
                >
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Meeting Date & Duration -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                        Meeting Date <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        name="meeting_date" 
                        required 
                        class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition"
                    >
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                        Duration (minutes)
                    </label>
                    <input 
                        type="number" 
                        name="duration" 
                        min="1"
                        placeholder="e.g., 60"
                        class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition"
                    >
                </div>
            </div>

            <!-- Participants -->
            <div>
                <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                    Participants
                </label>
                <textarea 
                    name="participants" 
                    rows="2"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition resize-none"
                    placeholder="e.g., John Doe, Jane Smith, Mike Johnson"
                ></textarea>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pisahkan dengan koma</p>
            </div>

            <!-- Overview -->
            <div>
                <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                    Overview <span class="text-red-500">*</span>
                </label>
                <textarea 
                    name="overview" 
                    required 
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition resize-none"
                    placeholder="Brief overview of the meeting in 1-2 sentences..."
                ></textarea>
            </div>

            <!-- Summary -->
            <div>
                <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                    Summary
                </label>
                <textarea 
                    name="summary" 
                    rows="5"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition resize-none"
                    placeholder="Detailed summary of key points discussed..."
                ></textarea>
            </div>

            <!-- Transcript -->
            <div>
                <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                    <i class="ph-bold ph-text-align-left text-purple-600 dark:text-purple-400"></i>
                    Full Transcript
                </label>
                <textarea 
                    name="transcript" 
                    rows="6"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition resize-none font-mono text-sm"
                    placeholder="Complete meeting transcript..."
                ></textarea>
            </div>

            <!-- Video URL -->
            <div>
                <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                    <i class="ph-bold ph-video-camera text-purple-600 dark:text-purple-400"></i>
                    Video Recording URL
                </label>
                <input 
                    type="url" 
                    name="video_url" 
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition"
                    placeholder="https://..."
                >
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Link ke recording Zoom, Google Meet, atau platform lainnya</p>
            </div>

            <!-- Tags -->
            <div>
                <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                    Tags
                </label>
                <input 
                    type="text" 
                    id="tagsInput"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition"
                    placeholder="e.g., Development, Weekly Sync, Q1 2026"
                >
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pisahkan dengan koma. Tags akan disimpan sebagai array.</p>
                <input type="hidden" name="tags" id="tagsHidden">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                    Status
                </label>
                <select name="status" class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                    <option value="draft">Draft</option>
                    <option value="approved">Approved</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button type="button" onclick="closeCreateModal()" class="flex-1 px-6 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:from-purple-700 hover:to-indigo-700 transition shadow-lg shadow-purple-500/30">
                    <span class="flex items-center justify-center gap-2">
                        <i class="ph-bold ph-plus-circle"></i>
                        Create Notulen
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Make functions globally accessible
window.openCreateModal = function() {
    document.getElementById('createModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

window.closeCreateModal = function() {
    document.getElementById('createModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    // Tags handling - convert comma-separated to JSON array
    const tagsInput = document.getElementById('tagsInput');
    const tagsHidden = document.getElementById('tagsHidden');

    if (tagsInput && tagsHidden) {
        tagsInput.addEventListener('input', (e) => {
            const tags = e.target.value.split(',').map(tag => tag.trim()).filter(tag => tag !== '');
            tagsHidden.value = JSON.stringify(tags);
        });
    }

    // Close modal on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            window.closeCreateModal();
        }
    });
});
</script>

<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { 
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.2s ease-out;
}

.animate-slideUp {
    animation: slideUp 0.3s ease-out;
}
</style>

@endsection
