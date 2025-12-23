<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskPrioritySeeder extends Seeder
{
    public function run()
    {
        DB::table('task_priorities')->insert([
            [
                'name'  => 'Low',
                'level' => 1,
                'color' => '#6c757d',
            ],
            [
                'name'  => 'Medium',
                'level' => 3,
                'color' => '#ffc107',
            ],
            [
                'name'  => 'High',
                'level' => 5,
                'color' => '#dc3545',
            ],
        ]);
    }
}
