<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan
     */
    public function index()
    {
        // Fitur ini khusus superadmin
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Unauthorized action.');
        }

        $fonnteToken = Setting::where('key', 'fonnte_token')->value('value');
        $telegramToken = Setting::where('key', 'telegram_bot_token')->value('value') ?? config('services.telegram.bot_token');
        
        return view('pages.settings', compact('fonnteToken', 'telegramToken'));
    }

    /**
     * Update token atau pengaturan lainnya
     */
    public function update(Request $request)
    {
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'fonnte_token' => 'nullable|string|max:255',
            'telegram_bot_token' => 'nullable|string|max:255',
        ]);

        if ($request->has('fonnte_token')) {
            Setting::updateOrCreate(
                ['key' => 'fonnte_token'],
                ['value' => $request->fonnte_token]
            );
        }

        if ($request->has('telegram_bot_token')) {
            Setting::updateOrCreate(
                ['key' => 'telegram_bot_token'],
                ['value' => $request->telegram_bot_token]
            );
        }

        return redirect()->route('settings')->with('success', 'Pengaturan Token berhasil diperbarui!');
    }
}
