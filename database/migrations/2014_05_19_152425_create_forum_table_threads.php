<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('forum_threads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_category');
            $table->foreign('parent_category')->references('id')->on('forum_categories');
            $table->foreignIdFor(config('forum.integration.user_model'), 'author_id');
            $table->string('title');
            $table->boolean('pinned');
            $table->boolean('locked');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('forum_threads');
    }
};
