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
    Departments,
    TaskAttachment
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;






class TaskController extends Controller
{

    /* =========================================================
     | TASK LIST
     ========================================================= */
    public function list(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role?->role_name === 'Super Admin';

        $tasks = Task::with([
            'status',
            'priority',
            'owner:id,name',
            'assignees:id,name',
            'attachments:id,task_id,file_path,original_name',
        ])
            ->when(!$isSuperAdmin, function ($q) use ($user) {
                $q->where(function ($q) use ($user) {
                    $q->where('task_owner_id', $user->id)
                        ->orWhereHas(
                            'assignees',
                            fn($a) =>
                            $a->where('users.id', $user->id)
                        );
                });
            })
            ->when(
                $request->status,
                fn($q) =>
                $q->where('task_status_id', $request->status)
            )
            ->when(
                $request->priority,
                fn($q) =>
                $q->where('task_priority_id', $request->priority)
            )
            ->when(
                $request->owner,
                fn($q) =>
                $q->where('task_owner_id', $request->owner)
            )
            ->when(
                $request->date,
                fn($q) =>
                $q->whereDate('created_at', $request->date)
            )
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas(
                            'owner',
                            fn($o) =>
                            $o->where('name', 'like', "%{$search}%")
                        )
                        ->orWhereHas(
                            'assignees',
                            fn($a) =>
                            $a->where('name', 'like', "%{$search}%")
                        );
                });
            })
            ->latest()
            ->paginate(15);

        $departments = Departments::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        if ($request->ajax() && $request->has('filter')) {
            return view('tasks.partials.table', compact(
                'tasks',
                'departments',
                'users'
            ));
        }

        return view('tasks.list', [
            'tasks' => $tasks,
            'statuses' => TaskStatus::all(),
            'priorities' => TaskPriority::all(),
            'users' => $users,
            'departments' => $departments,
        ]);
    }



    /* =========================================================
     | CREATE / EDIT VIEW
     ========================================================= */
    public function create(Request $request)
    {
        $task = null;

        if ($request->id) {
            $task = Task::with([
                'assignees',
                'attachments.uploader:id,name'
            ])->findOrFail($request->id);
        }

        return view('tasks.create', [
            'task' => $task,
            'statuses' => TaskStatus::all(),
            'priorities' => TaskPriority::all(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    /* =========================================================
     | STORE / UPDATE TASK (FINAL)
     ========================================================= */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|exists:tasks,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'task_status_id' => 'required|exists:task_statuses,id',
            'task_priority_id' => 'required|exists:task_priorities,id',
            'task_owner_id' => 'required|exists:users,id',
            'assignees' => 'nullable|array',
            'assignees.*' => 'exists:users,id',
            'due_at' => 'nullable|date',
            'attachments.*' => 'nullable|image|max:10240',
        ]);

        DB::beginTransaction();

        try {

            $isUpdate = !empty($validated['id']);
            $task = $isUpdate
                ? Task::findOrFail($validated['id'])
                : new Task();

            /* ===== SNAPSHOT BEFORE SAVE ===== */
            $old = $task->exists ? [
                'title' => $task->title,
                'description' => $task->description,
                'task_status_id' => $task->task_status_id,
                'task_priority_id' => $task->task_priority_id,
                'due_at' => optional($task->due_at)->format('Y-m-d'),
            ] : [];

            /* ===== SAVE TASK ===== */
            $task->fill([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'task_status_id' => $validated['task_status_id'],
                'task_priority_id' => $validated['task_priority_id'],
                'task_owner_id' => $validated['task_owner_id'],
                'due_at' => $validated['due_at'] ?? null,
            ]);

            if (!$isUpdate) {
                $task->created_by = auth()->id();
            }

            $task->save();

            /* ===== ASSIGNEES ===== */
            $oldAssignees = TaskRelation::where('task_id', $task->id)
                ->where('relation_type', 'assignee')
                ->pluck('related_id')
                ->toArray();

            $newAssignees = $validated['assignees'] ?? [];

            $added = array_diff($newAssignees, $oldAssignees);
            $removed = array_diff($oldAssignees, $newAssignees);

            if ($added || $removed) {

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

                if ($added) {
                    TaskActivity::create([
                        'task_id' => $task->id,
                        'actor_id' => auth()->id(),
                        'message' => "Assigned users:\n" .
                            User::whereIn('id', $added)->pluck('name')->join(', '),
                    ]);
                }

                if ($removed) {
                    TaskActivity::create([
                        'task_id' => $task->id,
                        'actor_id' => auth()->id(),
                        'message' => "Removed users:\n" .
                            User::whereIn('id', $removed)->pluck('name')->join(', '),
                    ]);
                }
            }

            /* ===== FIELD CHANGES ===== */
            if ($isUpdate) {

                if (($old['title'] ?? '') !== $task->title) {
                    TaskActivity::create([
                        'task_id' => $task->id,
                        'actor_id' => auth()->id(),
                        'message' => "Title changed to:\n{$task->title}",
                    ]);
                }

                if (($old['description'] ?? '') !== ($task->description ?? '')) {
                    TaskActivity::create([
                        'task_id' => $task->id,
                        'actor_id' => auth()->id(),
                        'message' => "Description updated:\n{$task->description}",
                    ]);
                }

                if (($old['task_status_id'] ?? null) != $task->task_status_id) {
                    TaskActivity::create([
                        'task_id' => $task->id,
                        'actor_id' => auth()->id(),
                        'message' =>
                            "Status changed\nFrom: " .
                            TaskStatus::find($old['task_status_id'])->name .
                            "\nTo: " . $task->status->name,
                    ]);
                }

                if (($old['task_priority_id'] ?? null) != $task->task_priority_id) {
                    TaskActivity::create([
                        'task_id' => $task->id,
                        'actor_id' => auth()->id(),
                        'message' =>
                            "Priority changed\nFrom: " .
                            TaskPriority::find($old['task_priority_id'])->name .
                            "\nTo: " . $task->priority->name,
                    ]);
                }

                if (($old['due_at'] ?? null) !== optional($task->due_at)->format('Y-m-d')) {
                    TaskActivity::create([
                        'task_id' => $task->id,
                        'actor_id' => auth()->id(),
                        'message' =>
                            "Due date changed\nTo: " .
                            optional($task->due_at)->format('d M Y'),
                    ]);
                }
            }

            /* ===== ATTACHMENTS ===== */
            if ($request->hasFile('attachments')) {

                $uploaded = [];

                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('task_attachments', 'public');

                    TaskAttachment::create([
                        'task_id' => $task->id,
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'uploaded_by' => auth()->id(),
                    ]);

                    $uploaded[] = $file->getClientOriginalName();
                }

                TaskActivity::create([
                    'task_id' => $task->id,
                    'actor_id' => auth()->id(),
                    'message' => "Uploaded screenshots:\n" . implode("\n", $uploaded),
                ]);
            }

            /* ===== CREATE SNAPSHOT ===== */
            if (!$isUpdate) {
                TaskActivity::create([
                    'task_id' => $task->id,
                    'actor_id' => auth()->id(),
                    'message' =>
                        "Created the task\n" .
                        "Title: {$task->title}\n" .
                        "Description: " . ($task->description ?? '-') . "\n" .
                        "Status: {$task->status->name}\n" .
                        "Priority: {$task->priority->name}\n" .
                        "Owner: {$task->owner->name}\n" .
                        "Assigned users: " .
                        User::whereIn('id', $newAssignees)->pluck('name')->join(', ') . "\n" .
                        "Due date: " . optional($task->due_at)->format('d M Y'),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isUpdate
                    ? 'Task updated successfully'
                    : 'Task created successfully',
                'task_id' => $task->id,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
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

    public function deleteAttachment($id)
    {
        $attachment = TaskAttachment::find($id);

        if (!$attachment) {
            return response()->json([
                'message' => 'Attachment not found'
            ], 404);
        }

        DB::transaction(function () use ($attachment) {

            if (
                $attachment->file_path &&
                Storage::disk('public')->exists($attachment->file_path)
            ) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            TaskActivity::create([
                'task_id' => $attachment->task_id,
                'actor_id' => auth()->id(),
                'message' => "Removed screenshot:\n{$attachment->original_name}",
            ]);

            $attachment->delete();
        });

        return response()->json(['success' => true]);
    }




    public function forward(Request $request, Task $task)
    {
        $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'user_id' => 'nullable|exists:users,id',
            'follow_up_date' => 'required|date',
        ]);

        if (!$request->department_id && !$request->user_id) {
            return response()->json([
                'message' => 'Select either a department or a user'
            ], 422);
        }

        DB::transaction(function () use ($request, $task) {

            if ($request->department_id) {

                $department = Departments::findOrFail($request->department_id);

                TaskForward::updateOrCreate(
                    [
                        'task_id' => $task->id,
                        'department_id' => $department->id,
                    ],
                    [
                        'user_id' => null,
                        'forwarded_by' => auth()->id(),
                        'follow_up_date' => $request->follow_up_date,
                    ]
                );

                TaskActivity::create([
                    'task_id' => $task->id,
                    'actor_id' => auth()->id(),
                    'message' =>
                        "Task forwarded to department\n" .
                        "Department: {$department->name}\n" .
                        "Follow-up date: " .
                        \Carbon\Carbon::parse($request->follow_up_date)->format('d M Y'),
                ]);
            }

            if ($request->user_id) {

                $user = User::findOrFail($request->user_id);

                TaskForward::updateOrCreate(
                    [
                        'task_id' => $task->id,
                        'user_id' => $user->id,
                    ],
                    [
                        'department_id' => null,
                        'forwarded_by' => auth()->id(),
                        'follow_up_date' => $request->follow_up_date,
                    ]
                );

                TaskActivity::create([
                    'task_id' => $task->id,
                    'actor_id' => auth()->id(),
                    'message' =>
                        "Task forwarded to user\n" .
                        "User: {$user->name}\n" .
                        "Follow-up date: " .
                        \Carbon\Carbon::parse($request->follow_up_date)->format('d M Y'),
                ]);
            }
        });

        return response()->json(['success' => true]);
    }




    public function deleteForward(TaskForward $forward)
    {
        $activity = DB::transaction(function () use ($forward) {

            if ($forward->department) {
                $target = "Department: {$forward->department->name}";
            } elseif ($forward->user) {
                $target = "User: {$forward->user->name}";
            } else {
                $target = "Unknown target";
            }

            $activity = TaskActivity::create([
                'task_id' => $forward->task_id,
                'actor_id' => auth()->id(),
                'message' => "Forward removed\n{$target}",
            ]);

            $forward->delete();

            return $activity->load('actor:id,name');
        });

        return response()->json([
            'success' => true,
            'activity' => [
                'id' => $activity->id,
                'user' => $activity->actor->name,
                'message' => nl2br(e($activity->message)),
                'time' => $activity->created_at->diffForHumans(),
            ]
        ]);
    }







    /** SUB-TASKS */
    public function storeSubTask(Request $request, Task $task)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_at' => 'nullable|date',
        ]);

        // Prevent duplicate sub-task for same user & task
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

        $subtask = DB::transaction(function () use ($task, $data) {

            $subtask = TaskSubTask::create([
                'task_id' => $task->id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'task_owner_id' => auth()->id(),
                'due_at' => $data['due_at'] ?? null,
            ]);

            TaskActivity::create([
                'task_id' => $task->id,
                'sub_task_id' => $subtask->id,
                'actor_id' => auth()->id(),
                'task_owner_id' => $task->task_owner_id,
                'message' =>
                    "Sub-task created\n" .
                    "Title: {$subtask->title}\n" .
                    "Description: " . ($subtask->description ?: '—'),
            ]);

            return $subtask;
        });


        return response()->json([
            'success' => true,
            'data' => [
                'id' => $subtask->id,
                'title' => $subtask->title,
                'description' => $subtask->description,
                'due_at' => optional($subtask->due_at)->format('d M Y'),
                'due_raw' => optional($subtask->due_at)->format('Y-m-d'),
                'owner' => [
                    'name' => $subtask->owner->name ?? '—'
                ]
            ]
        ]);
    }





    public function updateSubTask(Request $request, TaskSubTask $subtask)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_at' => 'nullable|date',
        ]);

        DB::transaction(function () use ($subtask, $data) {

            $subtask->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'due_at' => $data['due_at'] ?? null,
            ]);

            TaskActivity::create([
                'task_id' => $subtask->task_id,
                'sub_task_id' => $subtask->id,
                'actor_id' => auth()->id(),
                'message' => "Sub-task updated\nTitle: {$subtask->title}",
            ]);
        });


        return response()->json([
            'success' => true,
            'data' => [
                'id' => $subtask->id,
                'title' => $subtask->title,
                'description' => $subtask->description,
                'due_at' => optional($subtask->due_at)->format('d M Y'),
                'due_raw' => optional($subtask->due_at)->format('Y-m-d'),
                'owner' => [
                    'name' => $subtask->owner->name ?? '—'
                ]
            ]
        ]);
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
        DB::transaction(function () use ($subtask) {

            TaskActivity::create([
                'task_id' => $subtask->task_id,
                'sub_task_id' => $subtask->id,
                'actor_id' => auth()->id(),
                'task_owner_id' => $subtask->task->task_owner_id,
                'message' => "Sub-task permanently deleted",
            ]);

            //  PERMANENT DELETE
            $subtask->forceDelete();
        });

        return response()->json(['success' => true]);
    }



    public function comment(Request $request, Task $task)
    {
        $request->validate([
            'message' => 'required|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $activity = DB::transaction(function () use ($request, $task) {

            $activity = TaskActivity::create([
                'task_id' => $task->id,
                'actor_id' => auth()->id(),
                'task_owner_id' => $task->task_owner_id,
                'message' => $request->message, // multiline OK
            ]);

            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('task_attachments', 'public');

                TaskActivityAttachment::create([
                    'task_activity_id' => $activity->id,
                    'file_path' => $path,
                ]);
            }

            return $activity;
        });

        return response()->json(['success' => true]);
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

    public function updateStatusInline(Request $request, Task $task)
    {
        $request->validate([
            'task_status_id' => 'required|exists:task_statuses,id',
        ]);

        if ($task->task_status_id == $request->task_status_id) {
            return response()->json(['success' => true]);
        }

        DB::transaction(function () use ($task, $request) {

            $oldStatus = TaskStatus::find($task->task_status_id);
            $newStatus = TaskStatus::find($request->task_status_id);

            $task->update([
                'task_status_id' => $newStatus->id
            ]);

            TaskActivity::create([
                'task_id' => $task->id,
                'actor_id' => auth()->id(),
                'task_owner_id' => $task->task_owner_id,
                'message' =>
                    "Status changed\n" .
                    "From: {$oldStatus->name}\n" .
                    "To: {$newStatus->name}",
            ]);
        });

        return response()->json(['success' => true]);
    }


    public function updatePriorityInline(Request $request, Task $task)
    {
        $request->validate([
            'task_priority_id' => 'required|exists:task_priorities,id',
        ]);

        if ($task->task_priority_id == $request->task_priority_id) {
            return response()->json(['success' => true]);
        }

        DB::transaction(function () use ($task, $request) {

            $oldPriority = TaskPriority::find($task->task_priority_id);
            $newPriority = TaskPriority::find($request->task_priority_id);

            $task->update([
                'task_priority_id' => $newPriority->id
            ]);

            TaskActivity::create([
                'task_id' => $task->id,
                'actor_id' => auth()->id(),
                'task_owner_id' => $task->task_owner_id,
                'message' =>
                    "Priority changed\n" .
                    "From: {$oldPriority->name}\n" .
                    "To: {$newPriority->name}",
            ]);
        });

        return response()->json(['success' => true]);
    }


    public function removeAssignee(Task $task, User $user)
    {
        DB::transaction(function () use ($task, $user) {

            TaskRelation::where([
                'task_id' => $task->id,
                'related_id' => $user->id,
                'relation_type' => 'assignee',
            ])->delete();

            TaskActivity::create([
                'task_id' => $task->id,
                'actor_id' => auth()->id(),
                'message' => "Removed assigned user:\n{$user->name}",
            ]);
        });

        return response()->json(['success' => true]);
    }

}
