<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $fillable = ['nama'];

    public function fieldJobs()
    {
        return $this->hasMany(FieldJob::class);
    }

    /**
     * Teknisi yang bertugas di kecamatan ini (many-to-many)
     */
    public function teknisi()
    {
        return $this->belongsToMany(User::class, 'kecamatan_teknisi')
                    ->withTimestamps();
    }
}
