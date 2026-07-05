<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notice->title }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',Tahoma,sans-serif; }
        body { background:#f1f5f9; color:#1e293b; }
        .header { background:linear-gradient(135deg,#1e3a8a,#2563eb); color:#fff; padding:16px 20px; }
        .header a { color:#fff; text-decoration:none; font-size:14px; }
        .content { padding:18px; max-width:640px; margin:0 auto; }
        .badge { display:inline-block; font-size:12px; font-weight:700; padding:4px 12px; border-radius:20px; margin-bottom:12px; }
        .high { background:#fee2e2; color:#dc2626; } .medium { background:#fef3c7; color:#d97706; } .low { background:#dbeafe; color:#2563eb; }
        h1 { font-size:22px; margin-bottom:14px; line-height:1.3; }
        .body { background:#fff; border-radius:14px; padding:18px; font-size:16px; line-height:1.6; color:#334155;
                box-shadow:0 2px 10px rgba(0,0,0,.06); }
        .ai { background:#eff6ff; border:1px solid #bfdbfe; border-radius:14px; padding:16px; margin-top:14px; }
        .ai b { color:#2563eb; }
        .pdf-btn { display:block; text-align:center; background:#dc2626; color:#fff; padding:14px; border-radius:12px;
                   text-decoration:none; font-weight:700; margin-top:16px; }
        .img-att { width:100%; border-radius:12px; margin-top:14px; }
    </style>
</head>
<body>
    <div class="header">
        <a href="{{ route('public.notices') }}">← Back to all notices</a>
    </div>
    <div class="content">
        <span class="badge {{ $notice->priority }}">{{ strtoupper($notice->priority) }} PRIORITY</span>
        <h1>{{ $notice->title }}</h1>
        <div class="body">{{ $notice->body }}</div>

        @if($notice->ai_summary)
            <div class="ai"><b>✨ AI Summary</b><br>{{ $notice->ai_summary }}</div>
        @endif

        @if($notice->file_path)
            @if($notice->type === 'pdf')
                <a href="{{ asset('storage/'.$notice->file_path) }}" target="_blank" class="pdf-btn">
                    📎 View / Download PDF
                </a>
            @elseif($notice->type === 'image')
                <img src="{{ asset('storage/'.$notice->file_path) }}" class="img-att" alt="attachment">
            @endif
        @endif
    </div>
</body>
</html>