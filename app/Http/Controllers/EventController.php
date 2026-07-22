<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cloudinary\Cloudinary;

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
            'image' => 'required|image|mimes:jpg,jpeg,png|max:20480',
        ]);



        $cloud = new Cloudinary(env('CLOUDINARY_URL'));

        $result = $cloud->uploadApi()->upload(
            $request->file('image')->getRealPath(),
            [
                'folder' => 'events',
                'resource_type' => 'image',
            ]
        );

        $event = Event::create([
            'title'      => $request->title,
            'image_path' => $result['secure_url'],
            'created_by' => Auth::id(),
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'target_type' => 'Event',
            'target_id' => $event->id,
        ]);

        return back()->with('success', 'Event image added.');
    }

    public function destroy(Event $event)
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'target_type' => 'Event',
            'target_id' => $event->id,
        ]);

        // ভবিষ্যতে চাইলে Cloudinary API দিয়ে image delete করতে পারো।
        // আপাতত শুধু database থেকে record delete করছি।

        $event->delete();

        return back()->with('success', 'Event image deleted.');
    }
}