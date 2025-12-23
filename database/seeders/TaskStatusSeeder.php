<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskStatusSeeder extends Seeder
{
    public function run()
    {
        DB::table('task_statuses')->insert([
            [
                'name'        => 'Open',
                'slug'        => 'open',
                'is_initial'  => true,
                'is_terminal' => false,
                'sort_order'  => 1,
            ],
            [
                'name'        => 'In Progress',
                'slug'        => 'in_progress',
                'is_initial'  => false,
                'is_terminal' => false,
                'sort_order'  => 2,
            ],
            [
                'name'        => 'Paused',
                'slug'        => 'paused',
                'is_initial'  => false,
                'is_terminal' => false,
                'sort_order'  => 3,
            ],
            [
                'name'        => 'Closed',
                'slug'        => 'closed',
                'is_initial'  => false,
                'is_terminal' => true,
                'sort_order'  => 4,
            ],
        ]);
    }
}
