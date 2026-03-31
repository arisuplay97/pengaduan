@extends('layouts.nolana')

@section('content')
<div class="space-y-6">

    <!-- Back Button & Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('dokumen.index') }}" class="w-10 h-10 rounded-xl border-2 border-gray-200 dark:border-gray-700 flex items-center justify-center hover:border-purple-600 dark:hover:border-purple-400 hover:text-purple-600 dark:hover:text-purple-400 transition">
            <i class="ph-bold ph-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">Edit Dokumen</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Update informasi dokumen</p>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        
        <form action="{{ route('dokumen.update', $document) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Document Title -->
            <div>
                <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                    Document Title <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    value="{{ old('title', $document->title) }}" 
                    required 
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition @error('title') border-red-500 @enderror"
                    placeholder="e.g., Laporan Keuangan Q1 2026"
                >
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current File Info -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Current File</label>
                <div class="flex items-center gap-3">
                    @php
                        $iconColor = 'purple';
                        $iconClass = 'ph-file';
                        if ($document->file_type === 'pdf') {
                            $iconColor = 'red';
                            $iconClass = 'ph-file-pdf';
                        } elseif (in_array($document->file_type, ['doc', 'docx'])) {
                            $iconColor = 'blue';
                            $iconClass = 'ph-file-doc';
                        } elseif (in_array($document->file_type, ['xls', 'xlsx'])) {
                            $iconColor = 'green';
                            $iconClass = 'ph-file-xls';
                        } elseif (in_array($document->file_type, ['ppt', 'pptx'])) {
                            $iconColor = 'orange';
                            $iconClass = 'ph-file-ppt';
                        }
                    @endphp
                    <div class="w-12 h-12 bg-{{ $iconColor }}-50 dark:bg-{{ $iconColor }}-900/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="ph-fill {{ $iconClass }} text-{{ $iconColor }}-600 dark:text-{{ $iconColor }}-400 text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-gray-900 dark:text-white">{{ $document->file_name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($document->file_size / 1024, 2) }} KB • {{ strtoupper($document->file_type) }}</p>
                    </div>
                    <a href="{{ route('dokumen.download', $document) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition flex items-center gap-2">
                        <i class="ph-bold ph-download-simple"></i>
                        Download
                    </a>
                </div>
            </div>

            <!-- Replace File (Optional) -->
            <div>
                <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                    Replace File <span class="text-gray-500 text-xs">(Optional - leave empty to keep current file)</span>
                </label>
                <input 
                    type="file" 
                    name="file" 
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition @error('file') border-red-500 @enderror"
                >
                @error('file')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">PDF, DOC, XLS, PPT (max 10MB)</p>
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                    Category
                </label>
                <input 
                    type="text" 
                    name="category" 
                    value="{{ old('category', $document->category) }}"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition @error('category') border-red-500 @enderror"
                    placeholder="e.g., Financial Report, Meeting Notes"
                >
                @error('category')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                    Status
                </label>
                <select name="status" class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition @error('status') border-red-500 @enderror">
                    <option value="draft" {{ old('status', $document->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="approved" {{ old('status', $document->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="viewed" {{ old('status', $document->status) === 'viewed' ? 'selected' : '' }}>Viewed</option>
                    <option value="sent" {{ old('status', $document->status) === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="completed" {{ old('status', $document->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('dokumen.index') }}" class="flex-1 px-6 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl font-bold text-gray-700 dark:text-gray-300 hover:border-purple-600 dark:hover:border-purple-400 hover:text-purple-600 dark:hover:text-purple-400 transition text-center">
                    Cancel
                </a>
                <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:from-purple-700 hover:to-indigo-700 transition shadow-lg shadow-purple-500/30 flex items-center justify-center gap-2">
                    <i class="ph-bold ph-check"></i>
                    <span>Update Document</span>
                </button>
            </div>

        </form>

    </div>

</div>
@endsection

