<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        $user = auth()->user();
        
        // Petugas should never see admin dashboard
        if ($user->role === 'petugas') {
            return redirect()->route('worker.dashboard');
        }
        
        $username = $user->username;
        $isAdmin = $username === 'admin';
        
        // Map username to agenda type
        $typeMap = [
            'dirut' => 'DIRUT',
            'dirum' => 'UMUM',      // DIRUM = UMUM type in database
            'dirop' => 'OPERASIONAL' // DIROP = OPERASIONAL type in database
        ];
        
            if ($isAdmin) {
            // Admin lihat SEMUA
            $stats = [
                'agenda_count' => \App\Models\Agenda::whereMonth('start_at', now()->month)->count(),
                'today_agenda_count' => \App\Models\Agenda::whereDate('start_at', today())->count(),
                'document_count' => \App\Models\Document::count() + \App\Models\Notulen::count(),
                'pending_count' => \App\Models\Document::where('status', 'draft')->orWhere('status', 'pending')->count(),
                'disturbance_count' => \App\Models\FieldJob::whereDate('created_at', today())->count(), // NEW
            ];
            
            $todayAgendas = \App\Models\Agenda::whereDate('start_at', today())
                ->orderBy('start_at', 'asc')
                ->get();
                
            $reminders = \App\Models\Reminder::orderBy('deadline', 'asc')->get();
        } else {
            // Direksi lihat agenda sesuai TIPE mereka
            $agendaType = $typeMap[$username] ?? null;
            
            if ($agendaType) {
                $stats = [
                    'agenda_count' => \App\Models\Agenda::where('type', $agendaType)->whereMonth('start_at', now()->month)->count(),
                    'today_agenda_count' => \App\Models\Agenda::where('type', $agendaType)->whereDate('start_at', today())->count(),
                    'document_count' => \App\Models\Document::count() + \App\Models\Notulen::count(),
                    'pending_count' => \App\Models\Document::where('status', 'draft')->orWhere('status', 'pending')->count(),
                    'disturbance_count' => \App\Models\FieldJob::whereDate('created_at', today())->count(), // Shared for all? Or filtered? Usually disturbances are general info.
                ];
                
                $todayAgendas = \App\Models\Agenda::where('type', $agendaType)
                    ->whereDate('start_at', today())
                    ->orderBy('start_at', 'asc')
                    ->get();
                    
                // Reminder HANYA milik user ini (tidak termasuk NULL)
                $reminders = \App\Models\Reminder::where('user_id', $user->id)
                    ->orderBy('deadline', 'asc')
                    ->get();
            } else {
                // Fallback: user tidak dikenali, tampilkan kosong
                $stats = [
                    'agenda_count' => 0,
                    'today_agenda_count' => 0,
                    'document_count' => \App\Models\Document::count() + \App\Models\Notulen::count(),
                    'pending_count' => \App\Models\Document::where('status', 'draft')->orWhere('status', 'pending')->count(),
                    'disturbance_count' => 0,
                ];
                $todayAgendas = collect([]);
                $reminders = collect([]);
            }
        }
        // Live Status Direksi - cek agenda aktif saat ini per direksi
        $now = now();
        $direksiTypes = [
            ['type' => 'DIRUT', 'name' => 'Direktur Utama', 'short' => 'Dirut'],
            ['type' => 'UMUM', 'name' => 'Dir. Umum', 'short' => 'Dirum'],
            ['type' => 'OPERASIONAL', 'name' => 'Dir. Operasional', 'short' => 'Dirop'],
        ];
        
        $direksiStatus = [];
        foreach ($direksiTypes as $d) {
            $activeAgenda = \App\Models\Agenda::where('type', $d['type'])
                ->whereDate('start_at', today())
                ->where('start_at', '<=', $now)
                ->where(function($q) use ($now) {
                    $q->where('end_at', '>=', $now)
                      ->orWhereNull('end_at');
                })
                ->first();
            
            $direksiStatus[] = [
                'name' => $d['name'],
                'short' => $d['short'],
                'type' => $d['type'],
                'busy' => $activeAgenda !== null,
                'agenda_title' => $activeAgenda ? $activeAgenda->title : null,
                'agenda_time' => $activeAgenda ? $activeAgenda->start_at->format('H:i') . ($activeAgenda->end_at ? ' - ' . $activeAgenda->end_at->format('H:i') : '') : null,
            ];
        }
        // Recent Activities — real data for activity feed & notification bell
        $recentActivities = collect();

        // FieldJob activities (gangguan)
        $recentJobs = \App\Models\FieldJob::with('kecamatan')
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($job) {
                $statusMap = [
                    'pending' => ['color' => 'amber', 'icon' => 'ph-clock', 'action' => 'Laporan gangguan baru masuk'],
                    'on_progress' => ['color' => 'blue', 'icon' => 'ph-wrench', 'action' => 'Sedang dikerjakan'],
                    'selesai'    => ['color' => 'green', 'icon' => 'ph-check-circle', 'action' => 'Selesai diperbaiki'],
                ];
                $info = $statusMap[$job->status] ?? $statusMap['pending'];
                $area = $job->kecamatan ? $job->kecamatan->name : 'Unknown';
                return [
                    'color' => $info['color'],
                    'icon'  => $info['icon'],
                    'text'  => $info['action'] . ' — ' . ($job->problem_type ?? 'Gangguan') . ' di ' . $area,
                    'time'  => $job->updated_at,
                    'user'  => $job->reporter_name ?? 'Petugas',
                ];
            });
        $recentActivities = $recentActivities->merge($recentJobs);

        // Sort by time and take latest 8
        $recentActivities = $recentActivities->sortByDesc('time')->take(8)->values();

        // Count unread (last 24 hours)
        $notifCount = \App\Models\FieldJob::where('updated_at', '>=', now()->subHours(24))->count();

        return view('pages.nolana', compact('stats', 'todayAgendas', 'reminders', 'direksiStatus', 'recentActivities', 'notifCount'));
    }

    public function agenda() {
        return view('pages.agenda');
    }

    public function dokumen() {
        return view('pages.dokumen');
    }

    public function settings() {
        return view('pages.settings');
    }

    public function calendar() {
        return view('pages.calendar');
    }

    public function notulen() {
        return view('pages.notulen');
    }

    /**
     * Trigger scheduled WhatsApp notification manually
     */
    public function sendNotificationNow(\Illuminate\Http\Request $request)
    {
        $type = $request->input('type'); // 'agenda' or 'stats'
        
        if (!in_array($type, ['agenda', 'stats'])) {
            return back()->with('error', 'Tipe notifikasi tidak valid.');
        }

        try {
            \Illuminate\Support\Facades\Artisan::call("notify:direksi {$type}");
            $output = \Illuminate\Support\Facades\Artisan::output();
            
            if (str_contains($output, 'Successfully sent')) {
                return back()->with('success', "Notifikasi {$type} berhasil dikirim ke Direksi!");
            } else {
                return back()->with('error', "Gagal memproses notifikasi {$type}. Output: " . substr($output, 0, 100));
            }
        } catch (\Exception $e) {
            return back()->with('error', "Terjadi kesalahan sistem: " . $e->getMessage());
        }
    }
}