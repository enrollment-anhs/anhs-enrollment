<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('notification_types')) {
            Schema::create('notification_types', function (Blueprint $table) {
                $table->bigIncrements('notification_type_ID');
                $table->string('slug')->unique();
                $table->string('name');
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        Schema::table('notifications', function (Blueprint $table) {
            if (! Schema::hasColumn('notifications', 'notification_type_ID')) {
                $table->unsignedBigInteger('notification_type_ID')->nullable()->index()->after('data');
            }

            if (! Schema::hasColumn('notifications', 'seen_at')) {
                $table->timestamp('seen_at')->nullable()->after('read_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'notification_type_ID')) {
                $table->dropColumn('notification_type_ID');
            }

            if (Schema::hasColumn('notifications', 'seen_at')) {
                $table->dropColumn('seen_at');
            }
        });

        Schema::dropIfExists('notification_types');
    }
};
