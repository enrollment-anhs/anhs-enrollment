<?php

namespace Database\Seeders;

use App\Models\SchoolInformation;
use Illuminate\Database\Seeder;

class SchoolInformationSeeder extends Seeder
{
    public function run(): void
    {
        SchoolInformation::query()->updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Agusan National High School',
                'school_id' => '304756',
                'region' => 'Region XIII',
                'division' => 'Butuan City',
                'district' => 'Central Butuan District I (CBD-I)',
                'logo_path' => 'school-logos/9MSt3DrLcVZShD9ufpwxNbOr1FTQpsl68YtGNpzu.png',
            ],
        );
    }
}
