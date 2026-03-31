<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documents = Document::orderBy('upload_date', 'desc')->paginate(10);
        return view('pages.dokumen', compact('documents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.dokumen-create');
    }

    /**
     * Store a newly created resource in storage with file upload.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240', // 10MB max
            'category' => 'nullable|string|max:100',
            'status' => 'nullable|in:draft,approved,viewed,sent,completed',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $fileName, 'public');

            $validated['file_name'] = $fileName;
            $validated['file_path'] = $filePath;
            $validated['file_type'] = $file->getClientOriginalExtension();
            $validated['file_size'] = $file->getSize();
            $validated['upload_date'] = now();
        }

        Document::create($validated);

        return redirect()->route('dokumen.index')->with('success', 'Dokumen berhasil diupload!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        return view('pages.dokumen-show', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        return view('pages.dokumen-edit', compact('document'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'category' => 'nullable|string|max:100',
            'status' => 'nullable|in:draft,approved,viewed,sent,completed',
        ]);

        // Handle file upload if new file provided
        if ($request->hasFile('file')) {
            // Delete old file
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $fileName, 'public');

            $validated['file_name'] = $fileName;
            $validated['file_path'] = $filePath;
            $validated['file_type'] = $file->getClientOriginalExtension();
            $validated['file_size'] = $file->getSize();
        }

        $document->update($validated);

        return redirect()->route('dokumen.index')->with('success', 'Dokumen berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        try {
            \Log::info('Delete attempt started', ['id' => $document->id, 'title' => $document->title]);
            
            // Delete file from storage
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
                \Log::info('File deleted from storage', ['path' => $document->file_path]);
            }

            // Delete from database
            $deleted = $document->delete();
            
            \Log::info('Database delete result', ['success' => $deleted]);

            if ($deleted) {
                return redirect()->route('dokumen.index')->with('success', 'Dokumen berhasil dihapus!');
            } else {
                \Log::error('Delete failed - no exception but returned false');
                return redirect()->route('dokumen.index')->with('error', 'Gagal menghapus dokumen!');
            }
        } catch (\Exception $e) {
            \Log::error('Delete exception', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('dokumen.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Download the document file.
     */
    public function download(Document $document)
    {
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            return Storage::disk('public')->download($document->file_path, $document->file_name);
        }

        return redirect()->back()->with('error', 'File tidak ditemukan!');
    }
}
