<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('forum_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('newest_thread_id')->nullable()->after('accepts_threads');
            $table->unsignedBigInteger('latest_active_thread_id')->nullable()->after('newest_thread_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_categories', function (Blueprint $table) {
            $table->dropColumn('newest_thread_id');
            $table->dropColumn('latest_active_thread_id');
        });
    }
};
