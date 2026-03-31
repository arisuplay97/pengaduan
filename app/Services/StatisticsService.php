<?php

namespace App\Services;

use App\Models\FieldJob;
use App\Models\Kecamatan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsService
{
    private function cacheKey(string $prefix, array $filters): string
    {
        return 'stats_' . $prefix . '_' . md5(json_encode($filters));
    }

    private function baseQuery(array $filters)
    {
        $query = FieldJob::query();

        if (!empty($filters['bulan'])) {
            $query->whereMonth('jobs_field.created_at', $filters['bulan']);
        }
        if (!empty($filters['tahun'])) {
            $query->whereYear('jobs_field.created_at', $filters['tahun']);
        }
        if (!empty($filters['kecamatan_id'])) {
            $query->where('jobs_field.kecamatan_id', $filters['kecamatan_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('jobs_field.status', $filters['status']);
        }
        if (!empty($filters['problem_type'])) {
            $query->where('jobs_field.problem_type', $filters['problem_type']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('jobs_field.created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('jobs_field.created_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    public function getSummaryCards(array $filters): array
    {
        return Cache::remember($this->cacheKey('summary', $filters), 60, function () use ($filters) {
            $now = Carbon::now();
            $bulan = $filters['bulan'] ?? $now->month;
            $tahun = $filters['tahun'] ?? $now->year;

            // Current month
            $currentQuery = $this->baseQuery($filters);
            $totalBulan = (clone $currentQuery)->count();

            // Year total
            $totalTahun = FieldJob::whereYear('created_at', $tahun)->count();

            // Days in month for average
            $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
            $daysPassed = ($bulan == $now->month && $tahun == $now->year) ? $now->day : $daysInMonth;
            $avgPerDay = $daysPassed > 0 ? round($totalBulan / $daysPassed, 1) : 0;

            // Average completion time (hours) - only 'selesai' jobs
            $avgCompletion = FieldJob::where('status', 'selesai')
                ->whereMonth('created_at', $bulan)
                ->whereYear('created_at', $tahun)
                ->whereNotNull('started_at')
                ->whereNotNull('finished_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, started_at, finished_at)) as avg_minutes')
                ->value('avg_minutes');
            $avgCompletionHours = $avgCompletion ? round($avgCompletion / 60, 1) : 0;

            // SLA < 24 hours (created_at to finished_at)
            $doneJobs = FieldJob::where('status', 'selesai')
                ->whereMonth('created_at', $bulan)
                ->whereYear('created_at', $tahun)
                ->whereNotNull('finished_at')
                ->count();
            $slaJobs = FieldJob::where('status', 'selesai')
                ->whereMonth('created_at', $bulan)
                ->whereYear('created_at', $tahun)
                ->whereNotNull('finished_at')
                ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, finished_at) < 24')
                ->count();
            $slaPercent = $doneJobs > 0 ? round(($slaJobs / $doneJobs) * 100, 1) : 0;

            // Completion percentage
            $completionPercent = $totalBulan > 0 ? round(($doneJobs / $totalBulan) * 100, 1) : 0;

            // Previous month comparison
            $prevMonth = Carbon::createFromDate($tahun, $bulan, 1)->subMonth();
            $prevTotal = FieldJob::whereMonth('created_at', $prevMonth->month)
                ->whereYear('created_at', $prevMonth->year)
                ->count();
            $changePercent = $prevTotal > 0 ? round((($totalBulan - $prevTotal) / $prevTotal) * 100, 1) : 0;

            return [
                'total_bulan' => $totalBulan,
                'total_tahun' => $totalTahun,
                'avg_per_day' => $avgPerDay,
                'avg_completion_hours' => $avgCompletionHours,
                'sla_percent' => $slaPercent,
                'completion_percent' => $completionPercent,
                'change_percent' => $changePercent,
                'prev_total' => $prevTotal,
                'change_absolute' => $totalBulan - $prevTotal,
                'done_count' => $doneJobs,
                'working_count' => FieldJob::where('status', 'on_progress')
                    ->whereMonth('created_at', $bulan)
                    ->whereYear('created_at', $tahun)
                    ->count(),
            ];
        });
    }

    public function getDailyTrend(array $filters): array
    {
        return Cache::remember($this->cacheKey('trend', $filters), 60, function () use ($filters) {
            $now = Carbon::now();
            $bulan = $filters['bulan'] ?? $now->month;
            $tahun = $filters['tahun'] ?? $now->year;

            // Current month daily
            $current = FieldJob::whereMonth('created_at', $bulan)
                ->whereYear('created_at', $tahun)
                ->selectRaw('DAY(created_at) as day, COUNT(*) as total')
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('total', 'day')
                ->toArray();

            // Previous month daily
            $prev = Carbon::createFromDate($tahun, $bulan, 1)->subMonth();
            $previous = FieldJob::whereMonth('created_at', $prev->month)
                ->whereYear('created_at', $prev->year)
                ->selectRaw('DAY(created_at) as day, COUNT(*) as total')
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('total', 'day')
                ->toArray();

            $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
            $labels = [];
            $currentData = [];
            $previousData = [];

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labels[] = $d;
                $currentData[] = $current[$d] ?? 0;
                $previousData[] = $previous[$d] ?? 0;
            }

            return [
                'labels' => $labels,
                'current' => $currentData,
                'previous' => $previousData,
            ];
        });
    }

    public function getByKecamatan(array $filters): array
    {
        return Cache::remember($this->cacheKey('kecamatan', $filters), 60, function () use ($filters) {
            $data = $this->baseQuery($filters)
                ->join('kecamatans', 'jobs_field.kecamatan_id', '=', 'kecamatans.id')
                ->selectRaw('kecamatans.nama, COUNT(*) as total')
                ->groupBy('kecamatans.nama')
                ->orderByDesc('total')
                ->get();

            return [
                'labels' => $data->pluck('nama')->toArray(),
                'values' => $data->pluck('total')->toArray(),
            ];
        });
    }

    public function getByCategory(array $filters): array
    {
        return Cache::remember($this->cacheKey('category', $filters), 60, function () use ($filters) {
            $data = $this->baseQuery($filters)
                ->selectRaw("COALESCE(jobs_field.problem_type, jobs_field.title) as kategori, COUNT(*) as total")
                ->groupBy('kategori')
                ->orderByDesc('total')
                ->get();

            return [
                'labels' => $data->pluck('kategori')->toArray(),
                'values' => $data->pluck('total')->toArray(),
            ];
        });
    }

    public function getStatusDistribution(array $filters): array
    {
        return Cache::remember($this->cacheKey('status', $filters), 60, function () use ($filters) {
            $data = $this->baseQuery($filters)
                ->selectRaw("jobs_field.status, COUNT(*) as total")
                ->groupBy('jobs_field.status')
                ->pluck('total', 'status')
                ->toArray();

            return [
                'selesai' => $data['selesai'] ?? 0,
                'on_progress' => $data['on_progress'] ?? 0,
                'pending' => $data['pending'] ?? 0,
            ];
        });
    }

    public function getHourlyDistribution(array $filters): array
    {
        return Cache::remember($this->cacheKey('hourly', $filters), 60, function () use ($filters) {
            $data = $this->baseQuery($filters)
                ->selectRaw('HOUR(jobs_field.created_at) as jam, COUNT(*) as total')
                ->groupBy('jam')
                ->orderBy('jam')
                ->pluck('total', 'jam')
                ->toArray();

            $labels = [];
            $values = [];
            for ($h = 0; $h < 24; $h++) {
                $labels[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
                $values[] = $data[$h] ?? 0;
            }

            return ['labels' => $labels, 'values' => $values];
        });
    }

    public function getDailyDistribution(array $filters): array
    {
        return Cache::remember($this->cacheKey('daily', $filters), 60, function () use ($filters) {
            $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $data = $this->baseQuery($filters)
                ->selectRaw('DAYOFWEEK(jobs_field.created_at) as dow, COUNT(*) as total')
                ->groupBy('dow')
                ->orderBy('dow')
                ->pluck('total', 'dow')
                ->toArray();

            $values = [];
            for ($d = 1; $d <= 7; $d++) {
                $values[] = $data[$d] ?? 0;
            }

            return ['labels' => $days, 'values' => $values];
        });
    }

    public function getTopRiskAreas(array $filters): array
    {
        return Cache::remember($this->cacheKey('risk', $filters), 60, function () use ($filters) {
            $now = Carbon::now();
            $bulan = $filters['bulan'] ?? $now->month;
            $tahun = $filters['tahun'] ?? $now->year;

            $currentData = FieldJob::join('kecamatans', 'jobs_field.kecamatan_id', '=', 'kecamatans.id')
                ->whereMonth('jobs_field.created_at', $bulan)
                ->whereYear('jobs_field.created_at', $tahun)
                ->selectRaw('kecamatans.nama, COUNT(*) as total')
                ->groupBy('kecamatans.nama')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            $prev = Carbon::createFromDate($tahun, $bulan, 1)->subMonth();
            $prevData = FieldJob::join('kecamatans', 'jobs_field.kecamatan_id', '=', 'kecamatans.id')
                ->whereMonth('jobs_field.created_at', $prev->month)
                ->whereYear('jobs_field.created_at', $prev->year)
                ->selectRaw('kecamatans.nama, COUNT(*) as total')
                ->groupBy('kecamatans.nama')
                ->pluck('total', 'nama')
                ->toArray();

            $result = [];
            foreach ($currentData as $item) {
                $prevTotal = $prevData[$item->nama] ?? 0;
                $change = $prevTotal > 0 ? round((($item->total - $prevTotal) / $prevTotal) * 100, 1) : ($item->total > 0 ? 100 : 0);
                $result[] = [
                    'kecamatan' => $item->nama,
                    'total' => $item->total,
                    'change' => $change,
                    'trend' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'flat'),
                ];
            }

            return $result;
        });
    }

    public function getTimeAnalysis(array $filters): array
    {
        return Cache::remember($this->cacheKey('time', $filters), 60, function () use ($filters) {
            $doneJobs = $this->baseQuery($filters)
                ->where('jobs_field.status', 'selesai')
                ->whereNotNull('jobs_field.started_at')
                ->whereNotNull('jobs_field.finished_at');

            $stats = (clone $doneJobs)
                ->selectRaw('
                    AVG(TIMESTAMPDIFF(MINUTE, jobs_field.created_at, jobs_field.started_at)) as avg_response,
                    AVG(TIMESTAMPDIFF(MINUTE, jobs_field.started_at, jobs_field.finished_at)) as avg_completion,
                    MIN(TIMESTAMPDIFF(MINUTE, jobs_field.started_at, jobs_field.finished_at)) as min_completion,
                    MAX(TIMESTAMPDIFF(MINUTE, jobs_field.started_at, jobs_field.finished_at)) as max_completion
                ')
                ->first();

            return [
                'avg_response_min' => round($stats->avg_response ?? 0),
                'avg_completion_min' => round($stats->avg_completion ?? 0),
                'min_completion_min' => round($stats->min_completion ?? 0),
                'max_completion_min' => round($stats->max_completion ?? 0),
            ];
        });
    }

    public function getHeatmapData(array $filters): array
    {
        return Cache::remember($this->cacheKey('heatmap', $filters), 60, function () use ($filters) {
            return $this->baseQuery($filters)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->select('latitude', 'longitude', 'status')
                ->limit(500)
                ->get()
                ->map(fn($j) => [
                    'lat' => (float) $j->latitude,
                    'lng' => (float) $j->longitude,
                    'status' => $j->status,
                ])
                ->toArray();
        });
    }

    public function getExportData(array $filters)
    {
        return $this->baseQuery($filters)
            ->with(['user:id,name', 'kecamatan:id,nama'])
            ->orderByDesc('jobs_field.created_at')
            ->get()
            ->map(function ($job) {
                $end = $job->finished_at ?? now();
                return [
                    'tanggal' => $job->created_at->format('Y-m-d H:i'),
                    'judul' => $job->title,
                    'kategori' => $job->problem_type ?? $job->title,
                    'kecamatan' => $job->kecamatan?->nama ?? '-',
                    'alamat' => $job->address,
                    'status' => $job->status,
                    'teknisi' => $job->user?->name ?? '-',
                    'waktu_mulai' => $job->started_at?->format('Y-m-d H:i') ?? '-',
                    'waktu_selesai' => $job->finished_at?->format('Y-m-d H:i') ?? '-',
                    'durasi_menit' => $job->started_at && $job->finished_at
                        ? $job->started_at->diffInMinutes($job->finished_at) : '-',
                ];
            });
    }
}
