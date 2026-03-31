<?php

namespace App\Http\Controllers;

use App\Models\FieldJob;
use App\Models\User;
use Illuminate\Http\Request;

class CommandCenterController extends Controller
{
    /**
     * Director Command Center — Map view
     */
    public function index()
    {
        $petugasList = User::where('role', 'petugas')->get();
        return view('pages.command-center', compact('petugasList'));
    }

    /**
     * API: Get all jobs for today (JSON)
     */
    public function apiJobs()
    {
        $jobs = FieldJob::with('user:id,name')
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($jobs);
    }

    /**
     * Store a new field job
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string',
        ]);

        FieldJob::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'address' => $request->address,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return redirect()->route('command-center')->with('success', 'Job berhasil dibuat!');
    }
}
