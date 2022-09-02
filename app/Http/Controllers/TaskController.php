<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function taskView()
    {
        return view('task');
    }

    public function createTask(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        $userId = Auth::id();
        $task_record = [
            'user_id' => $userId,
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($task = Task::create($task_record)) {
            // $this->logCreateTask($task);

            return response()->json(['message' => 'Task created successfully.'], 200);
        }

        return response()->json(['error' => 'An error seems to have occurred, please try again.'], 400);
    }

    public function getTasks()
    {
        // TODO: Pick unix timestamp as well for sorting
        $user_id = Auth::id();
        $data = Task::where('user_id', $user_id)->orderByDesc('created_at')->get();

        return response()->json(['tasks' => $data], 200);
    }

    public function editTask(Request $request)
    {
        $request->validate([
            'taskId' => 'required',
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        $taskId = $request->get('taskId');
        $task = Task::find($taskId);

        $createdAt = strtotime($task->created_at);
        $updatedAt = strtotime($task->updated_at);
        $statusBefore = $task->status;
        $statusAfter = $request->get('status');

        $task->update([
            'status' => $request->get('status'),
            'title' => $request->get('title'),
            'description' => $request->get('description'),
        ]);

        if ('Todo' === $statusBefore && 'Done' === $statusAfter && $createdAt === $updatedAt) {
            $this->logUpdateTask($task);
        }

        return response()->json(['message' => 'Task updated successfully.'], 200);
    }

    public function deleteTask(Request $request)
    {
        $request->validate([
            'taskId' => 'required|integer',
        ]);

        $taskId = $request->get('taskId');
        $task = Task::find($taskId);

        if ($task->delete()) {
            return response()->json(['message' => 'Task has been removed.'], 200);
        }
    }

    private function logCreateTask(Task $task)
    {
        $user = Auth::user();
        activity('create-task')
            ->causedBy($user)
            ->performedOn($task)
            ->withProperties([
                'action' => 'Successful',
                'task' => $task,
            ])
            ->log("task - {$task->title} created")
        ;
    }

    // Only logs for marking the task as done the first time
    private function logUpdateTask(Task $task)
    {
        $user = Auth::user();
        activity('update-task')
            ->causedBy($user)
            ->performedOn($task)
            ->withProperties([
                'action' => 'Successful',
                'task' => $task,
            ])
            ->log("task - {$task->title} marked as done")
        ;
    }
}
