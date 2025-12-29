<?php

namespace App\Http\Controllers;

use App\Models\{
    Task,
    TaskStatus,
    TaskPriority,
    TaskRelation,
    TaskActivity,
    TaskSubTask,
    TaskActivityAttachment,
    User,
    TaskForward,
    Departments
};
use Illuminate\Http\Request;
use DB;

class TaskController extends Controller
{

    public function list(Request $request)
{
    $user = auth()->user();

    $tasks = Task::with([
        'status',
        'priority',
        'owner:id,name',
        'assignees:id,name'
    ])

    //  ROLE-BASED VISIBILITY (NO hasRole)
    ->when(!$user->roles()->where('role_name', 'Super Admin')->exists(), function ($q) use ($user) {

        $q->where(function ($q) use ($user) {

            // Owner can see
            $q->where('task_owner_id', $user->id)

              // Assignee can see
              ->orWhereHas('assignees', function ($a) use ($user) {
                  $a->where('users.id', $user->id);
              });

        });
    })

    // 🔎 FILTERS
    ->when($request->status, fn ($q) =>
        $q->where('task_status_id', $request->status)
    )
    ->when($request->priority, fn ($q) =>
        $q->where('task_priority_id', $request->priority)
    )
    ->when($request->owner, fn ($q) =>
        $q->where('task_owner_id', $request->owner)
    )
    ->when($request->search, fn ($q) =>
        $q->where('title', 'like', '%' . $request->search . '%')
    )
    ->latest()
    ->paginate(15);

    $departments = Departments::orderBy('name')->get();

    // AJAX FILTER RESPONSE
    if ($request->ajax() && $request->has('filter')) {
        return view('tasks.partials.table', compact('tasks', 'departments'));
    }

    // FULL LOAD
    return view('tasks.list', [
        'tasks'       => $tasks,
        'statuses'    => TaskStatus::all(),
        'priorities'  => TaskPriority::all(),
        'users'       => User::orderBy('name')->get(),
        'departments' => $departments,
    ]);
}



