<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Cluster;
use App\Models\GradeLevel;
use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Create the initial enrollment sections for the active school year.
     */
    public function run(): void
    {
        $academicYear = AcademicYear::query()->where('status', true)->firstOrFail();
        $gradeIds = GradeLevel::query()->pluck('grade_ID', 'grade_label');
        $clusterIds = Cluster::query()->pluck('cluster_ID', 'name');

        $sections = [];

        // Grade 7 has ten sections. Grades 8–10 have five sections each.
        foreach ([
            'Grade 7' => range('A', 'J'),
            'Grade 8' => range('A', 'E'),
            'Grade 9' => range('A', 'E'),
            'Grade 10' => range('A', 'E'),
        ] as $gradeLabel => $letters) {
            foreach ($letters as $letter) {
                $sections[] = [
                    'name' => "{$gradeLabel} - {$letter}",
                    'grade_label' => $gradeLabel,
                    'cluster_name' => null,
                ];
            }
        }

        // Grade 11 has five sections for each senior-high track.
        foreach ([
            'Arts, Social Sciences & Humanities',
            'Business and Entrepreneurship',
            'Science, Technology, Engineering and Mathematics',
            'Sports, Health, and Wellness',
            'Science, Technology, Engineering and Mathematics',
        ] as $index => $clusterName) {
            $sections[] = [
                'name' => 'Grade 11 Academic - '.chr(65 + $index),
                'grade_label' => 'Grade 11',
                'cluster_name' => $clusterName,
            ];
        }

        foreach ([
            'ICT Support and Computer Programming Technologies',
            'Aesthetic, Wellness, and Human Care',
            'Agri-Fishery Business and Food Innovation',
            'Automotive and Small Engine Technologies',
            'Hospitality and Tourism',
        ] as $index => $clusterName) {
            $sections[] = [
                'name' => 'Grade 11 Technical - '.chr(65 + $index),
                'grade_label' => 'Grade 11',
                'cluster_name' => $clusterName,
            ];
        }

        // Grade 12 has five sections, split across the available SHS tracks.
        foreach ([
            'Arts, Social Sciences & Humanities',
            'Business and Entrepreneurship',
            'Science, Technology, Engineering and Mathematics',
            'ICT Support and Computer Programming Technologies',
            'Hospitality and Tourism',
        ] as $index => $clusterName) {
            $sections[] = [
                'name' => 'Grade 12 - '.chr(65 + $index),
                'grade_label' => 'Grade 12',
                'cluster_name' => $clusterName,
            ];
        }

        foreach ($sections as $section) {
            Section::query()->updateOrCreate(
                [
                    'name' => $section['name'],
                    'SY_ID' => $academicYear->SY_ID,
                ],
                [
                    'grade_ID' => $gradeIds[$section['grade_label']],
                    'cluster_ID' => $section['cluster_name'] ? $clusterIds[$section['cluster_name']] : null,
                    'staff_ID' => null,
                    'capacity' => 45,
                    'status' => true,
                ],
            );
        }
    }
}
