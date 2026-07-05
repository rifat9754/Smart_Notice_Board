<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::latest()->get();
        return view('events.index', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $path = $request->file('image')->store('events', 'public');

        $event = Event::create([
            'title'      => $request->title,
            'image_path' => $path,
            'created_by' => Auth::id(),
        ]);

        AuditLog::create([
            'user_id' => Auth::id(), 'action' => 'created',
            'target_type' => 'Event', 'target_id' => $event->id,
        ]);

        return back()->with('success', 'Event image added.');
    }

    public function destroy(Event $event)
    {
        AuditLog::create([
            'user_id' => Auth::id(), 'action' => 'deleted',
            'target_type' => 'Event', 'target_id' => $event->id,
        ]);
        // ছবি file-ও মুছে দাও
        \Storage::disk('public')->delete($event->image_path);
        $event->delete();
        return back()->with('success', 'Event image deleted.');
    }
}