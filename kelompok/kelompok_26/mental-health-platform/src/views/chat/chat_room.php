<?php
// src/views/chat/chat_room.php
// Simple user-facing chat room reference UI

global $conn;

if (!isset($_SESSION['user'])) {
    echo "<script>window.location='index.php?p=login';</script>";
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['user_id'] ?? $user['id'] ?? null;

// For reference: fetch current active session if provided
$session_id = isset($_GET['session_id']) ? intval($_GET['session_id']) : null;
$konselor_id = isset($_GET['konselor_id']) ? intval($_GET['konselor_id']) : null;

// Jika tidak ada session_id atau konselor_id, cek session terakhir user
if (!$session_id && !$konselor_id) {
    $stmt = $conn->prepare("SELECT session_id FROM chat_session WHERE user_id = ? ORDER BY started_at DESC LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $last_session = $stmt->get_result()->fetch_assoc();
        if ($last_session) {
            $session_id = $last_session['session_id'];
        }
    }
}

// Jika hanya konselor_id diberikan (dari match), cek/buat session
if ($konselor_id && !$session_id) {
    // Check if session already exists
    $stmt = $conn->prepare("SELECT session_id FROM chat_session WHERE user_id = ? AND konselor_id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('ii', $user_id, $konselor_id);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();
        
        if ($existing) {
            $session_id = $existing['session_id'];
        } else {
            // Create new session
            $stmt_insert = $conn->prepare("INSERT INTO chat_session (user_id, konselor_id, started_at, status) VALUES (?, ?, NOW(), 'active')");
            if ($stmt_insert) {
                $stmt_insert->bind_param('ii', $user_id, $konselor_id);
                $stmt_insert->execute();
                $session_id = $stmt_insert->insert_id;
            }
        }
    }
}

// Jika tidak ada session_id, redirect ke match untuk memilih konselor
if (!$session_id) {
    echo "<script>window.location='?p=match';</script>";
    exit;
}

$konselor = null;
$messages = [];

// Fetch session dan konselor info
$stmt = $conn->prepare("SELECT s.*, k.name AS konselor_name, k.profile_picture AS konselor_pic FROM chat_session s LEFT JOIN konselor k ON k.konselor_id = s.konselor_id WHERE s.session_id = ? AND s.user_id = ? LIMIT 1");
if ($stmt) { 
    $stmt->bind_param('ii', $session_id, $user_id); 
    $stmt->execute(); 
    $konselor = $stmt->get_result()->fetch_assoc();
    
    if (!$konselor) {
        echo "<script>window.location='?p=match';</script>";
        exit;
    }
}

// Fetch messages dari database
$stmt = $conn->prepare("SELECT message_id, session_id, sender_type, sender_id, message, created_at FROM chat_message WHERE session_id = ? ORDER BY created_at ASC");
if ($stmt) {
    $stmt->bind_param('i', $session_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $messages[] = $row;
    }
}

?>


<div class="min-h-screen" style="background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 50%, var(--bg-primary) 100%);">

    <div class="flex min-h-screen">
        <?php $current_page = 'chat'; include dirname(__DIR__) . '/partials/sidebar.php'; ?>

        <main class="flex-1 p-6" style="margin-left:260px;">
            <div class="max-w-6xl mx-auto">
                <div class="flex items-start gap-6">
                    <div class="mb-4 flex items-center justify-between md:hidden">
                        <button id="mobileToggle" class="px-3 py-2 rounded-lg bg-[#3AAFA9] text-white">Menu</button>
                        <div class="font-semibold">Chat</div>
                    </div>
                    <!-- Chat Panel -->
                    <div class="flex-1 bg-white rounded-2xl soft-shadow p-4" style="min-height:520px;">
                        <style>
                            /* Chat bubble styles */
                            .msg-time{font-size:11px;color:#999;margin-bottom:6px}
                            .bubble-left{display:inline-block;background:#E8F8F6;color:#17252A;padding:10px 14px;border-radius:12px;border:1px solid rgba(58, 175, 169, 0.1);}
                            .bubble-right{display:inline-block;background:#3AAFA9;color:#FFFFFF;padding:10px 14px;border-radius:12px;}
                        </style>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <img src="<?= $konselor && $konselor['konselor_pic'] ? '../uploads/images/konselor_profile_pictures/'.htmlspecialchars($konselor['konselor_pic']) : 'https://via.placeholder.com/56x56?text=K' ?>" class="w-12 h-12 rounded-lg object-cover">
                                <div>
                                    <div class="font-semibold"><?= htmlspecialchars($konselor['konselor_name'] ?? 'Konselor Anda') ?></div>
                                    <div class="text-xs" style="color:var(--text-secondary);">Biasanya membalas dalam 1 jam</div>
                                </div>
                            </div>
                            <div class="text-sm" style="color:var(--text-secondary);">Sesi ID: <?= $session_id ? intval($session_id) : '-' ?></div>
                        </div>

                        <div id="messageList" style="height:380px; overflow:auto; padding:12px; border-radius:12px; background:linear-gradient(180deg, #f8fdfc, #ffffff);">
                            <!-- Messages akan dimuat dari database -->
                            <?php if (!empty($messages)): ?>
                                <?php foreach ($messages as $msg): ?>
                                    <div class="mb-4<?= $msg['sender_type'] === 'user' ? ' text-right' : '' ?>">
                                        <div class="msg-time"><?= date('H:i', strtotime($msg['created_at'])) ?></div>
                                        <div class="<?= $msg['sender_type'] === 'user' ? 'bubble-right' : 'bubble-left' ?>">
                                            <?= htmlspecialchars($msg['message']) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-8" style="color: var(--text-secondary);">
                                    <p>Belum ada pesan. Mulailah percakapan dengan konselor Anda!</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-4 flex items-center gap-2">
                            <input id="messageInput" placeholder="Tulis pesan..." class="flex-1 px-4 py-3 rounded-lg border" aria-label="Tulis pesan" />
                            <button id="sendBtn" class="px-4 py-3 bg-[#3AAFA9] text-white rounded-lg">Kirim</button>
                        </div>
                    </div>

                    <!-- Right panel: konselor info -->
                    <aside style="width:320px;">
                        <div class="bg-white rounded-2xl soft-shadow p-6 mb-4">
                            <div class="flex items-center gap-4">
                                <img src="<?= $konselor && $konselor['konselor_pic'] ? '../uploads/images/konselor_profile_pictures/'.htmlspecialchars($konselor['konselor_pic']) : 'https://via.placeholder.com/80x80?text=K' ?>" class="w-16 h-16 rounded-lg object-cover">
                                <div>
                                    <div class="font-semibold"><?= htmlspecialchars($konselor['konselor_name'] ?? 'Konselor Astral') ?></div>
                                    <div class="text-xs" style="color:var(--text-secondary);">Spesialis: Kecemasan & Depresi</div>
                                </div>
                            </div>

                            <div class="mt-4 text-sm" style="color:var(--text-secondary);">
                                <p>Rating: <strong>4.9</strong></p>
                                <p>Pengalaman: <strong>5 tahun</strong></p>
                                <p>Bahasa: <strong>Indonesia, English</strong></p>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl soft-shadow p-6">
                            <h4 class="font-semibold mb-2">Aksi Cepat</h4>
                            <button onclick="openRatingModal()" class="w-full px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg mb-2 text-center font-semibold transition">⭐ Rating Konselor</button>
                            <a href="index.php?p=match" class="block px-3 py-2 bg-[#3AAFA9] text-white rounded-lg mb-2 text-center">Ganti Konselor</a>
                            <a href="index.php?p=user_settings" class="block px-3 py-2 border border-gray-200 rounded-lg text-center">Pengaturan Sesi</a>
                        </div>
                    </aside>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const send = document.getElementById('sendBtn');
    const input = document.getElementById('messageInput');
    const list = document.getElementById('messageList');
    const mobileToggle = document.getElementById('mobileToggle');
    const sidebar = document.getElementById('sidebar');
    const sessionId = <?= $session_id ? intval($session_id) : 0 ?>;
    
    // Get proper base URL for API calls
    const baseUrl = window.location.origin + window.location.pathname.substring(0, window.location.pathname.indexOf('/src') + 4);

    if (mobileToggle && sidebar) {
        mobileToggle.addEventListener('click', function(){
            sidebar.classList.toggle('hidden');
        });
    }

    if (!list) return;

    function renderMessages(messages){
        list.innerHTML = '';
        messages.forEach(m => {
            const wrap = document.createElement('div');
            wrap.className = 'mb-4 ' + (m.sender_type === 'user' ? 'text-right' : '');
            const time = document.createElement('div'); time.className='msg-time';
            const dt = new Date(m.created_at);
            time.innerText = dt.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
            const msg = document.createElement('div');
            msg.className = (m.sender_type === 'user') ? 'bubble-right' : 'bubble-left';
            msg.innerText = m.message;
            wrap.appendChild(time); wrap.appendChild(msg);
            list.appendChild(wrap);
        });
        list.scrollTop = list.scrollHeight;
    }

    async function fetchMessages(){
        if (!sessionId) return;
        try {
            const res = await fetch(baseUrl + '/index.php?p=api_chat&action=fetch&session_id=' + sessionId);
            const j = await res.json();
            if (j.success) renderMessages(j.messages || []);
        } catch(e) {
            console.error('fetchMessages', e);
        }
    }

    async function sendMessage(text){
        if (!sessionId) return;
        try {
            const fd = new FormData();
            fd.append('action','send');
            fd.append('session_id', sessionId);
            fd.append('message', text);
            const res = await fetch(baseUrl + '/index.php?p=api_chat', {method:'POST', body:fd});
            const j = await res.json();
            if (j.success) {
                if (j.message) {
                    // append returned message (contains created_at)
                    const m = j.message;
                    const wrap = document.createElement('div');
                    wrap.className = 'mb-4 text-right';
                    const time = document.createElement('div'); time.className='msg-time'; time.innerText = new Date(m.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
                    const msg = document.createElement('div'); msg.className = 'bubble-right'; msg.innerText = m.message;
                    wrap.appendChild(time); wrap.appendChild(msg);
                    list.appendChild(wrap);
                    list.scrollTop = list.scrollHeight;
                } else {
                    // fallback: refetch
                    fetchMessages();
                }
            }
        } catch(e) { console.error('sendMessage', e); }
    }

    // initial load and polling
    fetchMessages();
    const poll = setInterval(fetchMessages, 3000);

    if (send && input) {
        send.addEventListener('click', function(){
            const v = input.value.trim();
            if (!v) return;
            sendMessage(v);
            input.value = '';
        });

        input.addEventListener('keydown', function(e){
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                send.click();
            }
        });
    }
});
</script>

<!-- Rating Modal -->
<div id="ratingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold mb-4 text-[#17252A]">Rating Konselor</h2>
        
        <div class="mb-6 text-center">
            <p class="text-gray-600 mb-4">Berapa rating untuk konselor ini?</p>
            <div class="flex justify-center gap-2 mb-4">
                <button onclick="setRating(1)" class="text-4xl rating-btn cursor-pointer opacity-40 transition-all" data-rating="1">⭐</button>
                <button onclick="setRating(2)" class="text-4xl rating-btn cursor-pointer opacity-40 transition-all" data-rating="2">⭐</button>
                <button onclick="setRating(3)" class="text-4xl rating-btn cursor-pointer opacity-40 transition-all" data-rating="3">⭐</button>
                <button onclick="setRating(4)" class="text-4xl rating-btn cursor-pointer opacity-40 transition-all" data-rating="4">⭐</button>
                <button onclick="setRating(5)" class="text-4xl rating-btn cursor-pointer opacity-40 transition-all" data-rating="5">⭐</button>
            </div>
            <p id="ratingText" class="text-gray-500">Pilih rating</p>
        </div>
        
        <div class="flex gap-3">
            <button onclick="submitRating()" class="flex-1 px-4 py-2 bg-[#3AAFA9] text-white rounded-lg font-semibold hover:bg-[#2a8b87] transition">Kirim Rating</button>
            <button onclick="closeRatingModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Batal</button>
        </div>
    </div>
</div>

<style>
#ratingModal .rating-btn.active {
    opacity: 1;
    filter: drop-shadow(0 0 3px gold);
}
</style>

<script>
let selectedRating = 0;
const konselorId = <?= isset($konselor['konselor_id']) ? intval($konselor['konselor_id']) : 0 ?>;

function openRatingModal() {
    if (konselorId === 0) {
        alert('Konselor tidak ditemukan');
        return;
    }
    document.getElementById('ratingModal').classList.remove('hidden');
    selectedRating = 0;
    updateRatingDisplay();
}

function closeRatingModal() {
    document.getElementById('ratingModal').classList.add('hidden');
}

function setRating(rating) {
    selectedRating = rating;
    updateRatingDisplay();
}

function updateRatingDisplay() {
    const btns = document.querySelectorAll('.rating-btn');
    btns.forEach(btn => {
        const btnRating = parseInt(btn.getAttribute('data-rating'));
        if (btnRating <= selectedRating) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
    
    const ratingTexts = ['Pilih rating', 'Sangat Buruk', 'Buruk', 'Biasa', 'Baik', 'Sangat Baik'];
    document.getElementById('ratingText').textContent = ratingTexts[selectedRating] || 'Pilih rating';
}

async function submitRating() {
    if (selectedRating === 0) {
        alert('Pilih rating terlebih dahulu');
        return;
    }
    
    const fd = new FormData();
    fd.append('action', 'submit_rating');
    fd.append('konselor_id', konselorId);
    fd.append('rating', selectedRating);
    // include session_id for traceability
    try {
        const sid = <?= $session_id ? intval($session_id) : 0 ?>;
        if (sid) fd.append('session_id', sid);
    } catch (e) {}
    
    try {
        const res = await fetch('index.php?p=handle_rating', {
            method: 'POST',
            body: fd
        });
        const text = await res.text();
        let data;
        try { data = JSON.parse(text); } catch (e) {
            alert('Error: Unexpected response, not JSON.');
            console.error('Rating response text:', text);
            return;
        }
        
        if (data.success) {
            alert('✓ ' + data.message);
            closeRatingModal();
        } else {
            alert('✗ ' + (data.message || 'Gagal submit rating'));
        }
    } catch (e) {
        alert('Error: ' + e.message);
    }
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('ratingModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeRatingModal();
            }
        });
    }
});
</script>

<?php
