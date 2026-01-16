<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="glass-card overflow-hidden" style="height: calc(100vh - 180px); min-height: 500px;">
        <div class="flex h-full">
            <!-- Conversations List (Sidebar) -->
            <div id="conversations-sidebar" class="w-full md:w-1/4 border-r border-slate-700 flex flex-col flex-shrink-0 <?= isset($activeConversation) || isset($isNewConversation) ? 'hidden md:flex' : '' ?>">
                <!-- Header -->
                <div class="p-4 border-b border-slate-700">
                    <h1 class="text-xl font-bold text-white" style="min-height:40px">Mesajlar</h1>
                </div>

                <!-- Conversations List -->
                <div class="flex-1 overflow-y-auto" id="conversations-list">
                    <?php if (empty($conversations)): ?>
                        <div class="p-8 text-center">
                            <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p class="text-slate-400">Henüz mesaj yok</p>
                            <p class="text-slate-500 text-sm mt-2">Bir kullanıcının profiline gidip mesaj gönderebilirsiniz.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($conversations as $conv): ?>
                            <a href="<?= base_url('messages/' . $conv['id']) ?>"
                               class="flex items-center gap-3 p-4 hover:bg-slate-700/50 transition-colors border-b border-slate-700/50 <?= (isset($activeConversation) && $activeConversation['id'] == $conv['id']) ? 'bg-purple-500/10 border-l-2 border-l-purple-500' : '' ?>">
                                <!-- Avatar -->
                                <?php if (!empty($conv['other_user']['avatar'])): ?>
                                    <img src="<?= esc($conv['other_user']['avatar']) ?>" alt="<?= esc($conv['other_user']['name']) ?>" class="w-12 h-12 rounded-full object-cover flex-shrink-0" referrerpolicy="no-referrer">
                                <?php else: ?>
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-medium flex-shrink-0">
                                        <?= strtoupper(substr($conv['other_user']['name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-medium text-white truncate"><?= esc($conv['other_user']['name']) ?></h3>
                                        <?php if ($conv['unread_count'] > 0): ?>
                                            <span class="bg-purple-500 text-white text-xs font-medium px-2 py-0.5 rounded-full"><?= $conv['unread_count'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($conv['last_message'])): ?>
                                        <p class="text-sm text-slate-400 truncate">
                                            <?php if ($conv['last_message']['sender_id'] == $currentUser['id']): ?>
                                                <span class="text-slate-500">Siz:</span>
                                            <?php endif; ?>
                                            <?= esc(mb_substr(strip_tags(html_entity_decode($conv['last_message']['content'])), 0, 40)) ?>
                                        </p>
                                        <p class="text-xs text-slate-500 mt-1">
                                            <?= date('d M H:i', strtotime($conv['last_message']['created_at'])) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Chat Area -->
            <div id="chat-area" class="w-full md:flex-1 flex flex-col <?= !isset($activeConversation) && !isset($isNewConversation) ? 'hidden md:flex' : '' ?>">
                <?php if (isset($otherUser) && $otherUser): ?>
                    <!-- Chat Header -->
                    <div class="p-4 border-b border-slate-700 flex items-center gap-3">
                        <!-- Back button (mobile) -->
                        <a href="<?= base_url('messages') ?>" class="md:hidden p-2 -ml-2 rounded-lg hover:bg-slate-700 transition-colors">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>

                        <!-- User Info -->
                        <a href="<?= base_url('user/' . $otherUser['id']) ?>" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                            <?php if (!empty($otherUser['avatar'])): ?>
                                <img src="<?= esc($otherUser['avatar']) ?>" alt="<?= esc($otherUser['name']) ?>" class="w-10 h-10 rounded-full object-cover" referrerpolicy="no-referrer">
                            <?php else: ?>
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-medium">
                                    <?= strtoupper(substr($otherUser['name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h2 class="font-semibold text-white"><?= esc($otherUser['name']) ?></h2>
                            </div>
                        </a>
                    </div>

                    <!-- Messages Area -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messages-container">
                        <?php if (isset($isNewConversation) && $isNewConversation && !$canMessage): ?>
                            <!-- Can't message warning -->
                            <div class="flex items-center justify-center h-full">
                                <div class="text-center p-8">
                                    <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    <h3 class="text-lg font-semibold text-white mb-2">Mesaj Gönderilemiyor</h3>
                                    <p class="text-slate-400 mb-4">Bu kullanıcıya mesaj göndermek için <strong class="text-white"><?= esc($otherUser['name']) ?></strong> tarafından takip edilmeniz gerekiyor.</p>
                                    <a href="<?= base_url('user/' . $otherUser['id']) ?>" class="btn-secondary inline-flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Profile Git
                                    </a>
                                </div>
                            </div>
                        <?php elseif (empty($messages)): ?>
                            <div class="flex items-center justify-center h-full">
                                <div class="text-center p-8">
                                    <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    <p class="text-slate-400">Henüz mesaj yok</p>
                                    <p class="text-slate-500 text-sm mt-2">İlk mesajı gönderin!</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($messages as $msg): ?>
                                <?php $isMine = $msg['sender_id'] == $currentUser['id']; ?>
                                <div class="flex <?= $isMine ? 'justify-end' : 'justify-start' ?>" data-message-id="<?= $msg['id'] ?>">
                                    <div class="max-w-[75%] <?= $isMine ? 'order-2' : '' ?>">
                                        <?php if (!$isMine): ?>
                                            <div class="flex items-end gap-2">
                                                <?php if (!empty($msg['sender_avatar'])): ?>
                                                    <img src="<?= esc($msg['sender_avatar']) ?>" alt="" class="w-8 h-8 rounded-full object-cover mb-1" referrerpolicy="no-referrer">
                                                <?php else: ?>
                                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-sm font-medium mb-1">
                                                        <?= strtoupper(substr($msg['sender_name'], 0, 1)) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="bg-slate-700 rounded-2xl rounded-bl-md px-4 py-2">
                                                    <p class="text-white whitespace-pre-wrap break-words"><?= nl2br(html_entity_decode($msg['content'])) ?></p>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="bg-purple-600 rounded-2xl rounded-br-md px-4 py-2">
                                                <p class="text-white whitespace-pre-wrap break-words"><?= nl2br(html_entity_decode($msg['content'])) ?></p>
                                            </div>
                                        <?php endif; ?>
                                        <p class="text-xs text-slate-500 mt-1 <?= $isMine ? 'text-right' : 'ml-10' ?>">
                                            <?= date('d M H:i', strtotime($msg['created_at'])) ?>
                                            <?php if ($isMine && $msg['is_read']): ?>
                                                <span class="text-purple-400 ml-1" title="Okundu">✓✓</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Message Input -->
                    <?php if (!isset($isNewConversation) || $canMessage): ?>
                    <div class="p-4 border-t border-slate-700">
                        <form id="message-form" class="flex gap-3">
                            <input type="hidden" id="conversation-id" value="<?= isset($activeConversation) ? $activeConversation['id'] : '' ?>">
                            <input type="hidden" id="recipient-id" value="<?= isset($recipientId) ? $recipientId : '' ?>">

                            <textarea
                                id="message-input"
                                rows="1"
                                placeholder="Mesajınızı yazın..."
                                class="flex-1 bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 resize-none transition-all"
                                maxlength="2000"
                            ></textarea>

                            <button type="submit" class="btn-primary px-4" id="send-btn">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </button>
                        </form>
                        <p class="text-xs text-slate-500 mt-2">Enter ile gönder, Shift+Enter ile yeni satır</p>
                    </div>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- No conversation selected -->
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center p-8">
                            <svg class="w-20 h-20 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <h2 class="text-xl font-semibold text-white mb-2">Mesajlarınız</h2>
                            <p class="text-slate-400">Bir konuşma seçin veya yeni bir sohbet başlatın.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const messagesContainer = document.getElementById('messages-container');
const messageForm = document.getElementById('message-form');
const messageInput = document.getElementById('message-input');
const conversationIdInput = document.getElementById('conversation-id');
const recipientIdInput = document.getElementById('recipient-id');
const sendBtn = document.getElementById('send-btn');

let conversationId = conversationIdInput ? conversationIdInput.value : '';
let lastMessageId = 0;
let pollInterval = 2000; // Start with 2 seconds
const maxPollInterval = 30000; // Max 30 seconds
let pollTimer = null;

// Initialize
if (messagesContainer) {
    scrollToBottom();

    // Get last message ID
    const messages = messagesContainer.querySelectorAll('[data-message-id]');
    if (messages.length > 0) {
        lastMessageId = parseInt(messages[messages.length - 1].dataset.messageId);
    }

    // Start polling if we have a conversation
    if (conversationId) {
        startPolling();
    }
}

// Auto-resize textarea
if (messageInput) {
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 150) + 'px';
    });

    // Handle Enter key
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            messageForm.dispatchEvent(new Event('submit'));
        }
    });
}

