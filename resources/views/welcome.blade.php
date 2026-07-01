<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Notice Board</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
        }
        .icon { font-size: 80px; margin-bottom: 20px; }
        h1 { font-size: 48px; margin-bottom: 12px; }
        .subtitle { font-size: 20px; color: #cbd5e1; margin-bottom: 12px; max-width: 600px; }
        .tagline { font-size: 15px; color: #94a3b8; margin-bottom: 40px; }
        .buttons { display: flex; gap: 16px; flex-wrap: wrap; justify-content: center; }
        .btn {
            padding: 14px 32px;
            border-radius: 10px;
            font-size: 17px;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.4); }
        .btn-primary { background: #fff; color: #1e3a8a;  }
        .btn-board { background: #fff; color: #1e3a8a; }
        .btn-register { background: #fff; color: #1e3a8a; border: 2px solid #fff; }
       
        .features {
            margin-top: 56px;
            display: flex;
            gap: 28px;
            flex-wrap: wrap;
            justify-content: center;
            max-width: 800px;
        }
        .feature {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 22px;
            width: 230px;
        }
        .feature .fi { font-size: 30px; margin-bottom: 10px; }
        .feature h3 { font-size: 17px; margin-bottom: 6px; }
        .feature p { font-size: 13px; color: #94a3b8; line-height: 1.5; }
        footer { margin-top: 56px; font-size: 13px; color: #64748b; }
    </style>
</head>
<body>
    <div class="icon">📢</div>
    <h1>KUET CSE Notice Board</h1>
    <p class="tagline">Department of Computer Science &amp; Engineering</p>

<div class="buttons">
        <a href="/login" class="btn btn-primary">Login</a>
        <a href="/register" class="btn btn-register">Register</a>
        <a href="/board/1" class="btn btn-board">View Display Board</a>
    </div>

    <div class="features">
        <div class="feature">
            <div class="fi">🧠</div>
            <h3>Smart Scheduling</h3>
            <p>Priority, urgency, freshness &amp; fairness decide what shows when.</p>
        </div>
        <div class="feature">
            <div class="fi">✨</div>
            <h3>AI Summarizer</h3>
            <p>Long PDF notices are auto-condensed into short summaries.</p>
        </div>
        <div class="feature">
            <div class="fi">📊</div>
            <h3>Analytics &amp; Audit</h3>
            <p>Track notice views and every admin action in one place.</p>
        </div>
    </div>

    <footer>KUET CSE Digital Notice Board &middot; 2026</footer>
</body>
</html>