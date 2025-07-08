<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Database\Seeders\EducationLevelSeeder;
use Modules\Core\Database\Seeders\GenderSeeder;
use Modules\Core\Database\Seeders\JobSeeder;
use Modules\Core\Database\Seeders\ReligionSeeder;
use Modules\Core\Database\Seeders\StatusSeeder;

class CoreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
			EducationLevelSeeder::class,
			GenderSeeder::class,
			JobSeeder::class,
			ReligionSeeder::class,
			StatusSeeder::class,
		]);
    }
}
