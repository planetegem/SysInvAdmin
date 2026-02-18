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
        Schema::create('item_relationships', function (Blueprint $table) {
            $table->id();
            $table->string('relationship');
            $table->foreignId('subject_item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreignId('direct_object_item_id')->references('id')->on('items')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_relationships');
    }
};
