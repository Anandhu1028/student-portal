<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('task_types')->insert([
            ['name' => 'General'],
            ['name' => 'Approval'],
            ['name' => 'Reminder'],
            ['name' => 'Coding'],
        ]);
    }
}
