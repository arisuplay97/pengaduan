<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kecamatan;

class KecamatanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Praya',
            'Praya Barat',
            'Praya Barat Daya',
            'Praya Tengah',
            'Praya Timur',
            'Batukliang',
            'Batukliang Utara',
            'Pujut',
            'Kopang',
            'Janapria',
            'Pringgarata',
            'Jonggat',
        ];

        foreach ($data as $nama) {
            Kecamatan::firstOrCreate(['nama' => $nama]);
        }
    }
}
