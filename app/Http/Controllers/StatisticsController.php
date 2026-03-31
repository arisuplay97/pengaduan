<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Services\StatisticsService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StatisticsController extends Controller
{
    public function __construct(private StatisticsService $service)
    {
    }

    public function index()
    {
        $kecamatans = Kecamatan::orderBy('nama')->get();
        $categories = ['Pipa Bocor', 'Meteran Mati', 'Air Keruh', 'Sambungan Lepas', 'Meteran Tersumbat', 'Lainnya'];

        return view('pages.statistics', compact('kecamatans', 'categories'));
    }

    public function getData(Request $request)
    {
        $filters = $request->only(['bulan', 'tahun', 'kecamatan_id', 'status', 'problem_type', 'date_from', 'date_to']);

        // Default to current month/year if not provided
        if (empty($filters['bulan'])) $filters['bulan'] = now()->month;
        if (empty($filters['tahun'])) $filters['tahun'] = now()->year;

        return response()->json([
            'summary' => $this->service->getSummaryCards($filters),
            'daily_trend' => $this->service->getDailyTrend($filters),
            'by_kecamatan' => $this->service->getByKecamatan($filters),
            'by_category' => $this->service->getByCategory($filters),
            'status_distribution' => $this->service->getStatusDistribution($filters),
            'hourly' => $this->service->getHourlyDistribution($filters),
            'daily' => $this->service->getDailyDistribution($filters),
            'top_risk' => $this->service->getTopRiskAreas($filters),
            'time_analysis' => $this->service->getTimeAnalysis($filters),
            'heatmap' => $this->service->getHeatmapData($filters),
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $filters = $request->only(['bulan', 'tahun', 'kecamatan_id', 'status', 'problem_type', 'date_from', 'date_to']);
        if (empty($filters['bulan'])) $filters['bulan'] = now()->month;
        if (empty($filters['tahun'])) $filters['tahun'] = now()->year;

        $data = $this->service->getExportData($filters);

        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['Tanggal', 'Judul', 'Kategori', 'Kecamatan', 'Alamat', 'Status', 'Teknisi', 'Waktu Mulai', 'Waktu Selesai', 'Durasi (menit)']);
            foreach ($data as $row) {
                fputcsv($out, array_values($row));
            }
            fclose($out);
        }, 'laporan_gangguan_' . now()->format('Ymd_His') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $filters = $request->only(['bulan', 'tahun', 'kecamatan_id', 'status', 'problem_type', 'date_from', 'date_to']);
        if (empty($filters['bulan'])) $filters['bulan'] = now()->month;
        if (empty($filters['tahun'])) $filters['tahun'] = now()->year;

        $data = $this->service->getExportData($filters);

        return response()->streamDownload(function () use ($data) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="UTF-8"></head><body><table border="1">';
            echo '<tr><th>Tanggal</th><th>Judul</th><th>Kategori</th><th>Kecamatan</th><th>Alamat</th><th>Status</th><th>Teknisi</th><th>Waktu Mulai</th><th>Waktu Selesai</th><th>Durasi (menit)</th></tr>';
            foreach ($data as $row) {
                echo '<tr>';
                foreach ($row as $cell) {
                    echo '<td>' . htmlspecialchars($cell) . '</td>';
                }
                echo '</tr>';
            }
            echo '</table></body></html>';
        }, 'laporan_gangguan_' . now()->format('Ymd_His') . '.xls', [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }
}
