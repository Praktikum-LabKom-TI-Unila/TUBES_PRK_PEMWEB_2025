<?php
// src/views/chat/konselor_chat.php
// Chat Room untuk Konselor - Astral Psychologist

global $conn;

if (!isset($_SESSION['konselor'])) {
    echo "<script>window.location='index.php?p=login';</script>";
    exit;
}

$konselor = $_SESSION['konselor'];
$konselor_id = $konselor['konselor_id'] ?? $konselor['id'] ?? null;

// Get session_id or user_id from query parameters
$session_id = $_GET['session_id'] ?? null;
$user_id = $_GET['user_id'] ?? null;

// If user_id provided, get the active/latest session with that user
if ($user_id && !$session_id) {
    $stmt = $conn->prepare("
        SELECT session_id FROM chat_session 
        WHERE konselor_id = ? AND user_id = ? 
        ORDER BY started_at DESC LIMIT 1
    ");
    $stmt->bind_param("ii", $konselor_id, $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows) {
        $session_id = $res->fetch_assoc()['session_id'];
    }
}

// Fetch session details
$session = null;
$user_info = null;
$user_survey = null;
if ($session_id) {
    $stmt = $conn->prepare("
        SELECT cs.*, u.name, u.email, u.profile_picture
        FROM chat_session cs
        JOIN users u ON u.user_id = cs.user_id
        WHERE cs.session_id = ? AND cs.konselor_id = ?
    ");
    $stmt->bind_param("ii", $session_id, $konselor_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows) {
        $session = $res->fetch_assoc();
        $user_info = [
            'name' => $session['name'],
            'email' => $session['email'],
            'profile_picture' => $session['profile_picture'],
            'user_id' => $session['user_id']
        ];
        // Fetch user's latest survey for communication/approach style
        $surveyStmt = $conn->prepare("SELECT q1, q2, q3, q4 FROM user_survey WHERE user_id = ? ORDER BY survey_id DESC LIMIT 1");
        if ($surveyStmt) {
            $surveyStmt->bind_param("i", $session['user_id']);
            $surveyStmt->execute();
            $surveyRes = $surveyStmt->get_result();
            if ($surveyRes && $surveyRes->num_rows) {
                $user_survey = $surveyRes->fetch_assoc();
            }
        }
    }
}

// Helper function to format user preference description
function getUserPreferenceDesc($survey) {
    if (!$survey) return null;
    
    // Communication style from q1, q2, q3
    $direct_count = 0;
    $gentle_count = 0;
    foreach ([$survey['q1'], $survey['q2'], $survey['q3']] as $ans) {
        if ($ans == 1) $direct_count++;
        if ($ans == 2) $gentle_count++;
    }
    
    if ($direct_count >= 2) {
        $comm_label = "Komunikator Tegas";
        $comm_desc = "Lebih nyaman dengan komunikasi langsung & jelas";
    } elseif ($gentle_count >= 2) {
        $comm_label = "Komunikator Empatik";
        $comm_desc = "Lebih nyaman dengan komunikasi lembut & hangat";
    } else {
        $comm_label = "Komunikator Seimbang";
        $comm_desc = "Fleksibel dengan gaya komunikasi";
    }
    
    // Emotional approach from q4
    if (($survey['q4'] ?? 0) == 1) {
        $approach_label = "Pemikir Logis";
        $approach_desc = "Lebih suka pendekatan rasional & analitis";
    } else {
        $approach_label = "Perasa Emosional";
        $approach_desc = "Lebih suka pendekatan hangat & suportif";
    }
    
    return [
        'comm_label' => $comm_label,
        'comm_desc' => $comm_desc,
        'approach_label' => $approach_label,
        'approach_desc' => $approach_desc
    ];
}

$userPreference = getUserPreferenceDesc($user_survey);

// Fetch messages
$messages = [];
if ($session_id) {
    $stmt = $conn->prepare("
        SELECT * FROM chat_message
        WHERE session_id = ?
        ORDER BY created_at ASC
    ");
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $messages[] = $row;
        }
    }
}

// Fetch all clients for sidebar
$clients = [];
$stmt = $conn->prepare("
    SELECT DISTINCT u.user_id, u.name, u.email, u.profile_picture,
           COUNT(cs.session_id) as session_count,
           MAX(cs.started_at) as last_session
    FROM chat_session cs
    JOIN users u ON u.user_id = cs.user_id
    WHERE cs.konselor_id = ?
    GROUP BY u.user_id
    ORDER BY MAX(cs.started_at) DESC
    LIMIT 15
");
if ($stmt) {
    $stmt->bind_param("i", $konselor_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $clients[] = $row;
        }
    }
}
?>

<div class="min-h-screen" style="background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 25%, var(--bg-primary) 50%, var(--bg-secondary) 75%, var(--bg-primary) 100%); position: relative;">

    <div class="flex min-h-screen">
        <!-- Sidebar Konselor -->
        <div style="width: 280px; background: var(--bg-card); border-right: 1px solid var(--border-color); position: fixed; height: 100vh; overflow-y: auto; z-index: 10;">
            <div class="p-6">
                <h2 style="color: var(--text-primary); font-weight: 600; font-size: 18px; margin-bottom: 20px;">Klien Saya</h2>
                
                <div class="space-y-2">
                    <?php if (empty($clients)): ?>
                        <div style="color: var(--text-secondary); font-size: 14px; padding: 20px;">Belum ada klien</div>
                    <?php else: ?>
                        <?php foreach ($clients as $c): ?>
                            <a href="index.php?p=konselor_chat&user_id=<?= intval($c['user_id']) ?>" 
                               style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 8px; text-decoration: none; transition: all 0.2s; background: <?= (isset($user_info) && $user_info['user_id'] == $c['user_id']) ? 'rgba(58, 175, 169, 0.15)' : 'transparent' ?>; border-left: 3px solid <?= (isset($user_info) && $user_info['user_id'] == $c['user_id']) ? '#3AAFA9' : 'transparent' ?>;">
                                <img src="<?= isset($c['profile_picture']) && $c['profile_picture'] ? "../uploads/images/user_profile_pictures/".htmlspecialchars($c['profile_picture']) : 'https://via.placeholder.com/40x40?text=U' ?>" 
                                     style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                <div style="flex: 1; min-width: 0;">
                                    <div style="color: var(--text-primary); font-weight: 500; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($c['name']) ?></div>
                                    <div style="color: var(--text-secondary); font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($c['email']) ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 20px; border-top: 1px solid var(--border-color); background: var(--bg-card);">
                <a href="index.php?p=konselor_dashboard" style="display: block; padding: 10px; text-align: center; background: #3AAFA9; color: white; border-radius: 8px; text-decoration: none; font-weight: 500;">← Kembali ke Dashboard</a>
            </div>
        </div>

        <!-- Main Chat Area -->
        <main style="margin-left: 280px; flex: 1; display: flex; flex-direction: column; background: var(--bg-secondary);">
            
            <?php if ($session && $user_info): ?>
                <!-- Chat Header -->
                <div style="border-bottom: 1px solid var(--border-color); padding: 20px; background: var(--bg-card);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <img src="<?= isset($user_info['profile_picture']) && $user_info['profile_picture'] ? "../uploads/images/user_profile_pictures/".htmlspecialchars($user_info['profile_picture']) : 'https://via.placeholder.com/50x50?text=User' ?>" 
                                 style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                            <div>
                                <h2 style="color: var(--text-primary); font-weight: 600; font-size: 18px;"><?= htmlspecialchars($user_info['name']) ?></h2>
                                <p style="color: var(--text-secondary); font-size: 14px;">Status: <strong><?= htmlspecialchars(ucfirst($session['status'] ?? '-')) ?></strong></p>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <p style="color: var(--text-secondary); font-size: 12px;">Mulai: <?= date('d M Y H:i', strtotime($session['started_at'] ?? date('Y-m-d H:i'))) ?></p>
                        </div>
                    </div>
                    
                    <!-- User Preference Description -->
                    <?php if ($userPreference): ?>
                    <div style="padding: 12px; background: rgba(58, 175, 169, 0.1); border-radius: 8px; border-left: 3px solid #3AAFA9;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <p style="color: var(--text-primary); font-weight: 600; font-size: 13px; margin: 0 0 4px 0;"><?= htmlspecialchars($userPreference['comm_label']) ?></p>
                                <p style="color: var(--text-secondary); font-size: 13px; margin: 0;"><?= htmlspecialchars($userPreference['comm_desc']) ?></p>
                            </div>
                            <div>
                                <p style="color: var(--text-primary); font-weight: 600; font-size: 13px; margin: 0 0 4px 0;"><?= htmlspecialchars($userPreference['approach_label']) ?></p>
                                <p style="color: var(--text-secondary); font-size: 13px; margin: 0;"><?= htmlspecialchars($userPreference['approach_desc']) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Messages Area -->
                <div id="messagesArea" style="flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 10px;">
                    <?php if (empty($messages)): ?>
                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: var(--text-secondary);">
                            Belum ada pesan. Mulai percakapan Anda.
                        </div>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <div data-message-id="<?= $msg['message_id'] ?>" style="display: flex; justify-content: <?= $msg['sender_type'] === 'konselor' ? 'flex-end' : 'flex-start' ?>;">
                                <div style="background: <?= $msg['sender_type'] === 'konselor' ? '#3AAFA9' : '#E8F8F6' ?>; color: <?= $msg['sender_type'] === 'konselor' ? '#FFFFFF' : '#17252A' ?>; padding: 12px 16px; border-radius: 12px; max-width: 60%; word-wrap: break-word;">
                                    <p style="margin: 0; font-size: 14px;"><?= htmlspecialchars($msg['message']) ?></p>
                                    <small style="opacity: 0.7; font-size: 12px; display: block; margin-top: 4px;"><?= date('H:i', strtotime($msg['created_at'])) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Input Area -->
                <div style="border-top: 1px solid var(--border-color); padding: 20px; background: var(--bg-card);">
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="messageInput" placeholder="Ketik pesan..." 
                               style="flex: 1; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 8px; background: var(--bg-secondary); color: var(--text-primary); font-family: inherit;">
                        <button onclick="sendMessage()" style="padding: 10px 20px; background: #3AAFA9; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">Kirim</button>
                    </div>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div style="flex: 1; display: flex; align-items: center; justify-content: center; color: var(--text-secondary);">
                    <div style="text-align: center;">
                        <h2 style="color: var(--text-primary); margin-bottom: 10px;">Pilih Klien untuk Mulai Chat</h2>
                        <p>Pilih klien dari daftar di sebelah kiri untuk memulai percakapan.</p>
                    </div>
                </div>
            <?php endif; ?>

        </main>
    </div>

</div>

<script>
const sessionId = <?= json_encode($session_id) ?>;
let lastMessageId = 0;

// Fetch messages (polling)
async function fetchMessages() {
    if (!sessionId) return;
    try {
        const res = await fetch('?p=api_chat&action=fetch&session_id=' + sessionId);
        const data = await res.json();
        if (data.success && data.messages) {
            const messagesArea = document.getElementById('messagesArea');
            if (!messagesArea) return;
            
            // Check if new messages arrived
            const newMessages = data.messages.filter(m => m.message_id > lastMessageId);
            if (newMessages.length > 0) {
                newMessages.forEach(msg => {
                    appendMessage(msg);
                    lastMessageId = Math.max(lastMessageId, msg.message_id);
                });
                messagesArea.scrollTop = messagesArea.scrollHeight;
            }
        }
    } catch (e) {
        console.error('fetchMessages', e);
    }
}

function appendMessage(msg) {
    const messagesArea = document.getElementById('messagesArea');
    if (!messagesArea) return;
    
    const isKonselor = msg.sender_type === 'konselor';
    
    // Create wrapper with flexbox (sama seperti PHP render)
    const wrapper = document.createElement('div');
    wrapper.setAttribute('data-message-id', msg.message_id);
    wrapper.style.display = 'flex';
    wrapper.style.justifyContent = isKonselor ? 'flex-end' : 'flex-start';
    
    // Create bubble container
    const bubble = document.createElement('div');
    bubble.style.background = isKonselor ? '#3AAFA9' : '#E8F8F6';
    bubble.style.color = isKonselor ? '#FFFFFF' : '#17252A';
    bubble.style.padding = '12px 16px';
    bubble.style.borderRadius = '12px';
    bubble.style.maxWidth = '60%';
    bubble.style.wordWrap = 'break-word';
    
    // Create message text
    const messageText = document.createElement('p');
    messageText.style.margin = '0';
    messageText.style.fontSize = '14px';
    messageText.textContent = msg.message;
    
    // Create timestamp
    const timestamp = document.createElement('small');
    timestamp.style.opacity = '0.7';
    timestamp.style.fontSize = '12px';
    timestamp.style.display = 'block';
    timestamp.style.marginTop = '4px';
    const msgDate = new Date(msg.created_at);
    timestamp.textContent = msgDate.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});
    
    // Assemble
    bubble.appendChild(messageText);
    bubble.appendChild(timestamp);
    wrapper.appendChild(bubble);
    messagesArea.appendChild(wrapper);
}