// Send message
if (messageForm) {
    messageForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const content = messageInput.value.trim();
        if (!content) return;

        // Disable button
        sendBtn.disabled = true;

        try {
            const formData = new FormData();
            formData.append('content', content);

            if (conversationId) {
                formData.append('conversation_id', conversationId);
            } else if (recipientIdInput && recipientIdInput.value) {
                formData.append('recipient_id', recipientIdInput.value);
            }

            const response = await fetch('<?= base_url('api/messages/send') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Clear input
                messageInput.value = '';
                messageInput.style.height = 'auto';

                // Add message to UI
                appendMessage(data.data, true);
                scrollToBottom();

                // Update conversation ID if this was a new conversation
                if (!conversationId && data.conversation_id) {
                    conversationId = data.conversation_id;
                    conversationIdInput.value = conversationId;

                    // Update URL without reload
                    history.replaceState(null, '', '<?= base_url('messages') ?>/' + conversationId);

                    // Start polling
                    startPolling();
                }

                // Reset poll interval
                pollInterval = 2000;
            } else {
                if (data.needsFollow) {
                    showToast(data.message, 'error');
                } else {
                    showToast(data.message || 'Mesaj gönderilemedi', 'error');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Mesaj gönderilemedi', 'error');
        }

        sendBtn.disabled = false;
        messageInput.focus();
    });
}

