<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\NoticeView;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $totalViews   = NoticeView::count();
        $totalNotices = Notice::count();
        $activeCount  = Notice::where('status', 'published')->count();
        $expiredCount = Notice::where('status', 'expired')->count();

        $mostViewed = Notice::withCount('views')
            ->orderByDesc('views_count')
            ->take(5)
            ->get();

        $viewsByDay = NoticeView::select(
                DB::raw('DATE(viewed_at) as day'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return view('analytics.index', compact(
            'totalViews', 'totalNotices', 'activeCount', 'expiredCount',
            'mostViewed', 'viewsByDay'
        ));
    }
}