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
        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('type');
            $table->longText('content');
            $table->morphs('contentable');
        });

        DB::table('items')->orderBy('id')->chunkById(100, function ($items) {
            foreach ($items as $item) {
                DB::table('content_blocks')->insert([
                    'contentable_id' => $item->id,
                    'contentable_type' => 'App\Models\Item',
                    'content' => $item->description,
                    'type' => 'source',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_blocks');
    }
};
