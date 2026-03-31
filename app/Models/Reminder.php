<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'pic',
        'deadline',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    // Relationship: Reminder belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
