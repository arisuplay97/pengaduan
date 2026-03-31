<?php

namespace App\Http\Controllers;

use App\Models\Notulen;
use App\Models\Agenda;
use Illuminate\Http\Request;

class NotulenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notulens = Notulen::with('agenda')->orderBy('meeting_date', 'desc')->paginate(10);
        return view('pages.notulen', compact('notulens'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $agendas = Agenda::where('status', 'completed')->get();
        return view('pages.notulen-create', compact('agendas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'agenda_id' => 'nullable|exists:agendas,id',
            'title' => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'duration' => 'nullable|integer|min:0',
            'participants' => 'nullable|string',
            'overview' => 'nullable|string',
            'summary' => 'nullable|string',
            'transcript' => 'nullable|string',
            'video_url' => 'nullable|url',
            'tags' => 'nullable',
            'status' => 'nullable|in:draft,completed,approved',
        ]);

        // Handle tags as JSON
        if ($request->has('tags') && is_string($request->tags)) {
            $validated['tags'] = json_decode($request->tags, true);
        }

        // Count participants
        if (!empty($validated['participants'])) {
            $validated['participants_count'] = count(array_filter(explode(',', $validated['participants'])));
        }

        Notulen::create($validated);

        return redirect()->route('notulen.index')->with('success', 'Notulen berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notulen $notulen)
    {
        $notulen->load('agenda');
        return view('pages.notulen-show', compact('notulen'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notulen $notulen)
    {
        $agendas = Agenda::where('status', 'completed')->get();
        return view('pages.notulen-edit', compact('notulen', 'agendas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notulen $notulen)
    {
        $validated = $request->validate([
            'agenda_id' => 'nullable|exists:agendas,id',
            'title' => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'duration' => 'nullable|integer|min:0',
            'participants' => 'nullable|string',
            'overview' => 'nullable|string',
            'summary' => 'nullable|string',
            'transcript' => 'nullable|string',
            'video_url' => 'nullable|url',
            'tags' => 'nullable',
            'status' => 'nullable|in:draft,completed,approved',
        ]);

        // Handle tags as JSON
        if ($request->has('tags') && is_string($request->tags)) {
            $validated['tags'] = json_decode($request->tags, true);
        }

        // Count participants
        if (!empty($validated['participants'])) {
            $validated['participants_count'] = count(array_filter(explode(',', $validated['participants'])));
        }

        $notulen->update($validated);

        return redirect()->route('notulen.index')->with('success', 'Notulen berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notulen $notulen)
    {
        $notulen->delete();

        return redirect()->route('notulen.index')->with('success', 'Notulen berhasil dihapus!');
    }
}
