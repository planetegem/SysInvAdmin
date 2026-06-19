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
        Schema::table('media', function (Blueprint $table) {
            $table->string('mediable_type')->nullable()->after('item_id');
            $table->unsignedBigInteger('mediable_id')->nullable()->after('mediable_type');
            $table->index(['mediable_type', 'mediable_id']);
        });

        DB::table('media')->whereNotNull('item_id')->update([
            'mediable_id' => DB::raw('item_id'),
            'mediable_type' => 'App\Models\Item',
        ]);

        Schema::table('media', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropColumn('item_id');

            $table->string('mediable_type')->nullable(false)->change();
            $table->unsignedBigInteger('mediable_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->nullable()->after('id');
        });

        DB::table('media')->where('mediable_type', 'App\Models\Item')->update([
            'item_id' => DB::raw('mediable_id'),
        ]);

        Schema::table('media', function (Blueprint $table) {
            $table->dropIndex(['mediable_type', 'mediable_id']);
            $table->dropColumn(['mediable_type', 'mediable_id']);
        });
    }
};
