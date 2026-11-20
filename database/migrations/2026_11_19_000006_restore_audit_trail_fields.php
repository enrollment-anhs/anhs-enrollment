<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('audit_logs', 'timestamp')) {
                $table->dateTime('timestamp')->nullable()->index()->after('audit_ID');
            }
            if (! Schema::hasColumn('audit_logs', 'user_id')) {
                $table->string('user_id')->nullable()->index()->after('timestamp');
            }
            if (! Schema::hasColumn('audit_logs', 'user_name')) {
                $table->string('user_name')->nullable()->after('user_id');
            }
            if (! Schema::hasColumn('audit_logs', 'role')) {
                $table->string('role', 80)->nullable()->index()->after('user_name');
            }
            if (! Schema::hasColumn('audit_logs', 'module')) {
                $table->string('module', 100)->nullable()->index()->after('action');
            }
            if (! Schema::hasColumn('audit_logs', 'reference')) {
                $table->longText('reference')->nullable()->after('module');
            }
            if (! Schema::hasColumn('audit_logs', 'description')) {
                $table->longText('description')->nullable()->after('reference');
            }
            if (! Schema::hasColumn('audit_logs', 'status')) {
                $table->string('status', 30)->nullable()->index()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            foreach (['timestamp', 'user_id', 'user_name', 'role', 'module', 'reference', 'description', 'status'] as $column) {
                if (Schema::hasColumn('audit_logs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
