<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Traits\SchoolSession;
use App\Interfaces\SchoolSessionInterface;

class EventController extends Controller
{
    use SchoolSession;
    protected $schoolSessionRepository;

    public function __construct(SchoolSessionInterface $schoolSessionRepository) {
        $this->schoolSessionRepository = $schoolSessionRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $loggedInUser = auth()->user();
            $current_school_session_id = $this->getSchoolCurrentSession();

            $data = Event::whereDate('start', '>=', $request->start)
                    ->whereDate('end',   '<=', $request->end)
                    ->where('session_id', $current_school_session_id)
                    ->where('school_id', $loggedInUser->school_id)
                    ->get(['id', 'title', 'start', 'end']);
            return response()->json($data);
        }
        return view('events.index');
    }

    public function calendarEvents(Request $request)
    {
        $loggedInUser = auth()->user();
        $current_school_session_id = $this->getSchoolCurrentSession();
        $event = null;
        switch ($request->type) {
            case 'create':
                $event = Event::create([
                    'title' => $request->title,
                    'start' => $request->start,
                    'end' => $request->end,
                    'session_id' => $current_school_session_id,
                    'school_id' => $loggedInUser->school_id,
                ]);
                break;
  
            case 'edit':
                $editableEvent = Event::where('id', $request->id)
                    ->where('school_id', $loggedInUser->school_id)
                    ->first();

                if (!$editableEvent) {
                    return response()->json(['message' => 'Event not found for this school.'], 404);
                }

                $event = $editableEvent->update([
                    'title' => $request->title,
                    'start' => $request->start,
                    'end' => $request->end,
                ]);
                break;
  
            case 'delete':
                $deletableEvent = Event::where('id', $request->id)
                    ->where('school_id', $loggedInUser->school_id)
                    ->first();

                if (!$deletableEvent) {
                    return response()->json(['message' => 'Event not found for this school.'], 404);
                }

                $event = $deletableEvent->delete();
                break;
             
            default:
                break;
        }
        return response()->json($event);
    }
}
