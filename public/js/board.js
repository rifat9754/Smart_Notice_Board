let playlist = [];
let lastPlaylist = [];   // offline cache
let currentIndex = 0;
let isEmergency = false;

// clock
function updateClock() {
    const now = new Date();
    document.getElementById('time').textContent =
        now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    document.getElementById('date').textContent =
        now.toLocaleDateString('en-US', { weekday: 'long', day: 'numeric', month: 'long' });
}
setInterval(updateClock, 1000);
updateClock();

// ---- backend to playlist (polling) ----
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

    // QR 
    const qrBox = document.getElementById('qrcode');
    qrBox.innerHTML = '';
new QRCode(document.getElementById("qrcode"), {
    text: window.location.origin + "/public-notices",
    width: 90, height: 90,
});

    // view log 
    fetch(`${API_URL}/notices/${n.id}/view`, { method: 'POST' }).catch(() => {});
}

// ---- ticker 
async function updateTicker() {
    try {
        const res = await fetch(`${API_URL}/ticker`);
        const data = await res.json();
        const messages = data.messages || [];
        const text = messages.length
            ? messages.join('     ●     ')
            : 'Welcome to the Department of CSE, KUET';
        document.getElementById('tickerText').textContent = text;
    } catch (e) {
        document.getElementById('tickerText').textContent = 'Welcome to the Department of CSE, KUET';
    }
}

// ---- per 8 second notice
function nextNotice() {
    if (!playlist.length) return;
    currentIndex = (currentIndex + 1) % playlist.length;
    showNotice();
}

// start
fetchPlaylist();                       // first
setInterval(fetchPlaylist, 8000);      // per 8 sec new data
setInterval(nextNotice, 8000);         // per 8 seconds to the next notice
setInterval(updateTicker, 30000);      // প্রতি ৩০ সেকেন্ডে ticker আলাদাভাবে refresh