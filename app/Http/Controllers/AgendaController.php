<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AgendaController extends Controller
{
    /**
     * 1. Halaman Kalender
     */
    public function index()
    {
        $agendas = Agenda::orderBy('start_at', 'desc')->paginate(10);
        return view('pages.agenda', compact('agendas'));
    }

    /**
     * 2. API: Ambil Data (READ)
     */
    public function events(Request $request)
    {
        // FullCalendar kirim start & end otomatis
        $start = $request->query('start');
        $end = $request->query('end');
        $types = $request->query('types'); // Filter tipe

        $typesArr = $types ? explode(',', $types) : ['DIRUT','UMUM','OPERASIONAL'];

        $agendas = Agenda::whereBetween('start_at', [$start, $end])
            ->whereIn('type', $typesArr)
            ->get();

        $events = $agendas->map(function ($agenda) {
            // Warna Event
            $color = '#3B82F6'; // Default Blue
            if ($agenda->type == 'OPERASIONAL') $color = '#10B981'; // Green
            if ($agenda->type == 'DIRUT') $color = '#7C3AED'; // Purple
            if ($agenda->type == 'UMUM') $color = '#F59E0B'; // Orange

            return [
                'id' => $agenda->id,
                'title' => $agenda->title,
                'start' => $agenda->start_at->toIso8601String(),
                'end' => $agenda->end_at->toIso8601String(),
                'allDay' => (bool)$agenda->is_all_day,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'location' => $agenda->location,
                    'type' => $agenda->type, // Penting buat edit
                    'description' => $agenda->description
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * 3. API: Simpan Baru (CREATE)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type'  => 'required|in:DIRUT,UMUM,OPERASIONAL',
            'bidang' => 'nullable|string|in:Sekper,Keuangan,Hublang,SPI,Umum,Perawatan,Produksi,Transdit,Perencana,Cabang',
            'location' => 'nullable|string|max:255',
            'start_at' => 'required|date',
            'end_at'   => 'nullable|date|after:start_at',
        ]);

        $validated['status'] = 'APPROVED'; // Default status
        $validated['user_id'] = auth()->id(); // Assign to current user

        $agenda = Agenda::create($validated);

        return response()->json(['ok' => true, 'id' => $agenda->id]);
    }

    /**
     * 4. API: Update Data (EDIT) - Only own agendas
     */
    public function update(Request $request, Agenda $agenda)
    {
        // Check permissions (Owner OR Admin OR Role Match)
        $user = auth()->user();
        $isOwner = $agenda->user_id === $user->id;
        $isAdmin = strtolower($user->username) === 'admin';
        
        $map = ['dirut' => 'DIRUT', 'dirum' => 'UMUM', 'dirop' => 'OPERASIONAL'];
        $roleType = $map[strtolower($user->username)] ?? null;
        
        // Allow if: Admin OR Owner OR Role Matches Agenda Type
        $hasPermission = $isAdmin || $isOwner || ($roleType && $agenda->type === $roleType);

        if (!$hasPermission) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type'  => 'required|in:DIRUT,UMUM,OPERASIONAL',
            'bidang' => 'nullable|string|in:Sekper,Keuangan,Hublang,SPI,Umum,Perawatan,Produksi,Transdit,Perencana,Cabang',
            'location' => 'nullable|string|max:255',
            'start_at' => 'required|date',
            'end_at'   => 'nullable|date|after:start_at',
        ]);

        $agenda->update($validated);

        return response()->json(['ok' => true]);
    }

    /**
     * 5. API: Hapus Data (DELETE) - Only own agendas
     */
    public function destroy(Agenda $agenda)
    {
        // Check permissions (Owner OR Admin OR Role Match)
        $user = auth()->user();
        $isOwner = $agenda->user_id === $user->id;
        $isAdmin = strtolower($user->username) === 'admin';
        
        $map = ['dirut' => 'DIRUT', 'dirum' => 'UMUM', 'dirop' => 'OPERASIONAL'];
        $roleType = $map[strtolower($user->username)] ?? null;
        
        // Allow if: Admin OR Owner OR Role Matches Agenda Type
        $hasPermission = $isAdmin || $isOwner || ($roleType && $agenda->type === $roleType);

        if (!$hasPermission) {
            $debugMsg = "Unauthorized. User: '{$user->username}', UserID: {$user->id}, AgendaID: {$agenda->id}, AgendaUserID: " . ($agenda->user_id ?? 'null') . ", AgendaType: '{$agenda->type}', RoleType: " . ($roleType ?? 'null') . ", IsAdmin: " . ($isAdmin?'true':'false');
            return response()->json(['ok' => false, 'message' => $debugMsg], 403);
        }

        $agenda->delete();
        return response()->json(['ok' => true]);
    }

    /**
     * 6. API: Geser Jadwal (MOVE)
     */
    public function move(Request $request, Agenda $agenda)
    {
        $request->validate([
            'start_at' => 'required|date',
            'end_at'   => 'nullable|date',
        ]);

        $agenda->update([
            'start_at' => Carbon::parse($request->start_at),
            'end_at'   => $request->end_at ? Carbon::parse($request->end_at) : $agenda->end_at,
        ]);

        return response()->json(['ok' => true]);
    }
    public function monitor()
    {
        $agendas = Agenda::orderBy('start_at', 'asc')->get();
        return view('pages.monitor-calendar', compact('agendas'));
    }

    public function upcoming()
    {
        // Notification Logic: Check next 30 minutes AND recent past (5 mins)
        $start = now()->subMinutes(5);
        $end = now()->addMinutes(30);
        
        $user = auth()->user();
        $query = Agenda::whereBetween('start_at', [$start, $end]);

        // Filter based on Role if NOT Admin
        if ($user && strtolower($user->username) !== 'admin') {
            $map = ['dirut' => 'DIRUT', 'dirum' => 'UMUM', 'dirop' => 'OPERASIONAL'];
            $roleType = $map[strtolower($user->username)] ?? null;
            
            if ($roleType) {
                $query->where('type', $roleType);
            } else {
                $query->whereRaw('0 = 1'); 
            }
            \Log::info("Upcoming Check: User={$user->username}, Role={$roleType}, Found=" . $query->count());
        } else {
             // Admin sees all
             \Log::info("Upcoming Check: Admin/Public. Found=" . $query->count());
        }
        
        $agendas = $query->get(['id', 'title', 'start_at', 'end_at', 'location', 'type']);
            
        return response()->json($agendas);
    }
}