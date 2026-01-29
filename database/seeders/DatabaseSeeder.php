<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $languages = [
            [
                'name' => 'English',
                'short_name' => 'EN',
                'native_name' => 'English',
            ],
            [
                'name' => 'Dutch',
                'short_name' => 'NL',
                'native_name' => 'Nederlands',
            ]
        ];

        DB::table('languages')->insert($languages);
        
    }
}
