<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FieldJob extends Model
{
    use HasFactory;

    protected $table = 'jobs_field';

    protected $fillable = [
        'user_id',
        'reporter_name',
        'reporter_phone',
        'customer_id',
        'ticket_code',
        'source',
        'title',
        'address',
        'kecamatan_id',
        'description',
        'photo_before',
        'photo_after',
        'upload_token',
        'problem_type',
        'status',
        'latitude',
        'longitude',
        'estimated_time',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Valid statuses
     */
    const STATUSES = [
        'pending', 'assigned', 'on_progress',
        'selesai', 'ditutup', 'ditolak', 'eskalasi',
    ];

    /**
     * Boot: auto-generate ticket_code on creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($job) {
            if (empty($job->ticket_code)) {
                $job->ticket_code = static::generateTicketCode();
            }
            if (empty($job->upload_token)) {
                $job->upload_token = Str::random(48);
            }
        });
    }

    /**
     * Generate ticket code: TSA-BBYY-XXXX
     * BB = 2 digit bulan, YY = 2 digit tahun, XXXX = urut (reset tiap bulan)
     */
    public static function generateTicketCode(): string
    {
        $now = now();
        $prefix = 'TSA-' . $now->format('m') . $now->format('y') . '-';

        // Count existing tickets this month
        $lastTicket = static::where('ticket_code', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(ticket_code, -4) AS UNSIGNED) DESC')
            ->value('ticket_code');

        if ($lastTicket) {
            $lastNum = (int) substr($lastTicket, -4);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        return $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}
