<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Cluster;
use App\Models\DocumentType;
use App\Models\Enrollment;
use App\Models\EnrollmentStatus;
use App\Models\GradeLevel;
use App\Models\GradeReturnReason;
use App\Models\GradeStatus;
use App\Models\GradingTerm;
use App\Models\LearnerType;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\StudentObservedValue;
use App\Models\StudentSubjectGrade;
use App\Models\Subject;
use App\Models\TeacherSubjectAssignment;
use App\Support\GradeRecordPeriodFilters;
use App\Support\LearnerPermanentRecordBuilder;
use App\Support\PrincipalGradeReleaseNotifier;
use App\Support\SectionGradeSubmissionProgress;
use App\Support\Sf9AttendanceSummary;
use App\Support\Sf9ReportCardBuilder;
use App\Support\TeacherGradeNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AcademicRecordsController extends Controller
{
    public function students(Request $request): View
    {
        $activeYear = AcademicYear::query()->where('status', true)->first();
        $search = trim($request->string('search')->toString());
        $status = $request->string('status')->toString();
        $learnerType = $request->string('learner_type')->toString();
        $gradeLevel = $request->string('grade_level')->toString();
        $gradeId = GradeLevel::idForValue($gradeLevel);
        $academicYearId = $request->string('academic_year_id')->toString();
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();
        $perPage = (int) $request->integer('per_page', 15);
        if (! in_array($perPage, [10, 15, 20, 50], true)) {
            $perPage = 15;
        }

        $students = Enrollment::query()
            ->with([
                'student' => fn ($query) => $query->with('application')->withCount([
                    'documents as supporting_documents_count' => fn ($documents) => $documents->where('doc_type', '!=', DocumentType::ID_PHOTO),
                ]),
                'gradeLevel',
                'cluster',
                'academicYear',
                'enrollmentStatus',
                'learnerType',
                'placementStatus',
            ])
            ->whereIn('enrollment_status_ID', EnrollmentStatus::activeIds())
            ->when($academicYearId !== '' && $academicYearId !== 'all', function ($query) use ($academicYearId): void {
                $query->where('SY_ID', $academicYearId);
            })
            ->when($academicYearId === '' && $activeYear, function ($query) use ($activeYear): void {
                $query->where('SY_ID', $activeYear->SY_ID);
            })
            ->when($status !== '' && $status !== 'all', function ($query) use ($status): void {
                $statusId = EnrollmentStatus::idFor($status);

                if ($statusId !== null) {
                    $query->where('enrollment_status_ID', $statusId);
                }
            })
            ->when($learnerType !== '', function ($query) use ($learnerType): void {
                $learnerTypeId = LearnerType::idFor($learnerType);

                if ($learnerTypeId !== null) {
                    $query->where('learner_type_ID', $learnerTypeId);
                }
            })
            ->when($gradeId, function ($query) use ($gradeId): void {
                $query->forGrade($gradeId);
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->whereHas('student', function ($studentQuery) use ($search): void {
                    $studentQuery->where('lrn', 'like', "%{$search}%")
                        ->orWhereHas('application', function ($appQuery) use ($search): void {
                            $appQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('middle_name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($dateFrom !== '' && $dateTo !== '', function ($query) use ($dateFrom, $dateTo): void {
                $query->whereBetween('created_at', [$dateFrom.' 00:00:00', $dateTo.' 23:59:59']);
            })
            ->orderByGrade()
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('users.academic-records.students', [
            'activeYear' => $activeYear,
            'students' => $students,
            'gradeLevels' => GradeLevel::options(),
            'academicYears' => AcademicYear::query()->orderByDesc('start_date')->get(),
        ]);
    }

    public function show(Request $request, Student $student): View
    {
        $request->validate(['enrollment_id' => ['nullable', 'integer']]);
        $student->load([
            'user',
            'application',
            'profile',
            'guardians',
            'addresses',
            'documents' => fn ($query) => $query->with(['documentType', 'documentStatus', 'returnReason'])
                ->where('doc_type', '!=', DocumentType::ID_PHOTO)->latest('date_uploaded'),
            'enrollments' => function ($query) {
                $query->with([
                    'academicYear',
                    'section',
                    'cluster',
                    'cluster.track',
                    'track',
                    'electives',
                    'gradeLevel',
                    'learnerType',
                    'placementStatus',
                    'grades.assignment.subject',
                    'grades.assignment.staff',
                    'enrollmentStatus',
                ])->latest('created_at');
            },
        ]);

        $enrollment = $request->filled('enrollment_id')
            ? $student->enrollments->firstWhere('enrollment_ID', $request->integer('enrollment_id'))
            : ($student->enrollments->firstWhere('SY_ID', AcademicYear::query()->where('status', true)->value('SY_ID')) ?? $student->enrollments->first());
        abort_if($request->filled('enrollment_id') && ! $enrollment, 404);

        return view('users.academic-records.students-show', [
            'student' => $student,
            'enrollment' => $enrollment,
        ]);
    }

    public function viewDocument(Student $student, StudentDocument $document): StreamedResponse|RedirectResponse
    {
        abort_unless((int) $document->student_ID === (int) $student->id, 404);

        if (! $document->file_path || ! Storage::disk('public')->exists($document->file_path)) {
            return redirect()->route(\App\Support\AcademicPortal::routeName('guidance.students.show'), $student)
                ->withErrors(['document' => 'Document file was not found.']);
        }

        return Storage::disk('public')->response($document->file_path, null, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function studentSf9(Student $student, Enrollment $enrollment): View
    {
        abort_if((int) $enrollment->student_ID !== (int) $student->id, 404);

        $enrollment->load(['student', 'section.academicYear', 'section.cluster', 'section.gradeLevel', 'section.adviser', 'section.curriculum', 'academicYear', 'cluster.track', 'track', 'gradeLevel', 'electives', 'studentSubjects']);
        $section = $enrollment->section;
        abort_if(! $section, 404);

        $assignments = TeacherSubjectAssignment::query()
            ->with(['subject.subjectType'])
            ->where('section_ID', $section->section_ID)
            ->where('SY_ID', $enrollment->SY_ID)
            ->get();

        $periods = $this->periodsForSection($section);
        $schoolDaysByMonth = Sf9AttendanceSummary::schoolDaysForSection($section);

        $grades = StudentSubjectGrade::query()
            ->whereHas('studentSubject', fn ($query) => $query->where('enrollment_ID', $enrollment->enrollment_ID))
            ->whereIn('assignment_ID', $assignments->pluck('assignment_ID'))
            ->get()
            ->groupBy('assignment_ID')
            ->map(fn ($assignmentGrades) => $assignmentGrades->keyBy('grading_period'));

        $observedValues = StudentObservedValue::query()
            ->where('enrollment_ID', $enrollment->enrollment_ID)
            ->get()
            ->groupBy('statement_key')
            ->map(fn ($statementValues) => $statementValues->keyBy('grading_period'));

        return view('users.teacher.advisory.sf9-print', [
            'section' => $section,
            'periods' => $periods,
            'cards' => collect([
                Sf9ReportCardBuilder::buildCard(
                    $enrollment,
                    $section,
                    $assignments,
                    $grades,
                    $observedValues,
                    $periods,
                    $schoolDaysByMonth,
                ),
            ]),
            'observedValueStatements' => Sf9ReportCardBuilder::observedValueStatements(),
            'observedValueMarkings' => Sf9ReportCardBuilder::observedValueMarkings(),
        ]);
    }

    public function studentSf10(Student $student): View
    {
        $enrollments = Enrollment::query()
            ->with(['student', 'section.academicYear', 'section.adviser', 'section.gradeLevel'])
            ->where('student_ID', $student->id)
            ->whereIn('enrollment_status_ID', EnrollmentStatus::activeIds())
            ->get()
            ->sortBy([
                fn (Enrollment $enrollment): int => (int) preg_replace('/\D+/', '', $enrollment->grade_level),
                fn (Enrollment $enrollment): string => $enrollment->academicYear?->school_year ?? '',
            ])
            ->values();

        abort_if($enrollments->isEmpty(), 404);

        return view('users.teacher.advisory.sf10-print', [
            'section' => $enrollments->last()?->section,
            'periods' => GradingTerm::periodsForSection($enrollments->last()?->section),
            'cards' => LearnerPermanentRecordBuilder::buildCardsForStudents($enrollments->take(1)),
        ]);
    }

}
