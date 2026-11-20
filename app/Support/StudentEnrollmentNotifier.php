<?php

namespace App\Support;

use App\Models\Enrollment;
use App\Models\Student;
use App\Notifications\EnrollmentStatusUpdated;

class StudentEnrollmentNotifier
{
    public static function send(Student $student, Enrollment $enrollment, string $status): void
    {
        $enrollment->loadMissing(['academicYear', 'student']);

        $student->notify(new EnrollmentStatusUpdated($enrollment, $status));
    }
}
