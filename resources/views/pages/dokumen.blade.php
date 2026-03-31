@extends('layouts.nolana')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2">Dokumen</h1>
            <p class="text-gray-500 dark:text-gray-400">Kelola semua dokumen dan notulen rapat</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-5 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-xl font-bold text-gray-700 dark:text-gray-300 hover:border-purple-600 hover:text-purple-600 transition flex items-center gap-2">
                <i class="ph-bold ph-export"></i>
                <span>Export</span>
            </button>
            <button onclick="openUploadModal()" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-2.5 rounded-xl font-bold hover:from-purple-700 hover:to-indigo-700 transition flex items-center gap-2 shadow-lg shadow-purple-500/30">
                <i class="ph-bold ph-plus"></i>
                <span>New Document</span>
            </button>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        
        <!-- Tab Filters -->
        <div class="border-b border-gray-100 dark:border-gray-700 px-6">
            <div class="flex items-center gap-1 -mb-px overflow-x-auto">
                <button class="px-4 py-3 font-bold text-purple-600 dark:text-purple-400 border-b-2 border-purple-600 dark:border-purple-400 whitespace-nowrap flex items-center gap-2 text-sm">
                    <i class="ph-fill ph-files"></i>
                    <span>All Documents</span>
                </button>
                <button class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 whitespace-nowrap flex items-center gap-2 text-sm transition">
                    <i class="ph ph-file-text"></i>
                    <span>Draft</span>
                </button>
                <button class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 whitespace-nowrap flex items-center gap-2 text-sm transition">
                    <i class="ph ph-check-circle"></i>
                    <span>Approved</span>
                </button>
                <button class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 whitespace-nowrap flex items-center gap-2 text-sm transition">
                    <i class="ph ph-eye"></i>
                    <span>Viewed</span>
                </button>
            </div>
        </div>

        <!-- Search & Filters -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3 flex-1">
                <div class="relative flex-1 max-w-md">
                    <i class="ph-bold ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" placeholder="Search documents..." class="w-full pl-10 pr-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button class="px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition flex items-center gap-2">
                    <i class="ph-bold ph-funnel"></i>
                    <span>Filter</span>
                </button>
            </div>
        </div>

        <!-- Documents Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 cursor-pointer">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Activity</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Recipients</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($documents as $document)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition group">
                        <td class="px-6 py-4">
                            <input type="checkbox" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 cursor-pointer">
                        </td>
                        <td class="px-6 py-4">
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
                                <div class="w-10 h-10 bg-{{ $iconColor }}-50 dark:bg-{{ $iconColor }}-900/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="ph-fill {{ $iconClass }} text-{{ $iconColor }}-600 dark:text-{{ $iconColor }}-400 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition">{{ $document->title }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $document->created_at->format('M d, Y, g:i A') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $document->upload_date ? $document->upload_date->format('M d, Y, g:i A') : '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex -space-x-2">
                                <div class="w-8 h-8 rounded-full bg-purple-500 border-2 border-white dark:border-gray-800 flex items-center justify-center text-white text-xs font-bold">U</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
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
                            <span class="px-3 py-1 {{ $statusClass }} text-xs font-bold rounded-full">{{ ucfirst($document->status ?? 'draft') }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('dokumen.show', $document) }}" class="px-4 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">View</a>
                                <a href="{{ route('dokumen.download', $document) }}" class="px-4 py-1.5 border border-green-200 dark:border-green-900 text-green-600 dark:text-green-400 rounded-lg text-sm font-bold hover:bg-green-50 dark:hover:bg-green-900/20 transition">
                                    <i class="ph-bold ph-download-simple"></i>
                                </a>
                                <a href="{{ route('dokumen.edit', $document) }}" class="px-4 py-1.5 border border-blue-200 dark:border-blue-900 text-blue-600 dark:text-blue-400 rounded-lg text-sm font-bold hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                    <i class="ph-bold ph-pencil"></i>
                                </a>
                                <form id="delete-form-{{ $document->id }}" action="{{ route('dokumen.destroy', $document) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Yakin ingin menghapus dokumen {{ $document->title }}?')" class="px-4 py-1.5 border border-red-200 dark:border-red-900 text-red-600 dark:text-red-400 rounded-lg text-sm font-bold hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                        <i class="ph-bold ph-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                    <i class="ph-bold ph-folder-open text-3xl text-gray-400 dark:text-gray-500"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Belum ada dokumen</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Upload dokumen pertama Anda dengan klik tombol "New Document"</p>
                                <button onclick="openUploadModal()" class="px-6 py-2.5 bg-purple-600 text-white rounded-xl font-bold hover:bg-purple-700 transition">
                                    <i class="ph-bold ph-plus mr-2"></i>Upload Dokumen
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Showing {{ $documents->firstItem() ?? 0 }} to {{ $documents->lastItem() ?? 0 }} of {{ $documents->total() }} documents
            </div>
            <div class="flex items-center gap-2">
                {{ $documents->links() }}
            </div>
        </div>
    </div>

</div>

