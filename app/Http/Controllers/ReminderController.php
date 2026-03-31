<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'pic' => 'required|string|max:255',
            'deadline' => 'required|date',
        ]);

        Reminder::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'pic' => $request->pic,
            'deadline' => $request->deadline,
        ]);

        return redirect()->back()->with('success', 'Reminder berhasil ditambahkan!');
    }

    public function destroy(Reminder $reminder)
    {
        // Only owner can delete their reminder
        if ($reminder->user_id !== auth()->id() && auth()->user()->username !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses!');
        }

        $reminder->delete();
        return redirect()->back()->with('success', 'Reminder berhasil dihapus!');
    }
}