    public function create(Request $request)
    {
        $task = null;

        if ($request->id) {
            $task = Task::with('assignees')->findOrFail($request->id);
        }

        return view('tasks.create', [
            'task' => $task,
            'statuses' => TaskStatus::all(),
            'priorities' => TaskPriority::all(),
            'users' => User::orderBy('name')->get(),
        ]);
    }




    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable|exists:tasks,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'task_status_id' => 'required|exists:task_statuses,id',
            'task_priority_id' => 'required|exists:task_priorities,id',
            'task_owner_id' => 'required|exists:users,id',
            'assignees' => 'nullable|array',
            'assignees.*' => 'exists:users,id',
            'due_at' => 'nullable|date',
        ]);

        DB::transaction(function () use ($data) {

            /** ---------------------------
             * CREATE / UPDATE TASK
             * -------------------------- */
            $isUpdate = isset($data['id']);

            $task = $isUpdate
                ? Task::findOrFail($data['id'])
                : new Task();

            // 🔹 CAPTURE OLD ASSIGNEES (CRITICAL)
            $oldAssignees = $task->exists
                ? TaskRelation::where('task_id', $task->id)
                    ->where('relation_type', 'assignee')
                    ->pluck('related_id')
                    ->toArray()
                : [];

            $task->fill([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'task_status_id' => $data['task_status_id'],
                'task_priority_id' => $data['task_priority_id'],
                'task_owner_id' => $data['task_owner_id'],
                'due_at' => $data['due_at'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $task->save();

            /** ---------------------------
             * ASSIGNEE SYNC
             * -------------------------- */
            $newAssignees = $data['assignees'] ?? [];

            // reset
            TaskRelation::where('task_id', $task->id)
                ->where('relation_type', 'assignee')
                ->delete();

            foreach ($newAssignees as $uid) {
                TaskRelation::create([
                    'task_id' => $task->id,
                    'related_type' => User::class,
                    'related_id' => $uid,
                    'relation_type' => 'assignee',
                ]);
            }

            /** ---------------------------
             * ASSIGNMENT ACTIVITY (IMPORTANT)
             * -------------------------- */
            $added = array_diff($newAssignees, $oldAssignees);
            $removed = array_diff($oldAssignees, $newAssignees);

            if ($added || $removed) {

                $parts = [];

                if ($added) {
                    $names = User::whereIn('id', $added)->pluck('name')->join(', ');
                    $parts[] = "Assigned {$names}";
                }

                if ($removed) {
                    $names = User::whereIn('id', $removed)->pluck('name')->join(', ');
                    $parts[] = "Removed {$names}";
                }

                TaskActivity::create([
                    'task_id' => $task->id,
                    'actor_id' => auth()->id(),
                    'message' => implode('. ', $parts),
                ]);
            }

            /** ---------------------------
             * CREATE / UPDATE ACTIVITY
             * -------------------------- */
            TaskActivity::create([
                'task_id' => $task->id,
                'actor_id' => auth()->id(),
                'message' => $isUpdate ? 'Task updated' : 'Task created',
            ]);
        });

        return response()->json(['success' => true]);
    }



    public function show(Task $task)
    {
        $task->load([
            'status',
            'priority',
            'owner:id,name',
            'assignees:id,name',

            'subTasks.status',
            'subTasks.priority',
            'subTasks.owner:id,name',

            //  FORWARDED DEPARTMENTS
            'forwards.department:id,name',
            'forwards.user:id,name',

            'activities' => fn($q) =>
                $q->latest()->with(['actor:id,name', 'attachments']),

            'activities.actor:id,name',
            'activities.attachments',
        ]);

        return view('tasks.show', compact('task'));
    }



    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:tasks,id'
        ]);

        $task = Task::findOrFail($request->id);

        DB::transaction(function () use ($task) {

            TaskActivity::create([
                'task_id' => $task->id,
                'actor_id' => auth()->id(),
                'task_owner_id' => $task->task_owner_id,
                'message' => 'Task deleted'
            ]);

            $task->forceDelete(); // FK cascade handles the rest
        });

        return response()->json(['success' => true]);
    }



    public function forward(Request $request, Task $task)
    {
        $data = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'message' => 'nullable|string|max:1000',
            'attachments.*' => 'file|max:10240',
        ]);

        DB::transaction(function () use ($task, $data, $request) {

            $department = Departments::findOrFail($data['department_id']);

            TaskForward::updateOrCreate(
                [
                    'task_id' => $task->id,
                    'department_id' => $department->id,
                ],
                [
                    'forwarded_by' => auth()->id(),
                    'message' => $data['message'] ?? null,
                ]
            );

            $activity = TaskActivity::create([
                'task_id' => $task->id,
                'actor_id' => auth()->id(),
                'message' => 'Task forwarded to ' . $department->name,
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {

                    $path = $file->store('task_attachments', 'public');

                    TaskActivityAttachment::create([
                        'task_activity_id' => $activity->id,
                        'file_path' => $path,
                    ]);
                }
            }
        });

        return response()->json(['success' => true]);
    }



    public function deleteForward(TaskForward $forward)
    {
        $taskId = $forward->task_id;

        $forward->delete();

        TaskActivity::create([
            'task_id' => $taskId,
            'actor_id' => auth()->id(),
            'message' => 'Task forward removed from department',
        ]);

        return response()->json(['success' => true]);
    }


    /** SUB-TASKS */
    public function storeSubTask(Request $request, Task $task)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'task_status_id' => 'required|exists:task_statuses,id',
            'task_priority_id' => 'required|exists:task_priorities,id',
            'due_at' => 'nullable|date',
        ]);

        // 🔒 HARD DUPLICATE PROTECTION
        $exists = TaskSubTask::where('task_id', $task->id)
            ->where('title', $data['title'])
            ->where('task_owner_id', auth()->id())
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This sub-task already exists'
            ], 422);
        }

        $subtask = DB::transaction(function () use ($data, $task) {

            $subtask = TaskSubTask::create([
                'task_id' => $task->id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'task_status_id' => $data['task_status_id'],
                'task_priority_id' => $data['task_priority_id'],
                'task_owner_id' => auth()->id(),
                'due_at' => $data['due_at'] ?? null,
            ]);

            TaskActivity::create([
                'task_id' => $task->id,
                'sub_task_id' => $subtask->id,
                'actor_id' => auth()->id(),
                'task_owner_id' => $task->task_owner_id,
                'message' => "Sub-task created: {$subtask->title}",
            ]);

            return $subtask;
        });

        return response()->json([
            'success' => true,
            'data' => $subtask->load('status', 'priority', 'owner'),
        ]);
    }


    public function updateSubTask(Request $request, TaskSubTask $subtask)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'task_status_id' => 'required|exists:task_statuses,id',
            'task_priority_id' => 'required|exists:task_priorities,id',
            'due_at' => 'nullable|date',
        ]);

        DB::transaction(function () use ($subtask, $data) {

            $subtask->update($data);

            TaskActivity::create([
                'task_id' => $subtask->task_id,
                'sub_task_id' => $subtask->id,
                'actor_id' => auth()->id(),
                'message' => "Sub-task updated: {$subtask->title}",
            ]);
        });

        return response()->json(['success' => true]);
    }


    public function changeSubTaskStatus(Request $request, TaskSubTask $subtask)
    {
        $request->validate([
            'task_status_id' => 'required|exists:task_statuses,id'
        ]);

        $from = $subtask->task_status_id;

        DB::transaction(function () use ($subtask, $request, $from) {

            $subtask->update([
                'task_status_id' => $request->task_status_id
            ]);

            TaskActivity::create([
                'task_id' => $subtask->task_id,
                'sub_task_id' => $subtask->id,
                'actor_id' => auth()->id(),
                'from_status_id' => $from,
                'to_status_id' => $request->task_status_id,
                'message' => "Sub-task status changed",
            ]);
        });

        return response()->json(['success' => true]);
    }


    public function deleteSubTask(TaskSubTask $subtask)
    {
        // OPTIONAL permission check
        // abort_unless(auth()->user()->can('delete', $subtask), 403);

        DB::transaction(function () use ($subtask) {

            TaskActivity::create([
                'task_id' => $subtask->task_id,
                'sub_task_id' => $subtask->id,
                'actor_id' => auth()->id(),
                'task_owner_id' => $subtask->task->task_owner_id,
                'message' => "Sub-task deleted",
            ]);

            $subtask->delete(); // soft delete
        });

        return response()->json(['success' => true]);
    }


    public function comment(Request $request, Task $task)
    {
        abort_if(!auth()->check(), 401);

        $request->validate([
            'message' => 'required|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $activity = DB::transaction(function () use ($request, $task) {

            $activity = TaskActivity::create([
                'task_id' => $task->id,
                'actor_id' => auth()->id(),
                'task_owner_id' => $task->task_owner_id,
                'message' => $request->message,
            ]);

            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('task_attachments', 'public');

                TaskActivityAttachment::create([
                    'task_activity_id' => $activity->id,
                    'file_path' => $path,
                ]);
            }

            return $activity->load('actor', 'attachments');
        });

        return response()->json([
            'success' => true,
            'activity' => $activity,
        ]);
    }


    public function changeTaskStatus(Request $request, Task $task)
    {
        $map = [
            'pause' => 'Paused',
            'resume' => 'In Progress',
            'close' => 'Closed',
        ];

        abort_unless(isset($map[$request->action]), 400);

        $status = TaskStatus::where('name', $map[$request->action])->firstOrFail();

        DB::transaction(function () use ($task, $status, $request) {
            $task->update(['task_status_id' => $status->id]);

            TaskActivity::create([
                'task_id' => $task->id,
                'actor_id' => auth()->id(),
                'task_owner_id' => $task->task_owner_id,
                'message' => "Task {$request->action}d",
            ]);
        });

        return response()->json(['success' => true]);
    }

}