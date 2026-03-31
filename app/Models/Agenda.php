<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    // Izinkan kolom ini diisi massal
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location',
        'type',
        'bidang',
        'start_at',
        'end_at',
        'is_all_day',
        'is_private',
        'meeting_link',
        'status',
        'created_by'
    ];

    // Relationship: Agenda belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Ubah tipe data otomatis
    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
        'is_all_day' => 'boolean',
        'is_private' => 'boolean',
    ];
}