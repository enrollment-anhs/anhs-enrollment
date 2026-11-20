<?php

namespace Database\Seeders;

use App\Models\SubjectType;
use Illuminate\Database\Seeder;

class SubjectTypeSeeder extends Seeder
{
    public function run(): void
    {
        SubjectType::query()->updateOrCreate(
            ['key' => 'elective'],
            ['label' => 'Elective', 'sort_order' => 1],
        );
    }
}
