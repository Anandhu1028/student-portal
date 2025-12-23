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
    User
};
use Illuminate\Http\Request;
use DB;

class TaskController extends Controller
{
    
     public function list(Request $request)
    {
        $tasks = Task::with([
                'status',
                'priority',
                'owner:id,name',
                'assignees:id,name'
            ])
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

        // 🔑 IMPORTANT: return PARTIAL only for filter AJAX
        if ($request->ajax() && $request->has('filter')) {
            return view('tasks.partials.table', compact('tasks'));
        }

        // 🔑 Sidebar initial load (AJAX) → FULL CONTENT ONLY
        return view('tasks.list', [
            'tasks'      => $tasks,
            'statuses'   => TaskStatus::all(),
            'priorities' => TaskPriority::all(),
            'users'      => User::orderBy('name')->get(),
        ]);
    }

    public function create()
{
    return view('tasks.create', [
        'statuses'   => TaskStatus::all(),
        'priorities' => TaskPriority::all(),
        'users'      => User::orderBy('name')->get(),
    ]);
}


   public function store(Request $request)
{
    $data = $request->validate([
        'title'            => 'required|string|max:255',
        'description'      => 'nullable|string',
        'task_status_id'   => 'required|exists:task_statuses,id',
        'task_priority_id' => 'required|exists:task_priorities,id',
        'task_owner_id'    => 'required|exists:users,id',
        'task_type_id'     => 'nullable|exists:task_types,id',
        'assignees'        => 'nullable|array',
        'assignees.*'      => 'exists:users,id',
        'due_at'           => 'nullable|date',
    ]);

    \DB::transaction(function () use ($data) {

        /** ---------------------------
         * CREATE TASK
         * -------------------------- */
        $task = Task::create([
            'title'            => $data['title'],
            'description'      => $data['description'] ?? null,
            'task_status_id'   => $data['task_status_id'],
            'task_priority_id' => $data['task_priority_id'],
            'task_owner_id'    => $data['task_owner_id'],
            'task_type_id'     => $data['task_type_id'] ?? null,
            'due_at'           => $data['due_at'] ?? null,
            'created_by'       => auth()->id(),
        ]);

        /** ---------------------------
         * ASSIGNED USERS (task_relations)
         * -------------------------- */
        foreach ($data['assignees'] ?? [] as $userId) {
            TaskRelation::create([
                'task_id'       => $task->id,
                'related_type'  => User::class,
                'related_id'    => $userId,
                'relation_type' => 'assignee',
            ]);
        }

        /** ---------------------------
         * ACTIVITY LOG (MATCHES YOUR SCHEMA)
         * -------------------------- */
           TaskActivity::create([
            'task_id'       => $task->id,
            'actor_id'      => auth()->id(),
            'task_owner_id' => $task->task_owner_id,
            'message'       => 'Task created',
        ]);
    });

    return response()->json(['success' => true]);
}


    public function show(Task $task)
    {
        $task->load([
            'status',
            'priority',
            'owner',
            'assignees',
            'subTasks.owner',
            'activities.actor',
            'activities.attachments'
        ]);

        return view('tasks.show', compact('task'));
    }


    public function delete(Request $request)
{
    $request->validate([
        'id' => 'required|exists:tasks,id'
    ]);

    $task = Task::withTrashed()->findOrFail($request->id);

    DB::transaction(function () use ($task) {
        // Create activity log
        TaskActivity::create([
            'task_id' => $task->id,
            'actor_id' => auth()->id(),
            'task_owner_id' => $task->task_owner_id,
            'message' => 'Task deleted'
        ]);

        // Force delete to cascade foreign keys
        $task->forceDelete();
    });

    return response()->json(['success' => true]);
}

    /** SUB-TASKS */
    public function storeSubTask(Request $request, Task $task)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'task_status_id' => 'nullable|exists:task_statuses,id',
            'task_priority_id' => 'nullable|exists:task_priorities,id',
            'task_owner_id' => 'nullable|exists:users,id',
            'due_at' => 'nullable|date'
        ]);

        $sub = TaskSubTask::create(array_merge($data, ['task_id' => $task->id]));

        TaskActivity::create([
            'task_id' => $task->id,
            'sub_task_id' => $sub->id,
            'actor_id' => auth()->id(),
            'task_owner_id' => $task->task_owner_id,
            'message' => "Sub-task created: {$sub->title}"
        ]);

        return response()->json(['success' => true, 'subtask' => $sub]);
    }

    public function updateSubTask(Request $request, TaskSubTask $subtask)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'task_status_id' => 'nullable|exists:task_statuses,id',
            'task_priority_id' => 'nullable|exists:task_priorities,id',
            'task_owner_id' => 'nullable|exists:users,id',
            'due_at' => 'nullable|date'
        ]);

        $subtask->update($data);

        TaskActivity::create([
            'task_id' => $subtask->task_id,
            'sub_task_id' => $subtask->id,
            'actor_id' => auth()->id(),
            'task_owner_id' => $subtask->task_owner_id,
            'message' => "Sub-task updated: {$subtask->title}"
        ]);

        return response()->json(['success' => true, 'subtask' => $subtask]);
    }

    public function editSubTask(TaskSubTask $subtask)
    {
        return view('tasks.partials.edit_sub_task', compact('subtask'));
    }

    public function changeSubTaskStatus(Request $request, TaskSubTask $subtask)
    {
        $data = $request->validate([
            'task_status_id' => 'required|exists:task_statuses,id'
        ]);

        $from = $subtask->task_status_id;
        $subtask->update(['task_status_id' => $data['task_status_id']]);

        TaskActivity::create([
            'task_id' => $subtask->task_id,
            'sub_task_id' => $subtask->id,
            'actor_id' => auth()->id(),
            'task_owner_id' => $subtask->task_owner_id,
            'from_status_id' => $from,
            'to_status_id' => $data['task_status_id'],
            'message' => "Sub-task status changed"
        ]);

        return response()->json(['success' => true]);
    }

    /** COMMENTS & ATTACHMENTS */
    public function comment(Request $request, Task $task)
    {
        $data = $request->validate([
            'message' => 'required|string'
        ]);

        $activity = TaskActivity::create([
            'task_id' => $task->id,
            'actor_id' => auth()->id(),
            'task_owner_id' => $task->task_owner_id,
            'message' => $data['message']
        ]);

        return response()->json(['success' => true, 'activity' => $activity]);
    }

    public function uploadAttachment(Request $request, Task $task)
    {
        $request->validate(['file' => 'required|file|max:10240']);

        $file = $request->file('file');
        $path = $file->store('task_attachments');

        $activity = TaskActivity::create([
            'task_id' => $task->id,
            'actor_id' => auth()->id(),
            'task_owner_id' => $task->task_owner_id,
            'message' => 'Attachment uploaded'
        ]);

        TaskActivityAttachment::create([
            'task_activity_id' => $activity->id,
            'file_path' => $path
        ]);

        return response()->json(['success' => true, 'activity' => $activity]);
    }


    
}