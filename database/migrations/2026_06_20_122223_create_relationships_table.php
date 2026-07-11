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
        // Create new relationships table
        Schema::create('relationships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('subject_label')->nullable();
            $table->string('object_label')->nullable();
            $table->string('subject_descriptor')->nullable();
            $table->string('object_descriptor')->nullable();
            $table->timestamps();
        });

        // Prefill with some default values
        DB::table('relationships')->insert([
            [
                'id' => 1,
                'name' => 'Parent/Child',
                'type' => 'hierarchical',
                'subject_label' => 'parent_of',
                'object_label' => 'child_of',
                'subject_descriptor' => 'is a parent of',
                'object_descriptor' => 'is a child of',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Sibling',
                'type' => 'lateral',
                'subject_label' => 'sibling_of',
                'object_label' => 'sibling_of',
                'subject_descriptor' => 'is a sibling of',
                'object_descriptor' => 'is a sibling of',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Update item_relationships table
        // 1. Add the new foreign key column as nullable temporarily
        Schema::table('item_relationships', function (Blueprint $table) {
            $table->foreignId('relationship_id')->nullable()->after('id');
        });

        // 2. Set all existing records to relationship_id 1 (Parent/Child)
        DB::table('item_relationships')->whereNull('relationship_id')->update([
            'relationship_id' => 1
        ]);

        // 3. Replace old relationship column with new foreign id column
        Schema::table('item_relationships', function (Blueprint $table) {
            $table->foreignId('relationship_id')->nullable(false)->change()->constrained('relationships')->onDelete('cascade');
            $table->dropColumn('relationship');
        });

        // 4. Remove type column from items
        Schema::table('items', function (Blueprint $table){
            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_relationships', function (Blueprint $table) {
            $table->string('relationship')->nullable()->after('id');
        });

        DB::table('item_relationships')->where('relationship_id', 1)->update(['relationship' => 'parent/child']);
        DB::table('item_relationships')->where('relationship_id', 2)->update(['relationship' => 'translation']);

        Schema::table('item_relationships', function (Blueprint $table) {
            $table->dropForeign(['relationship_id']);
            $table->dropColumn('relationship_id');
            $table->string('relationship')->nullable(false)->change();
        });

        Schema::dropIfExists('relationships');
    }
};
