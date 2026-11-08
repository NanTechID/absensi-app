<?php

namespace Database\Seeders;

use App\Models\Departemen;
use Illuminate\Database\Seeder;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departemens = [
            ['nama_departemen' => 'Information Technology'],
            ['nama_departemen' => 'Human Resources & GA'],
            ['nama_departemen' => 'Finance & Accounting'],
            ['nama_departemen' => 'Operations & Production'],
            ['nama_departemen' => 'Marketing & Sales'],
        ];

        foreach ($departemens as $dept) {
            Departemen::firstOrCreate(['nama_departemen' => $dept['nama_departemen']]);
        }
    }
}

