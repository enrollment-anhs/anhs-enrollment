<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('enrollments', 'semester')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->string('semester')->nullable()->after('cluster_ID');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('enrollments', 'semester')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->dropColumn('semester');
            });
        }
    }
};
