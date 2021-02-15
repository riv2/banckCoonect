<?php

use Illuminate\Database\Seeder;

class LanguageLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('language_level')->insert([
            [
                'id'        => 1,
                'language'  => 'en',
                'level'     => 'A1',
                'created_at' => DB::raw('now()'),
                'updated_at' => DB::raw('now()'),
            ],
            [
                'id'        => 2,
                'language'  => 'en',
                'level'     => 'A2',
                'created_at' => DB::raw('now()'),
                'updated_at' => DB::raw('now()'),
            ],
            [
                'id'        => 3,
                'language'  => 'en',
                'level'     => 'B1',
                'created_at' => DB::raw('now()'),
                'updated_at' => DB::raw('now()'),
            ],
            [
                'id'        => 4,
                'language'  => 'en',
                'level'     => 'B2',
                'created_at' => DB::raw('now()'),
                'updated_at' => DB::raw('now()'),
            ],
            [
                'id'        => 5,
                'language'  => 'en',
                'level'     => 'C1',
                'created_at' => DB::raw('now()'),
                'updated_at' => DB::raw('now()'),
            ],
            [
                'id'        => 6,
                'language'  => 'en',
                'level'     => 'C2',
                'created_at' => DB::raw('now()'),
                'updated_at' => DB::raw('now()'),
            ]
        ]);
    }
}
