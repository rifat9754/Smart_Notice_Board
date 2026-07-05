let playlist = [];
let lastPlaylist = [];   // offline cache: শেষবার যা পেয়েছিলাম
let currentIndex = 0;
let isEmergency = false;

// ---- ঘড়ি ----
function updateClock() {
    const now = new Date();
    document.getElementById('time').textContent =
        now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    document.getElementById('date').textContent =
        now.toLocaleDateString('en-US', { weekday: 'long', day: 'numeric', month: 'long' });
}
setInterval(updateClock, 1000);
updateClock();

// ---- backend থেকে playlist আনা (polling) ----
async function fetchPlaylist() {
    try {
        const res = await fetch(`${API_URL}/display?board_id=${BOARD_ID}`);
        const data = await res.json();
        lastPlaylist = data.playlist;           // সফল হলে cache update
        playlist = data.playlist;
        isEmergency = (data.mode === 'emergency');
        setStatus(true);
    } catch (e) {
        playlist = lastPlaylist;                // ব্যর্থ হলে পুরোনোটা দেখাও
        setStatus(false);
    }
    document.body.classList.toggle('emergency', isEmergency);
    updateTicker();
    if (currentIndex >= playlist.length) currentIndex = 0;
    showNotice();
}

function setStatus(online) {
    const el = document.getElementById('status');
    el.className = 'status ' + (online ? 'online' : 'offline');
    el.textContent = online ? '● online' : '● offline — showing last update';
}


function showNotice() {
    const box = document.getElementById('noticeBox');
    if (!playlist.length) {
        box.innerHTML = '<div class="notice-title">No active notices</div>';
        document.getElementById('qrcode').innerHTML = '';
        return;
    }

    const n = playlist[currentIndex];

    const tag = isEmergency
        ? '<div class="emergency-tag">⚠ EMERGENCY</div>'
        : `<div class="priority-badge priority-${n.priority}">${n.priority}</div>`;

    const summary = n.ai_summary
        ? `<div class="ai-summary"><span>AI Summary</span>${n.ai_summary}</div>` : '';

    box.innerHTML = `
        ${tag}
        <div class="notice-title">${n.title}</div>
        <div class="notice-body">${n.body}</div>
        ${summary}
    `;

    // QR — এই notice-এর detail link
    const qrBox = document.getElementById('qrcode');
    qrBox.innerHTML = '';
new QRCode(document.getElementById("qrcode"), {
    text: window.location.origin + "/public-notices",
    width: 90, height: 90,
});

    // view log (analytics-এর জন্য)
    fetch(`${API_URL}/notices/${n.id}/view`, { method: 'POST' }).catch(() => {});
}

// ---- ticker (জরুরি + high notice-এর শিরোনাম ঘুরবে) ----
function updateTicker() {
    const items = playlist
        .filter(n => isEmergency || n.priority === 'high')
        .map(n => n.title);
    const text = items.length ? items.join('     ●     ') : 'Department Notice Board';
    document.getElementById('tickerText').textContent = text;
}

// ---- প্রতি ৮ সেকেন্ডে পরের notice-এ যাওয়া ----
function nextNotice() {
    if (!playlist.length) return;
    currentIndex = (currentIndex + 1) % playlist.length;
    showNotice();
}

// ---- চালু ----
fetchPlaylist();                       // প্রথমবার সাথে সাথে
setInterval(fetchPlaylist, 8000);      // প্রতি ৮ সেকেন্ডে নতুন data
setInterval(nextNotice, 8000);         // প্রতি ৮ সেকেন্ডে পরের notice