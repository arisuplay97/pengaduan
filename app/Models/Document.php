<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'category',
        'upload_date',
        'status',
    ];

    protected $casts = [
        'upload_date' => 'date',
    ];
}
