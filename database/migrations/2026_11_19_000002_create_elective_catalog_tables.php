<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_types', function (Blueprint $table): void {
            $table->increments('subject_type_ID');
            $table->string('key')->unique();
            $table->string('label');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table): void {
            $table->increments('subject_ID');
            $table->string('school_level')->default('Senior High School');
            $table->unsignedBigInteger('cluster_ID')->nullable();
            $table->unsignedInteger('subject_type_ID');
            $table->string('code')->unique();
            $table->string('title');
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->timestamps();
            $table->foreign('cluster_ID')->references('cluster_ID')->on('clusters')->nullOnDelete();
            $table->foreign('subject_type_ID')->references('subject_type_ID')->on('subject_types')->restrictOnDelete();
        });

        Schema::create('enrollment_electives', function (Blueprint $table): void {
            $table->unsignedBigInteger('enrollment_ID');
            $table->unsignedInteger('subject_ID');
            $table->timestamps();
            $table->foreign('enrollment_ID')->references('enrollment_ID')->on('enrollments')->cascadeOnDelete();
            $table->foreign('subject_ID')->references('subject_ID')->on('subjects')->restrictOnDelete();
            $table->primary(['enrollment_ID', 'subject_ID']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_electives');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('subject_types');
    }
};
