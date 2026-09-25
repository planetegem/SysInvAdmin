<?php

use App\Models\Image;
use App\Models\Medium;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Move everything from the media table to images table
        $media = Medium::all();

        foreach ($media as $medium) {
            $image = $medium->images()->create([
                'path' => $medium->file_path,
                'name' => pathinfo($medium->file_name, PATHINFO_FILENAME),
                'alt' => $medium->alt
            ]);
        }

        // Clean media table
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'file_name', 'alt']);
            $table->renameColumn('file_type', 'type');
            $table->string('mediable_type')->nullable()->change();
            $table->unsignedBigInteger('mediable_id')->nullable()->change();
        });

        // Drop unnecessary column from items table
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('file_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('media', function (Blueprint $table) {
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('alt')->nullable();
            $table->renameColumn('type', 'file_type');
            $table->string('mediable_type')->nullable(false)->change();
            $table->unsignedBigInteger('mediable_id')->nullable(false)->change();
        });

        $images = Image::all();

        foreach ($images as $image) {
            if ($image->media) {
                $image->media->update([
                    'file_path' => $image->path,
                    'file_name' => $image->name,
                    'alt' => $image->alt,
                ]);
            }
        }
    }
};
