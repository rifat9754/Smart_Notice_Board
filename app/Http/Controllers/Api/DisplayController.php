<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;

class DisplayController extends Controller
{
    public function index(Request $request)
    {
        $boardId = $request->query('board_id');
        $today   = now()->toDateString();
        $now     = now();

        // 1) Active notices: published, within date window — CR notices excluded (they go to Class Updates)
        $notices = Notice::where('status', 'published')
            ->where('audience', 'all') 
            ->whereDoesntHave('author', fn($q) => $q->where('role', 'cr'))   
            ->where(fn($q) => $q->whereNull('show_from')->orWhereDate('show_from', '<=', $today))
            ->where(fn($q) => $q->whereNull('show_to')->orWhereDate('show_to', '>=', $today))
            ->withCount('views')
            ->get();

        // 2) Daily time-window filter (time_start / time_end)
        $notices = $notices->filter(function ($n) use ($now) {
            if ($n->time_start && $n->time_end) {
                $current = $now->format('H:i:s');
                return $current >= $n->time_start && $current <= $n->time_end;
            }
            return true;
        });

        // 3) Emergency override
        $emergency = $notices->where('is_emergency', true)->values();
        if ($emergency->isNotEmpty()) {
            return response()->json([
                'mode'     => 'emergency',
                'playlist' => $emergency->map(fn($n) => $this->format($n))->values(),
            ]);
        }

        // 4) Dynamic scheduling: score every notice
        $scored = $notices->map(function ($n) use ($now) {
            $priorityMap = ['high' => 1.0, 'medium' => 0.6, 'low' => 0.3];
            $priority    = $priorityMap[$n->priority] ?? 0.6;

            $urgency = 0;
            if ($n->show_to) {
                $daysLeft = abs($now->diffInDays($n->show_to));
                $urgency  = 1 / ($daysLeft + 1);
            }

            $ageDays   = abs($n->created_at->diffInDays($now));
            $freshness = 1 / ($ageDays + 1);

            $fairness  = 1 / ($n->views_count + 1);

            $score = 0.4 * $priority + 0.3 * $urgency + 0.2 * $freshness + 0.1 * $fairness;

            $data          = $this->format($n);
            $data['score'] = round($score, 4);
            return $data;
        });

        $playlist = $scored->sortByDesc('score')->values();

        return response()->json([
            'mode'     => 'normal',
            'playlist' => $playlist,
        ]);
    }

    public function classUpdates()
{
    $today = now()->toDateString();

    $notices = Notice::where('status', 'published')
        ->whereHas('author', fn($q) => $q->where('role', 'cr'))  
        ->where(fn($q) => $q->whereNull('show_from')->orWhereDate('show_from', '<=', $today))
        ->where(fn($q) => $q->whereNull('show_to')->orWhereDate('show_to', '>=', $today))
        ->latest()
        ->take(5)
        ->get()
        ->map(fn($n) => [
            'id'       => $n->id,
            'title'    => $n->title,
            'body'     => $n->body,
            'display'  => $n->display_line ?: $n->title,   
            'priority' => $n->priority,
            'year'     => $n->year,       
            'section'  => $n->section, 
        ]);

    return response()->json(['updates' => $notices]);
}

    private function format($n)
    {
        return [
            'id'           => $n->id,
            'title'        => $n->title,
            'body'         => $n->body,
            'type'         => $n->type,
            'priority'     => $n->priority,
            'file_url'     => $n->file_path ? asset('storage/' . $n->file_path) : null,
            'ai_summary'   => $n->ai_summary,
            'is_emergency' => (bool) $n->is_emergency,
        ];
    }

    public function events()
{
    $events = \App\Models\Event::latest()->take(10)->get()->map(fn($e) => [
        'title' => $e->title,
        'image' => asset('storage/'.$e->image_path),
    ]);
    return response()->json(['events' => $events]);
}

public function ticker()
{
    // ১. active custom ticker message আছে?
    $custom = \App\Models\TickerMessage::where('is_active', true)
        ->latest()
        ->pluck('message')
        ->toArray();

    if (!empty($custom)) {
        return response()->json([
            'source'   => 'custom',
            'messages' => $custom,
        ]);
    }

    // ২. না থাকলে — সব published notice-এর title
    $today = now()->toDateString();

    $titles = \App\Models\Notice::where('status', 'published')
        ->where(fn($q) => $q->whereNull('show_to')->orWhereDate('show_to', '>=', $today))
        ->latest()
        ->pluck('title')
        ->toArray();

    return response()->json([
        'source'   => 'notices',
        'messages' => $titles,
    ]);
}
public function teacherNotices()
{
    $today = now()->toDateString();

    $notices = Notice::where('status', 'published')
        ->where('audience', 'teachers')
        ->where(fn($q) => $q->whereNull('show_to')->orWhereDate('show_to', '>=', $today))
        ->latest()
        ->take(6)
        ->get()
        ->map(fn($n) => [
            'id'       => $n->id,
            'title'    => $n->title,
            'body'     => $n->body,
            'display'  => $n->display_line ?: $n->title,
            'priority' => $n->priority,
        ]);

    return response()->json(['notices' => $notices]);
}

}