<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\Board;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use App\Services\FcmService;
use Illuminate\Support\Facades\Auth;

use App\Services\AiSummaryService;
use Smalot\PdfParser\Parser;


class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::with('author')->latest()->get();
        return view('notices.index', compact('notices'));
    }

    public function create()
    {
        $boards = Board::all();
        return view('notices.create', compact('boards'));
    }

    public function store(Request $request)
    {
        $data = $this->validateNotice($request);
        $data['is_emergency'] = $request->has('is_emergency');
        $data['author_id'] = Auth::id();

        if ($request->hasFile('attachment')) {
            $data['file_path'] = $request->file('attachment')->store('notices', 'public');
        }
        unset($data['attachment']);


       $notice = Notice::create($data);

       AuditLog::create(['user_id' => auth()->id(), 'action' => 'created', 'target_type' => 'Notice', 'target_id' => $notice->id]);

       if ($notice->type === 'pdf' && $notice->file_path) {
           $this->generateSummary($notice);
        }


    if ($notice->status === 'published') {
        $tokens = \App\Models\User::whereIn('role', ['student', 'cr'])
            ->where('status', 'active')
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->toArray();

        if (!empty($tokens)) {
            app(FcmService::class)->sendToMany(
                $tokens,
                'New Notice: ' . $notice->title,
                \Illuminate\Support\Str::limit(strip_tags($notice->body), 100),
                ['notice_id' => $notice->id, 'type' => 'notice']
            );
        }
    }

        return redirect()->route('notices.index')->with('success', 'Notice created successfully.');
    }

    public function edit(Notice $notice)
    {
        $boards = Board::all();
        return view('notices.edit', compact('notice', 'boards'));
    }

    public function update(Request $request, Notice $notice)
    {
        $wasPublished = $notice->status === 'published';
        $data = $this->validateNotice($request);
        $data['is_emergency'] = $request->has('is_emergency');

        if ($request->hasFile('attachment')) {
            $data['file_path'] = $request->file('attachment')->store('notices', 'public');
        }
        unset($data['attachment']);

        $notice->update($data);

                AuditLog::create(['user_id' => auth()->id(), 'action' => 'updated', 'target_type' => 'Notice', 'target_id' => $notice->id]);


    if (!$wasPublished && $notice->status === 'published') {
        $tokens = \App\Models\User::whereIn('role', ['student', 'cr'])
            ->where('status', 'active')
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')->toArray();

        if (!empty($tokens)) {
            app(FcmService::class)->sendToMany(
                $tokens,
                'New Notice: ' . $notice->title,
                \Illuminate\Support\Str::limit(strip_tags($notice->body), 100),
                ['notice_id' => $notice->id, 'type' => 'notice']
            );
        }
    }


        return redirect()->route('notices.index')->with('success', 'Notice updated successfully.');
    }

public function destroy(Notice $notice)
    {
        AuditLog::create(['user_id' => auth()->id(), 'action' => 'deleted', 'target_type' => 'Notice', 'target_id' => $notice->id]);

        $notice->delete();
        return redirect()->route('notices.index')->with('success', 'Notice deleted.');
    }

    private function validateNotice(Request $request): array
    {
        return $request->validate([
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'type'        => 'required|in:text,image,pdf',
            'priority'    => 'required|in:high,medium,low',
            'status'      => 'required|in:draft,published,expired',
            'is_emergency'=> 'nullable|boolean',
            'show_from'   => 'nullable|date',
            'show_to'     => 'nullable|date',
            'time_start'  => 'nullable',
            'time_end'    => 'nullable',
            'board_id'    => 'nullable|exists:boards,id',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);
    }

    private function generateSummary(Notice $notice): void
{
    try {
        $path = storage_path('app/public/' . $notice->file_path);
        $text = (new Parser())->parseFile($path)->getText();

        $summary = app(AiSummaryService::class)->summarize($text);
        if ($summary) {
            $notice->update(['ai_summary' => $summary]);
        }
    } catch (\Throwable $e) {
        // PDF পড়া না গেলে চুপচাপ skip
    }
}
}