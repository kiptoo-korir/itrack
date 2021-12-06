<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class RemindersController extends Controller
{
    // function isValidTimezone($timezone) {
    //     return in_array($timezone, timezone_identifiers_list());
    // }
    public function create_reminder(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
            'due_date' => 'required|date',
            'due_time' => 'required|date_format:H:i',
            'timezone' => 'required',
        ]);

        $date_time = $request->due_date.' '.$request->due_time;
        $due_date = date('Y-m-d H:i:s', strtotime($date_time));
        $due_date = date_create($due_date, timezone_open($request->timezone));

        // If errors use getTimestamp() method
        if (now() > $due_date) {
            return response()->json(['error' => 'Please ensure that you don\'t provide a past date and time.'], 400);
        }

        $user_id = Auth::id();
        $project = $request->project;
        $type = ($request->project) ? 'SPECIFIC' : 'GENERAL';
        $reminder_record = [
            'owner' => $user_id,
            'title' => $request->title,
            'message' => $request->message,
            'project' => $project,
            'due_date' => $due_date,
            'type' => $type,
        ];

        if ($reminder = Reminder::create($reminder_record)) {
            $this->logCreateReminder($reminder);

            return response()->json(['success' => 'Reminder created successfully.', 'reminder' => $reminder], 200);
        }

        return response()->json(['error' => 'An error seems to have occurred, please try again.'], 400);
    }

    public function get_reminders()
    {
        $user_id = Auth::id();
        // Timezone set manually
        $data = Reminder::where('owner', $user_id)
            ->select('id', 'title', 'message', 'project', 'created_at as c_at', 'due_date as d_d')
            ->selectRaw('to_char(due_date, \'Dy DD Mon, YYYY at HH:MI AM \') as due_date')
            ->selectRaw('to_char(created_at at time zone \'Africa/Nairobi\', \'Dy DD Mon, YYYY at HH:MI AM\') as created')
            ->orderByDesc('c_at')->get();

        return DataTables::of($data)
            ->addColumn('order_created', function ($row) {
                return strtotime($row->c_at);
            })
            ->addColumn('order_due', function ($row) {
                return strtotime($row->d_d);
            })
            ->rawColumns(['order_date', 'order_created'])
            ->make(true)
        ;
    }

    public function edit_reminder(Request $request)
    {
        $request->validate([
            'reminder_id' => 'required',
            'title' => 'required|string',
            'message' => 'required|string',
            'due_date' => 'required|date',
            'due_time' => 'required|date_format:H:i',
            'timezone' => 'required',
        ]);

        $date_time = $request->due_date.' '.$request->due_time;
        $due_date = date('Y-m-d H:i:s', strtotime($date_time));
        $due_date = date_create($due_date, timezone_open($request->timezone));

        // If errors use getTimestamp() method
        if (now() > $due_date) {
            return response()->json(['error' => 'Please ensure that you don\'t provide a past date and time.'], 400);
        }

        $reminder_id = $request->get('reminder_id');
        $reminder = Reminder::find($reminder_id);
        $project = ($request->project) ?: null;
        $type = ($request->project) ? 'SPECIFIC' : 'GENERAL';

        $reminder->update([
            'title' => $request->title,
            'message' => $request->message,
            'project' => $project,
            'type' => $type,
            'due_date' => $due_date,
        ]);

        return response()->json(['success' => 'Reminder updated successfully.'], 200);
    }

    public function delete_reminder(Request $request)
    {
        $request->validate([
            'reminder_id' => 'required|integer',
        ]);

        $reminder_id = $request->get('reminder_id');
        $reminder = Reminder::find($reminder_id);

        if ($reminder->delete()) {
            return response()->json(['success' => 'Reminder has been removed.'], 200);
        }
    }

    public function get_specific_reminder(Request $request)
    {
        $reminder_id = $request->reminder_id;
        $reminder = Reminder::where('id', $reminder_id)
            ->select('title', 'project', 'message')
            ->selectRaw('to_char(due_date, \'YYYY-MM-DD\') as year')
            ->selectRaw('to_char(due_date, \'HH24:MI\') as time')
            ->get()
        ;

        return response()->json(['reminder' => $reminder[0]], 200);
    }

    public function reminders_view()
    {
        $userId = Auth::id();
        $data['projects'] = Project::select(['id', 'name'])
            ->where('owner', $userId)->get();

        return view('reminder')->with($data);
    }

    private function logCreateReminder(Reminder $reminder)
    {
        $user = Auth::user();
        activity('create-reminder')
            ->causedBy($user)
            ->performedOn($reminder)
            ->withProperties([
                'action' => 'Successful',
                'reminder' => $reminder,
            ])
            ->log("reminder - {$reminder->title} created")
        ;
    }
}
