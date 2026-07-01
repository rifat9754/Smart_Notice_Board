<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notice Board</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #0f172a;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top bar */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: clamp(12px, 2vw, 18px) clamp(16px, 3vw, 32px);
            background: #1e293b;
            border-bottom: 3px solid #3b82f6;
            gap: 12px;
            flex-wrap: wrap;
        }
        .topbar h1 { font-size: clamp(18px, 2.5vw, 28px); }
        .clock { text-align: right; font-size: clamp(15px, 1.8vw, 20px); }
        .clock .date { font-size: clamp(12px, 1.2vw, 15px); color: #94a3b8; }
        .status { font-size: 13px; margin-top: 4px; }
        .status.online { color: #4ade80; }
        .status.offline { color: #f87171; }

        /* Main notice area */
        .main {
            flex: 1;
            display: flex;
            padding: clamp(20px, 4vw, 40px);
            gap: clamp(20px, 4vw, 40px);
            align-items: center;
            flex-wrap: wrap;
        }
        .notice-box { flex: 1; min-width: 260px; }
        .priority-badge {
            display: inline-block;
            padding: 6px 18px;
            border-radius: 20px;
            font-size: clamp(13px, 1.4vw, 16px);
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        .priority-high { background: #dc2626; }
        .priority-medium { background: #d97706; }
        .priority-low { background: #2563eb; }
        .notice-title {
            font-size: clamp(28px, 5vw, 64px);
            line-height: 1.2;
            margin-bottom: 24px;
        }
        .notice-body {
            font-size: clamp(18px, 2.5vw, 32px);
            color: #cbd5e1;
            line-height: 1.5;
        }
        .ai-summary {
            margin-top: 24px;
            padding: 18px 24px;
            background: #1e293b;
            border-left: 4px solid #3b82f6;
            border-radius: 8px;
            font-size: clamp(16px, 1.8vw, 22px);
            color: #e2e8f0;
        }
        .ai-summary span { color: #60a5fa; font-size: 15px; display: block; margin-bottom: 6px; }

        /* QR */
        .qr-box { text-align: center; }
        .qr-box #qrcode { background: #fff; padding: 12px; border-radius: 12px; display: inline-block; }
        .qr-box #qrcode img, .qr-box #qrcode canvas { width: clamp(90px, 12vw, 150px) !important; height: auto !important; }
        .qr-box p { margin-top: 10px; font-size: 14px; color: #94a3b8; }

        /* Ticker */
        .ticker {
            background: #1e293b;
            border-top: 2px solid #3b82f6;
            padding: 14px 0;
            white-space: nowrap;
            overflow: hidden;
        }
        .ticker span {
            display: inline-block;
            padding-left: 100%;
            font-size: clamp(16px, 1.8vw, 22px);
            animation: scroll 22s linear infinite;
        }
        @keyframes scroll {
            from { transform: translateX(0); }
            to { transform: translateX(-100%); }
        }

        /* Emergency mode */
        body.emergency { background: #7f1d1d; }
        body.emergency .topbar { background: #991b1b; border-bottom-color: #fca5a5; }
        .emergency-tag {
            display: inline-block; background: #fff; color: #991b1b;
            padding: 6px 18px; border-radius: 20px; font-size: clamp(14px, 1.6vw, 18px);
            font-weight: bold; margin-bottom: 20px;
        }

        /* Small screens: stack everything */
        @media (max-width: 600px) {
            .topbar { flex-direction: column; align-items: flex-start; }
            .clock { text-align: left; }
            .main { flex-direction: column; text-align: center; }
            .notice-box { min-width: 100%; }
            .qr-box { margin: 0 auto; }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <h1>📢 Department Notice Board</h1>
        <div class="clock">
            <div id="time">--:--</div>
            <div class="date" id="date">---</div>
            <div class="status online" id="status">● online</div>
        </div>
    </div>

    <div class="main">
        <div class="notice-box" id="noticeBox">
            <div class="notice-title">Loading notices…</div>
        </div>
        <div class="qr-box">
            <div id="qrcode"></div>
            <p>Scan for details</p>
        </div>
    </div>

    <div class="ticker"><span id="tickerText">Welcome to the department notice board</span></div>

    <script>
        const BOARD_ID = {{ $boardId }};
        const API_URL = "{{ url('/api') }}";
    </script>
    <script src="{{ asset('js/board.js') }}"></script>
</body>
</html>