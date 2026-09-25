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
        Schema::create('images', function (Blueprint $table) {
            $table->id();

            // Properties
            $table->string('path')->unique();
            $table->string('name');
            $table->string('alt')->nullable();

            // File meta data
            // Should only be null if image path doesn't point to real image when saving
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->float('aspect_ratio', 4)->nullable();

            // Clipping mask: clip_path defines the path as a series of (x,y) coords, clip_type says how to interpret this
            // To be implemented
            $table->string('clip_type')->nullable();
            $table->json('clip_path')->nullable();

            // Fallbacks: to be implemented
            // 1. placeholder might be used for blurhash string or similar; to be decided 
            $table->string('placeholder')->nullable();
            // 2. Variants might hold paths to resized images (for use in srcset)
            $table->json('variants')->nullable();

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
        Schema::dropIfExists('images');
    }
};
