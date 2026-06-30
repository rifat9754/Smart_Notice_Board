<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\NoticeView;

class NoticeApiController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $notices = Notice::where('status', 'published')
            ->where(fn($q) => $q->whereNull('show_from')->orWhereDate('show_from', '<=', $today))
            ->where(fn($q) => $q->whereNull('show_to')->orWhereDate('show_to', '>=', $today))
            ->latest()
            ->get()
            ->map(fn($n) => $this->format($n));

        return response()->json($notices);
    }

    public function show(Notice $notice)
    {
        return response()->json($this->format($notice));
    }

    public function logView(Notice $notice)
    {
        NoticeView::create(['notice_id' => $notice->id]);
        return response()->json(['ok' => true]);
    }

    private function format($n)
    {
        return [
            'id'         => $n->id,
            'title'      => $n->title,
            'body'       => $n->body,
            'type'       => $n->type,
            'priority'   => $n->priority,
            'file_url'   => $n->file_path ? asset('storage/' . $n->file_path) : null,
            'ai_summary' => $n->ai_summary,
            'created_at' => $n->created_at,
        ];
    }
}