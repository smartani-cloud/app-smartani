<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = [
            ['code' => 'accepted', 'name' => 'Diterima', 'category' => 'acceptance_status'],
            ['code' => 'rejected', 'name' => 'Tidak Diterima', 'category' => 'acceptance_status'],
			['code' => 'approved', 'name' => 'Setuju', 'category' => 'acc_status'],
            ['code' => 'rejected', 'name' => 'Tolak', 'category' => 'acc_status'],
			['code' => 'active', 'name' => 'Aktif', 'category' => 'active_status'],
            ['code' => 'inactive', 'name' => 'Nonaktif', 'category' => 'active_status'],
			['code' => 'active', 'name' => 'Aktif', 'category' => 'archive_status'],
            ['code' => 'archived', 'name' => 'Arsip', 'category' => 'archive_status'],
			['code' => 'present', 'name' => 'Hadir', 'category' => 'attendance_status'],
            ['code' => 'absent', 'name' => 'Absen', 'category' => 'attendance_status'],
			['code' => 'mandatory', 'name' => 'Wajib', 'category' => 'mandatory_status'],
            ['code' => 'optional', 'name' => 'Pilihan', 'category' => 'mandatory_status'],
			['code' => 'married', 'name' => 'Menikah', 'category' => 'marital_status'],
            ['code' => 'single', 'name' => 'Belum Menikah', 'category' => 'marital_status'],
			['code' => 'continue', 'name' => 'Lanjut', 'category' => 'recommend_status'],
            ['code' => 'stop', 'name' => 'Dicukupkan', 'category' => 'recommend_status']
        ];

        foreach ($jobs as &$job) {
            $job['created_at'] = now();
            $job['updated_at'] = now();
        }
		
		DB::table('tref_statuses')->insert($jobs);
    }
}
