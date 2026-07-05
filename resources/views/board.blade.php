<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Notice Board</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        :root {
            --bg-1: #0b1220;
            --bg-2: #0f1a30;
            --panel: rgba(30, 41, 59, 0.65);
            --panel-solid: #172033;
            --border: rgba(148, 163, 184, 0.16);
            --blue: #3b82f6;
            --blue-soft: #60a5fa;
            --green: #22c55e;
            --green-soft: #4ade80;
            --text: #f1f5f9;
            --muted: #94a3b8;
            --muted-2: #cbd5e1;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, sans-serif;
            background:
                radial-gradient(1200px 700px at 85% -5%, rgba(59,130,246,0.18), transparent 60%),
                radial-gradient(900px 600px at 0% 110%, rgba(34,197,94,0.12), transparent 55%),
                linear-gradient(160deg, var(--bg-1), var(--bg-2));
            color: var(--text);
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* ---------- Top bar ---------- */
        .topbar {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: clamp(12px, 1.6vw, 20px) clamp(20px, 3vw, 40px);
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            box-shadow: 0 4px 24px rgba(0,0,0,0.25);
            flex-shrink: 0;
            position: relative;
        }
        .topbar::after {
            content: '';
            position: absolute; left: 0; right: 0; bottom: -1px; height: 3px;
            background: linear-gradient(90deg, var(--blue), var(--green-soft));
        }
        .topbar img { height: clamp(46px, 5.5vw, 66px); width: auto; }
        .topbar .titles { flex: 1; }
        .topbar h1 {
            font-size: clamp(21px, 3vw, 36px);
            font-weight: 800; letter-spacing: 0.3px;
            line-height: 1.05;
        }
        .topbar .sub { font-size: clamp(11px, 1.3vw, 15px); color: var(--muted); margin-top: 3px; }

        .clock { text-align: right; line-height: 1.15; }
        .clock #time {
            font-size: clamp(20px, 2.3vw, 30px); font-weight: 800;
            font-variant-numeric: tabular-nums; letter-spacing: 0.5px;
        }
        .clock .date { font-size: clamp(11px, 1.1vw, 14px); color: var(--muted); }
        .status {
            font-size: 12px; margin-top: 4px; display: inline-flex; align-items: center; gap: 6px;
            padding: 2px 10px; border-radius: 999px; font-weight: 600;
        }
        .status.online  { color: var(--green-soft); background: rgba(34,197,94,0.12); }
        .status.offline { color: #f87171;         background: rgba(248,113,113,0.12); }

        /* ---------- Main ---------- */
        .main {
            flex: 1;
            display: flex;
            padding: clamp(18px, 2.4vw, 34px);
            gap: clamp(18px, 2.4vw, 34px);
            min-height: 0;
        }

        /* Left column: notice (top) + QR (bottom) */
        .notice-panel {
            flex: 1.5;
            min-width: 0;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }
        #noticeBox {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
            --fit: 1;
        }

        /* priority label (high/medium/low) — hidden per request */
        #noticeBox .priority,
        #noticeBox [class*="priority"],
        .notice-priority,
        .notice-badge { display: none !important; }

        .notice-title {
            font-size: calc(clamp(30px, 4.4vw, 62px) * var(--fit));
            line-height: 1.12;
            margin-bottom: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(180deg, #ffffff, #cdd8ea);
            -webkit-background-clip: text; background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .notice-body {
            font-size: calc(clamp(18px, 2.1vw, 29px) * var(--fit));
            color: var(--muted-2);
            line-height: 1.5;
        }
        .ai-summary {
            margin-top: 24px;
            padding: 16px 22px;
            background: var(--panel);
            border: 1px solid var(--border);
            border-left: 4px solid var(--blue);
            border-radius: 14px;
            font-size: calc(clamp(15px, 1.6vw, 20px) * var(--fit));
            color: var(--muted-2);
            backdrop-filter: blur(8px);
        }
        .ai-summary span {
            color: var(--blue-soft); font-size: 12px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
            display: block; margin-bottom: 8px;
        }

        /* QR — under the notice; text sits BELOW the QR, left aligned */
        .qr-box {
            align-self: flex-start;
            display: flex; flex-direction: column; align-items: center; gap: 10px;
            margin-top: clamp(18px, 2vw, 28px);
            padding: 14px;
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            backdrop-filter: blur(8px);
        }
        .qr-box .qr-inner {
            background: #fff;
            padding: 8px;
            border-radius: 10px;
            line-height: 0;
        }
        .qr-box #qrcode { display: inline-block; line-height: 0; }
        .qr-box #qrcode img,
        .qr-box #qrcode canvas {
            width: clamp(64px, 5vw, 82px) !important;
            height: auto !important;
        }
        .qr-box .qr-text { text-align: center; }
        .qr-box .qr-text b { font-size: clamp(13px, 1.3vw, 16px); color: var(--text); display: block; }
        .qr-box .qr-text p { margin-top: 3px; font-size: clamp(11px, 1vw, 12px); color: var(--muted); }

        /* Right column: event image */
        .side-panel {
            flex: 1;
            min-width: 260px;
            display: flex;
            min-height: 0;
        }
        .event-box {
            flex: 1;
            background: var(--panel-solid);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            min-height: 0;
            box-shadow: 0 20px 50px rgba(0,0,0,0.35);
        }
        .event-box .empty {
            height: 100%; display: flex; align-items: center; justify-content: center;
            color: #64748b; font-size: 15px;
        }
        .event-header {
            position: absolute; top: 14px; left: 14px; z-index: 2;
            background: rgba(37,99,235,0.92);
            color: #fff; font-size: clamp(11px,1.2vw,14px); font-weight: 700;
            padding: 6px 14px; border-radius: 999px;
            letter-spacing: 0.5px;
        }
        .event-caption {
            position: absolute; left: 0; right: 0; bottom: 0;
            padding: 26px 22px 18px;
            background: linear-gradient(to top, rgba(0,0,0,0.9), rgba(0,0,0,0.35) 55%, transparent);
            font-size: clamp(16px, 1.9vw, 24px);
            font-weight: 700; color: #fff;
        }

        /* ---------- Class Updates ---------- */
        .class-updates {
            background: rgba(16, 32, 60, 0.6);
            border-top: 1px solid rgba(34,197,94,0.35);
            padding: clamp(10px, 1.2vw, 16px) clamp(20px, 3vw, 40px);
            max-height: 20vh;
            overflow: hidden;
            flex-shrink: 0;
            backdrop-filter: blur(6px);
        }
        .class-updates h3 {
            font-size: clamp(13px, 1.5vw, 17px);
            color: var(--green-soft);
            margin-bottom: 8px;
            display: flex; align-items: center; gap: 8px;
        }
        .cu-item {
            display: flex; align-items: baseline; gap: 12px;
            padding: 6px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            font-size: clamp(13px, 1.4vw, 18px);
        }
        .cu-item:last-child { border-bottom: none; }
        .cu-class {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: #fff; font-size: 0.72em; font-weight: 700;
            padding: 3px 11px; border-radius: 999px; flex-shrink: 0;
            letter-spacing: 0.3px;
        }
        .cu-title { font-weight: 700; color: #fff; }
        .cu-body { color: var(--muted); font-size: 0.88em; }

        /* ---------- Ticker ---------- */
        .ticker {
            background: rgba(15, 23, 42, 0.85);
            border-top: 1px solid var(--border);
            padding: 11px 0;
            white-space: nowrap;
            overflow: hidden;
            flex-shrink: 0;
            position: relative;
        }
        .ticker::before {
            content: 'LIVE';
            position: absolute; left: 0; top: 0; bottom: 0; z-index: 2;
            display: flex; align-items: center;
            padding: 0 18px;
            font-size: 12px; font-weight: 800; letter-spacing: 1px; color: #fff;
            background: linear-gradient(90deg, var(--blue), rgba(59,130,246,0));
        }
        .ticker span {
            display: inline-block;
            padding-left: 100%;
            font-size: clamp(14px, 1.6vw, 20px);
            color: var(--muted-2);
            animation: scroll 22s linear infinite;
        }
        @keyframes scroll { from { transform: translateX(0); } to { transform: translateX(-100%); } }

        /* ---------- Emergency ---------- */
        body.emergency {
            background:
                radial-gradient(1000px 600px at 80% 0%, rgba(248,113,113,0.25), transparent 60%),
                linear-gradient(160deg, #450a0a, #7f1d1d);
        }
        body.emergency .topbar { background: rgba(127,29,29,0.75); }
        body.emergency .topbar::after { background: linear-gradient(90deg, #fca5a5, #f87171); }
    </style>
</head>
<body>
    <div class="topbar">
        <img src="{{ asset('kuet-logo.png') }}" alt="KUET CSE" onerror="this.style.display='none'">
        <div class="titles">
            <h1>Department Notice Board</h1>
            <div class="sub">Department of Computer Science &amp; Engineering, KUET</div>
        </div>
        <div class="clock">
            <div id="time">--:--</div>
            <div class="date" id="date">---</div>
            <div class="status online" id="status">online</div>
        </div>
    </div>

    <div class="main">
        <!-- Left: notice + QR -->
        <div class="notice-panel">
            <div id="noticeBox">
                <div class="notice-title">Loading notices…</div>
            </div>

            <!-- QR now sits under the notice -->
            <div class="qr-box">
                <div class="qr-inner">
                    <div id="qrcode"></div>
                </div>
                <div class="qr-text">
                    <b>Scan for details</b>
                   
                </div>
            </div>
        </div>

        <!-- Right: event image -->
        <div class="side-panel">
            <div class="event-box">
                <div id="eventArea" class="empty">Loading…</div>
            </div>
        </div>
    </div>

    <!-- Class Updates -->
    <div class="class-updates" id="classUpdates" style="display:none;">
        <h3>📋 Class Updates (from CR)</h3>
        <div id="cuList"></div>
    </div>

    <div class="ticker"><span id="tickerText">Welcome to the department notice board</span></div>

    <script>
        const BOARD_ID = {{ $boardId }};
        const API_URL = "{{ url('/api') }}";
    </script>
    <script src="{{ asset('js/board.js') }}"></script>

    <!-- Auto-fit: shrink notice text so a long notice fully fits the screen -->
    <script>
        const noticeBox = document.getElementById('noticeBox');

        function fitNotice() {
            if (!noticeBox) return;
            // start at full size, then shrink until it fits (or hits a readable minimum)
            let fit = 1;
            noticeBox.style.setProperty('--fit', fit);
            let guard = 0;
            while (noticeBox.scrollHeight > noticeBox.clientHeight + 1 && fit > 0.35 && guard < 60) {
                fit -= 0.04;
                noticeBox.style.setProperty('--fit', fit.toFixed(3));
                guard++;
            }
        }

        // Re-fit whenever board.js swaps in new notice content
        if (noticeBox) {
            const observer = new MutationObserver(() => {
                // wait a frame so the browser lays the new text out first
                requestAnimationFrame(fitNotice);
            });
            observer.observe(noticeBox, { childList: true, subtree: true, characterData: true });
        }

        // Re-fit on window / screen size changes
        window.addEventListener('resize', () => requestAnimationFrame(fitNotice));
        window.addEventListener('load', fitNotice);
        setTimeout(fitNotice, 500);
    </script>

    <!-- Event image rotator -->
    <script>
        let eventImages = [];
        let eventIdx = 0;

        async function loadEvents() {
            try {
                const res = await fetch(`${API_URL}/events`);
                const data = await res.json();
                eventImages = data.events || [];
                renderEvent();
            } catch (e) {}
        }
        function renderEvent() {
            const area = document.getElementById('eventArea');
            if (!area) return;
            if (eventImages.length === 0) {
                area.className = 'empty';
                area.innerHTML = 'No events yet';
                return;
            }
            const ev = eventImages[eventIdx % eventImages.length];
            area.className = '';
            area.innerHTML = `
                <img src="${ev.image}" alt="event" style="width:100%;height:100%;object-fit:cover;">
                ${ev.title ? `<div class="event-caption">${ev.title}</div>` : ''}
            `;
        }
        function rotateEvent() {
            if (eventImages.length > 1) { eventIdx++; renderEvent(); }
        }
        loadEvents();
        setInterval(loadEvents, 30000);
        setInterval(rotateEvent, 8000);

        // Class Updates
let cuAll = [];      // সব CR notice
let cuPage = 0;      // এখন কোন জোড়া দেখাচ্ছি

async function loadClassUpdates() {
    try {
        const res = await fetch(`${API_URL}/class-updates`);
        const data = await res.json();
        cuAll = data.updates || [];
        renderClassUpdates();
    } catch (e) {
        document.getElementById('classUpdates').style.display = 'none';
    }
}

function renderClassUpdates() {
    const box = document.getElementById('classUpdates');
    const list = document.getElementById('cuList');
    if (cuAll.length === 0) { box.style.display = 'none'; return; }
    box.style.display = 'block';

    const perPage = 2;                                   // ২টা করে
    const totalPages = Math.ceil(cuAll.length / perPage);
    const start = (cuPage % totalPages) * perPage;
    const pageItems = cuAll.slice(start, start + perPage);

    list.innerHTML = pageItems.map(u => `
        <div class="cu-item">
            ${u.year && u.section ? `<span class="cu-class">${u.year}-${u.section}</span>` : ''}
            <span class="cu-title">${u.title}</span>
            <span class="cu-body">${u.body.length > 55 ? u.body.slice(0,55) + '…' : u.body}</span>
        </div>
    `).join('');
}

function rotateClassUpdates() {
    if (cuAll.length > 2) {          
        cuPage++;
        renderClassUpdates();
    }
}

loadClassUpdates();
setInterval(loadClassUpdates, 8000);  
setInterval(rotateClassUpdates, 4000);  
    </script>
</body>
</html>