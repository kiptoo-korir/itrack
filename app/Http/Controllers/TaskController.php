<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TaskController extends Controller
{
    public function task_view()
    {
        return view('task');
    }

    public function create_task(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        $user_id = Auth::id();
        $task_record = [
            'user_id' => $user_id,
            'title' => $request->title,
            'description' => $request->description,
        ];

        if (Task::create($task_record)) {
            return response()->json(['success' => 'Task created successfully.'], 200);
        }

        return response()->json(['error' => 'An error seems to have occurred, please try again.'], 400);
    }

    public function get_tasks()
    {
        $user_id = Auth::id();
        $data = Task::where('user_id', $user_id)->orderByDesc('created_at')->get();

        return DataTables::of($data)->make(true);
    }

    public function edit_task(Request $request)
    {
        $request->validate([
            'task_id' => 'required|integer',
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        $task_id = $request->get('task_id');
        $task = Task::find($task_id);

        $task->update([
            'status' => $request->get('status'),
            'title' => $request->get('title'),
            'description' => $request->get('description'),
        ]);

        return response()->json(['success' => 'Task updated successfully.'], 200);
    }

    public function delete_task(Request $request)
    {
        $request->validate([
            'task_id' => 'required|integer',
        ]);

        $task_id = $request->get('task_id');
        $task = Task::find($task_id);

        if ($task->delete()) {
            return response()->json(['success' => 'Task has been removed.'], 200);
        }
    }
}
