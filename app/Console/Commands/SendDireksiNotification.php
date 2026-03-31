<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Agenda;
use App\Models\FieldJob;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendDireksiNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:direksi {type : "agenda" for morning, "stats" for evening}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends scheduled WhatsApp notifications to Direksi via Fonnte';

    /**
     * Execute the console command.
     */
    public function handle(FonnteService $fonnte)
    {
        $type = $this->argument('type');
        
        $direksiUsers = User::where('role', 'direksi')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->get();

        if ($direksiUsers->isEmpty()) {
            $this->info("No Direksi users with phone numbers found.");
            return;
        }

        $targetNumbers = $direksiUsers->pluck('phone')->implode(',');

        if ($type === 'agenda') {
            $message = $this->buildAgendaMessage();
            if (!$message) {
                $this->info("No agendas for today. Skipping notification.");
                return;
            }
        } elseif ($type === 'stats') {
            $message = $this->buildStatsMessage();
            if (!$message) {
                $this->info("No field jobs found for today stats. Skipping notification.");
                return;
            }
        } else {
            $this->error("Invalid notification type. Use 'agenda' or 'stats'.");
            return;
        }

        $this->info("Sending {$type} notification to: {$targetNumbers}");
        
        $result = $fonnte->sendMessage($targetNumbers, $message);
        
        if ($result['status']) {
            $this->info("Successfully sent {$type} notification.");
        } else {
            $this->error("Failed to send {$type} notification.");
        }
    }

    private function buildAgendaMessage(): ?string
    {
        $today = Carbon::today();
        $agendas = Agenda::whereDate('start_at', $today)
            ->orderBy('start_at', 'asc')
            ->get();

        if ($agendas->isEmpty()) {
            return null;
        }

        $tanggal = $today->translatedFormat('l, d F Y');
        $msg = "📅 *Jadwal Pimpinan & Direksi*\n";
        $msg .= "Hari/Tanggal: {$tanggal}\n";
        $msg .= "--------------------------------------\n\n";

        foreach ($agendas as $idx => $agenda) {
            $no = $idx + 1;
            $waktu = $agenda->is_all_day ? 'Sepanjang Hari' : $agenda->start_at->format('H:i') . ' WITA';
            if (!$agenda->is_all_day && $agenda->end_at) {
                $waktu .= ' - ' . $agenda->end_at->format('H:i') . ' WITA';
            }
            
            $msg .= "*{$no}. {$agenda->title}*\n";
            $msg .= "🕒 Waktu    : {$waktu}\n";
            $msg .= "📍 Lokasi   : {$agenda->location}\n";
            $msg .= "📋 Kategori : " . ucfirst($agenda->bidang ?? $agenda->type) . "\n";
            if ($agenda->meeting_link) {
                $msg .= "🔗 Link     : {$agenda->meeting_link}\n";
            }
            $msg .= "\n";
        }

        $msg .= "--------------------------------------\n";
        $msg .= "Selamat beraktivitas! 💼\n";
        $msg .= "_Pesan otomatis dari Tiara Smart Assistant_";

        return $msg;
    }

    private function buildStatsMessage(): ?string
    {
        $today = Carbon::today();
        
        $jobs = FieldJob::with('kecamatan')->whereDate('created_at', $today)->get();
        if ($jobs->isEmpty()) {
            $msg = "📊 *Rekap Laporan Perbaikan Harian*\n";
            $msg .= "Tanggal: " . $today->translatedFormat('d F Y') . "\n";
            $msg .= "--------------------------------------\n\n";
            $msg .= "✅ Laporan Selesai    : 0\n";
            $msg .= "⏳ Sedang Dikerjakan  : 0\n";
            $msg .= "🕒 Menunggu (Pending) : 0\n\n";
            $msg .= "--------------------------------------\n";
            $msg .= "_Pesan otomatis dari Tiara Smart Assistant_";
            return $msg;
        }

        $formatKecamatan = function ($jobList) {
            if ($jobList->isEmpty()) return "0";
            $count = $jobList->count();
            
            $kecamatanNames = [];
            foreach ($jobList as $job) {
                if ($job->kecamatan && $job->kecamatan->nama) {
                    $kecamatanNames[] = $job->kecamatan->nama;
                }
            }
            
            $kecamatans = collect($kecamatanNames)->unique()->filter()->implode(', ');
            
            return $kecamatans ? "{$count} (Kecamatan {$kecamatans})" : "{$count} (Kecamatan Lainnya)";
        };

        $strTotal = $formatKecamatan($jobs);
        
        $selesaiJobs = $jobs->where('status', 'selesai');
        $strSelesai = $formatKecamatan($selesaiJobs);
        
        $prosesJobs = $jobs->where('status', 'on_progress');
        $strProses = $formatKecamatan($prosesJobs);
        
        $pendingJobs = $jobs->where('status', 'pending');
        $strPending = $formatKecamatan($pendingJobs);
        
        $lainnyaJobs = $jobs->whereNotIn('status', ['selesai', 'on_progress', 'pending']);
        $strLainnya = $formatKecamatan($lainnyaJobs);

        $msg = "📊 *Rekap Laporan Perbaikan Harian*\n";
        $msg .= "Malam Bapak Direksi,\n"; // Changed from Bapak/Ibu Direksi
        $msg .= "Berikut adalah update status penanganan gangguan teknis untuk hari ini (" . $today->translatedFormat('d F Y') . "):\n\n";
        
        $msg .= "📈 *Ringkasan Hari Ini:*\n";
        $msg .= "• Total Laporan Masuk : {$strTotal}\n";
        $msg .= "• ✅ Selesai Ditangani : {$strSelesai}\n";
        $msg .= "• ⏳ Sedang Dikerjakan : {$strProses}\n";
        $msg .= "• 🕒 Menunggu Assign   : {$strPending}\n";
        
        if ($lainnyaJobs->count() > 0) {
            $msg .= "• 📌 Status Lainnya    : {$strLainnya}\n";
        }

        $msg .= "\n--------------------------------------\n";
        $msg .= "Terima kasih atas dedikasinya hari ini. Selamat beristirahat! 🌙\n";
        $msg .= "_Pesan otomatis dari Tiara Smart Assistant_";

        return $msg;
    }
}
