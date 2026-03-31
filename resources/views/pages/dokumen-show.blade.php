@extends('layouts.nolana')

@section('content')
<div class="space-y-6">

    <!-- Back Button & Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('dokumen.index') }}" class="w-10 h-10 rounded-xl border-2 border-gray-200 dark:border-gray-700 flex items-center justify-center hover:border-purple-600 dark:hover:border-purple-400 hover:text-purple-600 dark:hover:text-purple-400 transition">
            <i class="ph-bold ph-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ $document->title }}</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Uploaded {{ $document->created_at->diffForHumans() }}</p>
        </div>
    </div>

    <!-- Document Details Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        
        <!-- Header with File Icon -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-8 text-white">
            <div class="flex items-center gap-4">
                @php
                    $iconClass = 'ph-file';
                    if ($document->file_type === 'pdf') {
                        $iconClass = 'ph-file-pdf';
                    } elseif (in_array($document->file_type, ['doc', 'docx'])) {
                        $iconClass = 'ph-file-doc';
                    } elseif (in_array($document->file_type, ['xls', 'xlsx'])) {
                        $iconClass = 'ph-file-xls';
                    } elseif (in_array($document->file_type, ['ppt', 'pptx'])) {
                        $iconClass = 'ph-file-ppt';
                    }
                @endphp
                <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="ph-fill {{ $iconClass }} text-5xl text-white"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold mb-2">{{ $document->title }}</h2>
                    <div class="flex items-center gap-4 text-sm">
                        <span class="flex items-center gap-1">
                            <i class="ph-bold ph-file"></i>
                            {{ strtoupper($document->file_type) }}
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="ph-bold ph-hard-drive"></i>
                            {{ number_format($document->file_size / 1024, 2) }} KB
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Information -->
        <div class="p-8 space-y-6">
            
            <!-- Quick Actions -->
            <div class="flex items-center gap-3">
                <a href="{{ route('dokumen.download', $document) }}" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-3 rounded-xl font-bold hover:from-green-700 hover:to-emerald-700 transition flex items-center justify-center gap-2 shadow-lg shadow-green-500/30">
                    <i class="ph-bold ph-download-simple text-xl"></i>
                    <span>Download</span>
                </a>
                <a href="{{ route('dokumen.edit', $document) }}" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition flex items-center justify-center gap-2">
                    <i class="ph-bold ph-pencil text-xl"></i>
                    <span>Edit</span>
                </a>
                <form action="{{ route('dokumen.destroy', $document) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus dokumen ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-red-700 transition flex items-center justify-center gap-2">
                        <i class="ph-bold ph-trash text-xl"></i>
                        <span>Delete</span>
                    </button>
                </form>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-2 gap-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                
                <!-- File Name -->
                <div>
                    <label class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2 block">File Name</label>
                    <p class="text-base font-bold text-gray-900 dark:text-white">{{ $document->file_name }}</p>
                </div>

                <!-- Upload Date -->
                <div>
                    <label class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2 block">Upload Date</label>
                    <p class="text-base font-bold text-gray-900 dark:text-white">{{ $document->upload_date ? $document->upload_date->format('F d, Y') : $document->created_at->format('F d, Y') }}</p>
                </div>

                <!-- Category -->
                <div>
                    <label class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2 block">Category</label>
                    <p class="text-base font-bold text-gray-900 dark:text-white">{{ $document->category ?? '-' }}</p>
                </div>

                <!-- Status -->
                <div>
                    <label class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2 block">Status</label>
                    @php
                        $statusColors = [
                            'draft' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                            'approved' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                            'viewed' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                            'sent' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                            'completed' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                        ];
                        $statusClass = $statusColors[$document->status ?? 'draft'] ?? $statusColors['draft'];
                    @endphp
                    <span class="inline-flex px-4 py-2 {{ $statusClass }} text-sm font-bold rounded-xl">{{ ucfirst($document->status ?? 'draft') }}</span>
                </div>

                <!-- Created At -->
                <div>
                    <label class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2 block">Created</label>
                    <p class="text-base font-bold text-gray-900 dark:text-white">{{ $document->created_at->format('F d, Y \a\t g:i A') }}</p>
                </div>

                <!-- Last Updated -->
                <div>
                    <label class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2 block">Last Updated</label>
                    <p class="text-base font-bold text-gray-900 dark:text-white">{{ $document->updated_at->format('F d, Y \a\t g:i A') }}</p>
                </div>

            </div>

            <!-- File Path (for debugging) -->
            <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                <label class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2 block">File Location</label>
                <p class="text-sm text-gray-600 dark:text-gray-400 font-mono bg-gray-50 dark:bg-gray-900 px-4 py-2 rounded-lg">
                    {{ $document->file_path }}
                </p>
            </div>

        </div>

    </div>

</div>
@endsection

