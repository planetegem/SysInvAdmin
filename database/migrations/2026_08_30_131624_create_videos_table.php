<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('path')->unique();
            $table->string('name');

            // SEO properties 
            $table->string('title')->nullable();
            $table->string('description')->nullable();

            // File metadata, 
            // Should only be null if path doesn't point to a real file
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->float('aspect_ratio', 4)->nullable();
            $table->float('duration', 2)->nullable();

            // Special type selectors
            $table->boolean('has_audio')->default(true);
            $table->boolean('loop')->default(false);

            // Poster / Placeholder
            $table->string('poster_path')->nullable();

            // Link to medium: should cascade on delete
            $table->foreignId('medium_id')->references('id')->on('media')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
