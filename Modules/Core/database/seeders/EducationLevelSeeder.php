<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EducationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tref_education_levels')->insert([
            ['code' => 'sd', 'name' => 'SD', 'desc' => 'Sekolah dasar, madrasah ibtidaiyah, atau yang setingkat dengannya'],
            ['code' => 'smp', 'name' => 'SMP', 'desc' => 'Sekolah menengah pertama, madrasah tsanawiyah, atau yang setingkat dengannya'],
			['code' => 'sma', 'name' => 'SMA', 'desc' => 'Sekolah menengah atas, madrasah aliyah, atau yang setingkat dengannya'],
            ['code' => 'd1', 'name' => 'D1', 'desc' => 'Diploma 1 atau sederajat'],
			['code' => 'd2', 'name' => 'D2', 'desc' => 'Diploma 2 atau sederajat'],
            ['code' => 'd3', 'name' => 'D3', 'desc' => 'Diploma 3 atau sederajat'],
			['code' => 's1', 'name' => 'S1', 'desc' => 'Strata 1 atau sederajat'],
            ['code' => 's2', 'name' => 'S2', 'desc' => 'Strata 2 atau sederajat'],
			['code' => 's3', 'name' => 'S3', 'desc' => 'Strata 3 atau sederajat']
        ]);
    }
}