function appendMessage(msg, isMine) {
    // Remove empty state if exists
    const emptyState = messagesContainer.querySelector('.flex.items-center.justify-center');
    if (emptyState) {
        emptyState.remove();
    }

    const messageHtml = createMessageHtml(msg, isMine);
    messagesContainer.insertAdjacentHTML('beforeend', messageHtml);

    // Update last message ID
    lastMessageId = Math.max(lastMessageId, parseInt(msg.id));
}

function createMessageHtml(msg, isMine) {
    const time = new Date(msg.created_at).toLocaleString('tr-TR', {
        day: '2-digit',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
    });

    if (isMine) {
        return `
            <div class="flex justify-end" data-message-id="${msg.id}">
                <div class="max-w-[75%]">
                    <div class="bg-purple-600 rounded-2xl rounded-br-md px-4 py-2">
                        <p class="text-white whitespace-pre-wrap break-words">${escapeHtml(msg.content).replace(/\n/g, '<br>')}</p>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 text-right">${time}</p>
                </div>
            </div>
        `;
    } else {
        const avatar = msg.sender_avatar
            ? `<img src="${escapeHtml(msg.sender_avatar)}" alt="" class="w-8 h-8 rounded-full object-cover mb-1" referrerpolicy="no-referrer">`
            : `<div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-sm font-medium mb-1">${escapeHtml(msg.sender_name).charAt(0).toUpperCase()}</div>`;

        return `
            <div class="flex justify-start" data-message-id="${msg.id}">
                <div class="max-w-[75%]">
                    <div class="flex items-end gap-2">
                        ${avatar}
                        <div class="bg-slate-700 rounded-2xl rounded-bl-md px-4 py-2">
                            <p class="text-white whitespace-pre-wrap break-words">${escapeHtml(msg.content).replace(/\n/g, '<br>')}</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 ml-10">${time}</p>
                </div>
            </div>
        `;
    }
}

function scrollToBottom() {
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Polling for new messages
function startPolling() {
    if (pollTimer) clearTimeout(pollTimer);

    pollTimer = setTimeout(async () => {
        try {
            const response = await fetch(`<?= base_url('api/messages') ?>/${conversationId}/poll?last_id=${lastMessageId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success && data.messages && data.messages.length > 0) {
                // New messages received - reset interval
                pollInterval = 2000;

                data.messages.forEach(msg => {
                    // Only add if not sent by current user (we already added sent messages)
                    if (msg.sender_id != <?= $currentUser['id'] ?>) {
                        appendMessage(msg, false);
                    }
                    lastMessageId = Math.max(lastMessageId, parseInt(msg.id));
                });

                scrollToBottom();

                // Play notification sound (optional)
                // playNotificationSound();
            } else {
                // No new messages - gradually increase interval
                pollInterval = Math.min(pollInterval * 1.5, maxPollInterval);
            }
        } catch (error) {
            console.error('Polling error:', error);
            pollInterval = Math.min(pollInterval * 2, maxPollInterval);
        }

        // Continue polling
        startPolling();
    }, pollInterval);
}

// Stop polling when page is hidden
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        if (pollTimer) clearTimeout(pollTimer);
    } else {
        if (conversationId) {
            pollInterval = 2000; // Reset interval
            startPolling();
        }
    }
});

// Update message badge in navbar
function updateMessageBadge() {
    fetch('<?= base_url('api/messages/unread-count') ?>')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('message-badge');
            if (badge) {
                if (data.count > 0) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.classList.remove('hidden');
                    badge.classList.add('flex');
                } else {
                    badge.classList.add('hidden');
                    badge.classList.remove('flex');
                }
            }
        });
}

// Update badge periodically
setInterval(updateMessageBadge, 30000);
</script>
<?= $this->endSection() ?>