<!-- Upload Document Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 animate-fadeIn">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto transform transition-all animate-slideUp">
        
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 px-8 py-6 flex items-center justify-between z-10">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Upload Dokumen</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Upload dokumen baru ke sistem</p>
            </div>
            <button onclick="closeUploadModal()" class="w-10 h-10 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition">
                <i class="ph-bold ph-x text-xl text-gray-600 dark:text-gray-400"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form action="{{ route('dokumen.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
            @csrf

            <!-- Document Title -->
            <div>
                <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                    Document Title <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    required 
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition"
                    placeholder="e.g., Laporan Keuangan Q1 2026"
                >
            </div>

            <!-- File Upload with Drag & Drop -->
            <div>
                <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                    Upload File <span class="text-red-500">*</span>
                </label>
                <div id="dropZone" class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center cursor-pointer hover:border-purple-500 transition">
                    <input type="file" name="file" id="fileInput" class="hidden" required accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                    
                    <!-- Drop Zone Content -->
                    <div id="dropZoneContent">
                        <div class="w-16 h-16 bg-purple-50 dark:bg-purple-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ph-bold ph-upload text-3xl text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white mb-1">Click to upload or drag and drop</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">PDF, DOC, XLS, PPT (max 10MB)</p>
                    </div>

                    <!-- File Preview -->
                    <div id="filePreview" class="hidden">
                        <div class="flex items-center justify-center gap-3">
                            <i id="fileIcon" class="ph-fill ph-file text-2xl text-purple-600 dark:text-purple-400"></i>
                            <div class="text-left">
                                <p id="fileName" class="text-sm font-bold text-gray-900 dark:text-white"></p>
                                <p id="fileSize" class="text-xs text-gray-500 dark:text-gray-400"></p>
                            </div>
                            <button type="button" onclick="clearFile()" class="ml-4 w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition">
                                <i class="ph-bold ph-x text-gray-600 dark:text-gray-400"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                    Category
                </label>
                <input 
                    type="text" 
                    name="category" 
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition"
                    placeholder="e.g., Financial Report, Meeting Notes"
                >
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                    Status
                </label>
                <select name="status" class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                    <option value="draft">Draft</option>
                    <option value="approved">Approved</option>
                    <option value="viewed">Viewed</option>
                    <option value="sent">Sent</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button type="button" onclick="closeUploadModal()" class="flex-1 px-6 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:from-purple-700 hover:to-indigo-700 transition shadow-lg shadow-purple-500/30">
                    <span class="flex items-center justify-center gap-2">
                        <i class="ph-bold ph-upload-simple"></i>
                        Upload Document
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Make functions globally accessible
window.openUploadModal = function() {
    document.getElementById('uploadModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

window.closeUploadModal = function() {
    document.getElementById('uploadModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    clearFile();
}

window.clearFile = function() {
    const fileInput = document.getElementById('fileInput');
    const dropZoneContent = document.getElementById('dropZoneContent');
    const filePreview = document.getElementById('filePreview');
    
    if (fileInput) fileInput.value = '';
    if (dropZoneContent) dropZoneContent.classList.remove('hidden');
    if (filePreview) filePreview.classList.add('hidden');
}

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    // File Upload Handling
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const dropZoneContent = document.getElementById('dropZoneContent');
    const filePreview = document.getElementById('filePreview');

    if (!dropZone || !fileInput) {
        console.error('Upload elements not found');
        return;
    }

    // Click to browse
    dropZone.addEventListener('click', () => fileInput.click());

    // Drag & Drop
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-purple-500', 'bg-purple-50', 'dark:bg-purple-900/20');
    });

    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-purple-500', 'bg-purple-50', 'dark:bg-purple-900/20');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-purple-500', 'bg-purple-50', 'dark:bg-purple-900/20');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            showFilePreview(files[0]);
        }
    });

    // File input change
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            showFilePreview(e.target.files[0]);
        }
    });

    function showFilePreview(file) {
        // Hide drop zone content, show preview
        dropZoneContent.classList.add('hidden');
        filePreview.classList.remove('hidden');
        
        // Set file name
        document.getElementById('fileName').textContent = file.name;
        
        // Set file size
        const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
        document.getElementById('fileSize').textContent = `${sizeInMB} MB`;
        
        // Set icon based on file type
        const fileIcon = document.getElementById('fileIcon');
        const ext = file.name.split('.').pop().toLowerCase();
        
        if (ext === 'pdf') {
            fileIcon.className = 'ph-fill ph-file-pdf text-2xl text-red-600 dark:text-red-400';
        } else if (['doc', 'docx'].includes(ext)) {
            fileIcon.className = 'ph-fill ph-file-doc text-2xl text-blue-600 dark:text-blue-400';
        } else if (['xls', 'xlsx'].includes(ext)) {
            fileIcon.className = 'ph-fill ph-file-xls text-2xl text-green-600 dark:text-green-400';
        } else if (['ppt', 'pptx'].includes(ext)) {
            fileIcon.className = 'ph-fill ph-file-ppt text-2xl text-orange-600 dark:text-orange-400';
        } else {
            fileIcon.className = 'ph-fill ph-file text-2xl text-purple-600 dark:text-purple-400';
        }
    }

    // Close modal on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            window.closeUploadModal();
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

