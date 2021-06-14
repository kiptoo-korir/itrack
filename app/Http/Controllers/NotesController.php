<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{
    public function create_note(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
        ]);

        $user_id = Auth::id();
        $repository = $request->repository;
        $type = ($request->repository) ? 'SPECIFIC' : 'GENERAL';
        $note_record = [
            'owner' => $user_id,
            'title' => $request->title,
            'message' => $request->message,
            'repository' => $repository,
            'type' => $type,
        ];

        if ($note = Note::create($note_record)) {
            return response()->json(['success' => 'Task created successfully.', 'note' => $note], 200);
        }

        return response()->json(['error' => 'An error seems to have occurred, please try again.'], 400);
    }

    public function get_notes()
    {
        $user_id = Auth::id();
        $data = Note::where('owner', $user_id)->orderByDesc('created_at')->limit(50)->get();

        return response()->json(['notes' => $data], 200);
    }

    public function edit_note(Request $request)
    {
        $request->validate([
            'note_id' => 'required|integer',
            'title' => 'required|string',
            'message' => 'required|string',
        ]);

        $note_id = $request->get('note_id');
        $task = Note::find($note_id);
        $repository = ($request->repository) ?: null;
        $type = ($request->repository) ? 'SPECIFIC' : 'GENERAL';

        $task->update([
            'title' => $request->title,
            'message' => $request->message,
            'repository' => $repository,
            'type' => $type,
        ]);

        return response()->json(['success' => 'Task updated successfully.'], 200);
    }

    public function delete_note(Request $request)
    {
        $request->validate([
            'note_id' => 'required|integer',
        ]);

        $note_id = $request->get('note_id');
        $note = Note::find($note_id);

        if ($note->delete()) {
            return response()->json(['success' => 'Note has been removed.'], 200);
        }
    }

    public function notes_view()
    {
        $data['user_data'] = Auth::user();
        $data['user_data']->first_letter = substr($data['user_data']->name, 0, 1);
        $data['repositories'] = Repository::select(['id', 'name'])
            ->where('owner', $data['user_data']->id)->get();
        $data['notes'] = Note::where('owner', $data['user_data']->id)->orderByDesc('created_at')->get();

        return view('note')->with($data);
    }

    public function get_specific_note(Request $request)
    {
        $note_id = $request->note_id;
        $note = Note::find($note_id);

        return response()->json(['note' => $note], 200);
    }
}
