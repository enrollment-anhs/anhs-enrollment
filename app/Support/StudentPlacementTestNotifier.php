<?php

namespace App\Support;

use App\Models\Enrollment;
use App\Models\PlacementStatus;
use App\Notifications\PlacementStatusUpdated;

class StudentPlacementTestNotifier
{
    public static function sendIfNewlyRecommended(Enrollment $enrollment, ?string $previousStatus = null): void
    {
        $enrollment->loadMissing(['student', 'academicYear', 'gradeLevel']);

        $student = $enrollment->student;

        if (! $student) {
            return;
        }

        $currentStatus = $enrollment->placement_status;

        if ($previousStatus === $currentStatus) {
            return;
        }

        $isInitialNonRecommendation = $previousStatus === null && $currentStatus !== PlacementStatus::RECOMMENDED;

        if (! $isInitialNonRecommendation && $currentStatus !== '') {
            $student->notify(new PlacementStatusUpdated($enrollment, $currentStatus));
        }

        if ($previousStatus === PlacementStatus::RECOMMENDED) {
            return;
        }

        if (! $enrollment->isPlacementRecommended()) {
            return;
        }

    }
}
