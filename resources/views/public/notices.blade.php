<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Notices</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',Tahoma,sans-serif; }
        body { background:#f1f5f9; color:#1e293b; }
        .header { background:linear-gradient(135deg,#1e3a8a,#2563eb); color:#fff; padding:20px; text-align:center; }
        .header h1 { font-size:20px; }
        .header p { font-size:13px; opacity:.85; margin-top:4px; }
        .list { padding:14px; max-width:640px; margin:0 auto; }
        .card { background:#fff; border-radius:14px; padding:16px; margin-bottom:12px;
                box-shadow:0 2px 10px rgba(0,0,0,.06); text-decoration:none; color:inherit; display:block; }
        .card h3 { font-size:16px; margin-bottom:6px; }
        .card p { font-size:14px; color:#64748b; line-height:1.4;
                  display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .badge { display:inline-block; font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px; margin-bottom:8px; }
        .high { background:#fee2e2; color:#dc2626; } .medium { background:#fef3c7; color:#d97706; } .low { background:#dbeafe; color:#2563eb; }
        .pdf-tag { font-size:11px; color:#dc2626; font-weight:600; margin-top:8px; display:inline-block; }
        .empty { text-align:center; color:#94a3b8; padding:60px 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📢 Department Notice Board</h1>
        <p>Department of CSE, KUET</p>
    </div>
    <div class="list">
        @forelse($notices as $n)
            <a href="{{ route('public.show', $n) }}" class="card">
                <span class="badge {{ $n->priority }}">{{ strtoupper($n->priority) }}</span>
                <h3>{{ $n->title }}</h3>
                <p>{{ $n->body }}</p>
                @if($n->type === 'pdf' && $n->file_path)
                    <span class="pdf-tag">📎 PDF attached</span>
                @endif
            </a>
        @empty
            <div class="empty">No notices available right now.</div>
        @endforelse
    </div>
</body>
</html>