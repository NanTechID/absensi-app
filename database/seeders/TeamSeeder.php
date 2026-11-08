<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mapping = [
            'Information Technology' => [
                'Mobile Development',
                'Web Development',
                'IT Support & Infrastructure',
                'Quality Assurance',
            ],
            'Human Resources & GA' => [
                'Recruitment & Talent',
                'Payroll & Benefits',
                'General Affairs',
            ],
            'Finance & Accounting' => [
                'Accounting',
                'Treasury & Tax',
            ],
            'Operations & Production' => [
                'Production Line A',
                'Production Line B',
                'Maintenance & Facility',
            ],
            'Marketing & Sales' => [
                'Digital Marketing',
                'Direct Sales',
            ],
        ];

        foreach ($mapping as $deptName => $teams) {
            $dept = Departemen::where('nama_departemen', $deptName)->first();
            if (! $dept) {
                continue;
            }

            foreach ($teams as $teamName) {
                Team::firstOrCreate([
                    'departemen_id' => $dept->id,
                    'nama_team' => $teamName,
                ]);
            }
        }
    }
}

