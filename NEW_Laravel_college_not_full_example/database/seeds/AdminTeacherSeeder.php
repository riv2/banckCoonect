<?php

use Illuminate\Database\Seeder;

class AdminTeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'name'          => 'admin_teacher',
            'created_at'    => DB::raw('now()'),
            'updated_at'    => DB::raw('now()'),
        ]);
    }
}
