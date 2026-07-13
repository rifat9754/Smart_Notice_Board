<?php

namespace App\Http\Controllers;

use App\Models\TickerMessage;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TickerController extends Controller
{
    public function index()
    {
        $messages = TickerMessage::with('creator')->latest()->get();
        return view('ticker.index', compact('messages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $ticker = TickerMessage::create([
            'message'    => $data['message'],
            'is_active'  => true,
            'created_by' => Auth::id(),
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action'  => 'created ticker message',
            'target_type' => 'TickerMessage',
            'target_id'   => $ticker->id,
        ]);

        return back()->with('success', 'Ticker message added. It will now show on the board.');
    }

    // চালু/বন্ধ করো
    public function toggle(TickerMessage $ticker)
    {
        $ticker->update(['is_active' => !$ticker->is_active]);
        $state = $ticker->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Ticker message {$state}.");
    }

    public function destroy(TickerMessage $ticker)
    {
        $ticker->delete();
        return back()->with('success', 'Ticker message deleted.');
    }
}