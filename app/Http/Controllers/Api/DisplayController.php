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

        // 1) Active notices: published, within date window, for this board or all boards
        $notices = Notice::where('status', 'published')
            ->where(fn($q) => $q->whereNull('show_from')->orWhereDate('show_from', '<=', $today))
            ->where(fn($q) => $q->whereNull('show_to')->orWhereDate('show_to', '>=', $today))
            ->when($boardId, function ($q) use ($boardId) {
                $q->where(fn($qq) => $qq->whereNull('board_id')->orWhere('board_id', $boardId));
            })
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

        // 3) Emergency override: if any emergency notice is active, show only those
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
}