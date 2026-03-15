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
        Schema::table('items', function (Blueprint $table) {
            $table->string('slug')->nullable();
        });
        DB::table('items')->orderBy('id')->chunkById(100, function ($items) {
            foreach ($items as $item) {
                DB::table('items')->where('id', $item->id)
                    ->update([
                        'slug' => Str::slug($item->title)
                    ]);
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('slug')->nullable();
        });
        DB::table('categories')->orderBy('id')->chunkById(100, function ($categories) {
            foreach ($categories as $category) {
                DB::table('categories')->where('id', $category->id)
                    ->update([
                        'slug' => Str::slug($category->name)
                    ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
