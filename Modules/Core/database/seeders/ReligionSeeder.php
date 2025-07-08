<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReligionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tref_religions')->insert([
            ['code' => 'islam', 'name' => 'Islam'],
            ['code' => 'christian', 'name' => 'Protestan'],
			['code' => 'catholic', 'name' => 'Katolik'],
			['code' => 'hindu', 'name' => 'Hindu'],
			['code' => 'buddhist', 'name' => 'Buddha'],
			['code' => 'confucianism', 'name' => 'Konghucu']
        ]);
    }
}
