<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tref_genders')->insert([
            ['code' => 'M', 'name' => 'Laki-laki'],
            ['code' => 'F', 'name' => 'Perempuan'],
        ]);
    }
}
