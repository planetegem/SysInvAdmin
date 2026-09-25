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
        Schema::create('3d_models', function (Blueprint $table) {
            $table->id();

            // Properties
            $table->string('path')->unique();
            $table->string('name');
            $table->string('alt')->nullable();

            // File metadata: automatically filled on model creation
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->integer('triangle_count')->nullable();
            $table->json('bounding_box')->nullable();

            // Camera & Viewport Initial State: to be implemented
            $table->string('camera_orbit')->nullable();
            $table->string('camera_target')->nullable();
            $table->string('field_of_view')->nullable();

            // Poster / Placeholder
            $table->string('poster_path')->nullable();

            // Animations
            $table->boolean('has_animations')->default(false);
            $table->json('animation_names')->nullable(); // e.g. ["Walk", "Idle", "Open"]

            // Foreign Key Link
            $table->foreignId('medium_id')->references('id')->on('media')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('3d_models');
    }
};
