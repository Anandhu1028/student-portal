<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\TaskStatus;
use App\Models\TaskPriority;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // minimal seed
        TaskStatus::create(['name' => 'Open']);
        TaskPriority::create(['name' => 'Normal']);
    }

    public function test_create_task()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson(route('tasks.store'), [
            'title' => 'Test Task',
            'task_status_id' => TaskStatus::first()->id,
            'task_priority_id' => TaskPriority::first()->id,
            'task_owner_id' => $user->id,
        ])->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('tasks', ['title' => 'Test Task']);
    }
}
