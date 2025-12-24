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

   public function create(Request $request)
{
    $task = null;

    if ($request->id) {
        $task = Task::with('assignees')->findOrFail($request->id);
    }

    return view('tasks.create', [
        'task'       => $task,
        'statuses'   => TaskStatus::all(),
        'priorities' => TaskPriority::all(),
        'users'      => User::orderBy('name')->get(),
    ]);
}




 public function store(Request $request)
{
    $data = $request->validate([
        'id'               => 'nullable|exists:tasks,id',
        'title'            => 'required|string|max:255',
        'description'      => 'nullable|string',
        'task_status_id'   => 'required|exists:task_statuses,id',
        'task_priority_id' => 'required|exists:task_priorities,id',
        'task_owner_id'    => 'required|exists:users,id',
        'assignees'        => 'nullable|array',
        'assignees.*'      => 'exists:users,id',
        'due_at'           => 'nullable|date',
    ]);

    DB::transaction(function () use ($data) {

        /** ---------------------------
         * CREATE vs UPDATE (EXPLICIT)
         * -------------------------- */
        $task = isset($data['id'])
            ? Task::findOrFail($data['id'])
            : new Task();

        $task->fill([
            'title'            => $data['title'],
            'description'      => $data['description'] ?? null,
            'task_status_id'   => $data['task_status_id'],
            'task_priority_id' => $data['task_priority_id'],
            'task_owner_id'    => $data['task_owner_id'],
            'due_at'           => $data['due_at'] ?? null,
            'created_by'       => auth()->id(),
        ]);

        $task->save();

        /** ---------------------------
         * ASSIGNEES RESET
         * -------------------------- */
        TaskRelation::where('task_id', $task->id)
            ->where('relation_type', 'assignee')
            ->delete();

        foreach ($data['assignees'] ?? [] as $uid) {
            TaskRelation::create([
                'task_id'       => $task->id,
                'related_type'  => User::class,
                'related_id'    => $uid,
                'relation_type' => 'assignee',
            ]);
        }

        /** ---------------------------
         * ACTIVITY LOG
         * -------------------------- */
        TaskActivity::create([
            'task_id'       => $task->id,
            'actor_id'      => auth()->id(),
            'task_owner_id' => $task->task_owner_id,
            'message'       => isset($data['id'])
                ? 'Task updated'
                : 'Task created',
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
        'activities' => fn ($q) => $q->latest(),
        'activities.actor:id,name',
        'activities.attachments'
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
            'task_id'       => $task->id,
            'actor_id'      => auth()->id(),
            'task_owner_id' => $task->task_owner_id,
            'message'       => 'Task deleted'
        ]);

        $task->forceDelete(); // FK cascade handles the rest
    });

    return response()->json(['success' => true]);
}


    /** SUB-TASKS */
   public function storeSubTask(Request $request, Task $task)
{
    $data = $request->validate([
        'title'            => 'required|string|max:255',
        'description'      => 'nullable|string',
        'task_status_id'   => 'required|exists:task_statuses,id',
        'task_priority_id' => 'required|exists:task_priorities,id',
        'due_at'           => 'nullable|date',
    ]);

    DB::transaction(function () use ($data, $task) {

        $subtask = TaskSubTask::create([
            ...$data,
            'task_id'       => $task->id,
            'task_owner_id' => auth()->id(),
        ]);

        TaskActivity::create([
            'task_id'       => $task->id,
            'sub_task_id'   => $subtask->id,
            'actor_id'      => auth()->id(),
            'task_owner_id' => $task->task_owner_id,
            'message'       => "Sub-task created: {$subtask->title}",
        ]);
    });

    return response()->json(['success' => true]);
}


public function editSubTask(TaskSubTask $subtask)
{
    return view('tasks.partials.edit_sub_task', compact('subtask'));
}

   public function updateSubTask(Request $request, TaskSubTask $subtask)
{
    $data = $request->validate([
        'title'            => 'required|string|max:255',
        'description'      => 'nullable|string',
        'task_status_id'   => 'required|exists:task_statuses,id',
        'task_priority_id' => 'required|exists:task_priorities,id',
        'due_at'           => 'nullable|date',
    ]);

    $subtask->update($data);

    $activity = TaskActivity::create([
        'task_id'     => $subtask->task_id,
        'sub_task_id' => $subtask->id,
        'actor_id'    => auth()->id(),
        'message'     => "Sub-task updated: {$subtask->title}",
    ]);

    return response()->json([
        'success' => true,
        'data' => [
            'subtask'  => $subtask->load('owner', 'status', 'priority'),
            'activity' => $activity->load('actor'),
        ]
    ]);
}

   public function changeSubTaskStatus(Request $request, TaskSubTask $subtask)
{
    $request->validate([
        'task_status_id' => 'required|exists:task_statuses,id'
    ]);

    $from = $subtask->task_status_id;
    $subtask->update(['task_status_id' => $request->task_status_id]);

    $activity = TaskActivity::create([
        'task_id'        => $subtask->task_id,
        'sub_task_id'    => $subtask->id,
        'actor_id'       => auth()->id(),
        'from_status_id' => $from,
        'to_status_id'   => $request->task_status_id,
        'message'        => "Sub-task status changed",
    ]);

    return response()->json([
        'success' => true,
        'data' => [
            'subtask'  => $subtask->load('status'),
            'activity' => $activity->load('actor'),
        ]
    ]);
}

  public function comment(Request $request, Task $task)
{
    abort_if(!auth()->check(), 401);

    $request->validate([
        'message' => 'required|string',
        'file'    => 'nullable|file|max:10240',
    ]);

    $activity = DB::transaction(function () use ($request, $task) {

        $activity = TaskActivity::create([
            'task_id'       => $task->id,
            'actor_id'      => auth()->id(),
            'task_owner_id' => $task->task_owner_id,
            'message'       => $request->message,
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('task_attachments', 'public');

            TaskActivityAttachment::create([
                'task_activity_id' => $activity->id,
                'file_path'        => $path,
            ]);
        }

        return $activity->load('actor', 'attachments');
    });

    return response()->json([
        'success'  => true,
        'activity' => $activity,
    ]);
}


public function changeTaskStatus(Request $request, Task $task)
{
    $map = [
        'pause'  => 'Paused',
        'resume' => 'In Progress',
        'close'  => 'Closed',
    ];

    abort_unless(isset($map[$request->action]), 400);

    $status = TaskStatus::where('name', $map[$request->action])->firstOrFail();

    DB::transaction(function () use ($task, $status, $request) {
        $task->update(['task_status_id' => $status->id]);

        TaskActivity::create([
            'task_id'       => $task->id,
            'actor_id'      => auth()->id(),
            'task_owner_id' => $task->task_owner_id,
            'message'       => "Task {$request->action}d",
        ]);
    });

    return response()->json(['success' => true]);
}





    // public function uploadAttachment(Request $request, Task $task)
    // {
    //     $request->validate(['file' => 'required|file|max:10240']);

    //     $file = $request->file('file');
    //     $path = $file->store('task_attachments');

    //     $activity = TaskActivity::create([
    //         'task_id' => $task->id,
    //         'actor_id' => auth()->id(),
    //         'task_owner_id' => $task->task_owner_id,
    //         'message' => 'Attachment uploaded'
    //     ]);

    //     TaskActivityAttachment::create([
    //         'task_activity_id' => $activity->id,
    //         'file_path' => $path
    //     ]);

    //     return response()->json(['success' => true, 'activity' => $activity]);
    // }



    


    
}