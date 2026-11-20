<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            // Authentication IDs are namespaced (for example, "staff:1"),
            // so this cannot be an integer foreign key.
            $table->string('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name')->unique();
            $table->timestamps();
        });
        Schema::create('specializations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
        Schema::create('staffs', function (Blueprint $table) {
            $table->bigIncrements('staff_id');
            $table->foreignId('role_id')->constrained('roles');
            $table->string('username')->unique();
            $table->string('password');
            $table->boolean('change_password')->default(true);
            $table->timestamp('password_changed_at')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix', 20)->nullable();
            $table->string('gender', 20)->nullable();
            $table->date('birthdate')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('mobile_no', 30)->nullable();
            $table->string('photo_path')->nullable();
            $table->string('employee_no')->nullable()->unique();
            $table->string('plantilla_item_no')->nullable();
            $table->string('appointment_status')->nullable();
            $table->string('fund_source')->nullable();
            $table->string('degree_earned')->nullable();
            $table->string('major_specialization')->nullable();
            $table->unsignedBigInteger('specialization_id')->nullable();
            $table->unsignedInteger('teaching_minutes')->default(0);
            $table->string('status')->default('active');
            $table->rememberToken();
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->timestamps();
            $table->foreign('specialization_id')->references('id')->on('specializations')->nullOnDelete();
        });
        Schema::create('user_activation_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->string('token')->unique();
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        Schema::create('rejection_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('reason_name')->unique();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
        Schema::create('religions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable()->unique();
            $table->string('password')->nullable();
            $table->boolean('change_password')->default(true);
            $table->timestamp('password_changed_at')->nullable();
            $table->string('lrn', 20)->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix', 20)->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('contact_no', 30)->nullable();
            $table->string('sex', 20)->nullable();
            $table->date('birthdate');
            $table->string('birthplace')->nullable();
            $table->string('mother_tongue')->nullable();
            $table->string('religion')->nullable();
            $table->string('status')->default('pending')->index();
            $table->unsignedBigInteger('activated_by')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->foreignId('rejection_reason_id')->nullable()->constrained('rejection_reasons')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->foreign('activated_by')->references('staff_id')->on('staffs')->nullOnDelete();
        });
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->bigIncrements('profile_ID');
            $table->foreignId('student_ID')->constrained('students')->cascadeOnDelete();
            $table->boolean('is_4ps')->default(false);
            $table->string('four_ps_household_id')->nullable();
            $table->boolean('is_ip')->default(false);
            $table->string('ip_community')->nullable();
            $table->boolean('has_disability')->default(false);
            $table->string('disability_name')->nullable();
            $table->timestamps();
            $table->unique('student_ID');
        });
        Schema::create('student_guardians', function (Blueprint $table) {
            $table->bigIncrements('guardian_id');
            $table->foreignId('student_ID')->constrained('students')->cascadeOnDelete();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('suffix', 20)->nullable();
            $table->string('relationship');
            $table->string('contact_no', 30)->nullable();
            $table->boolean('is_deceased')->default(false);
            $table->timestamps();
            $table->unique(['student_ID', 'relationship']);
        });
        Schema::create('student_addresses', function (Blueprint $table) {
            $table->bigIncrements('address_ID');
            $table->foreignId('student_ID')->constrained('students')->cascadeOnDelete();
            $table->string('address_type');
            $table->string('house_no')->nullable();
            $table->string('street_name')->nullable();
            $table->string('barangay')->nullable();
            $table->string('municipality')->nullable();
            $table->string('province')->nullable();
            $table->string('country')->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->timestamps();
            $table->unique(['student_ID', 'address_type']);
        });

        Schema::create('academic_years', function (Blueprint $table) {
            $table->bigIncrements('SY_ID');
            $table->string('school_year')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('status')->default(false)->index();
            $table->unsignedTinyInteger('attendance_start_month')->nullable();
            $table->unsignedTinyInteger('attendance_end_month')->nullable();
            $table->timestamps();
        });
        Schema::create('grade_level', function (Blueprint $table) {
            $table->bigIncrements('grade_ID');
            $table->string('grade_label')->unique();
            $table->string('category');
            $table->timestamps();
        });
        Schema::create('tracks', function (Blueprint $table) {
            $table->bigIncrements('track_ID');
            $table->string('name')->unique();
            $table->timestamps();
        });
        Schema::create('clusters', function (Blueprint $table) {
            $table->bigIncrements('cluster_ID');
            $table->foreignId('track_ID')->constrained('tracks', 'track_ID')->cascadeOnDelete();
            $table->string('name')->unique();
            $table->timestamps();
        });
        Schema::create('preferred_courses', function (Blueprint $table) {
            $table->bigIncrements('course_ID');
            $table->foreignId('cluster_ID')->constrained('clusters', 'cluster_ID')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['cluster_ID', 'name']);
        });
        Schema::create('sections', function (Blueprint $table) {
            $table->bigIncrements('section_ID');
            $table->string('name');
            $table->foreignId('grade_ID')->constrained('grade_level', 'grade_ID');
            $table->foreignId('cluster_ID')->nullable()->constrained('clusters', 'cluster_ID')->nullOnDelete();
            $table->unsignedBigInteger('staff_ID')->nullable();
            $table->foreignId('SY_ID')->constrained('academic_years', 'SY_ID');
            $table->string('room')->nullable();
            $table->unsignedInteger('capacity')->default(45);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->unique(['name', 'SY_ID']);
            $table->foreign('staff_ID')->references('staff_id')->on('staffs')->nullOnDelete();
        });

        foreach (['enrollment_statuses' => 'enrollment_status_ID', 'learner_types' => 'learner_type_ID', 'placement_statuses' => 'placement_status_ID', 'promotion_statuses' => 'promotion_status_ID'] as $tableName => $key) {
            Schema::create($tableName, function (Blueprint $table) use ($key) {
                $table->bigIncrements($key);
                $table->string('slug')->unique();
                $table->string('name');
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }
        Schema::create('movement_reasons', function (Blueprint $table) {
            $table->bigIncrements('reason_ID');
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
        Schema::create('enrollments', function (Blueprint $table) {
            $table->bigIncrements('enrollment_ID');
            $table->foreignId('student_ID')->constrained('students')->cascadeOnDelete();
            $table->foreignId('section_ID')->nullable()->constrained('sections', 'section_ID')->nullOnDelete();
            $table->foreignId('SY_ID')->constrained('academic_years', 'SY_ID');
            $table->foreignId('grade_ID')->constrained('grade_level', 'grade_ID');
            $table->foreignId('track_ID')->nullable()->constrained('tracks', 'track_ID')->nullOnDelete();
            $table->foreignId('cluster_ID')->nullable()->constrained('clusters', 'cluster_ID')->nullOnDelete();
            $table->string('semester')->nullable();
            $table->foreignId('course_ID')->nullable()->constrained('preferred_courses', 'course_ID')->nullOnDelete();
            $table->foreignId('learner_type_ID')->nullable()->constrained('learner_types', 'learner_type_ID')->nullOnDelete();
            $table->string('last_grade_level_completed')->nullable();
            $table->string('last_school_year_completed')->nullable();
            $table->string('last_school_attended')->nullable();
            $table->string('school_id_from_previous_school')->nullable();
            $table->foreignId('enrollment_status_ID')->nullable()->constrained('enrollment_statuses', 'enrollment_status_ID')->nullOnDelete();
            $table->foreignId('placement_status_ID')->nullable()->constrained('placement_statuses', 'placement_status_ID')->nullOnDelete();
            $table->foreignId('promotion_status_ID')->nullable()->constrained('promotion_statuses', 'promotion_status_ID')->nullOnDelete();
            $table->timestamps();
            $table->unique(['student_ID', 'SY_ID']);
        });
        Schema::create('student_movements', function (Blueprint $table) {
            $table->bigIncrements('movement_ID');
            $table->foreignId('student_ID')->constrained('students')->cascadeOnDelete();
            $table->foreignId('enrollment_ID')->nullable()->constrained('enrollments', 'enrollment_ID')->nullOnDelete();
            $table->foreignId('reason_ID')->nullable()->constrained('movement_reasons', 'reason_ID')->nullOnDelete();
            $table->date('movement_date');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('document_types', function (Blueprint $table) {
            $table->bigIncrements('document_type_ID');
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::create('document_statuses', function (Blueprint $table) {
            $table->bigIncrements('document_status_ID');
            $table->string('slug')->unique();
            $table->string('name');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::create('document_return_reasons', function (Blueprint $table) {
            $table->bigIncrements('reason_ID');
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
        Schema::create('student_documents', function (Blueprint $table) {
            $table->bigIncrements('doc_ID');
            $table->foreignId('student_ID')->constrained('students')->cascadeOnDelete();
            $table->foreignId('document_type_ID')->nullable()->constrained('document_types', 'document_type_ID')->nullOnDelete();
            $table->string('file_path');
            $table->string('original_filename')->nullable();
            $table->foreignId('document_status_ID')->nullable()->constrained('document_statuses', 'document_status_ID')->nullOnDelete();
            $table->timestamp('date_uploaded')->nullable();
            $table->timestamp('date_verified')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->foreignId('return_reason_ID')->nullable()->constrained('document_return_reasons', 'reason_ID')->nullOnDelete();
            $table->timestamps();
            $table->foreign('verified_by')->references('staff_id')->on('staffs')->nullOnDelete();
        });
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('audit_id');
            $table->dateTime('timestamp')->index();
            $table->string('user_id')->nullable()->index();
            $table->string('user_name');
            $table->string('role', 80)->index();
            $table->string('action');
            $table->string('module', 100)->index();
            $table->longText('reference')->nullable();
            $table->longText('description');
            $table->string('status', 30)->index();
        });
    }

    public function down(): void
    {
        Schema::dropAllTables();
    }
};
