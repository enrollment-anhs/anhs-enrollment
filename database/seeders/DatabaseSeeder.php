<?php

namespace Database\Seeders;

use App\Models\RejectionReason;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed only reference data required by the enrollment workflow.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DefaultNonStudentUsersSeeder::class,
            TrackSeeder::class,
            ClusterSeeder::class,
            SubjectTypeSeeder::class,
            SubjectSeeder::class,
            PreferredCourseSeeder::class,
            GradeLevelSeeder::class,
            SchoolInformationSeeder::class,
            ReligionSeeder::class,
            AcademicYearSeeder::class,
            MovementReasonSeeder::class,
            DocumentReturnReasonSeeder::class,
            DocumentTypeSeeder::class,
            DocumentStatusSeeder::class,
            NotificationTypeSeeder::class,
            EnrollmentStatusSeeder::class,
            PlacementStatusSeeder::class,
            PromotionStatusSeeder::class,
            LearnerTypeSeeder::class,
        ]);

        foreach (
            [
                ['reason_name' => 'LRN not found in LIS', 'description' => 'No matching learner record was found in LIS.'],
                ['reason_name' => 'Name mismatch with LIS', 'description' => 'Submitted name does not match LIS record.'],
                ['reason_name' => 'Birthdate mismatch with LIS', 'description' => 'Submitted birthdate does not match LIS record.'],
                ['reason_name' => 'Duplicate approved record', 'description' => 'An account for this learner already exists.'],
            ] as $reason
        ) {
            RejectionReason::query()->firstOrCreate(
                ['reason_name' => $reason['reason_name']],
                ['description' => $reason['description'], 'status' => true],
            );
        }
    }
}
