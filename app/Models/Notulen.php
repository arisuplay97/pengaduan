<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notulen extends Model
{
    use HasFactory;

    protected $fillable = [
        'agenda_id',
        'title',
        'meeting_date',
        'duration',
        'participants_count',
        'participants',
        'overview',
        'summary',
        'transcript',
        'video_url',
        'tags',
        'status',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'tags' => 'array',
    ];

    /**
     * Get the agenda that owns the notulen.
     */
    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }
}