function sendMessage() {
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    if (!message) return;
    
    const formData = new FormData();
    formData.append('action', 'send');
    formData.append('session_id', sessionId);
    formData.append('message', message);

    fetch('?p=api_chat', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageInput.value = '';
            // Message will appear via polling
            fetchMessages();
        } else {
            alert('Error: ' + (data.error || data.message || 'Gagal mengirim pesan'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengirim pesan.');
    });
}

// Initialize
if (sessionId) {
    // Get initial last message ID
    const messages = document.querySelectorAll('[data-message-id]');
    messages.forEach(el => {
        const id = parseInt(el.getAttribute('data-message-id'));
        lastMessageId = Math.max(lastMessageId, id);
    });
    
    // Start polling
    setInterval(fetchMessages, 3000);
}

// Auto scroll to bottom
const messagesArea = document.getElementById('messagesArea');
if (messagesArea) {
    messagesArea.scrollTop = messagesArea.scrollHeight;
}

// Enter key to send
document.getElementById('messageInput')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        sendMessage();
    }
});
</script>

<style>
body {
    margin: 0;
    padding: 0;
}

#messagesArea::-webkit-scrollbar {
    width: 8px;
}

#messagesArea::-webkit-scrollbar-track {
    background: var(--bg-secondary);
}

#messagesArea::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 4px;
}

#messagesArea::-webkit-scrollbar-thumb:hover {
    background: rgba(58, 175, 169, 0.5);
}
</style>