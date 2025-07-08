<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Smartani',
			'username' => 'smartani',
			'password' => Hash::make('adminsmartani1'),
			'role_id' => 36,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
