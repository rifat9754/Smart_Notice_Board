<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'department' => $user->department,
            ],
        ]);
    }

    public function register(Request $request)
{
    $data = $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|string|min:6',
        'role'     => 'required|in:student,teacher',
    ]);

    // student হলে email অবশ্যই @stud.kuet.ac.bd
    if ($data['role'] === 'student' && !str_ends_with($data['email'], '@stud.kuet.ac.bd')) {
        return response()->json([
            'message' => 'Students must use a @stud.kuet.ac.bd email.'
        ], 422);
    }

    $user = \App\Models\User::create([
        'name'     => $data['name'],
        'email'    => $data['email'],
        'password' => \Hash::make($data['password']),
        'role'     => $data['role'],
        'status'   => 'pending',   // admin approval লাগবে
    ]);

    return response()->json([
        'message' => 'Registration successful! Please wait for admin approval before logging in.',
    ], 201);
}

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}

/*

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
            ->whereDoesntHave('author', fn($q) => $q->where('role', 'cr'))   // without CR 
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
}

*/